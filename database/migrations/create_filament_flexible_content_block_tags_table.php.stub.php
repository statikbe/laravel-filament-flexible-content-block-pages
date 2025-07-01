<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;

return new class extends Migration
{
    public function up(): void
    {
        $tagTable = FilamentFlexibleContentBlockPages::config()->getTagsTable();
        $tagTypeTable = FilamentFlexibleContentBlockPages::config()->getTagTypesTable();
        $taggableTable = FilamentFlexibleContentBlockPages::config()->getTaggablesTable();

        Schema::create($tagTypeTable, function (Blueprint $table) {
            $table->string('code')
                ->primary();
            $table->json('name');
            $table->string('colour')
                ->nullable();
            $table->string('icon')
                ->nullable();
            $table->boolean('is_default_type')
                ->default(false);
            $table->boolean('has_seo_pages')
                ->default(false);
            $table->timestamps();
        });

        Schema::create($tagTable, function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->json('seo_description')->nullable();
            $table->json('slug');
            $table->string('type')->nullable();
            $table->foreign('type')
                ->references('code')
                ->on(FilamentFlexibleContentBlockPages::config()->getTagTypesTable())
                ->cascadeOnDelete();
            $table->integer('order_column')->nullable();
            $table->timestamps();
        });

        Schema::create($taggableTable, function (Blueprint $table) {
            $table->foreignIdFor(FilamentFlexibleContentBlockPages::config()->getTagModel())
                ->constrained()
                ->cascadeOnDelete();
            $table->morphs('taggable');
            $table->unique([FilamentFlexibleContentBlockPages::config()->getTagModel()->getForeignKey(), 'taggable_id', 'taggable_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(FilamentFlexibleContentBlockPages::config()->getTagsTable());
        Schema::dropIfExists(FilamentFlexibleContentBlockPages::config()->getTagTypesTable());
        Schema::dropIfExists(FilamentFlexibleContentBlockPages::config()->getTaggablesTable());
    }
};
