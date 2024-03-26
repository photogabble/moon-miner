<?php
    /** @var \Illuminate\Database\Eloquent\Collection<\App\Models\User> $players */
?>
<section>
    <x-panel-header>
        <span class="text-white">Players Ranking</span>

        <x-slot:actions>
            {{ $players->links('components.pagination-prev-next') }}
        </x-slot:actions>
    </x-panel-header>

    <table class="w-full">
        <thead>
        <tr class="border-b">
            <th class="p-1 text-left">Rank</th>
            <th class="p-1 text-left text-white">
                <x-table.column-sort-link :href="route('ranking', ['sort_players_by' => 'score', 'sort_players_direction' => $sortingBy === 'score' ? $direction->opposite() : $direction])" :is-sorting="$sortingBy === 'score'" :direction="$direction" >
                    Score
                </x-table.column-sort-link>
            </th>
            <th class="p-1 text-left">Player</th>
            <th class="p-1 text-left text-white">
                <x-table.column-sort-link :href="route('ranking', ['sort_players_by' => 'turns', 'sort_players_direction' => $sortingBy === 'turns' ? $direction->opposite() : $direction])" :is-sorting="$sortingBy === 'turns'" :direction="$direction" >
                    Turns Used
                </x-table.column-sort-link>
            </th>
            <th class="p-1 text-left text-white">
                <x-table.column-sort-link :href="route('ranking', ['sort_players_by' => 'login', 'sort_players_direction' => $sortingBy === 'login' ? $direction->opposite() : $direction])" :is-sorting="$sortingBy === 'login'" :direction="$direction" >
                    Last Login
                </x-table.column-sort-link>
            </th>
            <th class="p-1 text-left text-white">
                <a href="{{ route('ranking', ['sort_players_by' => 'good']) }}" class="{{$sortingBy === 'good' ? 'text-ui-yellow' : 'hover:text-ui-yellow'}}">Good</a> /
                <a href="{{ route('ranking', ['sort_players_by' => 'evil']) }}" class="{{$sortingBy === 'evil' ? 'text-ui-yellow' : 'hover:text-ui-yellow'}}">Evil</a>
            </th>
            <th class="p-1 text-left text-white">
                <x-table.column-sort-link :href="route('ranking', ['sort_players_by' => 'efficiency', 'sort_players_direction' => $sortingBy === 'efficiency' ? $direction->opposite() : $direction])" :is-sorting="$sortingBy === 'efficiency'" :direction="$direction" >
                    Eff. Rating
                </x-table.column-sort-link>
            </th>
        </tr>
        </thead>
        <tbody>
            @foreach($players as $player)
               <tr>
                   <td class="p-1">{{ $player->rank }}</td>
                   <td class="p-1">{{ $player->score }}</td>
                   <td class="p-1 flex items-center gap-2 {{ ($player->type === \App\Types\UserType::Admin) ? 'text-blue-500' : (($player->type === \App\Types\UserType::NPC) ? 'text-green-600' : '') }}">
                       {{ $player->type !== \App\Types\UserType::NPC ? $player->insignia() : '' }} {{ $player->name }} <x-user-ban-status :user="$player" />
                   </td>
                   <td class="p-1">{{ $player->turns_used }}</td>
                   <td class="p-1">{{ $player->last_login ?? 'never' }}</td>
                   <td class="p-1 {{ $player->rating < 0 ? 'text-red-600' : 'text-green-600' }}">{{ $player->rating }}</td>
                   <td class="p-1">{{ $player->efficiency }}</td>
               </tr>
            @endforeach
        </tbody>
    </table>
    <p>Total number of players: {{ $players->count() }}. Players with destroyed ships are not counted.</p>
</section>
