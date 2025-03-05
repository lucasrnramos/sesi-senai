<?php

namespace App\Http\Controllers\Perfis;

use App\Http\Controllers\Controller;
use App\Models\Colaborador;
use App\Models\Perfil;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Schema(
 *     schema="Perfil",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="perfil", type="string", example="Admin"),
 *     @OA\Property(property="status", type="string", example="A"),
 *     @OA\Property(property="data", type="string", format="date", example="2023-01-01")
 * )
 */

class PerfisController extends Controller
{

    /**
     * @OA\Get(
     *     path=":80/api/perfis",
     *     summary="Get all profiles",
     *     @OA\Response(
     *         response=200,
     *         description="Profiles retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="Perfis retornados com sucesso"),
     *             @OA\Property(property="object", type="array", @OA\Items(ref="#/components/schemas/Perfil"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No profiles found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=404),
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Nenhum perfil encontrado")
     *         )
     *     )
     * )
     */

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

    /**
     * @OA\Post(
     *     path=":80/api/perfis/criar",
     *     summary="Create a new profile",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"perfil", "status"},
     *             @OA\Property(property="perfil", type="string", example="Admin"),
     *             @OA\Property(property="status", type="string", example="A")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Profile created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=201),
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="Perfil cadastrado com sucesso"),
     *             @OA\Property(property="object", ref="#/components/schemas/Perfil")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=400),
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Erro de validação."),
     *             @OA\Property(property="object", type="object")
     *         )
     *     )
     * )
     */

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

    /**
     * @OA\Patch(
     *     path=":80/api/perfis/editar/{cpf}/{id_perfil}",
     *     summary="Update a profile",
     *     @OA\Parameter(
     *         name="cpf",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         description="CPF of the user"
     *     ),
     *     @OA\Parameter(
     *         name="id_perfil",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the profile"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profile updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="Perfil atualizado com sucesso"),
     *             @OA\Property(property="object", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=400),
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Erro de validação."),
     *             @OA\Property(property="object", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Profile not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=404),
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Perfil não encontrado")
     *         )
     *     )
     * )
     */

    public function update($cpf, $id_perfil)
    {
        try {

            $rules = [
                'cpf'       => 'required|string|max:14',
                'id_perfil' => 'required|integer',
            ];

            $messages = [
                'cpf.required'       => 'O campo cpf é obrigatório',
                'cpf.string'         => 'O campo cpf deve ser uma string',
                'cpf.max'            => 'O campo cpf deve ter no máximo 14 caracteres',
                'id_perfil.required' => 'O campo id_perfil é obrigatório',
                'id_perfil.integer'  => 'O campo id_perfil deve ser um inteiro',
            ];

            $validator = Validator::make(['cpf' => $cpf, 'id_perfil' => $id_perfil], $rules, $messages);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => 400,
                    'success' => false,
                    'msg'     => 'Erro de validação.',
                    'object'  => $validator->errors(),
                ], 400);
            }

            // Tratamento para remover caracteres especiais
            $cpf = preg_replace('/[^0-9]/', '', $cpf);

            // Instancia a model Colaborador e atualiza os dados
            $colaborador = Colaborador::where('cpf', $cpf)->first();

            if (!$colaborador) {
                return response()->json([
                    'status'  => 404,
                    'success' => false,
                    'msg'     => 'Perfil não encontrado',
                    'date'    => now()->format('Y-m-d H:i:s'),
                ], 404);
            }

            // Atribui o no novo id_perfil;
            $colaborador->id_perfil = $id_perfil;

            $colaborador->save();

            if (!$colaborador) {
                return response()->json([
                    'status'  => 400,
                    'success' => false,
                    'msg'     => 'Erro ao atualizar perfil',
                    'date'    => now()->format('Y-m-d H:i:s'),
                ], 400);
            }

            return response()->json([
                'status'  => 200,
                'success' => true,
                'msg'     => 'Perfil atualizado com sucesso',
                'object'  => true,
                'date'    => now()->format('Y-m-d H:i:s'),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 500,
                'success' => false,
                'msg'     => 'Erro ao atualizar perfil: ' . $e->getMessage(),
                'date'    => now()->format('Y-m-d H:i:s'),
            ], 500);
        }
    }
}
