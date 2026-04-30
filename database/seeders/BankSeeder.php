<?php

namespace Database\Seeders;

use App\Models\Bank;
use Illuminate\Database\Seeder;

class BankSeeder extends Seeder
{
    public function run(): void
    {
        $banks = [
            ['name_fr' => 'Cash',     'name_ar' => 'نقداً',  'logo' => null,           'balance' => 0],
            ['name_fr' => 'Bankily',  'name_ar' => 'بنكيلي', 'logo' => 'bankily.png',  'balance' => 0],
            ['name_fr' => 'Sadad',    'name_ar' => 'سداد',   'logo' => 'sedad.PNG',    'balance' => 0],
            ['name_fr' => 'Masrafi',  'name_ar' => 'مصرفي', 'logo' => 'masrvi.PNG',   'balance' => 0],
        ];

        foreach ($banks as $bank) {
            Bank::firstOrCreate(['name_fr' => $bank['name_fr']], $bank);
        }
    }
}
