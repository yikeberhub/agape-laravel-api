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
        Schema::create('disability', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->date('date_of_birth');
            $table->string('phone_number')->nullable()->unique();
            $table->string('region')->nullable();
            $table->string('zone')->nullable();
            $table->string('city')->nullable();
            $table->string('woreda')->nullable();
            $table->foreignId('recorder_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('warrant_id')->nullable()->constrained('warrants')->onDelete('set null');
            $table->foreignId('equipment_id')->nullable()->constrained('equipments')->onDelete('set null');
            $table->float('hip_width');
            $table->float('backrest_height');
            $table->float('thigh_length');
            $table->string('profile_image')->nullable();
            $table->string('id_image')->nullable();
            $table->boolean('is_provided')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_deleted')->default(false);
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
        Schema::dropIfExists('disability');
    }
};
