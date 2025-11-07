<x-layout title="Reservations & Maintenance">
    <h1 class="text-2xl font-semibold">Reservations & Maintenance</h1>
    <x-tasks.table :tasks="$tasks" :filters="$filters" :showTypeFilter="$showTypeFilter ?? false" />
</x-layout>
