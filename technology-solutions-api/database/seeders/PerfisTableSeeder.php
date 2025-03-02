<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PerfisTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('perfis')->insert([
            [
                'perfil' => 'Administrador',
                'status' => 'A',
                'data'   => now(),
            ],
            [
                'perfil' => 'Gente e Cultura',
                'status' => 'A',
                'data'   => now(),
            ],
            [
                'perfil' => 'Colaborador Comum',
                'status' => 'A',
                'data'   => now(),
            ],
        ]);
    }
}
