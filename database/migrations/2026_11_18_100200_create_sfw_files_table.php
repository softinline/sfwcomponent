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
        if (Schema::hasTable('sfw_files')) {
            return;
        }

        Schema::create('sfw_files', function (Blueprint $table) {
            $table->string('id', 50)->primary();
            $table->string('table', 191)->nullable()->index();
            $table->string('row_id', 50)->nullable()->index();
            $table->string('category', 191)->nullable()->index();
            $table->string('collection', 191)->nullable();
            $table->string('disk', 50)->nullable();
            $table->string('path', 500)->nullable();
            $table->string('file', 255)->nullable();
            $table->string('original_name', 255)->nullable();
            $table->string('title', 255)->nullable();
            $table->text('description')->nullable();
            $table->string('mime_type', 191)->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->boolean('is_main')->default(false);
            $table->integer('sort_order')->default(0);
            $table->string('owner_user_id', 50)->nullable()->index();
            $table->string('checksum', 191)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sfw_files');
    }
};
