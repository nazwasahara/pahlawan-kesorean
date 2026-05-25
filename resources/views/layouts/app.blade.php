<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Pahlawan Kesorean'))</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <style>
        .pattern-green {
            width: 100%;
            height: 40px;
            background-color: #fff;
            background-image:
                linear-gradient(45deg, #0b6b0b 25%, transparent 25%),
                linear-gradient(-45deg, #0b6b0b 25%, transparent 25%),
                linear-gradient(45deg, transparent 75%, #0b6b0b 75%),
                linear-gradient(-45deg, transparent 75%, #0b6b0b 75%);
            background-size: 40px 40px;
            background-position:
                0 0,
                0 20px,
                20px -20px,
                -20px 0;
        }

        @media (max-width: 768px) {
            .pattern-green {
                height: 32px;
            }
        }

        /* Custom scrollbar for sidebar */
        aside::-webkit-scrollbar {
            width: 5px;
        }
        aside::-webkit-scrollbar-track {
            background: transparent;
        }
        aside::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
        }
        aside::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.4);
        }
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.9.1/fonts/remixicon.min.css">
</head>
<body class="bg-gray-50 text-gray-900 antialiased">

    @yield('content')

    @stack('scripts')
</body>
</html>
