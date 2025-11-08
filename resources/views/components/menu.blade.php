<ul class="flex items-center gap-4">
    <li><a class="text-blue-600 hover:underline" href="{{ route('vessels.index') }}">Vessels</a></li>
    <li><a class="text-blue-600 hover:underline" href="{{ route('equipments.index') }}">Equipment</a></li>
    <li><a class="text-blue-600 hover:underline" href="{{ route('reservations.index') }}">Reservations</a></li>
    <li><a class="text-blue-600 hover:underline" href="{{ route('maintenances.index') }}">Maintenance</a></li>
    <li><a class="inline-flex items-center rounded bg-blue-600 px-3 py-1.5 text-white hover:bg-blue-700" href="{{ route('reservations.create') }}">New reservation</a></li>
</ul>
