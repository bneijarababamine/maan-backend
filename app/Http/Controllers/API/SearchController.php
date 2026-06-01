<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Guardian;
use App\Models\Orphan;
use App\Models\Member;
use App\Models\Donor;
use App\Models\Family;
use App\Models\ActivityBeneficiary;
use App\Models\Donation;
use App\Models\Contribution;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $q = trim($request->input('q', ''));

        if (strlen($q) < 2) {
            return response()->json(['status' => true, 'data' => [
                'guardians' => [], 'orphans' => [], 'members' => [],
                'donors' => [], 'families' => [],
            ]]);
        }

        $like = '%' . $q . '%';

        // ── Guardians ─────────────────────────────────────────
        $guardians = Guardian::where('name', 'like', $like)
            ->orWhere('father_name', 'like', $like)
            ->orWhere('phone', 'like', $like)
            ->orWhere('whatsapp', 'like', $like)
            ->with('orphans')
            ->get()
            ->map(function (Guardian $g) {
                $benefits = ActivityBeneficiary::where('beneficiary_type', 'orphan')
                    ->whereIn('beneficiary_id', $g->orphans->pluck('id'))
                    ->with('activity')
                    ->get()
                    ->groupBy(fn($b) => $b->activity_id)
                    ->map(function ($group) {
                        $first = $group->first();
                        return [
                            'activity_id'    => $first->activity->id,
                            'activity_title' => $first->activity->title_ar ?? $first->activity->title_fr,
                            'activity_date'  => $first->activity->activity_date?->format('Y-m-d'),
                            'activity_type'  => $first->activity->activity_type,
                            'payment_type'   => $first->activity->payment_type,
                            'total_received' => $group->sum(fn($b) => (float) $b->value_received),
                            'orphan_count'   => $group->count(),
                        ];
                    })
                    ->values();

                return [
                    'type'            => 'guardian',
                    'id'              => $g->id,
                    'name'            => $g->name,
                    'father_name'     => $g->father_name,
                    'phone'           => $g->phone,
                    'whatsapp'        => $g->whatsapp,
                    'is_active'       => $g->is_active,
                    'orphans_count'   => $g->orphans->count(),
                    'activity_benefits' => $benefits,
                ];
            });

        // ── Orphans ───────────────────────────────────────────
        $orphans = Orphan::where('full_name', 'like', $like)
            ->with('guardian')
            ->get()
            ->map(function (Orphan $o) {
                $benefits = ActivityBeneficiary::where('beneficiary_type', 'orphan')
                    ->where('beneficiary_id', $o->id)
                    ->with('activity')
                    ->get()
                    ->map(fn($b) => [
                        'activity_id'    => $b->activity->id,
                        'activity_title' => $b->activity->title_ar ?? $b->activity->title_fr,
                        'activity_date'  => $b->activity->activity_date?->format('Y-m-d'),
                        'activity_type'  => $b->activity->activity_type,
                        'payment_type'   => $b->activity->payment_type,
                        'value_received' => (float) $b->value_received,
                    ]);

                return [
                    'type'              => 'orphan',
                    'id'                => $o->id,
                    'name'              => $o->display_name ?? $o->full_name,
                    'full_name'         => $o->full_name,
                    'gender'            => $o->gender,
                    'age'               => $o->age,
                    'is_active'         => $o->is_active,
                    'guardian_id'       => $o->guardian_id,
                    'guardian_name'     => $o->guardian?->name,
                    'activity_benefits' => $benefits,
                ];
            });

        // ── Members ───────────────────────────────────────────
        $members = Member::where('full_name', 'like', $like)
            ->orWhere('phone', 'like', $like)
            ->orWhere('whatsapp', 'like', $like)
            ->get()
            ->map(function (Member $m) {
                $contributions = Contribution::where('member_id', $m->id)
                    ->orderByDesc('paid_at')
                    ->get()
                    ->map(fn($c) => [
                        'id'           => $c->id,
                        'total_amount' => (float) $c->total_amount,
                        'months_count' => $c->months_count,
                        'paid_at'      => $c->paid_at?->format('Y-m-d'),
                        'payment_method' => $c->payment_method,
                    ]);

                $donations = Donation::where('member_id', $m->id)
                    ->orderByDesc('donated_at')
                    ->get()
                    ->map(fn($d) => [
                        'id'        => $d->id,
                        'amount'    => (float) $d->amount,
                        'donated_at' => $d->donated_at?->format('Y-m-d'),
                        'payment_method' => $d->payment_method,
                    ]);

                $benefits = ActivityBeneficiary::where('beneficiary_type', 'member')
                    ->where('beneficiary_id', $m->id)
                    ->with('activity')
                    ->get()
                    ->map(fn($b) => [
                        'activity_id'    => $b->activity->id,
                        'activity_title' => $b->activity->title_ar ?? $b->activity->title_fr,
                        'activity_date'  => $b->activity->activity_date?->format('Y-m-d'),
                        'activity_type'  => $b->activity->activity_type,
                        'value_received' => (float) $b->value_received,
                    ]);

                return [
                    'type'          => 'member',
                    'id'            => $m->id,
                    'name'          => $m->full_name,
                    'phone'         => $m->phone,
                    'whatsapp'      => $m->whatsapp,
                    'is_active'     => $m->is_active,
                    'contributions' => $contributions,
                    'donations'     => $donations,
                    'activity_benefits' => $benefits,
                ];
            });

        // ── Donors ───────────────────────────────────────────
        $donors = Donor::where('full_name', 'like', $like)
            ->orWhere('phone', 'like', $like)
            ->orWhere('whatsapp', 'like', $like)
            ->get()
            ->map(function (Donor $d) {
                $donations = Donation::where('donor_id', $d->id)
                    ->orderByDesc('donated_at')
                    ->get()
                    ->map(fn($dn) => [
                        'id'             => $dn->id,
                        'amount'         => (float) $dn->amount,
                        'donated_at'     => $dn->donated_at?->format('Y-m-d'),
                        'payment_method' => $dn->payment_method,
                    ]);

                return [
                    'type'      => 'donor',
                    'id'        => $d->id,
                    'name'      => $d->full_name,
                    'phone'     => $d->phone,
                    'whatsapp'  => $d->whatsapp,
                    'donations' => $donations,
                    'activity_benefits' => [],
                ];
            });

        // ── Families ──────────────────────────────────────────
        $families = Family::where('name', 'like', $like)
            ->orWhere('head_of_family', 'like', $like)
            ->orWhere('phone', 'like', $like)
            ->get()
            ->map(function (Family $f) {
                $benefits = ActivityBeneficiary::where('beneficiary_type', 'family')
                    ->where('beneficiary_id', $f->id)
                    ->with('activity')
                    ->get()
                    ->map(fn($b) => [
                        'activity_id'    => $b->activity->id,
                        'activity_title' => $b->activity->title_ar ?? $b->activity->title_fr,
                        'activity_date'  => $b->activity->activity_date?->format('Y-m-d'),
                        'activity_type'  => $b->activity->activity_type,
                        'payment_type'   => $b->activity->payment_type,
                        'value_received' => (float) $b->value_received,
                    ]);

                return [
                    'type'            => 'family',
                    'id'              => $f->id,
                    'name'            => $f->name ?? $f->head_of_family,
                    'head_of_family'  => $f->head_of_family,
                    'phone'           => $f->phone,
                    'is_active'       => $f->is_active,
                    'members_count'   => $f->members_count,
                    'activity_benefits' => $benefits,
                ];
            });

        return response()->json([
            'status' => true,
            'data'   => [
                'guardians' => $guardians,
                'orphans'   => $orphans,
                'members'   => $members,
                'donors'    => $donors,
                'families'  => $families,
            ],
        ]);
    }
}
