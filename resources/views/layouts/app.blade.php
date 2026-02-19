<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Working Paper') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="h-full font-sans antialiased text-gray-600">
        <div x-data="{ sidebarOpen: false }" class="min-h-screen flex flex-col md:flex-row">
            
            <div class="md:hidden flex items-center justify-between bg-slate-900 text-white p-4">
                <div class="font-bold text-lg tracking-tight">Working Paper</div>
                <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-md hover:bg-slate-800">
                    <x-heroicon-o-bars-3 class="w-6 h-6" />
                </button>
            </div>

            <aside 
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
                class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 text-white transition-transform duration-300 ease-in-out md:relative md:translate-x-0 flex flex-col shadow-xl"
            >
                <div class="p-6 border-b border-slate-800 hidden md:block">
                    <h1 class="text-2xl font-bold tracking-tight text-white">Working Paper</h1>
                    <p class="text-xs text-slate-400 mt-1">Data Capture System</p>
                </div>

                @include('layouts.navigation')
                
                <div class="mt-auto p-4 border-t border-slate-800 bg-slate-950">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-indigo-500 flex items-center justify-center text-white font-bold">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <div class="overflow-hidden">
                            <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-slate-400 truncate">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                </div>
            </aside>

            <main class="flex-1 flex flex-col min-w-0 overflow-hidden bg-gray-50">
                @isset($header)
                <header class="bg-white border-b border-gray-200 px-8 py-5 hidden md:flex items-center justify-between">
                    <div class="flex-1">
                        {{ $header }}
                    </div>
                </header>
                @endisset

                <div class="flex-1 overflow-y-auto p-4 md:p-8">
                    {{ $slot }}
                </div>
            </main>
            
            <div 
                x-show="sidebarOpen" 
                @click="sidebarOpen = false"
                class="fixed inset-0 bg-black/50 z-40 md:hidden backdrop-blur-sm"
                x-transition.opacity
            ></div>
        </div>
    </body>
</html>