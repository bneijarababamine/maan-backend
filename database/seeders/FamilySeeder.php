<?php

namespace Database\Seeders;

use App\Models\Family;
use Illuminate\Database\Seeder;

class FamilySeeder extends Seeder
{
    public function run(): void
    {
        $families = [
            ['head_of_family' => 'Oumou Mint Abdallahi',    'phone' => '22231667788', 'address' => 'Dar Naim, Nouakchott',      'members_count' => 6, 'is_active' => true,  'notes' => 'Veuve, 5 enfants'],
            ['head_of_family' => 'Fatimata Mint Cheikh',    'phone' => '22244778899', 'address' => 'El Mina, Nouakchott',       'members_count' => 4, 'is_active' => true,  'notes' => 'Mari décédé en 2021'],
            ['head_of_family' => 'Meimouna Mint Sidi',      'phone' => '22257889900', 'address' => 'Sebkha, Nouakchott',        'members_count' => 7, 'is_active' => true,  'notes' => 'Famille nombreuse, situation précaire'],
            ['head_of_family' => 'Hawa Mint Boubacar',      'phone' => '22261990011', 'address' => 'Teyarett, Nouakchott',      'members_count' => 3, 'is_active' => true,  'notes' => null],
            ['head_of_family' => 'Khadijatou Mint Ahmed',   'phone' => '22274001122', 'address' => 'Arafat, Nouakchott',        'members_count' => 5, 'is_active' => true,  'notes' => 'Locataire, loyer difficile à payer'],
            ['head_of_family' => 'Mariem Mint Mokhtar',     'phone' => '22287112233', 'address' => 'Toujounine, Nouakchott',    'members_count' => 8, 'is_active' => true,  'notes' => 'Grand-mère avec petits-enfants'],
            ['head_of_family' => 'Salma Mint Yahya',        'phone' => '22291223344', 'address' => 'Ksar, Nouakchott',          'members_count' => 2, 'is_active' => true,  'notes' => 'Mère seule avec 1 enfant handicapé'],
            ['head_of_family' => 'Zeyneb Mint Lemrabott',   'phone' => '22231334455', 'address' => 'Tevragh Zeina, Nouakchott', 'members_count' => 4, 'is_active' => true,  'notes' => null],
            ['head_of_family' => 'Naha Mint Hamoud',        'phone' => '22244445566', 'address' => 'Riadh, Nouakchott',         'members_count' => 5, 'is_active' => true,  'notes' => 'Réfugiée, récemment arrivée'],
            ['head_of_family' => 'Vatimetou Mint Ely',      'phone' => '22257556677', 'address' => 'Dar Naim, Nouakchott',      'members_count' => 6, 'is_active' => false, 'notes' => 'Déménagée en province'],
        ];

        foreach ($families as $data) {
            Family::create($data);
        }
    }
}
