<?php

namespace Database\Seeders;

use App\Models\Donation;
use App\Models\Donor;
use App\Models\User;
use Illuminate\Database\Seeder;

class DonationSeeder extends Seeder
{
    public function run(): void
    {
        $admin   = User::where('phone', '36363833')->first();
        $manager = User::where('phone', '36064707')->first() ?? $admin;
        $donors  = Donor::all();

        $donations = [
            // [donor_index, amount, method, ref, date]
            [0,  5000,   'cash',    null,            '2024-01-20'],
            [1,  10000,  'bankily', 'BNK-DON-001',   '2024-02-15'],
            [2,  3000,   'cash',    null,             '2024-02-28'],
            [3,  8000,   'sadad',   'SAD-DON-001',    '2024-03-10'],
            [4,  2000,   'cash',    null,             '2024-04-05'],
            [5,  15000,  'masrafi', 'MSR-DON-001',   '2024-05-12'],
            [6,  5000,   'bankily', 'BNK-DON-002',   '2024-06-01'],
            [7,  7000,   'cash',    null,             '2024-07-20'],
            [8,  4000,   'bankily', 'BNK-DON-003',   '2024-08-14'],
            [9,  20000,  'sadad',   'SAD-DON-002',    '2024-09-01'],
            [10, 6000,   'cash',    null,             '2024-10-10'],
            [11, 3500,   'masrafi', 'MSR-DON-002',   '2024-11-05'],
            // Dons Ramadan 2025
            [0,  10000,  'bankily', 'BNK-RAM-001',   '2025-03-05'],
            [5,  25000,  'masrafi', 'MSR-RAM-001',   '2025-03-06'],
            [9,  50000,  'sadad',   'SAD-RAM-001',    '2025-03-07'],
            [1,  8000,   'bankily', 'BNK-RAM-002',   '2025-03-10'],
            [3,  12000,  'cash',    null,             '2025-03-15'],
            [7,  5000,   'cash',    null,             '2025-03-20'],
            // Dons récents
            [2,  3000,   'cash',    null,             '2025-04-01'],
            [6,  9000,   'bankily', 'BNK-APR-001',   '2025-04-10'],
        ];

        foreach ($donations as $i => [$donorIdx, $amount, $method, $ref, $date]) {
            if (!isset($donors[$donorIdx])) continue;
            Donation::create([
                'donor_id'             => $donors[$donorIdx]->id,
                'amount'               => $amount,
                'payment_method'       => $method,
                'transaction_ref'      => $ref,
                'screenshot_url'       => null,
                'screenshot_public_id' => null,
                'registered_by'        => $i % 2 === 0 ? $admin->id : $manager->id,
                'notes'                => null,
                'donated_at'           => $date,
            ]);
        }
    }
}
