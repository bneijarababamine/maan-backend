<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use App\Http\Resources\ContributionResource;
use App\Http\Resources\MemberResource;
use App\Models\ContributionMonth;
use App\Models\Member;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Member::query()->with(['contributions.months']);

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('full_name', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%");
            });
        }

        if ($request->has('is_active')) {
            $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        $now          = now();
        $currentYear  = $now->year;
        $currentMonth = $now->month;

        if ($request->has('filter')) {
            switch ($request->filter) {
                case 'unpaid_this_month':
                    $query->where('is_active', true)
                          ->whereDoesntHave('contributions', fn ($q) =>
                              $q->whereHas('months', fn ($q2) =>
                                  $q2->where('year', $currentYear)->where('month', $currentMonth)
                              )
                          );
                    break;

                case 'paid_this_month':
                    $query->where('is_active', true)
                          ->whereHas('contributions', fn ($q) =>
                              $q->whereHas('months', fn ($q2) =>
                                  $q2->where('year', $currentYear)->where('month', $currentMonth)
                              )
                          );
                    break;

                case 'paid_full_year':
                    // Members who have paid for every month of the current year so far (Jan → current month)
                    $query->where('is_active', true)
                          ->whereRaw("(
                              SELECT COUNT(DISTINCT cm.month)
                              FROM contribution_months cm
                              INNER JOIN contributions c ON c.id = cm.contribution_id
                              WHERE c.member_id = members.id
                              AND cm.year = ?
                          ) >= ?", [$currentYear, $currentMonth]);
                    break;

                case 'three_plus_unpaid':
                    // Members who haven't paid for the last 3 consecutive months
                    $dates = [];
                    $d = $now->copy()->startOfMonth();
                    for ($i = 0; $i < 3; $i++) {
                        $dates[] = ['year' => $d->year, 'month' => $d->month];
                        $d->subMonth();
                    }
                    $query->where('is_active', true);
                    foreach ($dates as $date) {
                        $query->whereDoesntHave('contributions', fn ($q) =>
                            $q->whereHas('months', fn ($q2) =>
                                $q2->where('year', $date['year'])->where('month', $date['month'])
                            )
                        );
                    }
                    break;
            }
        }

        $members = $query->orderBy('full_name')->get();

        return response()->json([
            'status'  => true,
            'message' => 'Liste des membres récupérée.',
            'data'    => MemberResource::collection($members),
        ]);
    }

    public function store(StoreMemberRequest $request): JsonResponse
    {
        $member = Member::create($request->validated());

        return response()->json([
            'status'  => true,
            'message' => 'Membre créé avec succès.',
            'data'    => new MemberResource($member),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $member = Member::with(['contributions.months'])->findOrFail($id);

        return response()->json([
            'status'  => true,
            'message' => 'Membre récupéré.',
            'data'    => new MemberResource($member),
        ]);
    }

    public function update(UpdateMemberRequest $request, int $id): JsonResponse
    {
        $member = Member::findOrFail($id);
        $member->update($request->validated());

        return response()->json([
            'status'  => true,
            'message' => 'Membre mis à jour.',
            'data'    => new MemberResource($member),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $member = Member::findOrFail($id);
        $member->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Membre supprimé.',
            'data'    => null,
        ]);
    }

    public function contributions(int $id): JsonResponse
    {
        $member = Member::findOrFail($id);
        $contributions = $member->contributions()
            ->with(['months', 'registeredBy'])
            ->orderByDesc('paid_at')
            ->get();

        return response()->json([
            'status'  => true,
            'message' => 'Cotisations du membre récupérées.',
            'data'    => ContributionResource::collection($contributions),
        ]);
    }

    public function unpaidMonths(int $id): JsonResponse
    {
        $member = Member::findOrFail($id);

        return response()->json([
            'status'  => true,
            'message' => 'Mois impayés récupérés.',
            'data'    => $member->unpaid_months,
        ]);
    }

    public function paidMonths(int $id): JsonResponse
    {
        Member::findOrFail($id);

        $paid = ContributionMonth::whereHas('contribution', fn ($q) =>
            $q->where('member_id', $id)
        )->get(['year', 'month'])
         ->map(fn ($m) => ['year' => $m->year, 'month' => $m->month]);

        return response()->json([
            'status'  => true,
            'message' => 'Mois payés récupérés.',
            'data'    => $paid,
        ]);
    }
}
