<?php

namespace Database\Seeders;

use App\Models\Member;
use Illuminate\Database\Seeder;

class MemberSeeder extends Seeder
{
    public function run(): void
    {
        $members = [
            ['full_name' => 'Mohamed Ahmed Ould Brahim',    'phone' => '22231245678', 'whatsapp' => '22231245678', 'address' => 'Tevragh Zeina, Nouakchott',       'profession' => 'Ingénieur',          'join_date' => '2022-01-15', 'monthly_amount' => 200, 'is_active' => true],
            ['full_name' => 'Abdallahi Ould Mohamed',       'phone' => '22236547891', 'whatsapp' => '22236547891', 'address' => 'Ksar, Nouakchott',                'profession' => 'Commerçant',         'join_date' => '2022-02-01', 'monthly_amount' => 200, 'is_active' => true],
            ['full_name' => 'Sidi Mohamed Ould Saleck',     'phone' => '22241235678', 'whatsapp' => null,          'address' => 'Dar Naim, Nouakchott',            'profession' => 'Enseignant',         'join_date' => '2022-03-10', 'monthly_amount' => 200, 'is_active' => true],
            ['full_name' => 'Moussa Ould Ahmed',            'phone' => '22248796541', 'whatsapp' => '22248796541', 'address' => 'Teyarett, Nouakchott',            'profession' => 'Médecin',            'join_date' => '2022-04-05', 'monthly_amount' => 200, 'is_active' => true],
            ['full_name' => 'Isselmou Ould Mohamed Lemine', 'phone' => '22231987654', 'whatsapp' => '22231987654', 'address' => 'El Mina, Nouakchott',             'profession' => 'Comptable',          'join_date' => '2022-05-20', 'monthly_amount' => 200, 'is_active' => true],
            ['full_name' => 'Bilal Ould Cheikh',            'phone' => '22245678912', 'whatsapp' => null,          'address' => 'Sebkha, Nouakchott',              'profession' => 'Chauffeur',          'join_date' => '2022-06-01', 'monthly_amount' => 200, 'is_active' => true],
            ['full_name' => 'Hamoud Ould Sidi',             'phone' => '22239871234', 'whatsapp' => '22239871234', 'address' => 'Toujounine, Nouakchott',          'profession' => 'Fonctionnaire',      'join_date' => '2022-07-15', 'monthly_amount' => 200, 'is_active' => true],
            ['full_name' => 'Brahim Ould Mokhtar',          'phone' => '22256781234', 'whatsapp' => '22256781234', 'address' => 'Arafat, Nouakchott',              'profession' => 'Avocat',             'join_date' => '2022-08-01', 'monthly_amount' => 200, 'is_active' => true],
            ['full_name' => 'Limam Ould Abdel Aziz',        'phone' => '22264321987', 'whatsapp' => null,          'address' => 'Riadh, Nouakchott',               'profession' => 'Pharmacien',         'join_date' => '2022-09-10', 'monthly_amount' => 200, 'is_active' => true],
            ['full_name' => 'Cheikh Ould Yahya',            'phone' => '22271239876', 'whatsapp' => '22271239876', 'address' => 'Tevragh Zeina, Nouakchott',       'profession' => 'Architecte',         'join_date' => '2022-10-01', 'monthly_amount' => 200, 'is_active' => true],
            ['full_name' => 'Vall Ould Meme',               'phone' => '22278651234', 'whatsapp' => '22278651234', 'address' => 'Ksar, Nouakchott',                'profession' => 'Entrepreneur',       'join_date' => '2022-11-05', 'monthly_amount' => 200, 'is_active' => true],
            ['full_name' => 'Deh Ould Aly',                 'phone' => '22285671234', 'whatsapp' => null,          'address' => 'Dar Naim, Nouakchott',            'profession' => 'Professeur',         'join_date' => '2022-12-01', 'monthly_amount' => 200, 'is_active' => true],
            ['full_name' => 'Noueigued Ould Salim',         'phone' => '22292345678', 'whatsapp' => '22292345678', 'address' => 'El Mina, Nouakchott',             'profession' => 'Infirmier',          'join_date' => '2023-01-15', 'monthly_amount' => 200, 'is_active' => true],
            ['full_name' => 'Ahmed Ould Hamady',            'phone' => '22299876543', 'whatsapp' => '22299876543', 'address' => 'Teyarett, Nouakchott',            'profession' => 'Technicien',         'join_date' => '2023-02-01', 'monthly_amount' => 200, 'is_active' => true],
            ['full_name' => 'Youssef Ould Abderrahmane',    'phone' => '22231234567', 'whatsapp' => null,          'address' => 'Sebkha, Nouakchott',              'profession' => 'Commerçant',         'join_date' => '2023-03-10', 'monthly_amount' => 200, 'is_active' => false, 'notes' => 'Déménagé à Rosso'],
        ];

        foreach ($members as $data) {
            Member::create($data);
        }
    }
}
