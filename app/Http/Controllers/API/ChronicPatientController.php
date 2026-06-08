<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\ChronicPatient;
use App\Models\PatientMedication;
use App\Services\CloudinaryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChronicPatientController extends Controller
{
    // ── Patients ──────────────────────────────────────────

    public function index(Request $request): JsonResponse
    {
        $query = ChronicPatient::withCount('medications')
            ->withSum('medications', 'price');

        if ($request->filled('search')) {
            $q = '%' . $request->search . '%';
            $query->where(function ($q2) use ($q) {
                $q2->where('full_name', 'like', $q)
                   ->orWhere('phone', 'like', $q)
                   ->orWhere('disease_name', 'like', $q);
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->boolean('with_medications')) {
            $query->with(['medications' => fn($q) => $q->orderByDesc('start_date')]);
        }

        $patients = $query->orderBy('full_name')->get()->map(fn($p) => $this->formatPatient($p));

        return response()->json(['status' => true, 'data' => $patients]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'full_name'    => 'required|string|max:255',
            'gender'       => 'nullable|in:male,female',
            'birth_date'   => 'nullable|date',
            'phone'        => 'nullable|string|max:30',
            'whatsapp'     => 'nullable|string|max:30',
            'disease_name' => 'required|string|max:255',
            'notes'        => 'nullable|string',
            'is_active'    => 'boolean',
        ]);

        $patient = ChronicPatient::create($request->all());

        return response()->json(['status' => true, 'data' => $this->formatPatient($patient)], 201);
    }

    public function show(int $id): JsonResponse
    {
        $patient = ChronicPatient::with(['medications' => fn($q) => $q->orderByDesc('start_date')])->findOrFail($id);

        $medications = $patient->medications->map(fn($m) => $this->formatMedication($m));
        $totalSpent  = $patient->medications->sum(fn($m) => (float) $m->price * (float) $m->quantity);

        return response()->json([
            'status' => true,
            'data'   => array_merge($this->formatPatient($patient), [
                'medications' => $medications,
                'total_spent' => $totalSpent,
            ]),
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $patient = ChronicPatient::findOrFail($id);
        $request->validate([
            'full_name'    => 'sometimes|required|string|max:255',
            'gender'       => 'nullable|in:male,female',
            'birth_date'   => 'nullable|date',
            'phone'        => 'nullable|string|max:30',
            'whatsapp'     => 'nullable|string|max:30',
            'disease_name' => 'sometimes|required|string|max:255',
            'notes'        => 'nullable|string',
            'is_active'    => 'boolean',
        ]);

        $patient->update($request->all());

        return response()->json(['status' => true, 'data' => $this->formatPatient($patient)]);
    }

    public function destroy(int $id): JsonResponse
    {
        $patient = ChronicPatient::with('medications')->findOrFail($id);
        $cloudinary = app(CloudinaryService::class);

        foreach ($patient->medications as $med) {
            if ($med->image_public_id) {
                $cloudinary->delete($med->image_public_id);
            }
            if ($med->payment_method && (float) $med->price > 0) {
                Bank::adjustByMethod($med->payment_method, (float) $med->price * (float) $med->quantity);
            }
        }

        $patient->delete();

        return response()->json(['status' => true, 'data' => null]);
    }

    // ── Medications ───────────────────────────────────────

    public function addMedication(Request $request, int $patientId): JsonResponse
    {
        $patient = ChronicPatient::findOrFail($patientId);

        $request->validate([
            'name'           => 'required|string|max:255',
            'price'          => 'required|numeric|min:0',
            'quantity'       => 'required|numeric|min:0.01',
            'payment_method' => 'required|string|max:50',
            'start_date'     => 'required|date',
            'duration_value' => 'required|integer|min:1',
            'duration_unit'  => 'required|in:days,weeks,months',
            'notes'          => 'nullable|string',
            'image'          => 'nullable|image|max:5120',
        ]);

        $price    = (float) $request->price;
        $quantity = (float) $request->quantity;
        $total    = $price * $quantity;
        $method   = $request->payment_method;

        // Check bank balance
        if ($total > 0) {
            $check = Bank::canDeduct($method, $total);
            if (!$check['ok']) {
                return response()->json([
                    'status' => false,
                    'error'  => 'insufficient_balance',
                    'data'   => $check,
                ], 422);
            }
        }

        $imageUrl      = null;
        $imagePublicId = null;
        if ($request->hasFile('image')) {
            $uploaded      = app(CloudinaryService::class)->upload($request->file('image'), 'charity/medications');
            $imageUrl      = $uploaded['url'];
            $imagePublicId = $uploaded['public_id'];
        }

        $med = PatientMedication::create([
            'patient_id'     => $patient->id,
            'name'           => $request->name,
            'price'          => $price,
            'quantity'       => $quantity,
            'payment_method' => $method,
            'start_date'     => $request->start_date,
            'duration_value' => (int) $request->duration_value,
            'duration_unit'  => $request->duration_unit,
            'notes'          => $request->notes,
            'image_url'      => $imageUrl,
            'image_public_id'=> $imagePublicId,
        ]);

        if ($total > 0) {
            Bank::adjustByMethod($method, -$total);
        }

        return response()->json(['status' => true, 'data' => $this->formatMedication($med)], 201);
    }

    public function removeMedication(int $patientId, int $medId): JsonResponse
    {
        ChronicPatient::findOrFail($patientId);
        $med = PatientMedication::where('patient_id', $patientId)->findOrFail($medId);

        if ($med->image_public_id) {
            app(CloudinaryService::class)->delete($med->image_public_id);
        }

        $total = (float) $med->price * (float) $med->quantity;
        if ($med->payment_method && $total > 0) {
            Bank::adjustByMethod($med->payment_method, $total);
        }

        $med->delete();

        return response()->json(['status' => true, 'data' => null]);
    }

    // ── Helpers ───────────────────────────────────────────

    private function formatPatient(ChronicPatient $p): array
    {
        $data = [
            'id'           => $p->id,
            'full_name'    => $p->full_name,
            'gender'       => $p->gender,
            'birth_date'   => $p->birth_date?->format('Y-m-d'),
            'phone'        => $p->phone,
            'whatsapp'     => $p->whatsapp,
            'disease_name' => $p->disease_name,
            'notes'        => $p->notes,
            'is_active'    => $p->is_active,
            'created_at'   => $p->created_at?->toISOString(),
        ];

        if ($p->relationLoaded('medications')) {
            $data['medications'] = $p->medications->map(fn($m) => $this->formatMedication($m));
            $data['total_spent'] = $p->medications->sum(fn($m) => (float) $m->price * (float) $m->quantity);
        }

        return $data;
    }

    private function formatMedication(PatientMedication $m): array
    {
        return [
            'id'             => $m->id,
            'patient_id'     => $m->patient_id,
            'name'           => $m->name,
            'price'          => (float) $m->price,
            'quantity'       => (float) $m->quantity,
            'total'          => (float) $m->price * (float) $m->quantity,
            'payment_method' => $m->payment_method,
            'start_date'     => $m->start_date?->format('Y-m-d'),
            'duration_value' => $m->duration_value,
            'duration_unit'  => $m->duration_unit,
            'end_date'       => $m->end_date->format('Y-m-d'),
            'days_remaining' => $m->days_remaining,
            'notes'          => $m->notes,
            'image_url'      => $m->image_url,
        ];
    }
}
