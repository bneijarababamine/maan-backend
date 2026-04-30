<?php

namespace Database\Seeders;

use App\Models\Donor;
use App\Models\Member;
use Illuminate\Database\Seeder;

class DonorSeeder extends Seeder
{
    public function run(): void
    {
        // Donateurs qui sont aussi membres
        $members = Member::take(4)->get();

        foreach ($members as $member) {
            Donor::create([
                'full_name'  => $member->full_name,
                'phone'      => $member->phone,
                'whatsapp'   => $member->whatsapp,
                'address'    => $member->address,
                'profession' => $member->profession,
                'is_member'  => true,
                'member_id'  => $member->id,
            ]);
        }

        // Donateurs externes
        $donors = [
            ['full_name' => 'Mokhtar Ould Djibril',      'phone' => '22231112233', 'whatsapp' => '22231112233', 'address' => 'Nouadhibou',                  'profession' => 'Armateur',          'is_member' => false],
            ['full_name' => 'Tijane Ould Abderrahmane',  'phone' => '22244556677', 'whatsapp' => '22244556677', 'address' => 'Zouerate',                    'profession' => 'Mineur',            'is_member' => false],
            ['full_name' => 'Hamza Ould Sidi Haiba',     'phone' => '22257891234', 'whatsapp' => null,          'address' => 'Atar',                        'profession' => 'Commerçant',        'is_member' => false],
            ['full_name' => 'Boubacar Ould Soumare',     'phone' => '22261234987', 'whatsapp' => '22261234987', 'address' => 'Kaédi',                       'profession' => 'Agriculteur',       'is_member' => false],
            ['full_name' => 'Samba Ould Demba',          'phone' => '22274561289', 'whatsapp' => null,          'address' => 'Rosso',                       'profession' => 'Éleveur',           'is_member' => false],
            ['full_name' => 'Abderrahmane Ould Khatri',  'phone' => '22287654312', 'whatsapp' => '22287654312', 'address' => 'Nouakchott, Tevragh Zeina',   'profession' => 'Homme d\'affaires', 'is_member' => false],
            ['full_name' => 'Mohamed Lemine Ould Bah',   'phone' => '22291234568', 'whatsapp' => '22291234568', 'address' => 'Kiffa',                       'profession' => 'Transporteur',      'is_member' => false],
            ['full_name' => 'Taleb Ould Dahah',          'phone' => '22298765431', 'whatsapp' => null,          'address' => 'Nouakchott, Ksar',            'profession' => 'Imam',              'is_member' => false],
        ];

        foreach ($donors as $data) {
            Donor::create($data);
        }
    }
}
