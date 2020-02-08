<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('logs')){
            Schema::create('logs', function (Blueprint $table) {
                $table->integer('id');
                $table->primary('id');
                $table->string('url')->nullable();
                $table->string('parameter')->nullable();
                $table->string('response')->nullable();
                $table->datetime('created_at');
                $table->datetime('updated_at');
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
        Schema::dropIfExists('logs');
    }
}
