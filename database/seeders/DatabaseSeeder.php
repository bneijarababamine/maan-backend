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
                'role'      => 'admin',
                'is_active' => true,
            ]
        );

        // Banques
        $this->call([BankSeeder::class]);

        $this->command->info('');
        $this->command->info('✓ Utilisateurs  : 36363833 / 36064707');
        $this->command->info('✓ Banques       : Cash, Bankily, Sadad, Masrafi (solde 0)');
    }
}
