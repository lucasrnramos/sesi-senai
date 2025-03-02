<?php

namespace App\Http\Controllers\Convite;

use App\Http\Controllers\Controller;

class ConviteController extends Controller
{
    public function index()
    {
        try {

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 500,
                'success' => false,
                'msg'     => 'Erro ao retornar convites: ' . $e->getMessage(),
                'data'    => now()->format('Y-m-d H:i:s'),
            ], 500);
        }
    }
}
