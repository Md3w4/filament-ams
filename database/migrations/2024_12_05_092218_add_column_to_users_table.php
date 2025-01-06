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
        Schema::table('users', function (Blueprint $table) {
            $table->string('nip')->after('id')->nullable();
            $table->after('full_name', function ($table) {
                $table->string('place_of_birth')->nullable();
                $table->date('date_of_birth')->nullable();
                $table->string('address')->nullable();
                $table->string('education')->nullable();
                $table->string('last_education')->nullable();
                $table->decimal('salary', 10, 2)->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'nip',
                'place_of_birth',
                'date_of_birth',
                'address',
                'education',
                'last_education',
                'salary'
            ]);
        });
    }
};
