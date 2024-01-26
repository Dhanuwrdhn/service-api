<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearExpiredTokens extends Command
{
    protected $signature = 'tokens:clear-expired';
    protected $description = 'Clear expired tokens';

    public function handle()
    {
        Sanctum::pruneRevokedTokens();
        $this->info('Expired tokens cleared successfully.');
    }
}
