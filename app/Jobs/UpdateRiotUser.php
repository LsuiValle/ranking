<?php

namespace App\Jobs;

use App\Models\RiotAccount;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class UpdateRiotUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $account;

    public function __construct(array $account)
    {
        $this->account = $account;
    }

    public function handle()
    {
        $puuid = $this->account['puuid'];
        $summonerId = $this->account['summonerId'];
        $level = $this->account['summonerLevel'];

        $user = RiotAccount::where('puuid', $puuid)->first();

        if ($user) {
            if (empty($user->summonerid)) {
                $user->update([
                    'summonerid' => $summonerId,
                    'level' => $level,
                    'updated_at' => now(),
                ]);
            }
        } else {
            $url = "https://americas.api.riotgames.com/riot/account/v1/accounts/by-puuid/$puuid";
            $response = Http::withHeaders(['X-Riot-Token' => config('app.token_lol')])->get($url);

            if ($response->ok()) {
                $data = $response->json();

                RiotAccount::updateOrCreate(
                    ['puuid' => $data['puuid']],
                    [
                        'summonerid' => $summonerId,
                        'game_name' => $data['gameName'],
                        'tag_line' => $data['tagLine'] ?? '',
                        'division' => "No ha Jugado",
                        'rango' => "No ha Jugado",
                        'level' => $level,
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}