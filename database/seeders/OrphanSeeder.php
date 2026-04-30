<?php

namespace Database\Seeders;

use App\Models\Orphan;
use Illuminate\Database\Seeder;

class OrphanSeeder extends Seeder
{
    public function run(): void
    {
        $orphans = [
            // Actifs jeunes
            ['full_name' => 'Fatima Mint Ahmed',         'birth_date' => '2015-03-12', 'gender' => 'female', 'school_name' => 'École Cheikh Zayed',          'grade' => 'CE2',    'guardian_name' => 'Mariem Mint Brahim',   'guardian_phone' => '22231445566', 'address' => 'Dar Naim, Nouakchott',      'is_active' => true],
            ['full_name' => 'Omar Ould Sidi',             'birth_date' => '2013-07-20', 'gender' => 'male',   'school_name' => 'École Ibn Khaldoun',          'grade' => 'CM1',    'guardian_name' => 'Khadijatou Mint Vall', 'guardian_phone' => '22244567891', 'address' => 'El Mina, Nouakchott',       'is_active' => true],
            ['full_name' => 'Zeinabou Mint Cheikh',      'birth_date' => '2016-11-05', 'gender' => 'female', 'school_name' => 'École Al Qods',               'grade' => 'CP',     'guardian_name' => 'Aminetou Mint Salem',  'guardian_phone' => '22257892345', 'address' => 'Teyarett, Nouakchott',      'is_active' => true],
            ['full_name' => 'Hamza Ould Mohamed',        'birth_date' => '2012-04-18', 'gender' => 'male',   'school_name' => 'Collège El Amal',             'grade' => '6ème',   'guardian_name' => 'Mbarka Mint Djibril', 'guardian_phone' => '22261239876', 'address' => 'Sebkha, Nouakchott',        'is_active' => true],
            ['full_name' => 'Mariem Mint Saleck',        'birth_date' => '2014-09-30', 'gender' => 'female', 'school_name' => 'École Cheikh Zayed',          'grade' => 'CE1',    'guardian_name' => 'Fatimata Mint Bah',   'guardian_phone' => '22274561234', 'address' => 'Arafat, Nouakchott',        'is_active' => true],
            ['full_name' => 'Abdallah Ould Mokhtar',     'birth_date' => '2011-02-14', 'gender' => 'male',   'school_name' => 'Collège Ibn Rochd',           'grade' => '5ème',   'guardian_name' => 'Oumou Mint Ahmed',    'guardian_phone' => '22287651234', 'address' => 'Toujounine, Nouakchott',    'is_active' => true],
            ['full_name' => 'Khadija Mint Yahya',        'birth_date' => '2017-06-22', 'gender' => 'female', 'school_name' => 'École Al Farabi',             'grade' => 'GS',     'guardian_name' => 'Sefiya Mint Ely',     'guardian_phone' => '22231987654', 'address' => 'Riadh, Nouakchott',         'is_active' => true],
            ['full_name' => 'Ibrahima Ould Demba',       'birth_date' => '2010-08-09', 'gender' => 'male',   'school_name' => 'Lycée Nationale',             'grade' => '4ème',   'guardian_name' => 'Hawa Mint Samba',     'guardian_phone' => '22245671234', 'address' => 'Ksar, Nouakchott',          'is_active' => true],
            ['full_name' => 'Aminetou Mint Brahim',      'birth_date' => '2015-12-03', 'gender' => 'female', 'school_name' => 'École Cheikh Zayed',          'grade' => 'CE2',    'guardian_name' => 'Naha Mint Moussa',    'guardian_phone' => '22258791234', 'address' => 'Dar Naim, Nouakchott',      'is_active' => true],
            ['full_name' => 'Souleymane Ould Vall',      'birth_date' => '2009-05-17', 'gender' => 'male',   'school_name' => 'Lycée Terminus',              'grade' => '3ème',   'guardian_name' => 'Vatimetou Mint Abdi', 'guardian_phone' => '22271236547', 'address' => 'Tevragh Zeina, Nouakchott', 'is_active' => true],
            ['full_name' => 'Roukaya Mint Mohamed',      'birth_date' => '2018-04-25', 'gender' => 'female', 'school_name' => null,                          'grade' => null,     'guardian_name' => 'Salma Mint Bilal',    'guardian_phone' => '22284562345', 'address' => 'El Mina, Nouakchott',       'is_active' => true],
            ['full_name' => 'Yacoub Ould Ahmed Lemine',  'birth_date' => '2013-10-11', 'gender' => 'male',   'school_name' => 'Collège Al Ittihad',          'grade' => 'CM2',    'guardian_name' => 'Meimouna Mint Sidi',  'guardian_phone' => '22231546789', 'address' => 'Sebkha, Nouakchott',        'is_active' => true],
            // Proches de 18 ans (near adult)
            ['full_name' => 'Mouhamed Ould Limam',       'birth_date' => '2007-08-15', 'gender' => 'male',   'school_name' => 'Lycée Nationale',             'grade' => 'Terminale', 'guardian_name' => 'Zeyneb Mint Cheikh', 'guardian_phone' => '22244897123', 'address' => 'Teyarett, Nouakchott',   'is_active' => true],
            ['full_name' => 'Mariem Mint Noueigued',     'birth_date' => '2008-03-20', 'gender' => 'female', 'school_name' => 'Lycée Ibn Sina',              'grade' => '1ère',   'guardian_name' => 'Mounina Mint Ely',    'guardian_phone' => '22257123456', 'address' => 'Arafat, Nouakchott',        'is_active' => true],
            ['full_name' => 'Dah Ould Brahim',           'birth_date' => '2007-11-02', 'gender' => 'male',   'school_name' => 'Lycée Terminus',              'grade' => 'Terminale', 'guardian_name' => 'Khadijatou Mint Abdi', 'guardian_phone' => '22269874512', 'address' => 'Ksar, Nouakchott',      'is_active' => true],
            // Désactivé (aged_out)
            ['full_name' => 'Sidi Ould Mohamed El Amine','birth_date' => '2005-06-10', 'gender' => 'male',   'school_name' => null,                          'grade' => null,     'guardian_name' => 'Vatimetou Mint Seck', 'guardian_phone' => '22231789456', 'address' => 'Rosso',                     'is_active' => false, 'deactivated_reason' => 'aged_out',  'deactivated_at' => '2023-06-10'],
            ['full_name' => 'Hawa Mint Abderrahmane',    'birth_date' => '2004-09-25', 'gender' => 'female', 'school_name' => null,                          'grade' => null,     'guardian_name' => 'Maouloud Ould Deh',   'guardian_phone' => '22244987651', 'address' => 'Kiffa',                     'is_active' => false, 'deactivated_reason' => 'aged_out',  'deactivated_at' => '2022-09-25'],
            ['full_name' => 'Taleb Ould Soumare',        'birth_date' => '2016-01-08', 'gender' => 'male',   'school_name' => 'École Al Qods',               'grade' => 'CE1',    'guardian_name' => 'Tennin Mint Ahmedou', 'guardian_phone' => '22258963214', 'address' => 'Dar Naim, Nouakchott',      'is_active' => false, 'deactivated_reason' => 'manual',    'deactivated_at' => '2024-06-01', 'notes' => 'Famille prise en charge par ONG'],
        ];

        foreach ($orphans as $data) {
            Orphan::create($data);
        }

        // Liens de fratrie : Omar et Hamza sont frères
        $omar  = Orphan::where('full_name', 'Omar Ould Sidi')->first();
        $hamza = Orphan::where('full_name', 'Hamza Ould Mohamed')->first();
        if ($omar && $hamza) {
            $omar->siblings()->syncWithoutDetaching([$hamza->id]);
            $hamza->siblings()->syncWithoutDetaching([$omar->id]);
        }

        // Fatima et Mariem Mint Saleck sont sœurs
        $fatima = Orphan::where('full_name', 'Fatima Mint Ahmed')->first();
        $mariem = Orphan::where('full_name', 'Mariem Mint Saleck')->first();
        if ($fatima && $mariem) {
            $fatima->siblings()->syncWithoutDetaching([$mariem->id]);
            $mariem->siblings()->syncWithoutDetaching([$fatima->id]);
        }
    }
}
