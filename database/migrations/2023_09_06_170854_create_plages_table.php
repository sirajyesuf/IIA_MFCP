<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\PaymentPeriod;
use App\Enums\Status;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('plages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('member_id');
            $table->integer('amount');
            $table->string('payment_period');
            $table->boolean('status')->default(1);
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('advance')->default(0);
            $table->integer('bonus')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('member_id')->references('id')->on('members');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plages');
    }
};
