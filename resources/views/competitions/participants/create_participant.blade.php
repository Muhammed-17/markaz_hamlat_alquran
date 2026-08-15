<x-layouts.markaz-layout>
    @include('competitions.participants.form_participant', [
        'competition' => $competition,
        'levels'      => $levels,
        'circles'     => $circles,
        'participant' => null,
        'formAction'  => route('competitions.participants.store', $competition),
        'formMethod'  => 'POST',
    ])
</x-layouts.markaz-layout>