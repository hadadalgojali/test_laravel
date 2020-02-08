<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMBarangsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('barang')){
            Schema::create('barang', function (Blueprint $table) {
                $table->integer('id');
                $table->primary('id');
                $table->string('code');
                $table->string('barang')->nullable();
                $table->datetime('created_at');
                $table->datetime('updated_at');
                $table->datetime('deleted_at');
                $table->engine = 'InnoDB';
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('barang');
    }
}
