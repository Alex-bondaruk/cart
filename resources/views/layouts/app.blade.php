<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Store')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('styles')
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        @include('layouts.partials.header')
        
        <main class="container mx-auto p-4">
            @include('layouts.partials.alerts')
            @yield('content')
        </main>

        @include('layouts.partials.footer')
    </div>
    @stack('scripts')
</body>
</html>
