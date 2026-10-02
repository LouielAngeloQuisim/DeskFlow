<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="min-h-full bg-slate-50">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
    <div class="min-h-screen lg:grid lg:grid-cols-[250px_minmax(0,1fr)]">
        <aside class="hidden border-r border-slate-200 bg-white lg:flex lg:flex-col">
            <div class="flex h-20 items-center px-7">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3" wire:navigate>
                    <span class="flex size-10 items-center justify-center rounded-2xl bg-indigo-600 text-lg font-black text-white">D</span>
                    <span class="text-lg font-bold tracking-tight">DeskFlow</span>
                </a>
            </div>
            <nav class="flex-1 space-y-1 px-4 py-5">
                <flux:sidebar.item icon="squares-2x2" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>Overview</flux:sidebar.item>
                @if(auth()->user()->isStaff())
                    <flux:sidebar.item icon="inbox-stack" :href="route('agent.queue')" :current="request()->routeIs('agent.*')" wire:navigate>Support queue</flux:sidebar.item>
                @else
                    <flux:sidebar.item icon="ticket" :href="route('tickets.index')" :current="request()->routeIs('tickets.index')" wire:navigate>My requests</flux:sidebar.item>
                    <a href="{{ route('tickets.create') }}" class="mt-5 flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700" wire:navigate><span>＋</span> New support request</a>
                @endif
                <flux:sidebar.item icon="cog-6-tooth" :href="route('profile.edit')" :current="request()->routeIs('profile.*')" wire:navigate>Settings</flux:sidebar.item>
            </nav>
            <div class="border-t border-slate-100 p-4">
                <div class="flex items-center gap-3 rounded-xl bg-slate-50 p-3">
                    <span class="flex size-9 items-center justify-center rounded-full bg-indigo-100 text-xs font-bold text-indigo-700">{{ auth()->user()->initials() }}</span>
                    <div class="min-w-0 flex-1"><p class="truncate text-sm font-semibold">{{ auth()->user()->name }}</p><p class="truncate text-xs text-slate-500">{{ auth()->user()->isStaff() ? 'Support team' : 'Customer' }}</p></div>
                    <form method="POST" action="{{ route('logout') }}">@csrf<button class="rounded-lg p-2 text-slate-500 hover:bg-white hover:text-slate-900" title="Log out" aria-label="Log out">↗</button></form>
                </div>
            </div>
        </aside>
        <div class="min-w-0">
            <header class="sticky top-0 z-20 flex h-16 items-center justify-between border-b border-slate-200 bg-white/90 px-4 backdrop-blur sm:px-8">
                <div class="flex items-center gap-3 lg:hidden"><span class="flex size-9 items-center justify-center rounded-xl bg-indigo-600 font-black text-white">D</span><span class="font-bold">DeskFlow</span></div>
                <p class="hidden text-sm text-slate-500 lg:block">Support that keeps work moving.</p>
                <div class="flex items-center gap-3"><span class="hidden rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 sm:inline-flex">● All systems operational</span><span class="text-sm font-semibold">{{ auth()->user()->name }}</span></div>
            </header>
            <nav class="grid grid-cols-3 gap-1 border-b border-slate-200 bg-white px-3 py-2 lg:hidden" aria-label="Primary navigation">
                <a href="{{ route('dashboard') }}" class="rounded-lg px-2 py-2 text-center text-xs font-semibold {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600' }}" wire:navigate>Overview</a>
                @if(auth()->user()->isStaff())
                    <a href="{{ route('agent.queue') }}" class="rounded-lg px-2 py-2 text-center text-xs font-semibold {{ request()->routeIs('agent.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600' }}" wire:navigate>Queue</a>
                    <a href="{{ route('profile.edit') }}" class="rounded-lg px-2 py-2 text-center text-xs font-semibold text-slate-600" wire:navigate>Settings</a>
                @else
                    <a href="{{ route('tickets.index') }}" class="rounded-lg px-2 py-2 text-center text-xs font-semibold {{ request()->routeIs('tickets.index') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600' }}" wire:navigate>My requests</a>
                    <a href="{{ route('tickets.create') }}" class="rounded-lg px-2 py-2 text-center text-xs font-semibold text-indigo-700" wire:navigate>＋ New request</a>
                @endif
            </nav>
            <main class="mx-auto w-full max-w-7xl px-4 py-8 sm:px-8 lg:py-10">
                @if(session('success'))<div role="status" class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">{{ session('success') }}</div>@endif
                {{ $slot }}
            </main>
        </div>
    </div>
    @fluxScripts
</body>
</html>