<x-layout title="Reserve a Vessel">
    <h1 class="text-2xl font-semibold">Reserve a Vessel</h1>

    <form action="{{ route('reservations.store') }}" method="POST" class="mt-6 space-y-4 max-w-xl">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700">Title</label>
            <input type="text" name="title" class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" value="{{ old('title') }}" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Start At</label>
            <input type="datetime-local" name="start_at" class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" value="{{ old('start_at') }}" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">End At</label>
            <input type="datetime-local" name="end_at" class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" value="{{ old('end_at') }}" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Required Equipment</label>
            <select name="required_equipment[]" class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" multiple>
                @foreach($vessels as $vessel)
                    @foreach($vessel->equipment as $equip)
                        <option value="{{ $equip->code }}">{{ $equip->name }} ({{ $equip->code }})</option>
                    @endforeach
                @endforeach
            </select>
            <p class="mt-1 text-xs text-gray-500">Hold Ctrl/Cmd to select multiple.</p>
        </div>
        <button class="inline-flex items-center rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700" type="submit">Reserve</button>
    </form>
</x-layout>
