@props(['message_errors' => []])

@if ($errors->any() || !empty($message_errors))
<div role="alert" class="p-4 mb-4 text-sm text-red-800 rounded-2xl bg-red-50 border border-red-100 relative">
    <strong class="font-bold block mb-2">🚨 يرجى تصحيح الأخطاء التالية:</strong>
    <ul class="list-disc list-inside space-y-1 text-xs">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
        @foreach ($message_errors as $msg)
        <li>{{ $msg }}</li>
        @endforeach
    </ul>
</div>
@endif