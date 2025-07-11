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
     */public function up()
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('country')->nullable()->after('email');
        $table->string('region')->nullable()->after('country');
        $table->string('city')->nullable()->after('region');
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['country', 'region', 'city']);
    });
}

};
