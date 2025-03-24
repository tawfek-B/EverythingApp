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
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('userName')->unique();
            $table->string('countryCode');
            $table->string('number')->unique();
            $table->string('password');
            $table->string('image');
            $table->json('links')->nullable();
            // $table->string('Subject');   //I'll figure what i need to do with this
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
