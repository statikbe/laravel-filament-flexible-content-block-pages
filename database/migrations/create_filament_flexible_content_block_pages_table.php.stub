<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;

return new class extends Migration
{
    public function up()
    {
        $pageTable = FilamentFlexibleContentBlockPages::config()->getPagesTable();
        $pageModel = FilamentFlexibleContentBlockPages::config()->getPageModel()::class;

        Schema::create($pageTable, function (Blueprint $table) use ($pageTable, $pageModel) {
            $table->id();

            $table->json('title');

            //Intro:
            $table->json('intro')
                ->nullable();

            //Hero image:
            $table->json('hero_image_copyright')
                ->nullable();
            $table->json('hero_image_title')
                ->nullable();
            if(FilamentFlexibleContentBlockPages::config()->isHeroVideoUrlEnabled($pageModel)) {
                $table->json('hero_video_url')
                    ->nullable();
            }
            if(FilamentFlexibleContentBlockPages::config()->isHeroCallToActionsEnabled($pageModel)) {
                $table->json('hero_call_to_actions')
                    ->nullable();
            }

            //Publishing:
            $table->timestamp('publishing_begins_at')
                ->nullable();
            $table->timestamp('publishing_ends_at')
                ->nullable();
            $table->index('publishing_begins_at');
            $table->index('publishing_ends_at');

            //SEO:
            $table->json('seo_title')
                ->nullable();
            $table->json('seo_description')
                ->nullable();
            $table->json('seo_keywords')
                ->nullable();

            //Overview:
            $table->json('overview_title')
                ->nullable();
            $table->json('overview_description')
                ->nullable();

            //Content blocks:
            $table->json('content_blocks')
                ->nullable();

            //Slug:
            $table->json('slug')
                ->nullable();

            //Unique code:
            $table->string('code')
                ->nullable()
                ->unique();

            //Author:
            if(FilamentFlexibleContentBlockPages::config()->isAuthorEnabled($pageModel)) {
                $table->unsignedBigInteger('author_id')
                    ->nullable();
                $table->foreign('author_id')
                    ->references('id')
                    ->on(FilamentFlexibleContentBlockPages::config()->getAuthorsTable())
                    ->onDelete('set null');
            }

            // Parent-child:
            if(FilamentFlexibleContentBlockPages::config()->isParentAndPageTreeEnabled($pageModel)) {
                $table->bigInteger('parent_id')
                    ->default(\SolutionForest\FilamentTree\Support\Utils::defaultParentId())
                    ->index();
                $table->integer('order')
                    ->default(0);
            }

            // Deletable:
            if(FilamentFlexibleContentBlockPages::config()->isUndeletableEnabled($pageModel)) {
                $table->boolean('is_undeletable')
                    ->default(false);
            }

            $table->timestamps();
        });
    }
};
