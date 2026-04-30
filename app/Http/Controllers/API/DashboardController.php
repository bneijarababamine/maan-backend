<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Contribution;
use App\Models\Donation;
use App\Models\Family;
use App\Models\Member;
use App\Models\Orphan;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function stats(): JsonResponse
    {
        $currentYear  = now()->year;
        $currentMonth = now()->month;

        $monthlyContributions = (float) Contribution::whereYear('paid_at', $currentYear)
            ->whereMonth('paid_at', $currentMonth)
            ->sum('total_amount');

        $monthlyDonations = (float) Donation::whereYear('donated_at', $currentYear)
            ->whereMonth('donated_at', $currentMonth)
            ->sum('amount');

        $activeMembers = Member::where('is_active', true)->count();
        $totalMembers  = Member::count();

        $activeOrphans  = Orphan::where('is_active', true)->count();
        $totalOrphans   = Orphan::count();

        $near18Orphans = Orphan::where('is_active', true)
            ->whereDate('birth_date', '<=', now()->subYears(17)->subMonths(6))
            ->whereDate('birth_date', '>', now()->subYears(18))
            ->count();

        $activeFamilies = Family::where('is_active', true)->count();
        $totalFamilies  = Family::count();

        return response()->json([
            'status' => true,
            'data'   => [
                'total_members'         => $totalMembers,
                'active_members'        => $activeMembers,
                'total_orphans'         => $totalOrphans,
                'active_orphans'        => $activeOrphans,
                'near_18_orphans'       => $near18Orphans,
                'total_families'        => $totalFamilies,
                'active_families'       => $activeFamilies,
                'monthly_contributions' => $monthlyContributions,
                'monthly_donations'     => $monthlyDonations,
                'monthly_total'         => $monthlyContributions + $monthlyDonations,
            ],
        ]);
    }

    public function revenue(): JsonResponse
    {
        $months = collect(range(1, 12))->map(function ($m) {
            $year = now()->year;
            $label = now()->setMonth($m)->format('M');

            $contributions = (float) Contribution::whereYear('paid_at', $year)
                ->whereMonth('paid_at', $m)
                ->sum('total_amount');

            $donations = (float) Donation::whereYear('donated_at', $year)
                ->whereMonth('donated_at', $m)
                ->sum('amount');

            return [
                'month'         => $label,
                'contributions' => $contributions,
                'donations'     => $donations,
            ];
        });

        return response()->json(['status' => true, 'data' => $months]);
    }

    public function unpaidMembers(): JsonResponse
    {
        $members = Member::where('is_active', true)->get()
            ->filter(fn($m) => count($m->unpaid_months) > 0)
            ->take(10)
            ->map(fn($m) => [
                'id'           => $m->id,
                'full_name'    => $m->full_name,
                'phone'        => $m->phone,
                'unpaid_count' => count($m->unpaid_months),
            ])
            ->values();

        return response()->json(['status' => true, 'data' => $members]);
    }

    public function near18Orphans(): JsonResponse
    {
        $orphans = Orphan::where('is_active', true)
            ->whereDate('birth_date', '<=', now()->subYears(17))
            ->whereDate('birth_date', '>', now()->subYears(18))
            ->orderBy('birth_date')
            ->take(10)
            ->get()
            ->map(fn($o) => [
                'id'               => $o->id,
                'full_name'        => $o->full_name,
                'birth_date'       => $o->birth_date->toDateString(),
                'months_until_18'  => $o->months_until_18,
            ]);

        return response()->json(['status' => true, 'data' => $orphans]);
    }

    public function recentActivities(): JsonResponse
    {
        $activities = Activity::orderByDesc('activity_date')
            ->take(5)
            ->get()
            ->map(fn($a) => [
                'id'            => $a->id,
                'title'         => $a->title,
                'activity_date' => $a->activity_date->toDateString(),
                'location'      => $a->location,
            ]);

        return response()->json(['status' => true, 'data' => $activities]);
    }

    public function treasury(): JsonResponse
    {
        $paymentMethods = ['especes', 'bankily', 'sadad', 'masrafi'];

        $contribByMethod = Contribution::select('payment_method', DB::raw('SUM(total_amount) as total'))
            ->groupBy('payment_method')
            ->pluck('total', 'payment_method');

        $donationsByMethod = Donation::select('payment_method', DB::raw('SUM(amount) as total'))
            ->groupBy('payment_method')
            ->pluck('total', 'payment_method');

        $cash    = (float) ($contribByMethod->get('especes', 0) + $donationsByMethod->get('especes', 0));
        $bankily = (float) ($contribByMethod->get('bankily', 0) + $donationsByMethod->get('bankily', 0));
        $sadad   = (float) ($contribByMethod->get('sadad', 0) + $donationsByMethod->get('sadad', 0));
        $masrafi = (float) ($contribByMethod->get('masrafi', 0) + $donationsByMethod->get('masrafi', 0));

        $total = $cash + $bankily + $sadad + $masrafi;

        return response()->json([
            'status' => true,
            'data'   => compact('total', 'cash', 'bankily', 'sadad', 'masrafi'),
        ]);
    }
}
