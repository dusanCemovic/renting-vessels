<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reservation Result</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">

    <x-menu />

    <h1>Reservation Result</h1>

    @if($success)
        <div class="alert alert-success">
            Task "<strong>{{ $task->title }}</strong>" successfully reserved for vessel "<strong>{{ $vessel->name }}</strong>".
        </div>
        <ul>
            <li>Start: {{ \Carbon\Carbon::parse($task->start_at)->format('Y-m-d H:i') }}</li>
            <li>End: {{ \Carbon\Carbon::parse($task->end_at)->format('Y-m-d H:i') }}</li>
            <li>Required Equipment: {{ implode(', ', $task->required_equipment ?? []) }}</li>
        </ul>
    @else
        <div class="alert alert-warning">
            No vessel available at requested time.
        </div>
        <h5>Suggestions:</h5>
        <ul>
            @foreach($suggestions as $s)
                <li>Vessel: {{ $s['vessel_name'] }} — Available from: {{ $s['available_from'] }}</li>
            @endforeach
        </ul>
    @endif

    <a href="{{ route('reserve.form') }}" class="btn btn-secondary mt-3">Back to Form</a>
</div>
</body>
</html>
