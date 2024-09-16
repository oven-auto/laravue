<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTradeMarker extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trade_markers', function (Blueprint $table) {
            $table->foreignId('author_id')->nullable()->references('id')->on('users')->onDelete('cascade');
            $table->string('text_color', 100)->default('#333');
            $table->string('body_color', 100)->default('#fff');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trade_markers', function (Blueprint $table) {
            //
        });
    }
}
