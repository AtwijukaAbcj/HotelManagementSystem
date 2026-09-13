<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Security & Access Center' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
<header class="border-b border-slate-200 bg-white">
    <div class="mx-auto flex max-w-7xl flex-wrap items-center gap-4 px-4 py-4 sm:px-6 lg:px-8">
        <a href="{{ url('/redirect') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:border-teal-400 hover:text-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500">← Back to Hotel Management</a>
        <div class="hidden text-sm text-slate-400 sm:block">/ Security & Access Center</div>
        <div class="ml-auto flex items-center gap-2">
            <label class="relative hidden md:block"><span class="sr-only">Search security center</span><input class="w-56 rounded-xl border-slate-200 bg-slate-50 py-2 pl-3 pr-3 text-sm focus:border-teal-500 focus:ring-teal-500" placeholder="Search security center..."></label>
            <a href="{{ route('notifications.index') }}" class="rounded-xl p-2 text-slate-500 transition hover:bg-slate-100 hover:text-teal-700" title="Notifications">Notifications</a>
            <div class="flex items-center gap-2 border-l border-slate-200 pl-3"><span class="flex h-9 w-9 items-center justify-center rounded-full bg-teal-100 font-bold text-teal-800">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span><div class="hidden sm:block"><div class="text-sm font-semibold">{{ auth()->user()->name }}</div><div class="text-xs text-slate-500">{{ ucfirst(auth()->user()->role) }}</div></div><form method="POST" action="{{ route('logout') }}">@csrf<button class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-red-600" title="Logout">Logout</button></form></div>
        </div>
    </div>
</header>
<main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">{{ $slot }}</main>
</body>
</html>
