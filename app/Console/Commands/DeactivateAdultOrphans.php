<?php

namespace App\Console\Commands;

use App\Models\Orphan;
use Illuminate\Console\Command;

class DeactivateAdultOrphans extends Command
{
    protected $signature   = 'orphans:deactivate-adults';
    protected $description = 'Désactiver automatiquement les orphelins ayant atteint 18 ans';

    public function handle(): int
    {
        $count = Orphan::where('is_active', true)
            ->whereYear('birth_date', '<=', now()->year - 18)
            ->count();

        Orphan::where('is_active', true)
            ->whereYear('birth_date', '<=', now()->year - 18)
            ->update([
                'is_active'          => false,
                'deactivated_reason' => 'aged_out',
                'deactivated_at'     => now(),
            ]);

        $this->info("{$count} orphelin(s) désactivé(s) automatiquement.");

        return Command::SUCCESS;
    }
}
