<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Surah
 * 
 * Represents a Surah (chapter) of the Quran.
 * Used as a reference table for memorization plans and progress tracking.
 * 
 * NOTE: Surah is a global reference table, not scoped by center.
 */
class Surah extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'surahs';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'number',
        'name_arabic',
        'name_english',
        'name_translation',
        'type',
        'total_ayahs',
        'juz_number',
        'page_start',
        'page_end',
    ];

    // ==========================================
    // Relationships
    // ==========================================

    /**
     * Get all construction details where this surah is the current surah.
     */
    public function constructionDetails(): HasMany
    {
        return $this->hasMany(StudentConstructionDetail::class, 'current_surah_id');
    }
    
    public function surahTests(): HasMany
    {
        return $this->hasMany(SurahTest::class);
    }
}
