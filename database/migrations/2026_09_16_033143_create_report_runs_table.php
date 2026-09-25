<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_runs', function (Blueprint $table) {
            $table->id();
            $table->string('period')->unique();          // e.g. 2026-08
            $table->string('source_file')->nullable();   // raw file from EA team
            $table->string('filtered_file')->nullable(); // filtered output
            $table->string('stage')->default('pending'); // pending|fetched|filtered|pushed|sms_sent|reported|failed
            $table->string('status')->default('pending');// pending|running|success|failed
            $table->unsignedInteger('total_rows')->nullable();
            $table->unsignedInteger('failed_count')->nullable();
            $table->text('error')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_runs');
    }
};
