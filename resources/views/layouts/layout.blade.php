{{--
    Blacknova Traders - A web-based massively multiplayer space combat and trading game
    Copyright (C) 2001-2024 Ron Harwood and the BNT development team.

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as
    published by the Free Software Foundation, either version 3 of the
    License, or (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

    File: layout.blade.php
--}}
    <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<!-- START OF HEADER -->
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="Description" content="A free online game - Open source, web game, with multiplayer space exploration">
    <meta name="Keywords" content="Free, online, game, Open source, web game, multiplayer, space, exploration, blacknova, traders">
    <meta name="Rating" content="General">
    <link rel="shortcut icon" href="favicon.ico">

    <!-- Styles -->
    @vite("resources/css/app.css")
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Ubuntu">

    <title>@yield('title')</title>
    @if($include_ckeditor)
        <script src="/javascript/ckeditor/ckeditor.js"></script>
    @endif
    <script async src="/javascript/framebuster.js.php"></script>
</head>
<!-- END OF HEADER -->

<body class="{{ $body_class ?? 'bnt' }}">
<div class="wrapper">

    <!-- START OF BODY -->
    @yield('content')
    <!-- END OF BODY -->

    <!-- START OF FOOTER -->
    <div class="footer">
        @if($news)
            <br>
            <script type="text/javascript" src="/javascript/newsticker.js.php"></script>
            <p id="news_ticker" class="faderlines" style="width:602px; margin:auto; text-align:center;">{{ __('news.l_news_broken') }}</p>
            <script>
                // News Ticker Constructor.
                news = new newsTicker();

                // I have put in some safaty precautions, but just in case always check the return value from initTicker().
                if (news.initTicker("news_ticker") == true) {
                    // Set the width of the Ticker (in pixles)
                    news.Width(500);

                    // Sets the Interval/Update Time in seconds.
                    news.Interval(5);

                    // I have decided on adding single news articles at a time due to it makes it more easier to add when using PHP or XSL.
                    // We can supply the information by either of the following ways:
                    // 1: Supply the information from a Database and inserting it with PHP.
                    // 2: Supply the information from a Database and convert it into XML (for formatting) and have the XSLT Stylesheet extract the information and insert it.

                    {{-- Cycle through the player list --}}
                    @foreach($news as $article)
                    news.addArticle('{{ $article['url'] }}', '{{ $article['text'] }}', '{{ $article['type'] }}', '{{ $article['delay'] }}');
                    @endforeach

                    // Starts the Ticker.
                    news.startTicker();

                    // If for some reason you need to stop the Ticker use the following line.
                    // news.stopTicker();
                }
            </script>

        @endif
        <br>

        {{-- Items to the left (SF logo) and to the right (mem, copyright, news) --}}
        @if(!$suppress_logo)
            <p style='float:left; text-align:left'><a href='http://www.sourceforge.net/projects/blacknova'><img style="border:none;" width="{$variables['sf_logo_width']}" height="{$variables['sf_logo_height']}" src="http://sflogo.sourceforge.net/sflogo.php?group_id=14248&amp;type={$variables['sf_logo_type']}" alt="Blacknova Traders at SourceForge.net"></a></p>
        @endif
        <p style="font-size:smaller; float:right; text-align:right"><a class="new_link" href="news.php">{{ __('global_includes.l_local_news') }}</a>
            <br>&copy; 2000-{{ date('Y') }} Ron Harwood &amp; the BNT Dev team

            @if($footer_show_debug)
                <br>{{ render_time_seconds() }} {{ __('footer.l_seconds') }} {{ __('footer.l_time_gen_page') }} / {{ mem_peak_usage() }}{{ __('footer.l_peak_mem') }}
            @endif
        </p>

        <p style="text-align:center;">
            {{-- Handle the Servers Update Ticker here  --}}
            @if($update_ticker && $update_ticker['display'] === true)
                <script type='text/javascript' src='/javascript/updateticker.js'></script>
                <script>
                    const seconds = {{ $update_ticker['seconds_left'] }};
                    const nextInterval = new Date().getTime();
                    const maxTicks = ({{ $update_ticker['sched_ticks'] }} * 60);
                    const l_running_update = '{{ __('footer.l_running_update') }}';
                    const l_footer_until_update = '{{ __('footer.l_footer_until_update') }}';

                    setTimeout("NextUpdate();", 100);
                </script>
                <span id=update_ticker>{{ __('footer.l_please_wait') }}</span>
            @endif

            {{-- End of Servers Update Ticker --}}

            <br>
            {{-- Handle the Online Players Counter --}}
            @if($players_online === 1)
                {{ __('footer.l_footer_one_player_on') }}
            @else
                {{ __('footer.l_footer_players_on_1') }} {{ $players_online }} {{ __('footer.l_footer_players_on_2') }}
            @endif
        </p>
        {{-- End of Online Players Counter --}}

    </div></div>
<!-- END OF FOOTER -->

</body>
</html>
