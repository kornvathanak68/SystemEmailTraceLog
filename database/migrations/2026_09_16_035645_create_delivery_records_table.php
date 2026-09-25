<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_run_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pic_id')->nullable()->constrained()->nullOnDelete();

            $table->string('message_id')->nullable()->index();
            $table->string('recipient_email')->nullable()->index();
            $table->string('subject')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->string('status')->nullable();          // delivered|failed|bounced|...
            $table->string('failure_reason')->nullable();
            $table->string('related_file_path')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_records');
    }
};
