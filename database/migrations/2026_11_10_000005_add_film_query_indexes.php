<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('films', function (Blueprint $table) {
            $table->index('created_at', 'films_created_at_index');
            $table->index('is_favorite', 'films_is_favorite_index');
            $table->index('tmdb_id', 'films_tmdb_id_index');
        });
    }

    public function down(): void
    {
        Schema::table('films', function (Blueprint $table) {
            $table->dropIndex('films_created_at_index');
            $table->dropIndex('films_is_favorite_index');
            $table->dropIndex('films_tmdb_id_index');
        });
    }
};
