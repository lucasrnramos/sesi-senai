<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Login\LoginController;
use App\Http\Controllers\Cadastro\CadastroController;
use App\Http\Controllers\Perfis\PerfisController;

/*
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


*/

Route::post('/cadastrar', [CadastroController::class, 'store']);
Route::post('/login', [LoginController::class, 'show']);
Route::get('/perfis', [PerfisController::class, 'index']);
Route::post('/perfis/criar', [PerfisController::class, 'store']);
