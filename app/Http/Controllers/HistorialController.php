<?php

namespace App\Http\Controllers;

use App\Models\historial;
use App\Models\partidas;
use App\Models\RiotAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Jobs\ProcessRiotAccountData;

class HistorialController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $id_user = session('id_user');
        if (!$id_user) {
            return redirect('/')->with('Warning', 'No se proporcionó el usuario.');
        }

        // Buscar el invocador en la base de datos
        $acount = RiotAccount::where('id', $id_user)->first();

        if (!$acount) {
            return redirect('/')->with('Warning', 'El invocador no existe.');
        }

        $token = config('app.token_lol');
        $start = 0;
        $count = 10;

        // Construcción de la URL de la API
        $url = "https://americas.api.riotgames.com/lol/match/v5/matches/by-puuid/" . $acount["puuid"] . "/ids?start=" . $start . "&count=" . $count;

        // Solicitud HTTP a la API
        $response = Http::withHeaders([
            'X-Riot-Token' => $token,
        ])->get($url);

        if ($response->failed()) {
            return redirect('/')->with('Warning', 'El invocador no se encuentra en los servidores de LATAM.');
        }

        $matchIds = $response->json();

        // Usa la función buscarPartidas para obtener detalles de las partidas
        $matchDetails = $this->buscarPartidas($matchIds);

        $games = [];
        foreach ($matchDetails as $index => $matchData) {
            if (!isset($matchData['info']['participants'])) {
                continue;
            }

            $participants = [];
            $status = [];
            $puuids = [];

            foreach ($matchData['info']['participants'] as $participant) {
                $participants[] = $participant['riotIdGameName'];
                $puuids[] = [
                    'puuid' => $participant['puuid'],
                    'summonerId' => $participant['summonerId'],
                    'summonerLevel' => $participant['summonerLevel'],
                            ];
                $status[] = [
                    'puuid' => $participant['puuid'],
                    'win' => $participant['win'],
                    'champ' => $participant['championName'],
                    'kills' => $participant['kills'],
                    'deaths' => $participant['deaths'],
                    'assists' => $participant['assists'],
                    'riotIdGameName' => $participant['riotIdGameName'],
                ];
            }

            foreach ($puuids as $puuidData) {
                RiotAccount::where('puuid', $puuidData['puuid'])->update([
                    'summonerid' => $puuidData['summonerId'],  // Actualizar o insertar este campo
                    'level' => $puuidData['summonerLevel'], // Actualmente valor 1
                    ]
                );
            }
            

            // Usa la función getAccountData para enriquecer los nombres de los participantes

            //*******************************************************************************
            //ProcessRiotAccountData::dispatch($puuids);
            //*******************************************************************************
            
            //$accountNames = app(TaskController::class)->getAccountData($puuids);
            
            foreach ($participants as $key => $participant) {
                $participants[$key] = $accountNames[$key] ?? $participant;
            }

            $games[] = [
                'game' => 'Game ' . ($index + 1),
                'participants' => array_chunk($participants, 10),
                'status' => $status,
            ];
        }

        return view("task.historial", ['games' => $games]);
    }

    public function buscarPartidas($data)
    {
        $token = config('app.token_lol');
        $resultados = [];

        foreach ($data as $idPartida) {
            $partida = partidas::where('uuid', $idPartida)->first();
            if ($partida) {
                $resultados[] = json_decode($partida->detalle_historial, true);
            } else {
                $url = "https://americas.api.riotgames.com/lol/match/v5/matches/" . $idPartida;

                try {
                    $response = Http::withHeaders([
                        'X-Riot-Token' => $token,
                    ])->get($url);

                    if ($response->ok()) {
                        $data = $response->json();

                        partidas::updateOrCreate(
                            ['uuid' => $idPartida],
                            ['detalle_historial' => json_encode($data)]
                        );

                        $resultados[] = $data;
                    } else {
                        $resultados[] = [
                            'matchId' => $idPartida,
                            'error' => 'No se pudo obtener información de esta partida.',
                            'status' => $response->status(),
                        ];
                    }
                } catch (\Exception $e) {
                    $resultados[] = [
                        'matchId' => $idPartida,
                        'error' => 'Hubo un error al consultar la API.',
                        'exception' => $e->getMessage(),
                    ];
                }
            }
        }

        return $resultados;
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $task= request()->all();
        historial::create($task);
        return redirect('/');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\task  $task
     * @return \Illuminate\Http\Response
     */
    public function show(historial $task)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\task  $task
     * @return \Illuminate\Http\Response
     */
    public function edit(historial $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, historial $task)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}