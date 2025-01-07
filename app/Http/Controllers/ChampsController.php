<?php

namespace App\Http\Controllers;

use App\Models\Champs;
use Illuminate\Http\Request;

class ChampsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function champions()
    {
        // URL de la API
        $url = 'https://ddragon.leagueoflegends.com/cdn/14.24.1/data/en_US/champion.json';

        // Realizar la solicitud HTTP a la API
        $response = file_get_contents($url);

        // Verificar si la respuesta es válida
        if ($response === false) {
            die('Error al obtener los datos de la API');
        }

        // Decodificar el JSON en un array asociativo
        $data = json_decode($response, true);

        // Recorrer los campeones y mostrar el "name", "title" y "tags"
        if (isset($data['data'])) {
            foreach ($data['data'] as $champion) {
                $name = $champion['name'];
                $title = $champion['title'];
                $tags = implode(', ', $champion['tags']);
                
            }
        } else {
            echo 'No se encontraron datos de campeones.';
        }
        return view("task.champs");
    }

    public function index(Request $request)
    {
        $query = Champs::query(); // Cambia "Task" al modelo que estás usando

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                ->orWhere('title', 'LIKE', '%' . $search . '%')
                ->orWhere('tags', 'LIKE', '%' . $search . '%');
            });
        }

        // Obtener los resultados con paginación
        $champs = $query->paginate(10);

        return view('task.champs', compact('champs'));
    }
    
}
