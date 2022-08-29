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
        Schema::create('xml', function (Blueprint $table) {
            $table->id();
            $table->string('data_agg_id')->unique();
            $table->enum('source',['GetDataSetChanges','GetDataSet']);
            $table->integer('rows')->nullable();
            $table->string('xml_files')->nullable();//json array
            /* data di esempio salvat su xml files e sql files
            [
            '/temp/2208202221807/agg_001.xml',
            '/temp/2208202221807/agg_002.xml',
            '/temp/2208202221807/agg_003.xml'
            ]
            */
            $table->string('sql_files')->nullable();//json array
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
        Schema::dropIfExists('xml');
    }
};
