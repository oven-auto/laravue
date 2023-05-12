<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientEventCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_event_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('author_id')->references('id')->on('users')->onDelete('cascade');
            $table->text('text');
            $table->foreignId('event_id')->references('id')->on('client_events')->onDelete('cascade');
            $table->foreign('client_event_status_id')->references('id')->on('client_event_statuses')->onDelete('cascade');
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
        Schema::dropIfExists('client_event_comments');
    }
}
