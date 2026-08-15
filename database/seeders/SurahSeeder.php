<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeder: Surahs
 *
 * Inserts all 114 Surahs of the Quran into the surahs table.
 * This is essential reference data for the memorization tracking system.
 */
class SurahSeeder extends Seeder
{
    /**
     * Array of all 114 Quran Surahs with their metadata.
     */
    private array $surahs = [
        ['number' => 1, 'name_arabic' => 'الفاتحة', 'name_english' => 'Al-Fatihah', 'name_translation' => 'The Opening', 'type' => 'meccan', 'total_ayahs' => 7, 'juz_number' => 1, 'page_start' => 1, 'page_end' => 1],
        ['number' => 2, 'name_arabic' => 'البقرة', 'name_english' => 'Al-Baqarah', 'name_translation' => 'The Cow', 'type' => 'medinan', 'total_ayahs' => 286, 'juz_number' => 1, 'page_start' => 2, 'page_end' => 49],
        ['number' => 3, 'name_arabic' => 'آل عمران', 'name_english' => 'Aal-E-Imran', 'name_translation' => 'The Family of Imran', 'type' => 'medinan', 'total_ayahs' => 200, 'juz_number' => 3, 'page_start' => 50, 'page_end' => 76],
        ['number' => 4, 'name_arabic' => 'النساء', 'name_english' => 'An-Nisa', 'name_translation' => 'The Women', 'type' => 'medinan', 'total_ayahs' => 176, 'juz_number' => 4, 'page_start' => 77, 'page_end' => 106],
        ['number' => 5, 'name_arabic' => 'المائدة', 'name_english' => 'Al-Ma\'idah', 'name_translation' => 'The Table Spread', 'type' => 'medinan', 'total_ayahs' => 120, 'juz_number' => 6, 'page_start' => 106, 'page_end' => 127],
        ['number' => 6, 'name_arabic' => 'الأنعام', 'name_english' => 'Al-An\'am', 'name_translation' => 'The Cattle', 'type' => 'meccan', 'total_ayahs' => 165, 'juz_number' => 7, 'page_start' => 128, 'page_end' => 150],
        ['number' => 7, 'name_arabic' => 'الأعراف', 'name_english' => 'Al-A\'raf', 'name_translation' => 'The Heights', 'type' => 'meccan', 'total_ayahs' => 206, 'juz_number' => 8, 'page_start' => 151, 'page_end' => 176],
        ['number' => 8, 'name_arabic' => 'الأنفال', 'name_english' => 'Al-Anfal', 'name_translation' => 'The Spoils of War', 'type' => 'medinan', 'total_ayahs' => 75, 'juz_number' => 9, 'page_start' => 177, 'page_end' => 186],
        ['number' => 9, 'name_arabic' => 'التوبة', 'name_english' => 'At-Tawbah', 'name_translation' => 'The Repentance', 'type' => 'medinan', 'total_ayahs' => 129, 'juz_number' => 10, 'page_start' => 187, 'page_end' => 207],
        ['number' => 10, 'name_arabic' => 'يونس', 'name_english' => 'Yunus', 'name_translation' => 'Jonah', 'type' => 'meccan', 'total_ayahs' => 109, 'juz_number' => 11, 'page_start' => 208, 'page_end' => 221],
        ['number' => 11, 'name_arabic' => 'هود', 'name_english' => 'Hud', 'name_translation' => 'Hud', 'type' => 'meccan', 'total_ayahs' => 123, 'juz_number' => 11, 'page_start' => 221, 'page_end' => 235],
        ['number' => 12, 'name_arabic' => 'يوسف', 'name_english' => 'Yusuf', 'name_translation' => 'Joseph', 'type' => 'meccan', 'total_ayahs' => 111, 'juz_number' => 12, 'page_start' => 235, 'page_end' => 248],
        ['number' => 13, 'name_arabic' => 'الرعد', 'name_english' => 'Ar-Ra\'d', 'name_translation' => 'The Thunder', 'type' => 'medinan', 'total_ayahs' => 43, 'juz_number' => 13, 'page_start' => 249, 'page_end' => 255],
        ['number' => 14, 'name_arabic' => 'إبراهيم', 'name_english' => 'Ibrahim', 'name_translation' => 'Abraham', 'type' => 'meccan', 'total_ayahs' => 52, 'juz_number' => 13, 'page_start' => 255, 'page_end' => 261],
        ['number' => 15, 'name_arabic' => 'الحجر', 'name_english' => 'Al-Hijr', 'name_translation' => 'The Rocky Tract', 'type' => 'meccan', 'total_ayahs' => 99, 'juz_number' => 14, 'page_start' => 262, 'page_end' => 267],
        ['number' => 16, 'name_arabic' => 'النحل', 'name_english' => 'An-Nahl', 'name_translation' => 'The Bee', 'type' => 'meccan', 'total_ayahs' => 128, 'juz_number' => 14, 'page_start' => 267, 'page_end' => 281],
        ['number' => 17, 'name_arabic' => 'الإسراء', 'name_english' => 'Al-Isra', 'name_translation' => 'The Night Journey', 'type' => 'meccan', 'total_ayahs' => 111, 'juz_number' => 15, 'page_start' => 282, 'page_end' => 293],
        ['number' => 18, 'name_arabic' => 'الكهف', 'name_english' => 'Al-Kahf', 'name_translation' => 'The Cave', 'type' => 'meccan', 'total_ayahs' => 110, 'juz_number' => 15, 'page_start' => 293, 'page_end' => 304],
        ['number' => 19, 'name_arabic' => 'مريم', 'name_english' => 'Maryam', 'name_translation' => 'Mary', 'type' => 'meccan', 'total_ayahs' => 98, 'juz_number' => 16, 'page_start' => 305, 'page_end' => 312],
        ['number' => 20, 'name_arabic' => 'طه', 'name_english' => 'Ta-Ha', 'name_translation' => 'Ta-Ha', 'type' => 'meccan', 'total_ayahs' => 135, 'juz_number' => 16, 'page_start' => 312, 'page_end' => 321],
        ['number' => 21, 'name_arabic' => 'الأنبياء', 'name_english' => 'Al-Anbiya', 'name_translation' => 'The Prophets', 'type' => 'meccan', 'total_ayahs' => 112, 'juz_number' => 17, 'page_start' => 322, 'page_end' => 331],
        ['number' => 22, 'name_arabic' => 'الحج', 'name_english' => 'Al-Hajj', 'name_translation' => 'The Pilgrimage', 'type' => 'medinan', 'total_ayahs' => 78, 'juz_number' => 17, 'page_start' => 332, 'page_end' => 341],
        ['number' => 23, 'name_arabic' => 'المؤمنون', 'name_english' => 'Al-Mu\'minun', 'name_translation' => 'The Believers', 'type' => 'meccan', 'total_ayahs' => 118, 'juz_number' => 18, 'page_start' => 342, 'page_end' => 349],
        ['number' => 24, 'name_arabic' => 'النور', 'name_english' => 'An-Nur', 'name_translation' => 'The Light', 'type' => 'medinan', 'total_ayahs' => 64, 'juz_number' => 18, 'page_start' => 350, 'page_end' => 359],
        ['number' => 25, 'name_arabic' => 'الفرقان', 'name_english' => 'Al-Furqan', 'name_translation' => 'The Criterion', 'type' => 'meccan', 'total_ayahs' => 77, 'juz_number' => 18, 'page_start' => 359, 'page_end' => 366],
        ['number' => 26, 'name_arabic' => 'الشعراء', 'name_english' => 'Ash-Shu\'ara', 'name_translation' => 'The Poets', 'type' => 'meccan', 'total_ayahs' => 227, 'juz_number' => 19, 'page_start' => 367, 'page_end' => 376],
        ['number' => 27, 'name_arabic' => 'النمل', 'name_english' => 'An-Naml', 'name_translation' => 'The Ant', 'type' => 'meccan', 'total_ayahs' => 93, 'juz_number' => 19, 'page_start' => 377, 'page_end' => 385],
        ['number' => 28, 'name_arabic' => 'القصص', 'name_english' => 'Al-Qasas', 'name_translation' => 'The Stories', 'type' => 'meccan', 'total_ayahs' => 88, 'juz_number' => 20, 'page_start' => 385, 'page_end' => 396],
        ['number' => 29, 'name_arabic' => 'العنكبوت', 'name_english' => 'Al-Ankabut', 'name_translation' => 'The Spider', 'type' => 'meccan', 'total_ayahs' => 69, 'juz_number' => 20, 'page_start' => 396, 'page_end' => 404],
        ['number' => 30, 'name_arabic' => 'الروم', 'name_english' => 'Ar-Rum', 'name_translation' => 'The Romans', 'type' => 'meccan', 'total_ayahs' => 60, 'juz_number' => 21, 'page_start' => 404, 'page_end' => 410],
        ['number' => 31, 'name_arabic' => 'لقمان', 'name_english' => 'Luqman', 'name_translation' => 'Luqman', 'type' => 'meccan', 'total_ayahs' => 34, 'juz_number' => 21, 'page_start' => 411, 'page_end' => 414],
        ['number' => 32, 'name_arabic' => 'السجدة', 'name_english' => 'As-Sajda', 'name_translation' => 'The Prostration', 'type' => 'meccan', 'total_ayahs' => 30, 'juz_number' => 21, 'page_start' => 415, 'page_end' => 417],
        ['number' => 33, 'name_arabic' => 'الأحزاب', 'name_english' => 'Al-Ahzab', 'name_translation' => 'The Combined Forces', 'type' => 'medinan', 'total_ayahs' => 73, 'juz_number' => 21, 'page_start' => 418, 'page_end' => 427],
        ['number' => 34, 'name_arabic' => 'سبأ', 'name_english' => 'Saba', 'name_translation' => 'Sheba', 'type' => 'meccan', 'total_ayahs' => 54, 'juz_number' => 22, 'page_start' => 428, 'page_end' => 434],
        ['number' => 35, 'name_arabic' => 'فاطر', 'name_english' => 'Fatir', 'name_translation' => 'Originator', 'type' => 'meccan', 'total_ayahs' => 45, 'juz_number' => 22, 'page_start' => 434, 'page_end' => 440],
        ['number' => 36, 'name_arabic' => 'يس', 'name_english' => 'Ya-Sin', 'name_translation' => 'Ya-Sin', 'type' => 'meccan', 'total_ayahs' => 83, 'juz_number' => 22, 'page_start' => 440, 'page_end' => 445],
        ['number' => 37, 'name_arabic' => 'الصافات', 'name_english' => 'As-Saffat', 'name_translation' => 'Those Who Set The Ranks', 'type' => 'meccan', 'total_ayahs' => 182, 'juz_number' => 23, 'page_start' => 446, 'page_end' => 452],
        ['number' => 38, 'name_arabic' => 'ص', 'name_english' => 'Sad', 'name_translation' => 'The Letter Sad', 'type' => 'meccan', 'total_ayahs' => 88, 'juz_number' => 23, 'page_start' => 453, 'page_end' => 458],
        ['number' => 39, 'name_arabic' => 'الزمر', 'name_english' => 'Az-Zumar', 'name_translation' => 'The Troops', 'type' => 'meccan', 'total_ayahs' => 75, 'juz_number' => 23, 'page_start' => 458, 'page_end' => 467],
        ['number' => 40, 'name_arabic' => 'غافر', 'name_english' => 'Ghafir', 'name_translation' => 'The Forgiver', 'type' => 'meccan', 'total_ayahs' => 85, 'juz_number' => 24, 'page_start' => 467, 'page_end' => 476],
        ['number' => 41, 'name_arabic' => 'فصلت', 'name_english' => 'Fussilat', 'name_translation' => 'Explained in Detail', 'type' => 'meccan', 'total_ayahs' => 54, 'juz_number' => 24, 'page_start' => 477, 'page_end' => 482],
        ['number' => 42, 'name_arabic' => 'الشورى', 'name_english' => 'Ash-Shura', 'name_translation' => 'The Consultation', 'type' => 'meccan', 'total_ayahs' => 53, 'juz_number' => 25, 'page_start' => 483, 'page_end' => 489],
        ['number' => 43, 'name_arabic' => 'الزخرف', 'name_english' => 'Az-Zukhruf', 'name_translation' => 'The Ornaments of Gold', 'type' => 'meccan', 'total_ayahs' => 89, 'juz_number' => 25, 'page_start' => 489, 'page_end' => 495],
        ['number' => 44, 'name_arabic' => 'الدخان', 'name_english' => 'Ad-Dukhan', 'name_translation' => 'The Smoke', 'type' => 'meccan', 'total_ayahs' => 59, 'juz_number' => 25, 'page_start' => 496, 'page_end' => 498],
        ['number' => 45, 'name_arabic' => 'الجاثية', 'name_english' => 'Al-Jathiyah', 'name_translation' => 'The Crouching', 'type' => 'meccan', 'total_ayahs' => 37, 'juz_number' => 25, 'page_start' => 499, 'page_end' => 502],
        ['number' => 46, 'name_arabic' => 'الأحقاف', 'name_english' => 'Al-Ahqaf', 'name_translation' => 'The Wind-Curved Sandhills', 'type' => 'meccan', 'total_ayahs' => 35, 'juz_number' => 26, 'page_start' => 502, 'page_end' => 506],
        ['number' => 47, 'name_arabic' => 'محمد', 'name_english' => 'Muhammad', 'name_translation' => 'Muhammad', 'type' => 'medinan', 'total_ayahs' => 38, 'juz_number' => 26, 'page_start' => 507, 'page_end' => 510],
        ['number' => 48, 'name_arabic' => 'الفتح', 'name_english' => 'Al-Fath', 'name_translation' => 'The Victory', 'type' => 'medinan', 'total_ayahs' => 29, 'juz_number' => 26, 'page_start' => 511, 'page_end' => 515],
        ['number' => 49, 'name_arabic' => 'الحجرات', 'name_english' => 'Al-Hujurat', 'name_translation' => 'The Rooms', 'type' => 'medinan', 'total_ayahs' => 18, 'juz_number' => 26, 'page_start' => 515, 'page_end' => 517],
        ['number' => 50, 'name_arabic' => 'ق', 'name_english' => 'Qaf', 'name_translation' => 'The Letter Qaf', 'type' => 'meccan', 'total_ayahs' => 45, 'juz_number' => 26, 'page_start' => 518, 'page_end' => 520],
        ['number' => 51, 'name_arabic' => 'الذاريات', 'name_english' => 'Adh-Dhariyat', 'name_translation' => 'The Winnowing Winds', 'type' => 'meccan', 'total_ayahs' => 60, 'juz_number' => 26, 'page_start' => 520, 'page_end' => 523],
        ['number' => 52, 'name_arabic' => 'الطور', 'name_english' => 'At-Tur', 'name_translation' => 'The Mount', 'type' => 'meccan', 'total_ayahs' => 49, 'juz_number' => 27, 'page_start' => 523, 'page_end' => 525],
        ['number' => 53, 'name_arabic' => 'النجم', 'name_english' => 'An-Najm', 'name_translation' => 'The Star', 'type' => 'meccan', 'total_ayahs' => 62, 'juz_number' => 27, 'page_start' => 526, 'page_end' => 528],
        ['number' => 54, 'name_arabic' => 'القمر', 'name_english' => 'Al-Qamar', 'name_translation' => 'The Moon', 'type' => 'meccan', 'total_ayahs' => 55, 'juz_number' => 27, 'page_start' => 528, 'page_end' => 531],
        ['number' => 55, 'name_arabic' => 'الرحمن', 'name_english' => 'Ar-Rahman', 'name_translation' => 'The Beneficent', 'type' => 'medinan', 'total_ayahs' => 78, 'juz_number' => 27, 'page_start' => 531, 'page_end' => 534],
        ['number' => 56, 'name_arabic' => 'الواقعة', 'name_english' => 'Al-Waqi\'a', 'name_translation' => 'The Inevitable', 'type' => 'meccan', 'total_ayahs' => 96, 'juz_number' => 27, 'page_start' => 534, 'page_end' => 537],
        ['number' => 57, 'name_arabic' => 'الحديد', 'name_english' => 'Al-Hadid', 'name_translation' => 'The Iron', 'type' => 'medinan', 'total_ayahs' => 29, 'juz_number' => 27, 'page_start' => 537, 'page_end' => 541],
        ['number' => 58, 'name_arabic' => 'المجادلة', 'name_english' => 'Al-Mujadila', 'name_translation' => 'The Pleading Woman', 'type' => 'medinan', 'total_ayahs' => 22, 'juz_number' => 28, 'page_start' => 542, 'page_end' => 545],
        ['number' => 59, 'name_arabic' => 'الحشر', 'name_english' => 'Al-Hashr', 'name_translation' => 'The Exile', 'type' => 'medinan', 'total_ayahs' => 24, 'juz_number' => 28, 'page_start' => 545, 'page_end' => 548],
        ['number' => 60, 'name_arabic' => 'الممتحنة', 'name_english' => 'Al-Mumtahanah', 'name_translation' => 'She That Is To Be Examined', 'type' => 'medinan', 'total_ayahs' => 13, 'juz_number' => 28, 'page_start' => 549, 'page_end' => 551],
        ['number' => 61, 'name_arabic' => 'الصف', 'name_english' => 'As-Saff', 'name_translation' => 'The Ranks', 'type' => 'medinan', 'total_ayahs' => 14, 'juz_number' => 28, 'page_start' => 551, 'page_end' => 552],
        ['number' => 62, 'name_arabic' => 'الجمعة', 'name_english' => 'Al-Jumu\'ah', 'name_translation' => 'The Congregation', 'type' => 'medinan', 'total_ayahs' => 11, 'juz_number' => 28, 'page_start' => 553, 'page_end' => 554],
        ['number' => 63, 'name_arabic' => 'المنافقون', 'name_english' => 'Al-Munafiqun', 'name_translation' => 'The Hypocrites', 'type' => 'medinan', 'total_ayahs' => 11, 'juz_number' => 28, 'page_start' => 554, 'page_end' => 555],
        ['number' => 64, 'name_arabic' => 'التغابن', 'name_english' => 'At-Taghabun', 'name_translation' => 'The Mutual Disillusion', 'type' => 'medinan', 'total_ayahs' => 18, 'juz_number' => 28, 'page_start' => 556, 'page_end' => 557],
        ['number' => 65, 'name_arabic' => 'الطلاق', 'name_english' => 'At-Talaq', 'name_translation' => 'The Divorce', 'type' => 'medinan', 'total_ayahs' => 12, 'juz_number' => 28, 'page_start' => 558, 'page_end' => 559],
        ['number' => 66, 'name_arabic' => 'التحريم', 'name_english' => 'At-Tahrim', 'name_translation' => 'The Prohibition', 'type' => 'medinan', 'total_ayahs' => 12, 'juz_number' => 28, 'page_start' => 560, 'page_end' => 561],
        ['number' => 67, 'name_arabic' => 'الملك', 'name_english' => 'Al-Mulk', 'name_translation' => 'The Sovereignty', 'type' => 'meccan', 'total_ayahs' => 30, 'juz_number' => 29, 'page_start' => 562, 'page_end' => 564],
        ['number' => 68, 'name_arabic' => 'القلم', 'name_english' => 'Al-Qalam', 'name_translation' => 'The Pen', 'type' => 'meccan', 'total_ayahs' => 52, 'juz_number' => 29, 'page_start' => 564, 'page_end' => 566],
        ['number' => 69, 'name_arabic' => 'الحاقة', 'name_english' => 'Al-Haqqah', 'name_translation' => 'The Reality', 'type' => 'meccan', 'total_ayahs' => 52, 'juz_number' => 29, 'page_start' => 566, 'page_end' => 568],
        ['number' => 70, 'name_arabic' => 'المعارج', 'name_english' => 'Al-Ma\'arij', 'name_translation' => 'The Ascending Stairways', 'type' => 'meccan', 'total_ayahs' => 44, 'juz_number' => 29, 'page_start' => 568, 'page_end' => 570],
        ['number' => 71, 'name_arabic' => 'نوح', 'name_english' => 'Nuh', 'name_translation' => 'Noah', 'type' => 'meccan', 'total_ayahs' => 28, 'juz_number' => 29, 'page_start' => 570, 'page_end' => 571],
        ['number' => 72, 'name_arabic' => 'الجن', 'name_english' => 'Al-Jinn', 'name_translation' => 'The Jinn', 'type' => 'meccan', 'total_ayahs' => 28, 'juz_number' => 29, 'page_start' => 572, 'page_end' => 573],
        ['number' => 73, 'name_arabic' => 'المزمل', 'name_english' => 'Al-Muzzammil', 'name_translation' => 'The Enshrouded One', 'type' => 'meccan', 'total_ayahs' => 20, 'juz_number' => 29, 'page_start' => 574, 'page_end' => 575],
        ['number' => 74, 'name_arabic' => 'المدثر', 'name_english' => 'Al-Muddaththir', 'name_translation' => 'The Cloaked One', 'type' => 'meccan', 'total_ayahs' => 56, 'juz_number' => 29, 'page_start' => 575, 'page_end' => 577],
        ['number' => 75, 'name_arabic' => 'القيامة', 'name_english' => 'Al-Qiyamah', 'name_translation' => 'The Resurrection', 'type' => 'meccan', 'total_ayahs' => 40, 'juz_number' => 29, 'page_start' => 577, 'page_end' => 578],
        ['number' => 76, 'name_arabic' => 'الإنسان', 'name_english' => 'Al-Insan', 'name_translation' => 'The Man', 'type' => 'medinan', 'total_ayahs' => 31, 'juz_number' => 29, 'page_start' => 578, 'page_end' => 580],
        ['number' => 77, 'name_arabic' => 'المرسلات', 'name_english' => 'Al-Mursalat', 'name_translation' => 'The Emissaries', 'type' => 'meccan', 'total_ayahs' => 50, 'juz_number' => 29, 'page_start' => 580, 'page_end' => 581],
        ['number' => 78, 'name_arabic' => 'النبأ', 'name_english' => 'An-Naba', 'name_translation' => 'The Tidings', 'type' => 'meccan', 'total_ayahs' => 40, 'juz_number' => 30, 'page_start' => 582, 'page_end' => 583],
        ['number' => 79, 'name_arabic' => 'النازعات', 'name_english' => 'An-Nazi\'at', 'name_translation' => 'Those Who Drag Forth', 'type' => 'meccan', 'total_ayahs' => 46, 'juz_number' => 30, 'page_start' => 583, 'page_end' => 584],
        ['number' => 80, 'name_arabic' => 'عبس', 'name_english' => '\'Abasa', 'name_translation' => 'He Frowned', 'type' => 'meccan', 'total_ayahs' => 42, 'juz_number' => 30, 'page_start' => 585, 'page_end' => 586],
        ['number' => 81, 'name_arabic' => 'التكوير', 'name_english' => 'At-Takwir', 'name_translation' => 'The Overthrowing', 'type' => 'meccan', 'total_ayahs' => 29, 'juz_number' => 30, 'page_start' => 586, 'page_end' => 587],
        ['number' => 82, 'name_arabic' => 'الإنفطار', 'name_english' => 'Al-Infitar', 'name_translation' => 'The Cleaving', 'type' => 'meccan', 'total_ayahs' => 19, 'juz_number' => 30, 'page_start' => 587, 'page_end' => 587],
        ['number' => 83, 'name_arabic' => 'المطففين', 'name_english' => 'Al-Mutaffifin', 'name_translation' => 'The Defrauding', 'type' => 'meccan', 'total_ayahs' => 36, 'juz_number' => 30, 'page_start' => 587, 'page_end' => 589],
        ['number' => 84, 'name_arabic' => 'الإنشقاق', 'name_english' => 'Al-Inshiqaq', 'name_translation' => 'The Sundering', 'type' => 'meccan', 'total_ayahs' => 25, 'juz_number' => 30, 'page_start' => 589, 'page_end' => 590],
        ['number' => 85, 'name_arabic' => 'البروج', 'name_english' => 'Al-Buruj', 'name_translation' => 'The Mansions of the Stars', 'type' => 'meccan', 'total_ayahs' => 22, 'juz_number' => 30, 'page_start' => 590, 'page_end' => 590],
        ['number' => 86, 'name_arabic' => 'الطارق', 'name_english' => 'At-Tariq', 'name_translation' => 'The Nightcomer', 'type' => 'meccan', 'total_ayahs' => 17, 'juz_number' => 30, 'page_start' => 591, 'page_end' => 591],
        ['number' => 87, 'name_arabic' => 'الأعلى', 'name_english' => 'Al-A\'la', 'name_translation' => 'The Most High', 'type' => 'meccan', 'total_ayahs' => 19, 'juz_number' => 30, 'page_start' => 591, 'page_end' => 592],
        ['number' => 88, 'name_arabic' => 'الغاشية', 'name_english' => 'Al-Ghashiyah', 'name_translation' => 'The Overwhelming', 'type' => 'meccan', 'total_ayahs' => 26, 'juz_number' => 30, 'page_start' => 592, 'page_end' => 593],
        ['number' => 89, 'name_arabic' => 'الفجر', 'name_english' => 'Al-Fajr', 'name_translation' => 'The Dawn', 'type' => 'meccan', 'total_ayahs' => 30, 'juz_number' => 30, 'page_start' => 593, 'page_end' => 594],
        ['number' => 90, 'name_arabic' => 'البلد', 'name_english' => 'Al-Balad', 'name_translation' => 'The City', 'type' => 'meccan', 'total_ayahs' => 20, 'juz_number' => 30, 'page_start' => 594, 'page_end' => 595],
        ['number' => 91, 'name_arabic' => 'الشمس', 'name_english' => 'Ash-Shams', 'name_translation' => 'The Sun', 'type' => 'meccan', 'total_ayahs' => 15, 'juz_number' => 30, 'page_start' => 595, 'page_end' => 595],
        ['number' => 92, 'name_arabic' => 'الليل', 'name_english' => 'Al-Layl', 'name_translation' => 'The Night', 'type' => 'meccan', 'total_ayahs' => 21, 'juz_number' => 30, 'page_start' => 595, 'page_end' => 596],
        ['number' => 93, 'name_arabic' => 'الضحى', 'name_english' => 'Ad-Duha', 'name_translation' => 'The Morning Hours', 'type' => 'meccan', 'total_ayahs' => 11, 'juz_number' => 30, 'page_start' => 596, 'page_end' => 596],
        ['number' => 94, 'name_arabic' => 'الشرح', 'name_english' => 'Ash-Sharh', 'name_translation' => 'The Relief', 'type' => 'meccan', 'total_ayahs' => 8, 'juz_number' => 30, 'page_start' => 596, 'page_end' => 597],
        ['number' => 95, 'name_arabic' => 'التين', 'name_english' => 'At-Tin', 'name_translation' => 'The Fig', 'type' => 'meccan', 'total_ayahs' => 8, 'juz_number' => 30, 'page_start' => 597, 'page_end' => 597],
        ['number' => 96, 'name_arabic' => 'العلق', 'name_english' => 'Al-\'Alaq', 'name_translation' => 'The Clot', 'type' => 'meccan', 'total_ayahs' => 19, 'juz_number' => 30, 'page_start' => 597, 'page_end' => 598],
        ['number' => 97, 'name_arabic' => 'القدر', 'name_english' => 'Al-Qadr', 'name_translation' => 'The Power', 'type' => 'meccan', 'total_ayahs' => 5, 'juz_number' => 30, 'page_start' => 598, 'page_end' => 598],
        ['number' => 98, 'name_arabic' => 'البينة', 'name_english' => 'Al-Bayyinah', 'name_translation' => 'The Clear Proof', 'type' => 'medinan', 'total_ayahs' => 8, 'juz_number' => 30, 'page_start' => 598, 'page_end' => 599],
        ['number' => 99, 'name_arabic' => 'الزلزلة', 'name_english' => 'Az-Zalzalah', 'name_translation' => 'The Earthquake', 'type' => 'medinan', 'total_ayahs' => 8, 'juz_number' => 30, 'page_start' => 599, 'page_end' => 599],
        ['number' => 100, 'name_arabic' => 'العاديات', 'name_english' => 'Al-\'Adiyat', 'name_translation' => 'The Courser', 'type' => 'meccan', 'total_ayahs' => 11, 'juz_number' => 30, 'page_start' => 599, 'page_end' => 600],
        ['number' => 101, 'name_arabic' => 'القارعة', 'name_english' => 'Al-Qari\'ah', 'name_translation' => 'The Calamity', 'type' => 'meccan', 'total_ayahs' => 11, 'juz_number' => 30, 'page_start' => 600, 'page_end' => 600],
        ['number' => 102, 'name_arabic' => 'التكاثر', 'name_english' => 'At-Takathur', 'name_translation' => 'The Rivalry in World Increase', 'type' => 'meccan', 'total_ayahs' => 8, 'juz_number' => 30, 'page_start' => 600, 'page_end' => 600],
        ['number' => 103, 'name_arabic' => 'العصر', 'name_english' => 'Al-\'Asr', 'name_translation' => 'The Declining Day', 'type' => 'meccan', 'total_ayahs' => 3, 'juz_number' => 30, 'page_start' => 601, 'page_end' => 601],
        ['number' => 104, 'name_arabic' => 'الهمزة', 'name_english' => 'Al-Humazah', 'name_translation' => 'The Traducer', 'type' => 'meccan', 'total_ayahs' => 9, 'juz_number' => 30, 'page_start' => 601, 'page_end' => 601],
        ['number' => 105, 'name_arabic' => 'الفيل', 'name_english' => 'Al-Fil', 'name_translation' => 'The Elephant', 'type' => 'meccan', 'total_ayahs' => 5, 'juz_number' => 30, 'page_start' => 601, 'page_end' => 601],
        ['number' => 106, 'name_arabic' => 'قريش', 'name_english' => 'Quraysh', 'name_translation' => 'Quraysh', 'type' => 'meccan', 'total_ayahs' => 4, 'juz_number' => 30, 'page_start' => 602, 'page_end' => 602],
        ['number' => 107, 'name_arabic' => 'الماعون', 'name_english' => 'Al-Ma\'un', 'name_translation' => 'The Small Kindnesses', 'type' => 'meccan', 'total_ayahs' => 7, 'juz_number' => 30, 'page_start' => 602, 'page_end' => 602],
        ['number' => 108, 'name_arabic' => 'الكوثر', 'name_english' => 'Al-Kawthar', 'name_translation' => 'The Abundance', 'type' => 'meccan', 'total_ayahs' => 3, 'juz_number' => 30, 'page_start' => 602, 'page_end' => 602],
        ['number' => 109, 'name_arabic' => 'الكافرون', 'name_english' => 'Al-Kafirun', 'name_translation' => 'The Disbelievers', 'type' => 'meccan', 'total_ayahs' => 6, 'juz_number' => 30, 'page_start' => 603, 'page_end' => 603],
        ['number' => 110, 'name_arabic' => 'النصر', 'name_english' => 'An-Nasr', 'name_translation' => 'The Divine Support', 'type' => 'medinan', 'total_ayahs' => 3, 'juz_number' => 30, 'page_start' => 603, 'page_end' => 603],
        ['number' => 111, 'name_arabic' => 'المسد', 'name_english' => 'Al-Masad', 'name_translation' => 'The Palm Fiber', 'type' => 'meccan', 'total_ayahs' => 5, 'juz_number' => 30, 'page_start' => 603, 'page_end' => 603],
        ['number' => 112, 'name_arabic' => 'الإخلاص', 'name_english' => 'Al-Ikhlas', 'name_translation' => 'The Sincerity', 'type' => 'meccan', 'total_ayahs' => 4, 'juz_number' => 30, 'page_start' => 604, 'page_end' => 604],
        ['number' => 113, 'name_arabic' => 'الفلق', 'name_english' => 'Al-Falaq', 'name_translation' => 'The Daybreak', 'type' => 'meccan', 'total_ayahs' => 5, 'juz_number' => 30, 'page_start' => 604, 'page_end' => 604],
        ['number' => 114, 'name_arabic' => 'الناس', 'name_english' => 'An-Nas', 'name_translation' => 'Mankind', 'type' => 'meccan', 'total_ayahs' => 6, 'juz_number' => 30, 'page_start' => 604, 'page_end' => 604],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->surahs as $surah) {
            DB::table('surahs')->insert(array_merge($surah, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
