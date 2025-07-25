<?php

namespace App\Jobs;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\RiotAccount;
use Dflydev\DotAccessData\Data;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ProcessRiotAccountData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    protected $accounts;

    public function __construct(array $accounts)
    {
        $this->accounts = $accounts;
    }

    public function handle()
    {
        $token = config('app.token_lol');

        foreach ($this->accounts as $account) {
            $summonerId = $account['summonerid'];

            $url = "https://la2.api.riotgames.com/lol/league/v4/entries/by-summoner/$summonerId";
            $response = Http::withHeaders(['X-Riot-Token' => $token])->get($url);

            if ($response->failed()) {
                info("Error al obtener datos para el summonerId: $summonerId");
                continue;
            }

            $data = $response->json();
            $point = RiotAccount::where('summonerid', $summonerId)->first();

            if ($point && !empty($data)) {
                info($data);
                foreach ($data as $entry) {
                    $isSame = 
                        ($point->division === ($entry['tier'] ?? '')) &&
                        ($point->rango === ($entry['rank'] ?? '')) &&
                        ($point->wins === ($entry['wins'] ?? 0)) &&
                        ($point->defeat === ($entry['losses'] ?? 0)) &&
                        ($point->points === ($entry['leaguePoints'] ?? 0));

                    if ($isSame) {
                        info("No se realizaron cambios para el summonerId: $summonerId.");
                        continue;
                    }
                    
                    $updateData = [];
                    if ($entry['queueType'] == 'RANKED_SOLO_5x5') {
                        $updateData = [
                            'division' => $entry['tier'] ?? '',
                            'rango' => $entry['rank'] ?? '',
                            'wins' => $entry['wins'] ?? 0,
                            'defeat' => $entry['losses'] ?? 0,
                            'points' => $entry['leaguePoints'] ?? 0,
                            'updated_at' => now(),
                        ];
                    } else {
                        // Reset ranking data for non-solo queue types to default values
                        info("Resetting ranking data for non-solo queue type for summonerId: $summonerId.");
                        RiotAccount::where('summonerid', $summonerId)->update([
                            'division' => '',
                            'rango' => '',
                            'wins' => 0,
                            'defeat' => 0,
                            'points' => 0,
                            'updated_at' => now(),
                        ]);
                        continue;
                    }
                    RiotAccount::where('summoner_id', $summonerId)->update($updateData);
                }
            }
        }
    }
}