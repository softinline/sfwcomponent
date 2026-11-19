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
        if (Schema::hasTable('sfw_configs')) {
            return;
        }

        Schema::create('sfw_configs', function (Blueprint $table) {
            $table->string('id', 64)->primary();
            $table->string('key', 191)->unique();
            $table->longText('value')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sfw_configs');
    }
};
