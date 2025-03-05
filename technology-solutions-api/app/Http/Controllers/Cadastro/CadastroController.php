<?php

namespace App\Http\Controllers\Cadastro;

use App\Http\Controllers\Controller;
use App\Models\Colaborador;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

/**
 * @OA\Schema(
 *     schema="Cadastro",
 *     type="object",
 *     @OA\Property(property="nome", type="string", example="John Doe"),
 *     @OA\Property(property="id_perfil", type="integer", example=1),
 *     @OA\Property(property="email", type="string", example="example@example.com"),
 *     @OA\Property(property="cpf", type="string", example="123.456.789-00"),
 *     @OA\Property(property="celular", type="string", example="1234567890"),
 *     @OA\Property(property="cep", type="string", example="12345-678"),
 *     @OA\Property(property="uf", type="string", example="SP"),
 *     @OA\Property(property="localidade", type="string", example="São Paulo"),
 *     @OA\Property(property="bairro", type="string", example="Centro"),
 *     @OA\Property(property="logradouro", type="string", example="Rua Exemplo"),
 *     @OA\Property(property="senha", type="string", example="Password123!")
 * )
 */

class CadastroController extends Controller
{

    /**
     * @OA\Post(
     *     path=":80/api/cadastrar",
     *     summary="Create a new user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Cadastro")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=201),
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="Colaborador cadastrado com sucesso."),
     *             @OA\Property(property="date", type="string", example="2023-01-01T00:00:00Z")
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
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=500),
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Erro ao cadastrar usuário."),
     *             @OA\Property(property="date", type="string", example="2023-01-01T00:00:00Z")
     *         )
     *     )
     * )
     */

