<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Working Paper') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
        <link href="https://fonts.bunny.net/css?family=dm-serif-display:400&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        <style>
            .brand-font {
                font-family: 'DM Serif Display', serif;
            }
            .panel-grid {
                background-image:
                    linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
                    linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
                background-size: 40px 40px;
            }
            .glow-dot {
                position: absolute;
                border-radius: 50%;
                filter: blur(80px);
                pointer-events: none;
            }
        </style>
    </head>
    <body class="h-full font-sans antialiased bg-gray-50">
        
        <div class="min-h-screen flex">
            
            <!-- Left Side Panel -->
            <div class="hidden lg:flex lg:w-[52%] relative bg-slate-900 flex-col overflow-hidden panel-grid">

                <!-- Ambient glows -->
                <div class="glow-dot w-96 h-96 bg-indigo-600/25 top-[-80px] left-[-80px]"></div>
                <div class="glow-dot w-80 h-80 bg-indigo-400/10 bottom-[10%] right-[-40px]"></div>

                <!-- Top bar -->
                <div class="relative z-10 p-10">
                    <div class="flex items-center gap-2.5">
                        <div class="w-12 h-12 rounded-lg bg-indigo-500 flex items-center justify-center">
                            <x-heroicon-o-document-chart-bar class="w-8 h-8 text-white" />
                        </div>
                        <span class="text-white font-bold text-xl tracking-tight">Working Paper</span>
                    </div>
                </div>

                <!-- Main copy -->
                <div class="relative z-10 flex-1 flex flex-col justify-center px-14 pb-16">
                    <p class="text-indigo-400 text-xs font-semibold uppercase tracking-widest mb-5">Data Capture System</p>
                    <h1 class="brand-font text-white text-5xl leading-tight mb-6">
                        Your tax data, organised<br>and ready.
                    </h1>
                    <p class="text-slate-400 text-base leading-relaxed max-w-sm">
                        Submit your financial working papers securely. Our reviewers process your data and keep you informed every step of the way.
                    </p>

                    <!-- Feature list -->
                    <div class="mt-12 space-y-4">
                        @foreach([
                            ['icon' => 'shield-check', 'text' => 'End-to-end secure file handling'],
                            ['icon' => 'bell-alert',   'text' => 'Real-time status notifications'],
                            ['icon' => 'clock',        'text' => 'Fast turnaround by our review team'],
                        ] as $feature)
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-slate-800 border border-slate-700 flex items-center justify-center text-indigo-400 flex-shrink-0">
                                @if($feature['icon'] === 'shield-check')
                                    <x-heroicon-o-shield-check class="w-4 h-4" />
                                @elseif($feature['icon'] === 'bell-alert')
                                    <x-heroicon-o-bell-alert class="w-4 h-4" />
                                @else
                                    <x-heroicon-o-clock class="w-4 h-4" />
                                @endif
                            </div>
                            <span class="text-slate-300 text-sm">{{ $feature['text'] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Bottom quote -->
                <div class="relative z-10 px-14 py-8 border-t border-slate-800">
                    <p class="text-slate-500 text-xs">© {{ date('Y') }} {{ config('app.name', 'Working Paper') }}. All rights reserved.</p>
                </div>
            </div>

            <!-- Right Side Panel -->
            <div class="flex-1 flex flex-col justify-center items-center px-6 py-12 bg-white">

                <!-- Mobile logo -->
                <div class="lg:hidden mb-10 flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-indigo-500 flex items-center justify-center">
                        <x-heroicon-o-document-chart-bar class="w-4 h-4 text-white" />
                    </div>
                    <span class="text-slate-900 font-bold text-lg tracking-tight">TaxPortal</span>
                </div>

                <div class="w-full max-w-md">
                    {{ $slot }}
                </div>
            </div>

        </div>

    </body>
</html>
