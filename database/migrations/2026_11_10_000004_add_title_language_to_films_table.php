<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('films', 'title_language')) {
            Schema::table('films', function (Blueprint $table) {
                $table->string('title_language')->nullable()->after('original_title');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('films', 'title_language')) {
            Schema::table('films', function (Blueprint $table) {
                $table->dropColumn('title_language');
            });
        }
    }
};
