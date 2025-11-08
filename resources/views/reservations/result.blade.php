<x-layout title="Reservation Result">
    <h1 class="text-2xl font-semibold">Reservation Result</h1>

    @if($success)
        <div class="mt-4 rounded border border-green-300 bg-green-50 text-green-800 px-4 py-3">
            Task <strong>{{ $task->title }}</strong> successfully reserved for vessel <strong>{{ $vessel->name }}</strong>.
        </div>
        <ul class="mt-4 list-disc pl-6 text-sm text-gray-700">
            <li>Start: @slDate($task->start_at)</li>
            <li>End: @slDate($task->end_at)</li>
            <li>Required Equipment: {{ implode(', ', $task->required_equipment ?? []) }}</li>
        </ul>
    @else
        <div class="mt-4 rounded border border-yellow-300 bg-yellow-50 text-yellow-800 px-4 py-3">
            No vessel available at requested time.
        </div>
        <h2 class="mt-6 font-semibold">Suggestions:</h2>
        <ul class="mt-2 list-disc pl-6 text-sm text-gray-700">
            @foreach($suggestions as $s)
                <li>Vessel: {{ $s['vessel_name'] }} — Available from: @slDate($s['available_from'])</li>
            @endforeach
        </ul>
    @endif

    <a href="{{ route('reservations.create') }}" class="mt-6 inline-flex items-center rounded bg-gray-700 px-4 py-2 text-white hover:bg-gray-800">Back to Form</a>
</x-layout>
