<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Vessels</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">

    <x-menu />

    <h1>All Vessels</h1>

    @if($vessels->isEmpty())
        <div class="alert alert-warning">No vessels found.</div>
    @else
        <table class="table table-bordered table-striped mt-3">
            <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Type</th>
                <th>Size</th>
                <th>Equipment</th>
            </tr>
            </thead>
            <tbody>
            @foreach($vessels as $index => $vessel)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><a href="{{url('/vessels/' . $vessel->id . '/tasks') }}">{{ $vessel->name }}</a></td>
                    <td>{{ $vessel->type }}</td>
                    <td>{{ $vessel->size }}</td>
                    <td>
                        @if($vessel->equipment->isEmpty())
                            None
                        @else
                            {{ $vessel->equipment->pluck('name')->join(', ') }}
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
</div>
</body>
</html>
