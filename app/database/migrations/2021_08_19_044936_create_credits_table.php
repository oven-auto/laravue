<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained()->onDelete('cascade');
            $table->boolean('status')->default(0); //Статус вкл/выкл
            $table->string('name'); //название кредита
            $table->string('banner'); //банер кредита
            $table->decimal('rate'); //ставка кредита
            $table->integer('pay'); //ежимесяч.платеж
            $table->integer('period'); //срок кредита
            $table->integer('contribution'); //ПВ кредита
            $table->date('begin_at');
            $table->date('end_at');
            $table->text('disclaimer');

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
        Schema::dropIfExists('credits');
    }
}
