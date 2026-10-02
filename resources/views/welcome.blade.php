<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="A calmer way to get help. Submit a request, follow progress, and keep every support conversation in one place.">
    <title>DeskFlow — Support, in motion</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
</head>
<body class="min-h-screen bg-white text-slate-900 antialiased">
    <header class="mx-auto flex max-w-7xl items-center justify-between px-6 py-6 lg:px-8">
        <a href="{{ route('home') }}" class="flex items-center gap-3"><span aria-hidden="true" class="flex size-10 items-center justify-center rounded-2xl bg-indigo-600 text-lg font-black text-white">D</span><span class="text-lg font-bold tracking-tight">DeskFlow</span></a>
        <nav class="flex items-center gap-3"><a href="{{ route('login') }}" class="rounded-xl px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100">Log in</a><a href="{{ route('register') }}" class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">Get support <span aria-hidden="true">→</span></a></nav>
    </header>
    <main>
        <section class="relative isolate overflow-hidden">
            <div class="pointer-events-none absolute inset-0 -z-10 bg-[radial-gradient(ellipse_at_top_right,_rgba(99,102,241,0.12),_transparent_55%)]"></div>
            <div class="mx-auto grid max-w-7xl gap-16 px-6 pb-20 pt-16 lg:grid-cols-[1.05fr_0.95fr] lg:items-center lg:px-8 lg:pb-28 lg:pt-24">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-full border border-indigo-100 bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700"><span class="size-1.5 rounded-full bg-indigo-600"></span> THE HELP DESK THAT FLOWS</div>
                    <h1 class="mt-7 max-w-2xl text-5xl font-semibold tracking-tight text-slate-950 sm:text-6xl lg:text-7xl">Good support starts with <span class="text-indigo-600">a clear next step.</span></h1>
                    <p class="mt-6 max-w-xl text-lg leading-8 text-slate-600">Send your request, keep the conversation together, and see what’s happening at every step. No chasing, no guessing.</p>
                    <div class="mt-9 flex flex-wrap gap-3"><a href="{{ route('register') }}" class="rounded-xl bg-indigo-600 px-5 py-3.5 text-sm font-semibold text-white shadow-lg shadow-indigo-200 transition hover:-translate-y-0.5 hover:bg-indigo-700">Create a support request <span class="ml-2">→</span></a><a href="{{ route('login') }}" class="rounded-xl border border-slate-200 bg-white px-5 py-3.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">I have an account</a></div>
                    <div class="mt-10 flex items-center gap-3 text-sm text-slate-500"><span class="flex -space-x-2"><span class="flex size-8 items-center justify-center rounded-full border-2 border-white bg-indigo-100 text-xs font-bold text-indigo-700">S</span><span class="flex size-8 items-center justify-center rounded-full border-2 border-white bg-cyan-100 text-xs font-bold text-cyan-700">A</span><span class="flex size-8 items-center justify-center rounded-full border-2 border-white bg-amber-100 text-xs font-bold text-amber-700">✓</span></span><span>A real person is on the other side of every request.</span></div>
                </div>
                <div class="relative mx-auto w-full max-w-xl">
                    <div class="absolute -inset-5 rounded-[2rem] bg-indigo-100/70 blur-2xl"></div>
                    <div class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl shadow-indigo-100/60">
                        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-5"><div><p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">REQUEST OVERVIEW</p><p class="mt-1 text-lg font-semibold">Your support, at a glance</p></div><span class="rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700">● Live updates</span></div>
                        <div class="grid grid-cols-3 gap-3 p-5"><div class="rounded-2xl bg-indigo-50 p-4"><p class="text-xs font-medium text-indigo-600">Open</p><p class="mt-2 text-3xl font-semibold text-indigo-950">02</p></div><div class="rounded-2xl bg-amber-50 p-4"><p class="text-xs font-medium text-amber-700">In progress</p><p class="mt-2 text-3xl font-semibold text-amber-950">01</p></div><div class="rounded-2xl bg-emerald-50 p-4"><p class="text-xs font-medium text-emerald-700">Resolved</p><p class="mt-2 text-3xl font-semibold text-emerald-950">08</p></div></div>
                        <div class="space-y-3 px-5 pb-5"><div class="flex items-center gap-4 rounded-2xl border border-slate-100 p-4"><span class="flex size-10 items-center justify-center rounded-xl bg-violet-50 text-violet-600">⌘</span><div class="min-w-0 flex-1"><p class="truncate text-sm font-semibold">Can't access my workspace</p><p class="mt-1 text-xs text-slate-500">DF-8K4M2P1Q · Account access</p></div><span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">In progress</span></div><div class="flex items-center gap-4 rounded-2xl border border-slate-100 p-4"><span class="flex size-10 items-center justify-center rounded-xl bg-cyan-50 text-cyan-700">↗</span><div class="min-w-0 flex-1"><p class="truncate text-sm font-semibold">Invoice question for October</p><p class="mt-1 text-xs text-slate-500">DF-2J7R9B3N · Billing</p></div><span class="rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700">Open</span></div></div>
                        <div class="border-t border-slate-100 bg-slate-50/80 px-6 py-4 text-center text-xs text-slate-500">A clean demo preview — your requests stay private to your account.</div>
                    </div>
                </div>
            </div>
        </section>
        <section class="border-y border-slate-100 bg-slate-50/70"><div class="mx-auto grid max-w-7xl gap-8 px-6 py-12 sm:grid-cols-3 lg:px-8"><div><p class="text-sm font-semibold text-slate-900">01 <span class="ml-2 text-indigo-600">Tell us what’s up</span></p><p class="mt-2 text-sm leading-6 text-slate-500">Share the details once. Your request gets a reference you can always find.</p></div><div><p class="text-sm font-semibold text-slate-900">02 <span class="ml-2 text-indigo-600">Follow the progress</span></p><p class="mt-2 text-sm leading-6 text-slate-500">See status changes and replies in one organized conversation.</p></div><div><p class="text-sm font-semibold text-slate-900">03 <span class="ml-2 text-indigo-600">Get back to work</span></p><p class="mt-2 text-sm leading-6 text-slate-500">Your support team has the context they need to move quickly.</p></div></div></section>
    </main>
    <footer class="mx-auto flex max-w-7xl flex-col gap-3 px-6 py-8 text-sm text-slate-500 sm:flex-row sm:items-center sm:justify-between lg:px-8"><p>© {{ date('Y') }} DeskFlow · Support, in motion.</p><p>Built with Laravel, Livewire & PostgreSQL.</p></footer>
    @fluxScripts
</body>
</html>