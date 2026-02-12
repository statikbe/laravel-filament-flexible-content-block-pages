<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fcbp_tags', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->json('seo_description')->nullable();
            $table->json('slug');
            $table->string('type')->nullable();
            $table->foreign('type')->references('code')->on('fcbp_tag_types')->cascadeOnDelete();
            $table->integer('order_column')->nullable();
            $table->timestamps();
        });

        Schema::create('fcbp_taggables', function (Blueprint $table) {
            $table->foreignId('tag_id')->constrained('fcbp_tags')->cascadeOnDelete();
            $table->morphs('taggable');
            $table->unique(['tag_id', 'taggable_id', 'taggable_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fcbp_taggables');
        Schema::dropIfExists('fcbp_tags');
    }
};
