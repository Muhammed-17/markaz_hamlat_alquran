<x-layouts.markaz-layout>
    @include('collection_rounds.form', [
    'mode' => 'create',
    'circles' => $circles,
    'creators' => $creators,
    'breakdown' => $breakdown,
    'previousRounds' => $previousRounds,
    'nextRoundNumber' => $nextRoundNumber,
    'selectedCircleId' => $selectedCircleId,
    'selectedMonth' => $selectedMonth,
    'selectedSubscriptionIds' => [],
    ])
</x-layouts.markaz-layout>