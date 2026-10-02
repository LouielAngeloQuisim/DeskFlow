<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.auth-head')
    </head>
    <body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
        <div class="flex min-h-svh flex-col items-center justify-center gap-6 bg-[radial-gradient(ellipse_at_top,_rgba(99,102,241,0.1),_transparent_55%)] p-5 sm:p-8">
            <div class="w-full max-w-md">
                <a href="{{ route('home') }}" class="mx-auto mb-6 flex w-fit items-center gap-3" wire:navigate>
                    <span aria-hidden="true" class="flex size-10 items-center justify-center rounded-2xl bg-indigo-600 text-lg font-black text-white shadow-sm">D</span>
                    <span class="text-lg font-bold tracking-tight text-slate-900">DeskFlow</span>
                </a>
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xl shadow-slate-200/60 sm:p-8">
                    {{ $slot }}
                </div>
                <p class="mt-6 text-center text-xs text-slate-400">Support, in motion.</p>
            </div>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>