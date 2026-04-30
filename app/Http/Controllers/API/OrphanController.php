<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrphanRequest;
use App\Http\Resources\OrphanResource;
use App\Models\ActivityBeneficiary;
use App\Models\Orphan;
use App\Services\CloudinaryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrphanController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Orphan::query();

        match ($request->status) {
            'active'     => $query->where('is_active', true),
            'inactive'   => $query->where('is_active', false),
            'aged_out'   => $query->where('deactivated_reason', 'aged_out'),
            'near_adult' => $query->where('is_active', true)
                                  ->whereDate('birth_date', '<=', now()->subYears(17)->subMonths(6)),
            default      => null,
        };

        if ($request->has('gender')) {
            $query->where('gender', $request->gender);
        }

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('full_name', 'like', "%{$request->search}%")
                  ->orWhere('guardian_name', 'like', "%{$request->search}%");
            });
        }

        $orphans = $query->orderBy('full_name')->paginate(20);

        return response()->json([
            'status'  => true,
            'message' => 'Liste des orphelins récupérée.',
            'data'    => OrphanResource::collection($orphans)->response()->getData(true),
        ]);
    }

    public function store(StoreOrphanRequest $request): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $uploaded           = app(CloudinaryService::class)->upload(
                $request->file('photo'), 'charity/orphans'
            );
            $data['photo_url']       = $uploaded['url'];
            $data['photo_public_id'] = $uploaded['public_id'];
        }

        $orphan = Orphan::create($data);

        return response()->json([
            'status'  => true,
            'message' => 'Orphelin créé avec succès.',
            'data'    => new OrphanResource($orphan),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $orphan = Orphan::with('siblings')->findOrFail($id);

        return response()->json([
            'status'  => true,
            'message' => 'Orphelin récupéré.',
            'data'    => new OrphanResource($orphan),
        ]);
    }

    public function update(StoreOrphanRequest $request, int $id): JsonResponse
    {
        $orphan = Orphan::findOrFail($id);
        $data   = $request->validated();

        if ($request->hasFile('photo')) {
            if ($orphan->photo_public_id) {
                app(CloudinaryService::class)->delete($orphan->photo_public_id);
            }
            $uploaded               = app(CloudinaryService::class)->upload(
                $request->file('photo'), 'charity/orphans'
            );
            $data['photo_url']       = $uploaded['url'];
            $data['photo_public_id'] = $uploaded['public_id'];
        }

        $orphan->update($data);

        return response()->json([
            'status'  => true,
            'message' => 'Orphelin mis à jour.',
            'data'    => new OrphanResource($orphan),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $orphan = Orphan::findOrFail($id);

        if ($orphan->photo_public_id) {
            app(CloudinaryService::class)->delete($orphan->photo_public_id);
        }

        $orphan->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Orphelin supprimé.',
            'data'    => null,
        ]);
    }

    public function deactivate(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'reason' => 'required|in:manual,other',
            'notes'  => 'nullable|string',
        ]);

        $orphan = Orphan::where('is_active', true)->findOrFail($id);
        $orphan->update([
            'is_active'          => false,
            'deactivated_reason' => $request->reason,
            'deactivated_at'     => now(),
            'notes'              => $request->notes ?? $orphan->notes,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Orphelin désactivé.',
            'data'    => new OrphanResource($orphan),
        ]);
    }

    public function siblings(int $id): JsonResponse
    {
        $orphan   = Orphan::findOrFail($id);
        $siblings = $orphan->siblings;

        return response()->json([
            'status'  => true,
            'message' => 'Fratrie récupérée.',
            'data'    => OrphanResource::collection($siblings),
        ]);
    }

    public function addSibling(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'sibling_id' => 'required|exists:orphans,id|different:id',
        ], [
            'sibling_id.required'  => 'L\'identifiant du frère/sœur est obligatoire.',
            'sibling_id.exists'    => 'Cet orphelin n\'existe pas.',
            'sibling_id.different' => 'Un orphelin ne peut pas être son propre frère/sœur.',
        ]);

        $orphan    = Orphan::findOrFail($id);
        $siblingId = $request->sibling_id;

        if ($orphan->siblings()->where('sibling_id', $siblingId)->exists()) {
            return response()->json([
                'status'  => false,
                'message' => 'Ce lien de fratrie existe déjà.',
                'data'    => null,
            ], 422);
        }

        $orphan->siblings()->attach($siblingId);
        Orphan::find($siblingId)->siblings()->syncWithoutDetaching([$id]);

        return response()->json([
            'status'  => true,
            'message' => 'Lien de fratrie ajouté.',
            'data'    => null,
        ]);
    }

    public function removeSibling(int $orphanId, int $siblingId): JsonResponse
    {
        $orphan = Orphan::findOrFail($orphanId);
        $orphan->siblings()->detach($siblingId);
        Orphan::findOrFail($siblingId)->siblings()->detach($orphanId);

        return response()->json([
            'status'  => true,
            'message' => 'Lien de fratrie supprimé.',
            'data'    => null,
        ]);
    }

    public function benefits(int $id): JsonResponse
    {
        Orphan::findOrFail($id);

        $benefits = ActivityBeneficiary::with('activity')
            ->where('beneficiary_type', 'orphan')
            ->where('beneficiary_id', $id)
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($b) => [
                'id'             => $b->id,
                'activity_id'    => $b->activity_id,
                'activity_title' => $b->activity?->title_fr ?? $b->activity?->title_ar ?? '—',
                'activity_type'  => $b->activity?->activity_type,
                'activity_date'  => $b->activity?->activity_date,
                'value_received' => (float) $b->value_received,
                'notes'          => $b->notes,
            ]);

        return response()->json([
            'status'  => true,
            'message' => 'Bénéfices récupérés.',
            'data'    => $benefits,
        ]);
    }
}
// Note: benefits() method will be added via sed
