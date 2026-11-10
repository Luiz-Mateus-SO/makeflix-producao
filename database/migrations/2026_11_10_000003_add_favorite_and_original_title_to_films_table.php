<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('films', function (Blueprint $table) {
            if (! Schema::hasColumn('films', 'original_title')) {
                $table->string('original_title')->nullable()->after('title');
            }

            if (! Schema::hasColumn('films', 'is_favorite')) {
                $table->boolean('is_favorite')->default(false)->after('genre');
            }
        });
    }

    public function down(): void
    {
        Schema::table('films', function (Blueprint $table) {
            $columns = [];

            if (Schema::hasColumn('films', 'original_title')) {
                $columns[] = 'original_title';
            }

            if (Schema::hasColumn('films', 'is_favorite')) {
                $columns[] = 'is_favorite';
            }

            if ($columns) {
                $table->dropColumn($columns);
            }
        });
    }
};
