<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\champs;

class FetchChampions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:champions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Traera todos los champs';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {
        $url = config('app.lol_champion_api');
        $response = file_get_contents($url);
        $data = json_decode($response, true);

        if (isset($data['data'])) {
            foreach ($data['data'] as $champion) {
                champs::updateOrCreate(
                    ['name' => $champion['name']], // Busca por nombre
                    [
                        'title' => $champion['title'],
                        'tags' => implode(', ', $champion['tags']),
                    ]
                );
            }
            $this->info('Campeones actualizados correctamente.');
        } else {
            $this->error('No se pudieron obtener datos de campeones.');
        }
    }

}
