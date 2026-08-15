<x-layouts.markaz-layout>
    @include('competitions.participants.form_participant', [
        'competition'    => $competition,
        'levels'         => $levels,
        'circles'        => $circles,
        'participant'    => $participant,
        'studentOptions' => $studentOptions ?? [],
        'supervisors'    => $supervisors,
        'centers'        => $centers,     
        'formAction'     => route('competitions.participants.update', [$competition, $participant]),
        'formMethod'     => 'PUT',
    ])
</x-layouts.markaz-layout>