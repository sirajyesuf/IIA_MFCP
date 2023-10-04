<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('member_id')->unique();
            $table->string('full_name');
            $table->string('gender');
            $table->string('phone_number_1')->unique();
            $table->string('phone_number_2')->unique()->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('g_address');
            $table->string('s_address');
            $table->string('status');
            $table->string('tg_handler')->unique()->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
