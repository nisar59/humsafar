<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('desk_users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('email',150)->nullable();
            $table->string('father_name',255);
            $table->string('cnic',15)->unique();
            $table->string('phone',12)->unique();
            $table->smallInteger('emp_code',false)->unique();
            $table->string('role_name', 2)->nullable();
            $table->boolean('status')->default(1);
            $table->string('access_level', 15)->nullable();
            $table->smallInteger('branch_code',false);
            $table->smallInteger('branch_id',false)->nullable();
            $table->smallInteger('area_id',false)->nullable();
            $table->smallInteger('region_id',false)->nullable();
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
        Schema::dropIfExists('desk_users');
    }
};
