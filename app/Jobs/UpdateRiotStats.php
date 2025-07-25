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

class UpdateRiotStats implements ShouldQueue
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

            $url = "https://la2.api.riotgames.com/lol/summoner/v4/summoners/$summonerId";
            $response = Http::withHeaders(['X-Riot-Token' => $token])->get($url);

            if ($response->failed()) {
                info("Error al obtener datos para el summonerId: $summonerId");
                continue;
            }

            $data = $response->json();
            $point = RiotAccount::where('summonerid', $summonerId)->first();

            if ($point && !empty($data)) {
                if($data['profileIconId']){
                    RiotAccount::where('summonerid', $summonerId)->update([
                        'icon_id' => $data['profileIconId'] ?? '',
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}