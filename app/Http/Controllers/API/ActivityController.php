<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreActivityRequest;
use App\Http\Resources\ActivityResource;
use App\Models\Activity;
use App\Models\ActivityBeneficiary;
use App\Models\ActivityItem;
use App\Models\ActivityPhoto;
use App\Models\Bank;
use App\Services\CloudinaryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Activity::with(['creator', 'photos']);

        if ($request->has('activity_type')) {
            $query->where('activity_type', $request->activity_type);
        }
        if ($request->has('beneficiary_type')) {
            $query->where('beneficiary_type', $request->beneficiary_type);
        }
        if ($request->has('year')) {
            $query->whereYear('activity_date', $request->year);
        }

        $activities = $query->orderByDesc('activity_date')->get();

        return response()->json([
            'status'  => true,
            'message' => 'Liste des activités récupérée.',
            'data'    => ActivityResource::collection($activities),
        ]);
    }

    public function store(StoreActivityRequest $request): JsonResponse
    {
        $data = array_merge($request->validated(), ['created_by' => auth()->id()]);
        $data['payment_type']   = $request->input('payment_type', 'financial');
        $data['payment_method'] = $request->input('payment_method');
        $data['total_cost']     = 0;

        $activity = Activity::create($data);

        return response()->json([
            'status'  => true,
            'message' => 'Activité créée avec succès.',
            'data'    => new ActivityResource($activity->load(['creator', 'photos', 'items'])),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $activity = Activity::with([
            'creator', 'photos', 'items',
            'beneficiaries.orphanEntity', 'beneficiaries.familyEntity',
        ])->findOrFail($id);

        return response()->json([
            'status'  => true,
            'message' => 'Activité récupérée.',
            'data'    => new ActivityResource($activity),
        ]);
    }

    public function update(StoreActivityRequest $request, int $id): JsonResponse
    {
        $activity = Activity::findOrFail($id);
        $data = $request->validated();
        $data['payment_type']   = $request->input('payment_type', $activity->payment_type);
        $data['payment_method'] = $request->input('payment_method', $activity->payment_method);
        unset($data['total_cost']);
        $activity->update($data);

        return response()->json([
            'status'  => true,
            'message' => 'Activité mise à jour.',
            'data'    => new ActivityResource($activity->load(['creator', 'photos', 'items'])),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $activity = Activity::with(['photos', 'beneficiaries', 'items'])->findOrFail($id);
        $cloudinary = app(CloudinaryService::class);

        foreach ($activity->photos as $photo) {
            $cloudinary->delete($photo->photo_public_id);
        }

        // Reverse bank balances
        if ($activity->payment_type === 'financial') {
            foreach ($activity->beneficiaries as $b) {
                if ($b->payment_method && (float) $b->value_received > 0) {
                    Bank::adjustByMethod($b->payment_method, (float) $b->value_received);
                }
                if ($b->screenshot_public_id) {
                    $cloudinary->delete($b->screenshot_public_id);
                }
            }
        } elseif ($activity->payment_type === 'in_kind' && $activity->payment_method && (float) $activity->total_cost > 0) {
            Bank::adjustByMethod($activity->payment_method, (float) $activity->total_cost);
        }

        $activity->delete();

        return response()->json(['status' => true, 'message' => 'Activité supprimée.', 'data' => null]);
    }

    // ── Photos ────────────────────────────────────────────
    public function addPhotos(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'photos'        => 'required|array|min:1',
            'photos.*'      => 'required|image|max:5120',
            'captions_ar.*' => 'nullable|string|max:255',
            'captions_fr.*' => 'nullable|string|max:255',
        ]);

        $activity   = Activity::findOrFail($id);
        $cloudinary = app(CloudinaryService::class);
        $added      = [];

        foreach ($request->file('photos') as $index => $photo) {
            $uploaded = $cloudinary->upload($photo, 'charity/activities');
            $record   = ActivityPhoto::create([
                'activity_id'     => $activity->id,
                'photo_url'       => $uploaded['url'],
                'photo_public_id' => $uploaded['public_id'],
                'caption_ar'      => $request->captions_ar[$index] ?? null,
                'caption_fr'      => $request->captions_fr[$index] ?? null,
            ]);
            $added[] = $record;
        }

        return response()->json(['status' => true, 'message' => count($added) . ' photo(s) ajoutée(s).', 'data' => $added], 201);
    }

    public function deletePhoto(int $id, int $photoId): JsonResponse
    {
        $photo = ActivityPhoto::where('activity_id', $id)->findOrFail($photoId);
        app(CloudinaryService::class)->delete($photo->photo_public_id);
        $photo->delete();

        return response()->json(['status' => true, 'message' => 'Photo supprimée.', 'data' => null]);
    }

    // ── Beneficiaries ─────────────────────────────────────
    public function addBeneficiaries(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'beneficiary_type' => 'required|in:orphan,family',
            'beneficiary_id'   => 'required|integer',
            'value_received'   => 'nullable|numeric|min:0',
            'notes'            => 'nullable|string',
            'payment_method'   => 'nullable|string|max:50',
            'screenshot'       => 'nullable|image|max:5120',
        ]);

        $activity = Activity::findOrFail($id);
        $value    = (float) ($request->value_received ?? 0);
        $method   = $request->payment_method;

        // Check bank balance per beneficiary
        if ($activity->payment_type === 'financial' && $method && $value > 0) {
            $check = Bank::canDeduct($method, $value);
            if (!$check['ok']) {
                return response()->json([
                    'status' => false,
                    'error'  => 'insufficient_balance',
                    'data'   => $check,
                ], 422);
            }
        }

        // Upload screenshot if provided
        $screenshotUrl       = null;
        $screenshotPublicId  = null;
        if ($request->hasFile('screenshot')) {
            $uploaded           = app(CloudinaryService::class)->upload($request->file('screenshot'), 'charity/beneficiaries');
            $screenshotUrl      = $uploaded['url'];
            $screenshotPublicId = $uploaded['public_id'];
        }

        ActivityBeneficiary::updateOrCreate(
            [
                'activity_id'      => $activity->id,
                'beneficiary_type' => $request->beneficiary_type,
                'beneficiary_id'   => $request->beneficiary_id,
            ],
            [
                'value_received'       => $value,
                'notes'                => $request->notes,
                'payment_method'       => $method,
                'screenshot_url'       => $screenshotUrl,
                'screenshot_public_id' => $screenshotPublicId,
            ]
        );

        if ($activity->payment_type === 'financial' && $method && $value > 0) {
            Bank::adjustByMethod($method, -$value);
        }

        $totalCost = $activity->beneficiaries()->sum('value_received');
        $activity->update(['total_cost' => $totalCost]);

        return response()->json(['status' => true, 'message' => 'Bénéficiaire ajouté.', 'data' => null]);
    }

    public function removeBeneficiary(int $id, int $benefId): JsonResponse
    {
        $activity = Activity::findOrFail($id);
        $benef    = ActivityBeneficiary::where('activity_id', $id)->findOrFail($benefId);

        // Credit back using the beneficiary's own payment method
        if ($activity->payment_type === 'financial' && $benef->payment_method && (float) $benef->value_received > 0) {
            Bank::adjustByMethod($benef->payment_method, (float) $benef->value_received);
        }

        if ($benef->screenshot_public_id) {
            app(CloudinaryService::class)->delete($benef->screenshot_public_id);
        }

        $benef->delete();

        $totalCost = $activity->beneficiaries()->sum('value_received');
        $activity->update(['total_cost' => $totalCost]);

        return response()->json(['status' => true, 'message' => 'Bénéficiaire supprimé.', 'data' => null]);
    }

    // ── Items (En nature) ────────────────────────────────
    public function addItems(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'items'                  => 'required|array|min:1',
            'items.*.name'           => 'required|string|max:255',
            'items.*.quantity'       => 'required|numeric|min:0.01',
            'items.*.unit_value'     => 'nullable|numeric|min:0',
            'items.*.payment_method' => 'nullable|string|max:50',
        ]);

        $activity = Activity::findOrFail($id);

        // Pre-check balance per item before creating any
        foreach ($request->items as $item) {
            $method = $item['payment_method'] ?? null;
            $cost   = (float) ($item['unit_value'] ?? 0) * (float) $item['quantity'];
            if ($method && $cost > 0) {
                $check = Bank::canDeduct($method, $cost);
                if (!$check['ok']) {
                    return response()->json([
                        'status' => false,
                        'error'  => 'insufficient_balance',
                        'data'   => $check,
                    ], 422);
                }
            }
        }

        $added = [];
        foreach ($request->items as $item) {
            $unitValue  = (float) ($item['unit_value'] ?? 0);
            $qty        = (float) $item['quantity'];
            $itemMethod = $item['payment_method'] ?? null;

            $added[] = ActivityItem::create([
                'activity_id'    => $activity->id,
                'name'           => $item['name'],
                'quantity'       => $qty,
                'unit_value'     => $unitValue,
                'payment_method' => $itemMethod,
            ]);

            if ($itemMethod && $unitValue > 0) {
                Bank::adjustByMethod($itemMethod, -($qty * $unitValue));
            }
        }

        $totalCost = $activity->items()->selectRaw('SUM(quantity * unit_value) as total')->value('total') ?? 0;
        $activity->update(['total_cost' => $totalCost]);

        return response()->json(['status' => true, 'message' => 'Éléments ajoutés.', 'data' => $added], 201);
    }

    public function removeItem(int $id, int $itemId): JsonResponse
    {
        $activity = Activity::findOrFail($id);
        $item     = ActivityItem::where('activity_id', $id)->findOrFail($itemId);
        $itemCost = (float) $item->quantity * (float) $item->unit_value;

        $item->delete();

        // Credit back using the item's own payment method
        if ($item->payment_method && $itemCost > 0) {
            Bank::adjustByMethod($item->payment_method, $itemCost);
        }

        $totalCost = $activity->items()->selectRaw('SUM(quantity * unit_value) as total')->value('total') ?? 0;
        $activity->update(['total_cost' => $totalCost]);

        return response()->json(['status' => true, 'message' => 'Élément supprimé.', 'data' => null]);
    }
}
