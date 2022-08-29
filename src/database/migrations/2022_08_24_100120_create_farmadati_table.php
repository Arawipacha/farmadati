<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('farmadati', function (Blueprint $table) {
            $table->id();
            $table->string("cod_product")->unique();
            $table->string("description")->nullable();
            $table->string("ditta")->nullable();
            $table->string("tipo_product")->nullable();
            $table->string("atc_gmp")->nullable();
            $table->float("cod_principio_attivo")->nullable();
            $table->string("cod_forma_farmaceutica")->nullable();
            $table->float("prezzo")->nullable();
            $table->date("data_prezzo")->nullable();
            $table->float("prezzo2")->nullable();
            $table->date("data_prezzo2")->nullable();
            $table->float("iva")->nullable();
            $table->string("data_variazione")->nullable();
            $table->string("tipo_variazione")->nullable();
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
        Schema::dropIfExists('farmadati');
    }
};
