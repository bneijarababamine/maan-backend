<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Utilisateurs
        User::updateOrCreate(
            ['phone' => '36363833'],
            [
                'name'      => 'Admin',
                'email'     => null,
                'password'  => bcrypt('121314'),
                'role'      => 'admin',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['phone' => '36064707'],
            [
                'name'      => 'Manager',
                'email'     => null,
                'password'  => bcrypt('360600'),
                'role'      => 'manager',
                'is_active' => true,
            ]
        );

        // Données de test
        $this->call([
            MemberSeeder::class,
            DonorSeeder::class,
            DonationSeeder::class,
            OrphanSeeder::class,
            FamilySeeder::class,
            ActivitySeeder::class,
        ]);

        $this->command->info('');
        $this->command->info('✓ Utilisateurs  : 36363833 / 36064707');
        $this->command->info('✓ Membres       : 15 membres créés');
        $this->command->info('✓ Donateurs     : 12 donateurs créés');
        $this->command->info('✓ Dons          : 20 dons créés');
        $this->command->info('✓ Orphelins     : 18 orphelins créés (avec fratries)');
        $this->command->info('✓ Familles      : 10 familles créées');
        $this->command->info('✓ Activités     : 6 activités avec bénéficiaires');
    }
}
