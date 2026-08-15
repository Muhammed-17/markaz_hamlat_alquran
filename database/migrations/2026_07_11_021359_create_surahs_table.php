<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Create Surahs Table
 * Description: Stores all 114 Surahs of the Quran with their metadata.
 * Used as a reference table for memorization plans and progress tracking.
 * 
 * IMPORTANT: Uses bigIncrements for id to ensure compatibility with foreign keys.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('surahs', function (Blueprint $table) {
            // Explicitly use bigIncrements to match foreign key types
            $table->bigIncrements('id');

            // Surah identification
            $table->integer('number')->unique()->comment('Surah number from 1 to 114');
            $table->string('name_arabic', 100)->comment('Surah name in Arabic');
            $table->string('name_english', 100)->comment('Surah name transliterated to English');
            $table->string('name_translation', 100)->nullable()->comment('Translated meaning of the name');

            // Surah metadata
            $table->string('type', 20)->comment('Revelation type: meccan or medinan');
            $table->integer('total_ayahs')->comment('Total number of verses in this Surah');
            $table->integer('juz_number')->comment('Juz (Part) number this Surah belongs to');
            $table->integer('page_start')->comment('Starting page number in the Mushaf');
            $table->integer('page_end')->comment('Ending page number in the Mushaf');

            // Timestamps
            $table->timestamps();

            // Indexes for performance
            $table->index('number', 'idx_surahs_number');
            $table->index('type', 'idx_surahs_type');
            $table->index('juz_number', 'idx_surahs_juz');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surahs');
    }
};
