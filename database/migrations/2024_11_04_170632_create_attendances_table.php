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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('schedule_id')->constrained()->cascadeOnDelete();
            $table->foreignId('overtime_request_id')->nullable()->constrained()->onDelete('set null');
            $table->time('time_in')->nullable();
            $table->double('latitude_in')->nullable();
            $table->double('longitude_in')->nullable();
            $table->enum('status_in', ['on_time', 'late', 'system_generated'])->nullable();
            $table->time('time_out')->nullable();
            $table->double('latitude_out')->nullable();
            $table->double('longitude_out')->nullable();
            $table->enum('status_out', ['on_time', 'early_leave', 'overtime', 'system_generated'])->nullable();
            $table->boolean('is_automated_checkout')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
