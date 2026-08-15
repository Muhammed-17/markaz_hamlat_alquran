<x-layouts.markaz-layout>
    @include('competitions.level_questions.form_level_question', [
        'competition' => $competition,
        'levels' => $levels,
        'surahs' => $surahs,
        'selectedLevelId' => $selectedLevelId,
    ])
</x-layouts.markaz-layout>