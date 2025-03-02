<?php

namespace app\Http\Controllers\Login;

use App\Http\Controllers\Controller;
use App\Models\Colaborador;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{

    /*
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user  = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'access_token' => $token,
                'token_type'   => 'Bearer',
            ]);
        }

        return response()->json([
            'message' => 'Unauthorized'
        ], 401);
    }
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
                    'regex:/[a-z]/',      // deve conter pelo menos uma letra minúscula
                    'regex:/[A-Z]/',      // deve conter pelo menos uma letra maiúscula
                    'regex:/[0-9]/',      // deve conter pelo menos um número
                    'regex:/[@$!%*?&]/',  // deve conter pelo menos um caractere especial
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

            // Encontra o usuário pelo CPF
            $colaborador = Colaborador::where('cpf', $cpf)->first();

            // Verifica se o usuário foi encontrado
            if (!$colaborador || !Hash::check($senha, $colaborador->senha)) {
                return response()->json([
                    'status'  => 401,
                    'success' => false,
                    'msg'     => 'CPF ou senha inválidos.',
                ], 401);
            }

            return response()->json([
                'status'  => 200,
                'success' => true,
                'msg'     => 'Login bem-sucedido.',
                'object'  => true,
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
