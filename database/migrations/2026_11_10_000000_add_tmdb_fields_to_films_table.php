<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('films', function (Blueprint $table) {
            $table->unsignedBigInteger('tmdb_id')->nullable()->after('id');
            $table->string('backdrop')->nullable()->after('cover');
            $table->string('preview_video')->nullable()->after('backdrop');
            $table->string('logo')->nullable()->after('preview_video');
            $table->date('release_date')->nullable()->after('preview_video');
        });
    }

    public function down(): void
    {
        Schema::table('films', function (Blueprint $table) {
            $table->dropColumn(['tmdb_id', 'backdrop', 'preview_video', 'logo', 'release_date']);
        });
    }
};
