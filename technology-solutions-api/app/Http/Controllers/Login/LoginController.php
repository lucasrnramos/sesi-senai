<?php

namespace app\Http\Controllers\Login;

use App\Http\Controllers\Controller;
use App\Models\Colaborador;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
//use App\Models\Convite;

/**
 * @OA\Info(
 *     title="API Documentation",
 *     version="1.0.0"
 * ),
 * @OA\Schema(
 *      schema="Login",
 *      type="object",
 *      @OA\Property(property="cpf", type="string", example="123.456.789-00"),
 *      @OA\Property(property="senha", type="string", example="Password123!")
 *  )
 */

class LoginController extends Controller
{

    /**
     * @OA\Post(
     *     path=":80/api/login",
     *     summary="Login user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"cpf", "senha"},
     *             @OA\Property(property="cpf", type="string", example="123.456.789-00"),
     *             @OA\Property(property="senha", type="string", example="Password123!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="Login bem-sucedido."),
     *             @OA\Property(property="object", type="object",
     *                 @OA\Property(property="id_perfil", type="integer", example=1),
     *                 @OA\Property(property="nome", type="string", example="John Doe")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=401),
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="CPF ou senha inválido(s).")
     *         )
     *     )
     * )
     */

    public function show(Request $request): JsonResponse
    {
        try {

            $rules = [
                'cpf' => 'required|string|max:14',
                'senha'      => [
                    'required',
                    'string',
                    'min:8',
                    'regex:/[a-z]/',      // Deve conter pelo menos uma letra minúscula;
                    'regex:/[A-Z]/',      // Deve conter pelo menos uma letra maiúscula;
                    'regex:/[0-9]/',      // Deve conter pelo menos um número;
                    'regex:/[@$!%*?&]/',  // Deve conter pelo menos um caractere especial;
                ]
            ];

            $messages = [
                'cpf.required'      => 'O campo cpf é obrigatório',
                'cpf.string'        => 'O campo cpf deve ser uma string',
                'cpf.max'           => 'O campo cpf deve ter 14 caracteres',
                'senha.required'    => 'O campo senha é obrigatório',
                'senha.string'      => 'O campo senha deve ser uma string',
                'senha.min'         => 'O campo senha deve ter no mínimo 8 caracteres',
                'senha.regex'       => 'O campo senha deve conter pelo menos uma letra maiúscula, uma letra minúscula, um número e um caractere especial',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            // Verifica se os campos obrigatórios foram informados;
            if ($validator->fails()) {
                return response()->json([
                    'status'  => 400,
                    'success' => false,
                    'msg'     => 'Erro ao validar dados: ' . $validator->errors(),
                    'date'    => now()->format('Y-m-d H:i:s'),
                ], 400);
            }

            // Trata o CPF removendo caracteres especiais;
            $cpf   = preg_replace('/[^0-9]/', '', $request->cpf);

            $senha = $request->post('senha');

            // Encontra o usuário pelo CPF;
            $colaborador = Colaborador::where('cpf', $cpf)->first();

            // Verifica se o usuário foi encontrado;
            if (!$colaborador || !Hash::check($senha, $colaborador->senha)) {
                return response()->json([
                    'status'  => 401,
                    'success' => false,
                    'msg'     => 'CPF ou senha inválido(s).',
                ], 401);
            }

            return response()->json([
                'status'  => 200,
                'success' => true,
                'msg'     => 'Login bem-sucedido.',
                'object'  => [
                    'id_perfil' => $colaborador->id_perfil,
                    'nome'      => $colaborador->nome,
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 500,
                'success' => false,
                'msg'     => 'Erro ao retornar dados: ' . $e->getMessage(),
                'date'    => now()->format('Y-m-d H:i:s'),
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path=":80/api/login/redefinir",
     *     summary="Redefine user password",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"hash", "senha"},
     *             @OA\Property(property="hash", type="string", example="randomhashstring"),
     *             @OA\Property(property="senha", type="string", example="NewPassword123!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="Senha atualizada com sucesso.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Invitation not found or expired",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=404),
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Convite não encontrado ou expirado.")
     *         )
     *     )
     * )
     */

    public function update(Request $request): JsonResponse
    {
        try {

            $rules = [
                'hash' => 'required|string',
                'senha'      => [
                    'required',
                    'string',
                    'min:8',
                    'regex:/[a-z]/',      // Deve conter pelo menos uma letra minúscula;
                    'regex:/[A-Z]/',      // Deve conter pelo menos uma letra maiúscula;
                    'regex:/[0-9]/',      // Deve conter pelo menos um número;
                    'regex:/[@$!%*?&]/',  // Deve conter pelo menos um caractere especial;
                ]
            ];

            $messages = [
                'hash.required'     => 'O campo hash é obrigatório',
                'hash.string'       => 'O campo hash deve ser uma string',
                'senha.required'    => 'O campo senha é obrigatório',
                'senha.string'      => 'O campo senha deve ser uma string',
                'senha.min'         => 'O campo senha deve ter no mínimo 8 caracteres',
                'senha.regex'       => 'O campo senha deve conter pelo menos uma letra maiúscula, uma letra minúscula, um número e um caractere especial',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            // Verifica se os campos obrigatórios foram informados;
            if ($validator->fails()) {
                return response()->json([
                    'status'  => 400,
                    'success' => false,
                    'msg'     => 'Erro ao validar dados: ' . $validator->errors(),
                    'date'    => now()->format('Y-m-d H:i:s'),
                ], 400);
            }

            // Encontra o convite pelo hash e verifica se a mesma está dentro das 24 horas desde seu envio;
            $convite = DB::table('convites')->where('hash', $request->hash)
                ->where('data_e_hora', '>=', now()->subHours(24))
                ->first();

            // Verifica se o convite foi encontrado;
            if (!$convite) {
                return response()->json([
                    'status'  => 404,
                    'success' => false,
                    'msg'     => 'Convite não encontrado ou expirado.',
                ], 404);
            }

            // Faz o update da senha do colaborador na tabela colaboradores;
            $colaborador = Colaborador::where('email', $convite->email)->first();
            $colaborador->senha = bcrypt($request->senha);
            $colaborador->save();

            if (!$colaborador) {
                return response()->json([
                    'status'  => 400,
                    'success' => false,
                    'msg'     => 'Erro ao atualizar senha.',
                    'date'    => now()->format('Y-m-d H:i:s'),
                ], 500);
            }

            return response()->json([
                'status'  => 200,
                'success' => true,
                'msg'     => 'Senha atualizada com sucesso.',
                'date'    => now()->format('Y-m-d H:i:s'),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 500,
                'success' => false,
                'msg'     => 'Erro ao retornar dados: ' . $e->getMessage(),
                'date'    => now()->format('Y-m-d H:i:s'),
            ], 500);
        }
    }
}
