<?php

namespace App\Console\Commands;

use App\Models\Inventory;
use Illuminate\Console\Command;

class PruneOldInventory extends Command
{
    protected $signature = 'inventory:prune-old';

    protected $description = 'Remove registros de estoque nao atualizados nos ultimos 90 dias';

    public function handle(): int
    {
        $cutoff = now()->subDays(config('commands.pruneOldInventory.days'));
        $deleted = Inventory::query()
            ->where(function ($query) use ($cutoff) {
                $query->whereNull('last_updated')
                    ->orWhere('last_updated', '<', $cutoff);
            })
            ->delete();

        $this->info("Registros de estoque removidos: {$deleted}");

        return Command::SUCCESS;
    }
}
