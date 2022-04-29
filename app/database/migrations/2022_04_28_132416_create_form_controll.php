<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormControll extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_controlls', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('title');
            $table->string('type');
            $table->string('placeholder');
            $table->string('class');
            $table->string('required');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('form_controlls');
    }
}
