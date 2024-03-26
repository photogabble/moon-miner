<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="description" content="A free online game - Open source, web game, with multiplayer space exploration">
    <meta name="keywords" content="Free, online, game, Open source, web game, multiplayer, space, exploration, blacknova, traders">
    <meta name="rating" content="General">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="shortcut icon" href="favicon.ico">
    <title>{{ $title ?? 'Moon Miner' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">

<div class="p-10 flex flex-col h-screen w-screen justify-center items-center">
    <main class="w-full max-h-screen font-mono text-ui-orange-500 relative overflow-hidden">

        <!-- TODO: pullouts -->

        <!-- TODO: Modal backdrop -->

        <div class="grid grid-cols-[350px_minmax(350px,_1fr)_64px] w-full h-full">
            <!-- Left Slot -->
            @if (isset($sidebar) && $sidebar->hasActualContent())
            <div class="flex flex-col h-full overflow-hidden">
                {{ $sidebar }}
            </div>
            @endif

            <!-- Middle area -->
            <div class="w-full flex flex-col {{ isset($sidebar) && $sidebar->hasActualContent() ? 'px-1' : 'col-span-2 pr-1' }}">
                <div class="text-sm flex flex-row border-t border-ui-orange-500/50 justify-between">
                    <div class="border-t border-ui-orange-500 border-l border-partway-r p-1 px-2">
                        <span class="uppercase text-white">Turns Available:</span> {{ $turnsAvailable ?? 'X.001' }}
                        <span class="text-ui-yellow">&middot;&nbsp;</span>
                        <span class="uppercase text-white">Turns Used:</span> {{ $turnsUsed ?? 'X.002' }}
                        <span class="text-ui-yellow">&middot;&nbsp;</span>
                        <span class="uppercase text-white">Credits</span> {{ $credits ?? 'X.003' }}
                    </div>

                    <div class="border-ui-orange-500 border-partway-t px-2 p-1">
                        <span class="uppercase text-white">Score</span> {{ $score ?? 'X.X' }}
                    </div>
                </div>

                {{ $slot }}
            </div>

            <x-navigation-column />
        </div>
    </main>
</div>
</body>
</html>
