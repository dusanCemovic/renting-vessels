<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Renting Vessels' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-900">
<header class="mb-8 bg-white shadow">
    <div class="mx-auto max-w-6xl px-4 py-4 flex items-center justify-between">
        <a href="/" class="text-lg font-semibold">{{ $headerTitle ?? 'Renting Vessels' }}</a>
        <nav>
            <x-menu />
        </nav>
    </div>
</header>
<main class="mx-auto max-w-6xl px-4">
    {{ $slot }}
</main>
<footer class="mt-12 border-t bg-white">
    <div class="mx-auto max-w-6xl px-4 py-6 text-sm text-gray-500 flex items-center justify-between">
        <span>&copy; {{ date('Y') }} Renting Vessels</span>
        <span>Built with Laravel</span>
    </div>
</footer>
</body>
</html>
