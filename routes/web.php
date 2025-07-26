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
Route::get('/login', function () {
    return view('auth.login');
})->name('tasks.index');

Route::match(['GET', 'POST'], '/', [TaskController::class, 'index'])->name('tasks.index');
Route::post('/tasks/update-all', [TaskController::class, 'updateAll'])->name('tasks.updateAll');
Route::post('/', [TaskController::class,'store']);

Route::delete('/{id}',[TaskController::class,'destroy'])->name('task.destroy');

Route::get('/champs',[ChampsController::class,'index']);

Route::get('/store-session/{id_user}', function ($id_user) {
    session(['id_user' => $id_user]);
    return redirect()->route('historial.index');
})->name('historial.storeSession');

Route::get('/historial', [HistorialController::class, 'index'])->name('historial.index');
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