    public function store(Request $request): JsonResponse
    {
        try {

            $rules = [
                'nome'       => 'required|string|max:100',
                'id_perfil'  => 'required|integer|exists:perfis,id',
                'email'      => 'required|string|email|max:50|unique:colaborador',
                'cpf'        => 'required|string|max:14|unique:colaborador',
                'celular'    => 'nullable|string|max:11',
                'cep'        => 'nullable|string|max:9',
                'uf'         => 'nullable|string|max:2',
                'localidade' => 'nullable|string|max:30',
                'bairro'     => 'nullable|string|max:40',
                'logradouro' => 'nullable|string|max:100',
                'senha'      => [
                    'required',
                    'string',
                    'min:8',
                    'regex:/[a-z]/',      // Deve conter pelo menos uma letra minúscula;
                    'regex:/[A-Z]/',      // Deve conter pelo menos uma letra maiúscula;
                    'regex:/[0-9]/',      // Deve conter pelo menos um número;
                    'regex:/[@$!%*?&]/',  // Deve conter pelo menos um caractere especial;
                ],
            ];

            $messages = [
                'nome.required'      => 'O campo nome é obrigatório',
                'nome.string'        => 'O campo nome deve ser uma string',
                'nome.max'           => 'O campo nome deve ter no máximo 100 caracteres',
                'id_perfil.required' => 'O campo id_perfil é obrigatório',
                'id_perfil.integer'  => 'O campo id_perfil deve ser um inteiro',
                'id_perfil.exists'   => 'O id_perfil informado não existe',
                'email.required'     => 'O campo email é obrigatório',
                'email.string'       => 'O campo email deve ser uma string',
                'email.email'        => 'O campo email deve ser um email válido',
                'email.max'          => 'O campo email deve ter no máximo 50 caracteres',
                'email.unique'       => 'O email informado já está cadastrado',
                'cpf.required'       => 'O campo cpf é obrigatório',
                'cpf.string'         => 'O campo cpf deve ser uma string',
                'cpf.max'            => 'O campo cpf deve ter 14 caracteres',
                'cpf.unique'         => 'O cpf informado já está cadastrado',
                'senha.required'     => 'O campo senha é obrigatório',
                'senha.string'       => 'O campo senha deve ser uma string',
                'senha.min'          => 'O campo senha deve ter no mínimo 8 caracteres',
                'senha.regex'        => 'O campo senha deve conter pelo menos uma letra maiúscula, uma letra minúscula, um número e um caractere especial',
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

            // Trata o CPF removendo caracteres especiais;
            $cpf = preg_replace('/[^0-9]/', '', $request->cpf);

            // Trata o email removendo espaços em branco;
            $email = trim($request->email);

            // Trata o celular removendo caracteres especiais;
            $celular = preg_replace('/[^0-9]/', '', $request->celular);

            // Hash da senha antes de salvar
            $password = bcrypt($request->senha);

            // Instancia a model Colaborador e atribui os valores;
            $colaborador = new Colaborador();

            // Vincula os valores aos campos da tabela;
            $colaborador->nome       = $request->nome;
            $colaborador->id_perfil  = $request->id_perfil;
            $colaborador->email      = $email;
            $colaborador->cpf        = $cpf;
            $colaborador->celular    = $celular;
            $colaborador->cep        = $request->cep;
            $colaborador->uf         = $request->uf;
            $colaborador->localidade = $request->localidade;
            $colaborador->bairro     = $request->bairro;
            $colaborador->logradouro = $request->logradouro;
            $colaborador->senha      = $password;

            // Salva o registro na tabela;
            $colaborador->save();

            if (!$colaborador) {
                return response()->json([
                    'status'  => 400,
                    'success' => false,
                    'msg'     => 'Erro ao cadastrar colaborador.',
                    'date'    => now()->format('Y-m-d H:i:s'),
                ], 500);
            }

            return response()->json([
                'status'  => 201,
                'success' => true,
                'msg'     => 'Colaborador cadastrado com sucesso.',
                "date"    => now()->format('Y-m-d H:i:s')
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 500,
                'success' => false,
                'msg'     => 'Erro ao cadastrar usuário: ' . $e->getMessage(),
                'date'    => now()->format('Y-m-d H:i:s'),
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path=":80/api/cadastrar/buscar/{hash}",
     *     summary="Get invite details by hash",
     *     @OA\Parameter(
     *         name="hash",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Invite details retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="email", type="string", example="example@example.com"),
     *             @OA\Property(property="data_e_hora", type="string", example="2023-01-01T00:00:00Z")
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
     *         description="Invite not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=404),
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Convite não encontrado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=500),
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Erro ao retornar dados."),
     *             @OA\Property(property="date", type="string", example="2023-01-01T00:00:00Z")
     *         )
     *     )
     * )
     */

    public function show($hash)
    {
        try {

            $rules = [
                'hash' => 'required|string',
            ];

            $messages = [
                'hash.required' => 'O campo hash é obrigatório',
                'hash.string'   => 'O campo hash deve ser uma string',
            ];

            $validator = Validator::make(['hash' => $hash], $rules, $messages);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => 400,
                    'success' => false,
                    'msg'     => 'Erro de validação.',
                    'object'  => $validator->errors(),
                ], 400);
            }

            // Consulta o convite pelo hash;
            $convite = DB::table('convites')
                ->where('hash', $hash)
                ->first(['email', 'data_e_hora']);

            if (!$convite) {
                return response()->json([
                    'status'  => 404,
                    'success' => false,
                    'msg'     => 'Convite não encontrado',
                ], 404);
            }

            // Verifica se o convite está dentro das 24 horas;
            $dataConvite = Carbon::parse($convite->data_e_hora);
            $agora = Carbon::now();

            if ($dataConvite->diffInHours($agora) > 24) {
                return response()->json([
                    'status'  => 400,
                    'success' => false,
                    'msg'     => 'Convite expirado',
                ], 400);
            }

            return response()->json([
                'status'      => 200,
                'success'     => true,
                'email'       => $convite->email,
                'data_e_hora' => $convite->data_e_hora,
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
