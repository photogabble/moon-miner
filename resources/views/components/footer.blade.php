<div class="footer">
    @if($news)
        <news-ticker interval="5" items="{{json_encode($news)}}"></news-ticker>
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
            <update-ticker
                remainder="{{ $update_ticker['seconds_left'] }}"
                max="{{ $update_ticker['sched_ticks'] * 60 }}"
                l-running-update="{{ __('footer.l_running_update') }}"
                l-until-update="{{ __('footer.l_footer_until_update') }}"
            >{{ __('footer.l_please_wait') }}</update-ticker>
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

</div>
