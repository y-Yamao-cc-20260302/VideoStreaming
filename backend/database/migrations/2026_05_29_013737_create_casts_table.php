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
        Schema::create('casts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('gender')->nullable();;
            $table->date('birthday')->nullable();;
            $table->integer('occupation_id')->nullable();;
            $table->string('picture_path')->nullable();;
            $table->boolean('is_publish')->nullable();;
            $table->timestamps();
            $table->softDeletes();

            $table->unique('name');
            $table->foreign('occupation_id')->references('id')->on('occupations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('casts');
    }
};
