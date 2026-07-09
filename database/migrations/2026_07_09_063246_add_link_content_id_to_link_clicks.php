<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('link_clicks', function (Blueprint $table) {
            $table->foreignId('link_content_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('link_clicks', function (Blueprint $table) {
            $table->dropForeign(['link_content_id']);
            $table->dropColumn('link_content_id');
        });
    }
};