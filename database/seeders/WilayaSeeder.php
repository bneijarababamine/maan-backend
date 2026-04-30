<?php

namespace Database\Seeders;

use App\Models\Wilaya;
use Illuminate\Database\Seeder;

class WilayaSeeder extends Seeder
{
    public function run(): void
    {
        $wilayas = [
            ['name_fr' => 'Nouakchott Nord',      'name_ar' => 'نواكشوط الشمالية'],
            ['name_fr' => 'Nouakchott Ouest',     'name_ar' => 'نواكشوط الغربية'],
            ['name_fr' => 'Nouakchott Sud',       'name_ar' => 'نواكشوط الجنوبية'],
            ['name_fr' => 'Hodh Ech Chargui',     'name_ar' => 'الحوض الشرقي'],
            ['name_fr' => 'Hodh El Gharbi',       'name_ar' => 'الحوض الغربي'],
            ['name_fr' => 'Assaba',               'name_ar' => 'العصابة'],
            ['name_fr' => 'Gorgol',               'name_ar' => 'كوركول'],
            ['name_fr' => 'Brakna',               'name_ar' => 'البراكنة'],
            ['name_fr' => 'Trarza',               'name_ar' => 'الترارزة'],
            ['name_fr' => 'Adrar',                'name_ar' => 'آدرار'],
            ['name_fr' => 'Dakhlet Nouadhibou',   'name_ar' => 'داخلت نواذيبو'],
            ['name_fr' => 'Tagant',               'name_ar' => 'تاكانت'],
            ['name_fr' => 'Guidimagha',           'name_ar' => 'كيدماغا'],
            ['name_fr' => 'Tiris Zemmour',        'name_ar' => 'تيرس زمور'],
            ['name_fr' => 'Inchiri',              'name_ar' => 'إينشيري'],
        ];

        foreach ($wilayas as $w) {
            Wilaya::firstOrCreate(['name_fr' => $w['name_fr']], $w);
        }
    }
}
