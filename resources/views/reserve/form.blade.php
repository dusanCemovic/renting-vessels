<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reserve a Vessel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">

    <x-menu />

    <h1>Reserve a Vessel</h1>

    <form action="{{ route('reserve.submit') }}" method="POST" class="mt-4">
        @csrf
        <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Start At</label>
            <input type="datetime-local" name="start_at" class="form-control" value="{{ old('start_at') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">End At</label>
            <input type="datetime-local" name="end_at" class="form-control" value="{{ old('end_at') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Required Equipment</label>
            <select name="required_equipment[]" class="form-select" multiple>
                @foreach($vessels as $vessel)
                    @foreach($vessel->equipment as $equip)
                        <option value="{{ $equip->code }}">{{ $equip->name }} ({{ $equip->code }})</option>
                    @endforeach
                @endforeach
            </select>
        </div>
        <button class="btn btn-primary" type="submit">Reserve</button>
    </form>
</div>
</body>
</html>
