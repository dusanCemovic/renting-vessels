<x-layout title="Reservations">
    <h1 class="text-2xl font-semibold">Reservations</h1>
    <x-tasks.table :tasks="$tasks" :filters="$filters" />
</x-layout>
