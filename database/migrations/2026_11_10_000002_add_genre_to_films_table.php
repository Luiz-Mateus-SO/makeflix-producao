<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('films', 'genre')) {
            Schema::table('films', function (Blueprint $table) {
                $table->string('genre')->nullable()->after('release_date');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('films', 'genre')) {
            Schema::table('films', function (Blueprint $table) {
                $table->dropColumn('genre');
            });
        }
    }
};
