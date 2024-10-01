<script setup lang="ts">
/**
 * Moon Miner, a Free & Opensource (FOSS), web-based 4X space/strategy game forked
 * and based upon Black Nova Traders.
 *
 * @copyright 2024 Simon Dann
 * @copyright 2001-2014 Ron Harwood and the BNT development team
 *
 * @license GNU AGPL version 3.0 or (at your option) any later version.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

import PanelHeader from "@/Components/Atoms/PanelHeader.vue";
import type {Ranking, TeamRankingParams, TeamRankingResource} from "@/types/resources/ranking";
import type {PaginatedResource} from "@/types/laravel/pagination";
import PaginationPrevNext from "@/Components/Atoms/Links/PaginationPrevNext.vue";
import ColumnSortLink from "@/Components/Atoms/Links/ColumnSortLink.vue";
import {Link as InertiaLink} from "@inertiajs/vue3";

const props = defineProps<{
    teams: Ranking<PaginatedResource<TeamRankingResource>>,
}>();

const linkParams = (s: string) => {
    const ret : TeamRankingParams = {sort_teams_by: s};
    if (props.teams.sorting_by === s) {
        ret.sort_teams_direction = props.teams.sorting_direction === 'ASC'
            ? 'DESC'
            : 'ASC'
    }

    return ret;
}
</script>

<template>
    <section>
        <panel-header>
            <span class="text-white">Teams Ranking</span>

            <template #actions>
                <pagination-prev-next :pagination="teams.ranking" />
            </template>
        </panel-header>

        <table class="w-full">
            <thead>
            <tr class="border-b">
                <th class="p-1 text-left">Rank</th>
                <th class="p-1 text-left text-white">
                    <column-sort-link :href="route('ranking', linkParams('score'))" :is-sorting="teams.sorting_by === 'score'" :direction="teams.sorting_direction">
                        Score
                    </column-sort-link>
                </th>
                <th class="p-1 text-left">Team Name</th>
                <th class="p-1 text-left text-white">
                    <column-sort-link :href="route('ranking', linkParams('members'))" :is-sorting="teams.sorting_by === 'members'" :direction="teams.sorting_direction">
                        # Players
                    </column-sort-link>
                </th>
                <th class="p-1 text-left text-white">
                    <inertia-link :href="route('ranking', {sort_teams_by: 'good'})" :class="`${teams.sorting_by === 'good' ? 'text-ui-yellow' : 'hover:text-ui-yellow' }`">Good</inertia-link>/<inertia-link :href="route('ranking', {sort_teams_by: 'bad'})" :class="`${teams.sorting_by === 'bad' ? 'text-ui-yellow' : 'hover:text-ui-yellow' }`">Evil</inertia-link>
                </th>
                <th class="p-1 text-left text-white">
                    <column-sort-link :href="route('ranking', linkParams('efficiency'))" :is-sorting="teams.sorting_by === 'efficiency'" :direction="teams.sorting_direction">
                        Eff. Rating
                    </column-sort-link>
                </th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(team, idx) in teams.ranking.data">
                <td class="p-1">{{ (teams.ranking.meta.per_page * (teams.ranking.meta.current_page-1)) + (idx+1) }}</td>
                <td class="p-1">{{ team.score }}</td>
                <td class="p-1">{{ team.name }}</td>
                <td class="p-1">{{ team.player_count }}</td>
                <td :class="['p-1', {'text-red-600': team.rating < 0, 'text-green-600': team.rating > 0}]">{{ team.rating }}</td>
                <td class="p-1">{{ team.efficiency }}</td>
            </tr>
            </tbody>
        </table>
    </section>
</template>
