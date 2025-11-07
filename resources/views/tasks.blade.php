<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $vessel->name }} - Tasks</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light p-4">
<div class="container">

    <x-menu />

    <h1 class="mb-4">Tasks for Vessel: <strong>{{ $vessel->name }}</strong></h1>

    @if($vessel->tasks->isEmpty())
        <div class="alert alert-warning">No tasks found for this vessel.</div>
    @else
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Task Name</th>
                <th>Description</th>
                <th>Start At</th>
                <th>End At</th>
            </tr>
            </thead>
            <tbody>
            @foreach($vessel->tasks as $index => $task)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $task->title }}</td>
                    <td>{{ $task->description ?? '—' }}</td>
                    <td>{{ \Carbon\Carbon::parse($task->start_at)->format('Y-m-d H:i') }}</td>
                    <td>{{ \Carbon\Carbon::parse($task->end_at)->format('Y-m-d H:i') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
</div>
</body>
</html>
