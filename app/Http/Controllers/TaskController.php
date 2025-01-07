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
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $rankings = [
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
        $orderByRank = implode(' ', array_map(function ($rank, $value) {
            return "WHEN '$rank' THEN $value";
        }, array_keys($rankings), $rankings));

        $tasks = $query->where('activo', 1)
            ->orderByRaw("
                CASE division
                    $orderByRank
                    ELSE 0
                END DESC,
                points DESC
            ")
            ->get();

        if ($request->ajax()) {
            return response()->json(['tasks' => $tasks]);
        }

        // Cargar la vista normal
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
    }
}
