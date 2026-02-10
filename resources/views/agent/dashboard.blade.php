<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agent Dashboard - Evalia</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <nav class="bg-white shadow-sm p-4">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold text-gray-800">Agent Dashboard</h1>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-red-600 font-medium">Logout</button>
            </form>
        </div>
    </nav>
    <main class="flex-grow flex items-center justify-center">
        <div class="text-center">
            <h2 class="text-3xl font-semibold text-gray-700">agent dashoaord</h2>
        </div>
    </main>
</body>
</html>
