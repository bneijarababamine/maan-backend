<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ne tourne que si les anciennes colonnes existent encore
        if (! Schema::hasColumn('orphans', 'guardian_name')) {
            return;
        }

        // Regroupe par guardian_name — un nom = une famille = un tuteur
        $groups = DB::table('orphans')
            ->select(
                'guardian_name',
                DB::raw('MIN(guardian_phone) as guardian_phone'),
                DB::raw('MIN(address) as address')
            )
            ->whereNotNull('guardian_name')
            ->where('guardian_name', '!=', '')
            ->groupBy('guardian_name')
            ->get();

        foreach ($groups as $group) {
            $guardianId = null;

            // 1. Cherche un tuteur avec ce nom exact
            $byName = DB::table('guardians')
                ->where('name', $group->guardian_name)
                ->first();

            if ($byName) {
                $guardianId = $byName->id;
            }

            // 2. Si pas trouvé par nom, cherche par téléphone (évite la violation de la contrainte unique)
            if (! $guardianId && $group->guardian_phone) {
                $byPhone = DB::table('guardians')
                    ->where('phone', $group->guardian_phone)
                    ->first();

                if ($byPhone) {
                    $guardianId = $byPhone->id;
                }
            }

            // 3. Rien trouvé → crée un nouveau tuteur
            if (! $guardianId) {
                // Si le téléphone est déjà pris par un autre tuteur (cas rare), on laisse null
                $phoneToUse = null;
                if ($group->guardian_phone) {
                    $phoneTaken = DB::table('guardians')
                        ->where('phone', $group->guardian_phone)
                        ->exists();
                    $phoneToUse = $phoneTaken ? null : $group->guardian_phone;
                }

                $guardianId = DB::table('guardians')->insertGetId([
                    'name'       => $group->guardian_name,
                    'phone'      => $phoneToUse ?? '0000000000_' . uniqid(), // temporaire si aucun tel
                    'address'    => $group->address,
                    'is_active'  => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Lie tous les orphelins de ce nom de tuteur
            DB::table('orphans')
                ->where('guardian_name', $group->guardian_name)
                ->update(['guardian_id' => $guardianId]);
        }

        $unlinked = DB::table('orphans')->whereNull('guardian_id')->count();
        if ($unlinked > 0) {
            \Log::warning("migrate_guardian_data: {$unlinked} orphelin(s) sans guardian_id après migration.");
        }
    }

    public function down(): void
    {
        DB::table('orphans')->update(['guardian_id' => null]);
    }
};
