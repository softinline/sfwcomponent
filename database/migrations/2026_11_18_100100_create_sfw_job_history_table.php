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
        if (Schema::hasTable('sfw_job_history')) {
            return;
        }

        Schema::create('sfw_job_history', function (Blueprint $table) {
            $table->string('id', 64)->primary();
            $table->string('uuid', 191)->nullable()->index();
            $table->string('connection', 191)->nullable();
            $table->string('queue', 191)->nullable()->index();
            $table->string('job_name', 191)->nullable();
            $table->longText('payload')->nullable();
            $table->string('status', 32)->default('processing')->index(); // processing|succeeded|failed
            $table->unsignedInteger('attempts')->default(0);
            $table->longText('exception')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->unsignedInteger('duration_ms')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sfw_job_history');
    }
};
