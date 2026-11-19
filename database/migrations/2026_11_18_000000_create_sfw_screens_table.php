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
        if (Schema::hasTable('sfw_screens')) {
            return;
        }

        Schema::create('sfw_screens', function (Blueprint $table) {
            $table->string('id', 64)->primary();
            $table->string('parent_id', 64)->nullable()->index();
            $table->string('screen_id', 64)->nullable()->index();
            $table->string('type', 64)->nullable()->index();
            $table->string('key', 191)->nullable()->index();
            $table->string('name')->nullable();
            $table->string('class')->nullable();
            $table->longText('definition')->nullable();
            $table->json('meta')->nullable();
            $table->unsignedInteger('version')->default(1);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sfw_screens');
    }
};
