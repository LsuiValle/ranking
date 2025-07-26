<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Console\View\Components\Task as ComponentsTask;
use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Http;
use App\Models\RiotAccount;
use App\Jobs\ProcessRiotAccountData;
use Illuminate\Support\Facades\Cache;

class TaskController extends Controller
{
    public $timestamps = false;

    public function __construct()
    {
        $this->middleware('auth'); // Require authentication for all methods
        $this->middleware('throttle:10,1')->only('updateAll'); // Rate limit updateAll to 10 requests per minute
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $divisionOrder = [
            'IRON' => 1,
            'BRONZE' => 2,
            'SILVER' => 3,
            'GOLD' => 4,
            'PLATINUM' => 5,
            'EMERALD' => 6,
            'DIAMOND' => 7,
            'MASTER' => 8,
            'GRANDMASTER' => 9,
            'CHALLENGER' => 10,
        ];

        $rangoOrder = [
            'I' => 4,
            'II' => 3,
            'III' => 2,
            'IV' => 1,
            'V' => 0,
            'No ha Jugado' => -1,
        ];

        $query = RiotAccount::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('game_name', 'LIKE', "%$search%")
                  ->orWhere('tag_line', 'LIKE', "%$search%")
                  ->orWhere('division', 'LIKE', "%$search%");
            });
        }

        // Ordenar directamente en la base de datos
        $divisionCase = implode(' ', array_map(
        fn($division, $value) => "WHEN '$division' THEN $value",
        array_keys($divisionOrder),
        $divisionOrder
        ));

        $rangoCase = implode(' ', array_map(
            fn($rango, $value) => "WHEN '$rango' THEN $value",
            array_keys($rangoOrder),
            $rangoOrder
        ));

        $tasks = $query->where('activo', 1)
            ->orderByRaw("
                CASE division
                    $divisionCase
                    ELSE 0
                END DESC
            ")
            ->orderByRaw("
                CASE rango
                    $rangoCase
                    ELSE -1
                END DESC
            ")
            ->orderByDesc('points')
            ->get();

        if ($request->ajax()) {
            return response()->json(['tasks' => $tasks]);
        }

        return view('task.index', compact('tasks'));
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validación de los datos proporcionados en la solicitud
        $request->validate([
            'name' => 'required|string',
            'tag' => 'required|string',
        ]);

        // Asignar valores proporcionados en el request
        $name = $request->name;
        $tag = $request->tag;

        // Token de la configuración
        $token = config('app.token_lol');

        // Construcción de la URL de la API
        $url = "https://americas.api.riotgames.com/riot/account/v1/accounts/by-riot-id/$name/$tag";

        // Solicitud HTTP a la API
        $response = Http::withHeaders([
            'X-Riot-Token' => $token,
        ])->get($url);

        // Verificación del estado de la respuesta
        if ($response->failed()) {
            return redirect('/')->with('Warning','Invocador no se encuentre en los servidores de Latam.');
        }

        // Decodificar respuesta JSON
        $data = $response->json();

        // Validación: Verifica que gameName esté presente
        if (!isset($data['gameName']) || trim($data['gameName']) === '') {
            return response()->json([
                'message' => 'Error: El campo game_name es obligatorio y no fue proporcionado correctamente.',
            ], 422);
        }

        // Guarda o actualiza los datos en la base de datos
        $riotAccount = RiotAccount::updateOrCreate(
            [
                'puuid' => $data['puuid'],
            
            ], // Evitar duplicados según `puuid`
            [
                'game_name' => $data['gameName'],
                'tag_line' => $data['tagLine'],
                'puuid' => $data['puuid'],
                'division' => "No ha Jugado", // Actualmente valores nulos
                'rango' => "No ha Jugado", // Actualmente valores nulos
                'activo' => 1, // Actualmente valor 1
                'update_at' => now(),
            ]
        );
        // Verifica si el registro fue creado o ya existía
        if ($riotAccount->wasRecentlyCreated) {
            // Caso: El invocador fue creado  
            RiotAccount::where('puuid', $data['puuid'])->update([
                'activo' => 1, // Actualmente valor 1
                ]
            );
            return redirect('/')->with('Success', 'Invocador con Nick: ' . $data['gameName'] . ' creado correctamente.');
        } else {
            RiotAccount::where('puuid', $data['puuid'])->update([
                'activo' => 1, // Actualmente valor 1
                ]
            );
            // Caso: El invocador ya existía
            return redirect('/')->with('Success', 'Invocador con Nick: ' . $data['gameName'] . ' Se actualizo en la vista.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
        $existingAccounts = RiotAccount::whereIn('id', $id)->get()->keyBy('summonerid');

        return redirect('/');
        foreach ($accounts as $account) {
            // Dispatch a job for each account to update asynchronously
            ProcessRiotAccountData::dispatch($account);
        }
            // Here you should put the logic to update each summoner,
            // for example, by calling the Riot API and updating the data.
            // You can reuse part of the logic from your store() method.
            // Example using the RiotApiService:
        // Example: Improved error handling for bulk operation
        $failedAccounts = [];
        foreach ($accounts as $account) {
            try {
                // Dispatch a job for each account to update asynchronously
                ProcessRiotAccountData::dispatch($account);
            } catch (\Exception $e) {
                \Log::error("Failed to dispatch job for account ID {$account->id}: " . $e->getMessage());
                $failedAccounts[] = $account->id;
            }
        }

        if (!empty($failedAccounts)) {
            return response()->json([
                'success' => false,
                'failed_accounts' => $failedAccounts,
                'message' => 'Some accounts failed to update. Check logs for details.'
            ], 207); // 207 Multi-Status
        }

        return response()->json(['success' => true]);
    }

    public function updateAll(Request $request)
    {
        $ids = $request->input('ids', []);
        \Log::info('IDs recibidos para actualizar:', $ids);

        if (empty($ids)) {
            \Log::warning('No se recibieron IDs para actualizar.');
            return response()->json(['success' => false, 'message' => 'No hay invocadores para actualizar.'], 400);
        }

        $accounts = RiotAccount::whereIn('id', $ids)->get();
        $token = config('app.token_lol');
        $failedAccounts = [];

        foreach ($accounts as $account) {
            $puuid = $account->puuid;
            if (!$puuid) {
                // Si no tienes el puuid, consíguelo primero
                $urlPuuid = "https://americas.api.riotgames.com/riot/account/v1/accounts/by-riot-id/{$account->game_name}/{$account->tag_line}";
                $responsePuuid = \Illuminate\Support\Facades\Http::withHeaders([
                    'X-Riot-Token' => $token,
                ])->get($urlPuuid);

                if ($responsePuuid->ok()) {
                    $dataPuuid = $responsePuuid->json();
                    $puuid = $dataPuuid['puuid'] ?? null;
                    $account->update(['puuid' => $puuid]);
                }
            }

            if ($puuid) {
                $url = "https://la2.api.riotgames.com/lol/league/v4/entries/by-puuid/{$puuid}";
                $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'X-Riot-Token' => $token,
                ])->get($url);

                \Log::info("Consultando Riot API para: {$account->game_name}#{$account->tag_line} - URL: $url");

                if ($response->ok()) {
                    $data = $response->json();
                    // Solo nos importa RANKED_SOLO_5x5
                    $soloQ = collect($data)->firstWhere('queueType', 'RANKED_SOLO_5x5');
                    if ($soloQ) {
                        $account->update([
                            'division' => $soloQ['tier'] ?? 'No ha Jugado',
                            'rango' => $soloQ['rank'] ?? 'No ha Jugado',
                            'points' => $soloQ['leaguePoints'] ?? 0,
                            'updated_at' => now(),
                        ]);
                    } else {
                        // Si no tiene soloQ, deja en "No ha Jugado"
                        $account->update([
                            'division' => 'No ha Jugado',
                            'rango' => 'No ha Jugado',
                            'points' => 0,
                            'updated_at' => now(),
                        ]);
                    }
                } else {
                    $failedAccounts[] = $account->id;
                    \Log::warning("Fallo al actualizar: {$account->id}");
                }
            } else {
                $failedAccounts[] = $account->id;
                \Log::warning("No se pudo obtener el puuid para: {$account->id}");
            }
        }

        if (!empty($failedAccounts)) {
            \Log::warning('Cuentas que fallaron:', $failedAccounts);
            return response()->json([
                'success' => false,
                'failed_accounts' => $failedAccounts,
                'message' => 'Algunas cuentas no se pudieron actualizar.'
            ], 207);
        }

        \Log::info('Actualización completada correctamente.');
        return response()->json(['success' => true]);
    }
}
