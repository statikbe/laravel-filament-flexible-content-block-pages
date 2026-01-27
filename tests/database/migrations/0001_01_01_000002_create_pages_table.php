<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fcbp_pages', function (Blueprint $table) {
            $table->id();
            $table->json('title');
            $table->json('intro')->nullable();
            $table->json('hero_image_copyright')->nullable();
            $table->json('hero_image_title')->nullable();
            $table->json('hero_video_url')->nullable();
            $table->json('hero_call_to_actions')->nullable();
            $table->timestamp('publishing_begins_at')->nullable();
            $table->timestamp('publishing_ends_at')->nullable();
            $table->json('seo_title')->nullable();
            $table->json('seo_description')->nullable();
            $table->json('seo_keywords')->nullable();
            $table->json('overview_title')->nullable();
            $table->json('overview_description')->nullable();
            $table->json('content_blocks')->nullable();
            $table->json('slug')->nullable();
            $table->string('code')->nullable()->unique();
            $table->bigInteger('parent_id')->default(-1)->index();
            $table->integer('order')->default(0);
            $table->boolean('is_undeletable')->default(false);
            $table->timestamps();

            $table->index('publishing_begins_at');
            $table->index('publishing_ends_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fcbp_pages');
    }
};
