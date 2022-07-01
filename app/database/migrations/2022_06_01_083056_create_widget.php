<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWidget extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('widgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained()->onDelete('cascade');
            $table->boolean('badge_line')->default(false);
            $table->boolean('badge_table')->default(false);
            $table->boolean('badge_number')->default(false);
            $table->string('badge_align')->default('');
            $table->integer('badge_position')->default(1);
            $table->text('description')->default('');
            $table->boolean('widget_status')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('widgets');
    }
}
