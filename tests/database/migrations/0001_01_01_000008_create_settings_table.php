<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fcbp_settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_title');
            $table->json('contact_info')->nullable();
            $table->json('footer_copyright')->nullable();
            // columns used by the CustomSettings fixture to test extending the settings model:
            $table->json('intro')->nullable();
            $table->json('social_links')->nullable();
            $table->unsignedInteger('items_per_page')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fcbp_settings');
    }
};
