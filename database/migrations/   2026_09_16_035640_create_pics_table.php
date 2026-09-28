<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pics', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('match_rule')->nullable(); // department code, subject keyword, etc.
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pics');
    }
};
