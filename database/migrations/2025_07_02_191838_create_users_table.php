<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('password');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->string('phone_number')->nullable()->unique();
            $table->string('profile_image')->nullable();
            $table->enum('role', ['admin', 'field_worker'])->default('field_worker');
            $table->boolean('is_active')->default(false);
            $table->boolean('is_deleted')->default(false);
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->softDeletes(); // 👈 Adds deleted_at column
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
        Schema::dropIfExists('users');
    }
};
