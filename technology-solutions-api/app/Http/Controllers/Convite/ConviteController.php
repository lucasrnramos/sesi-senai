<?php

namespace App\Http\Controllers\Convite;

//use App\Models\Convite;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use App\Mail\ConviteMail;
use Illuminate\Support\Facades\Validator;
use App\Models\Colaborador;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ConviteController extends Controller
{
    public function index(): JsonResponse
    {
        try {

            $convites = Colaborador::all()->makeHidden('senha');

            if ($convites->isEmpty()) {
                return response()->json([
                    'status'  => 404,
                    'success' => false,
                    'msg'     => 'Nenhum convite encontrado',
                    'date'    => now()->format('Y-m-d H:i:s'),
                ], 404);
            }

            return response()->json([
                'status'  => 200,
                'success' => true,
                'msg'     => 'Convites retornados com sucesso',
                'object'  => $convites,
                'date'    => now()->format('Y-m-d H:i:s'),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 500,
                'success' => false,
                'msg'     => 'Erro ao retornar convites: ' . $e->getMessage(),
                'date'    => now()->format('Y-m-d H:i:s'),
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {

            $rules = [
                'email' => 'required|email',
            ];

            $messages = [
                'email.required' => 'O campo email é obrigatório',
                'email.email'    => 'O campo email deve ser um email válido',
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

            // Gera uma string hash aleatória
            $hash = Str::random(32);

            // Enfileira o envio do e-mail
            $disparo = Mail::to($request->email)->queue(new ConviteMail($request->email, $hash, 1));

            // Salva os dados na tabela convites (por algum motivo a model não estava sendo localizada, então utilizei a classe DB abaixo)
            /*
            $convite = new Convite();

            $convite->email = $request->email;
            $convite->hash = $hash;
            $convite->data_e_hora = now();
            $convite->save();
            */

            // Salva os dados na tabela convites
            $convite = DB::table('convites')->insert([
                'email'       => $request->email,
                'hash'        => $hash,
                'tipo_envio'  => 1, // 1 = Convite para criar perfil
                'data_e_hora' => now(),
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            if (!$convite) {
                return response()->json([
                    'status'  => 500,
                    'success' => false,
                    'msg'     => 'Erro ao criar convite',
                    'date'    => now()->format('Y-m-d H:i:s'),
                ], 500);
            }

            return response()->json([
                'status'  => 201,
                'success' => true,
                'msg'     => 'Convite enviado com sucesso'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 500,
                'success' => false,
                'msg'     => 'Erro ao criar convite: ' . $e->getMessage(),
                'date'    => now()->format('Y-m-d H:i:s'),
            ], 500);
        }
    }

    public function storePassword(Request $request): JsonResponse
    {
        try {

            $rules = [
                'email' => 'required|email',
            ];

            $messages = [
                'email.required' => 'O campo email é obrigatório',
                'email.email'    => 'O campo email deve ser um email válido',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            // Verifica se os campos obrigatórios foram informados;
            if ($validator->fails()) {
                return response()->json([
                    'status'  => 400,
                    'success' => false,
                    'msg'     => 'Erro de validação.',
                    'object'  => $validator->errors(),
                ], 400);
            }

            // Verifica se o email existe na tabela colaboradores;
            $colaborador = Colaborador::where('email', $request->email)->first();

            if (!$colaborador) {
                return response()->json([
                    'status'  => 404,
                    'success' => false,
                    'msg'     => 'Email não encontrado',
                    'date'    => now()->format('Y-m-d H:i:s'),
                ], 404);
            }

            // Gera uma string hash aleatória;
            $hash = Str::random(32);

            // Enfileira o envio do e-mail;
            $disparo = Mail::to($request->email)->queue(new ConviteMail($request->email, $hash, 2));

            // Salva os dados na tabela convites;
            $convite = DB::table('convites')->insert([
                'email'       => $request->email,
                'hash'        => $hash,
                'tipo_envio'  => 2, // 2 = Convite para redefinir senha
                'data_e_hora' => now(),
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            if (!$convite) {
                return response()->json([
                    'status'  => 500,
                    'success' => false,
                    'msg'     => 'Erro ao criar convite',
                    'date'    => now()->format('Y-m-d H:i:s'),
                ], 500);
            }

            return response()->json([
                'status'  => 201,
                'success' => true,
                'msg'     => 'Convite enviado com sucesso',
                'date'    => now()->format('Y-m-d H:i:s'),
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 500,
                'success' => false,
                'msg'     => 'Erro ao criar convite: ' . $e->getMessage(),
                'date'    => now()->format('Y-m-d H:i:s'),
            ], 500);
        }
    }
}
