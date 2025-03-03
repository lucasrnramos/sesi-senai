<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Login\LoginController;
use App\Http\Controllers\Cadastro\CadastroController;
use App\Http\Controllers\Perfis\PerfisController;
use App\Http\Controllers\Convite\ConviteController;

/*
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
*/

Route::post('/cadastrar', [CadastroController::class, 'store']);
Route::get('/cadastrar/buscar/{hash}', [CadastroController::class, 'show']);
Route::post('/login', [LoginController::class, 'show']);
Route::get('/perfis', [PerfisController::class, 'index']);
Route::post('/perfis/criar', [PerfisController::class, 'store']);
Route::patch('/perfis/editar/{cpf}/{id_perfil}', [PerfisController::class, 'update']);
Route::post('/convite/criar', [ConviteController::class, 'store']);

//Rota que envia um convite para redefinir senha;
Route::get('/convite/redefinir/{email}', [ConviteController::class, 'storePassword']);

Route::get('/convite', [ConviteController::class, 'index']);

// Rota para redefinir senha;
Route::post('/login/redefinir', [LoginController::class, 'update']);
