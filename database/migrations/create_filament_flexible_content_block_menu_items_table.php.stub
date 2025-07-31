<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use SolutionForest\FilamentTree\Support\Utils;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;

return new class extends Migration
{
    public function up()
    {
        Schema::create(FilamentFlexibleContentBlockPages::config()->getMenuItemsTable(), function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained(FilamentFlexibleContentBlockPages::config()->getMenusTable())->cascadeOnDelete();

            // Translatable label
            $table->json('label')->nullable();

            // Link type (url, route, or model alias)
            $table->string('link_type');

            // Link options
            $table->string('url')->nullable();
            $table->string('route')->nullable();
            $table->string('linkable_type')->nullable();
            $table->unsignedBigInteger('linkable_id')->nullable();

            // Additional options
            $table->string('target')->default('_self');
            $table->string('icon')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->boolean('use_model_title')->default(false);

            $table->integer(Utils::parentColumnName())->default(Utils::defaultParentId())->index();
            $table->integer(Utils::orderColumnName())->default(0);

            $table->timestamps();

            // Indexes
            $table->index(['linkable_type', 'linkable_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists(FilamentFlexibleContentBlockPages::config()->getMenuItemsTable());
    }
};
