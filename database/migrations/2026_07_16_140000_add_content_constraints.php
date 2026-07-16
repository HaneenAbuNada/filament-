<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->unique('slug');
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->unique('slug');
            $table->foreign('category_id')->references('id')->on('categories')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropUnique(['slug']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropUnique(['slug']);
        });
    }
};
