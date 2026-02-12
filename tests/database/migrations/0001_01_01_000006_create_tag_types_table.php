<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fcbp_tag_types', function (Blueprint $table) {
            $table->string('code')->primary();
            $table->json('name');
            $table->string('colour')->nullable();
            $table->string('icon')->nullable();
            $table->boolean('is_default_type')->default(false);
            $table->boolean('has_seo_pages')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fcbp_tag_types');
    }
};
