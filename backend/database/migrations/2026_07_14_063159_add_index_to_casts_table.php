<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('casts', function (Blueprint $table) {
            // index専用のファイルを作成することも可能
            $table->index(['name']);
            $table->index(['occupation_id', 'is_publish'], 'occupation_publish');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('casts', function (Blueprint $table) {
            // upに対応するdownを記載する
            $table->dropIndex(['name']);
            $table->dropIndex('occupation_publish');
        });
    }
};
