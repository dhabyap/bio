<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('link_contents', function (Blueprint $table) {
            $table->longText('state')->change();
        });
    }

    public function down(): void
    {
        Schema::table('link_contents', function (Blueprint $table) {
            $table->json('state')->change();
        });
    }
};