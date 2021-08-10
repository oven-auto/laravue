<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMarkDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mark_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mark_id')->constrained()->onDelete('cascade');
            $table->string('brochure')->nullable();
            $table->string('manual')->nullable();
            $table->string('price')->nullable();
            $table->string('accessory')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mark_documents');
    }
}
