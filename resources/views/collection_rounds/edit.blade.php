<x-layouts.markaz-layout>
    @include('collection_rounds.form', [
    'mode' => 'edit',
    'collectionRound' => $collectionRound,
    'creators' => $creators,
    'breakdown' => $breakdown,
    'selectedSubscriptionIds' => $selectedSubscriptionIds,
    'selectedCircleId' => $collectionRound->circle_id,
    ])
</x-layouts.markaz-layout>