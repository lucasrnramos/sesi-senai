<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100);
            $table->string('email', 50);
            $table->string('cpf', 11);
            $table->string('celular', 11)->nullable();
            $table->string('cep', 9)->nullable();
            $table->string('uf', 2)->nullable();
            $table->string('localidade', 30)->nullable();
            $table->string('bairro', 40)->nullable();
            $table->string('logradouro', 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers');
    }
}
