<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDnmWorksheetEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dnm_worksheet_events', function (Blueprint $table) {
            $table->id();
            $table->string('dnm_event_id');
            $table->foreignId('dnm_worksheet_id')->references('id')->on('dnm_worksheets')->onDelete('cascade');
            $table->string('dnm_appeal_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dnm_worksheet_events');
    }
}
