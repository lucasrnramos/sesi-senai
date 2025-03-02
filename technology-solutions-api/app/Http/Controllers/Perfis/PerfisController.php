<?php

namespace App\Http\Controllers\Perfis;

use App\Http\Controllers\Controller;
use App\Models\Perfil;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PerfisController extends Controller
{
    public function index()
    {
        try {
            // Instacia a model Perfil e retorna todos os perfis
            $perfis = Perfil::all();

            if ($perfis->isEmpty()) {
                return response()->json([
                    'status'  => 404,
                    'success' => false,
                    'msg'     => 'Nenhum perfil encontrado',
                    'date'    => now()->format('Y-m-d H:i:s'),
                ], 404);
            }

            return response()->json([
                'status'  => 200,
                'success' => true,
                'msg'     => 'Perfis retornados com sucesso',
                'object'  => $perfis,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 500,
                'success' => false,
                'msg'     => 'Erro ao retornar perfis: ' . $e->getMessage(),
                'date'    => now()->format('Y-m-d H:i:s'),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {

            $rules = [
              'perfil' => 'required|string|max:50',
              'status' => 'required|string|max:1'
            ];

            $messages = [
              'perfil.required' => 'O campo perfil é obrigatório',
              'perfil.string'   => 'O campo perfil deve ser uma string',
              'perfil.max'      => 'O campo perfil deve ter no máximo 50 caracteres',
              'status.required' => 'O campo status é obrigatório',
              'status.string'   => 'O campo status deve ser uma string',
              'status.max'      => 'O campo status deve ter no máximo 1 caracter'
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => 400,
                    'success' => false,
                    'msg'     => 'Erro de validação.',
                    'object'  => $validator->errors(),
                ], 400);
            }

            // Instancia a model Perfil e salva os dados
            $perfil = new Perfil();

            $perfil->perfil = $request->perfil;
            $perfil->status = $request->status;
            $perfil->data = now()->format('Y-m-d');

            $perfil->save();

            if (!$perfil) {
                return response()->json([
                    'status'  => 400,
                    'success' => false,
                    'msg'     => 'Erro ao cadastrar perfil',
                    'date'    => now()->format('Y-m-d H:i:s'),
                ], 400);
            }

            return response()->json([
                'status'  => 201,
                'success' => true,
                'msg'     => 'Perfil cadastrado com sucesso',
                'object'  => $perfil,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 500,
                'success' => false,
                'msg'     => 'Erro ao cadastrar perfil: ' . $e->getMessage(),
                'date'    => now()->format('Y-m-d H:i:s'),
            ], 500);
        }
    }
}
