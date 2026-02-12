<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fcbp_menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained('fcbp_menus')->cascadeOnDelete();
            $table->json('label')->nullable();
            $table->string('link_type');
            $table->string('url')->nullable();
            $table->string('route')->nullable();
            $table->string('linkable_type')->nullable();
            $table->unsignedBigInteger('linkable_id')->nullable();
            $table->string('target')->default('_self');
            $table->string('icon')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->boolean('use_model_title')->default(false);
            $table->integer('parent_id')->default(\SolutionForest\FilamentTree\Support\Utils::defaultParentId())->index();
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index(['linkable_type', 'linkable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fcbp_menu_items');
    }
};
