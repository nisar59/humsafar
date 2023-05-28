<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('portal_name')->nullable();
            $table->text('portal_email')->nullable();
            $table->text('portal_logo')->nullable();
            $table->text('portal_favicon')->nullable();
            $table->text('layout')->nullable();
            $table->text('sidebar_color')->nullable();
            $table->text('color_theme')->nullable();
            $table->text('mini_sidebar')->nullable();
            $table->text('sticky_header')->nullable();
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
        Schema::dropIfExists('settings');
    }
}
