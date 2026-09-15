<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'AI Email Agent')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f5f4f0; }
        .font-serif-brand { font-family: 'Fraunces', serif; }
    </style>
</head>
<body class="text-slate-900 antialiased">
    <nav class="mb-8 border-b border-slate-800 bg-[#1B2430] text-white shadow-lg">
        <div class="mx-auto flex h-20 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <div class="flex items-center">
                    <a href="{{ route('dashboard') }}" class="font-serif-brand text-xl tracking-tight">Inbox Agent</a>
                </div>
                <div class="flex items-center ">
                    <span class="text-sm text-slate-300 mr-8">{{ Auth::user()->name ?? 'Guest' }}</span>
                </div>
        </div>
    </nav>

    <div class="mx-auto max-w-7xl px-4 pb-12 sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        @yield('content')
    </div>
</body>
</html>
