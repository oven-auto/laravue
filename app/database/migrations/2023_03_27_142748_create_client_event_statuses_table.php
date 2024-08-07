<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientEventStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_event_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->references('id')->on('client_events')->onDelete('cascade');
            $table->enum('confirm', ['waiting','processed'])->default('waiting');
            $table->foreignId('author_id')->nullable()->references('id')->on('users')->onDelete('cascade');
            $table->date('processed_at')->nullable();
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
        Schema::dropIfExists('client_event_statuses');
    }
}
