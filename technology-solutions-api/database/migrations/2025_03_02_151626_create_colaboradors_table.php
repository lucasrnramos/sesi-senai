<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('colaborador', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100);
            $table->string('email', 50)->unique();
            $table->string('cpf', 11)->unique();
            $table->string('celular', 11)->nullable();
            $table->string('cep', 9)->nullable();
            $table->string('uf', 2)->nullable();
            $table->string('localidade', 30)->nullable();
            $table->string('bairro', 40)->nullable();
            $table->string('logradouro', 100)->nullable();
            $table->string('senha');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('colaborador');
    }
};
