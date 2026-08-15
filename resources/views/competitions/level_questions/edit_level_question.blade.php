<x-layouts.markaz-layout>
    @include('competitions.level_questions.form_level_question', [
        'competition' => $competition,
        'levels' => $levels,
        'surahs' => $surahs,
        'question' => $question,
    ])
</x-layouts.markaz-layout>