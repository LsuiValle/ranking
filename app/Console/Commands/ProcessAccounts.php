<?php

namespace App\Console\Commands;

use App\Models\RiotAccount;
use App\Jobs\UpdateRiotStats;
use Illuminate\Console\Command;
use App\Jobs\ProcessRiotAccountData;

class ProcessRiotAccounts extends Command
{
    // Nombre del comando Artisan
    protected $signature = 'riot:process-accounts';

    // Descripción del comando
    protected $description = 'Procesa datos de Riot Accounts cada 20 minutos';

    /**
     * Ejecuta el comando.
     */
    public function handle()
    {
        $accounts = RiotAccount::where('activo', 1)
            ->whereNotNull('summonerid')
            ->get(['summonerid'])
            ->toArray(); // Convierte la colección en un array

        // Despachar el Job
        ProcessRiotAccountData::dispatch($accounts);
        UpdateRiotStats::dispatch($accounts);

        // Confirmar que el comando se ejecutó
        $this->info('Job despachado exitosamente.');
    }
}
