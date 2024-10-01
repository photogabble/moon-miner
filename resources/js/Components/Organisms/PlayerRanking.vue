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
import type {PlayerRankingResource, Ranking, PlayerRankingParams} from "@/types/resources/ranking.d.ts";
import type {PaginatedResource} from "@/types/laravel/pagination";
import PaginationPrevNext from "@/Components/Atoms/Links/PaginationPrevNext.vue";
import ColumnSortLink from "@/Components/Atoms/Links/ColumnSortLink.vue";
import {Link as InertiaLink} from "@inertiajs/vue3";

const props = defineProps<{
    player: Ranking<PaginatedResource<PlayerRankingResource>>,
}>();

const linkParams = (s: string) => {
    const ret : PlayerRankingParams = {sort_players_by: s};
    if (props.player.sorting_by === s) {
        ret.sort_players_direction = props.player.sorting_direction === 'ASC'
            ? 'DESC'
            : 'ASC'
    }

    return ret;
}
</script>

<template>
    <section>
        <panel-header>
            <span class="text-white">Players Ranking</span>
            <template #actions>
                <pagination-prev-next :pagination="player.ranking" />
            </template>
        </panel-header>

        <table class="w-full">
            <thead>
            <tr class="border-b">
                <th class="p-1 text-left">Rank</th>
                <th class="p-1 text-left text-white">
                    <column-sort-link :href="route('ranking', linkParams('score'))" :is-sorting="player.sorting_by === 'score'" :direction="player.sorting_direction">
                        Score
                    </column-sort-link>
                </th>
                <th class="p-1 text-left">Player</th>
                <th class="p-1 text-left text-white">
                    <column-sort-link :href="route('ranking', linkParams('turns'))" :is-sorting="player.sorting_by === 'turns'" :direction="player.sorting_direction">
                        Turns Used
                    </column-sort-link>
                </th>
                <th class="p-1 text-left text-white">
                    <column-sort-link :href="route('ranking', linkParams('login'))" :is-sorting="player.sorting_by === 'login'" :direction="player.sorting_direction">
                        Last Active
                    </column-sort-link>
                </th>
                <th class="p-1 text-left text-white">
                    <inertia-link :href="route('ranking', {sort_players_by: 'good'})" :class="`${player.sorting_by === 'good' ? 'text-ui-yellow' : 'hover:text-ui-yellow' }`">Good</inertia-link>/<inertia-link :href="route('ranking', {sort_players_by: 'bad'})" :class="`${player.sorting_by === 'bad' ? 'text-ui-yellow' : 'hover:text-ui-yellow' }`">Evil</inertia-link>
                </th>
                <th class="p-1 text-left text-white">
                    <column-sort-link :href="route('ranking', linkParams('efficiency'))" :is-sorting="player.sorting_by === 'efficiency'" :direction="player.sorting_direction">
                        Eff. Rating
                    </column-sort-link>
                </th>
            </tr>
            </thead>
            <tbody>
                <tr v-for="item in player.ranking.data" :class="{'font-bold': item.is_player}">
                    <td class="p-1">{{ item.rank }}</td>
                    <td class="p-1">{{ item.score }}</td>
                    <td class="p-1 flex items-center gap-2" :class="{'text-blue-500': item.is_admin, 'text-green-600': item.is_player}">
                        <span v-if="item.is_player" class="text-white">*</span> {{ item.insignia }} {{ item.name }}
                    </td>
                    <td class="p-1">{{ item.turns_used }}</td>
                    <td class="p-1">{{ item.last_active }}</td>
                    <td :class="['p-1', {'text-red-600': item.rating < 0, 'text-green-600': item.rating > 0}]">{{ item.rating }}</td>
                    <td class="p-1">{{item.efficiency}}</td>
                </tr>
            </tbody>
        </table>
    </section>
</template>
