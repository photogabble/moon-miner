<?php
/** @var \Illuminate\Database\Eloquent\Collection<\App\Models\Team> $teams */
?>
<section>
    <x-panel-header>
        <span class="text-white">Team Ranking</span>

        <x-slot:actions>
            {{ $teams->links() }}
        </x-slot:actions>
    </x-panel-header>

    <table class="w-full">
        <thead>
        <tr class="border-b">
            <th class="p-1 text-left">Rank</th>
            <th class="p-1 text-left text-white">
                <x-table.column-sort-link :href="route('ranking', ['sort_teams_by' => 'score', 'sort_teams_direction' => $sortingBy === 'score' ? $direction->opposite() : $direction])" :is-sorting="$sortingBy === 'score'" :direction="$direction" >
                    Score
                </x-table.column-sort-link>
            </th>
            <th class="p-1 text-left">Team Name</th>
            <th class="p-1 text-left text-white">
                <x-table.column-sort-link :href="route('ranking', ['sort_teams_by' => 'members', 'sort_teams_direction' => $sortingBy === 'members' ? $direction->opposite() : $direction])" :is-sorting="$sortingBy === 'members'" :direction="$direction" >
                    # Players
                </x-table.column-sort-link>
            </th>
            <th class="p-1 text-left text-white">
                <a href="{{ route('ranking', ['sort_teams_by' => 'good']) }}" class="{{$sortingBy === 'good' ? 'text-ui-yellow' : 'hover:text-ui-yellow'}}">Good</a> /
                <a href="{{ route('ranking', ['sort_teams_by' => 'evil']) }}" class="{{$sortingBy === 'evil' ? 'text-ui-yellow' : 'hover:text-ui-yellow'}}">Evil</a>
            </th>
            <th class="p-1 text-left text-white">
                <x-table.column-sort-link :href="route('ranking', ['sort_teams_by' => 'efficiency', 'sort_teams_direction' => $sortingBy === 'efficiency' ? $direction->opposite() : $direction])" :is-sorting="$sortingBy === 'efficiency'" :direction="$direction" >
                    Eff. Rating
                </x-table.column-sort-link>
            </th>
        </tr>
        </thead>
        <tbody>
        @foreach($teams as $team)
            <tr>
                <td class="p-1">{{ $team->rank }}</td>
                <td class="p-1">{{ $team->score }}</td>
                <td class="p-1">{{ $team->name }}</td>
                <td class="p-1">{{ $team->turns_used }}</td>
                <td class="p-1">{{ $team->last_login ?? 'never' }}</td>
                <td class="p-1 {{ $team->rating < 0 ? 'text-red-600' : 'text-green-600' }}">{{ $player->rating }}</td>
                <td class="p-1">{{ $team->efficiency }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</section>
