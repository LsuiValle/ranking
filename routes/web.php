<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ChampsController;
use App\Http\Controllers\HistorialController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
/*
Route::get('/', function () {
    return view('task.index');
});
*/

Route::match(['GET', 'POST'], '/', [TaskController::class, 'index'])->name('tasks.index');

Route::post('/', [TaskController::class,'store']);

Route::delete('/{id}',[TaskController::class,'destroy'])->name('task.destroy');

Route::get('/champs',[ChampsController::class,'index']);

Route::get('/store-session/{id_user}', function ($id_user) {
    session(['id_user' => $id_user]);
    return redirect()->route('historial.index');
})->name('historial.storeSession');

Route::get('/historial', [HistorialController::class, 'index'])->name('historial.index');