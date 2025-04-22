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
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import MainPanel from "@/Components/Atoms/MainPanel.vue";
import {usePage} from "@inertiajs/vue3";
import {usePlayerState} from "@/Composables/usePlayerState";
import PlayerShip from "@/Components/Organisms/Panel/PlayerShip.vue";
import PlayerShipCargo from "@/Components/Organisms/Panel/PlayerShipCargo.vue";
import type {NaviComPlanetProps} from "@/types/navicom";
import FilledTextLink from "@/Components/Atoms/Button/FilledTextLink.vue";
import {computed} from "vue";
import PlanetTile from "@/Components/Atoms/PlanetTile.vue";
import PlanetTiles from "@/Components/Molecules/PlanetTiles.vue";
import PlanetIcon from "@/Components/Atoms/PlanetIcon.vue";

const { ship } = usePlayerState();
const { planet } = usePage<NaviComPlanetProps>().props;

const systemHref = computed(() => {
    if (ship && ship.system_id === planet.system_id) return route('navicom')
    return route('navicom', planet.system_id);
});

</script>
<template>
    <authenticated-layout>
        <template #sidebar>
            <player-ship v-if="ship" :ship="ship" />

            <div class="flex flex-col flex-grow overflow-y-scroll max-h-full space-y-2">
                <player-ship-cargo v-if="ship" :cargo="ship.cargo_holds" :energy="ship.energy" />
            </div>
        </template>

        <main-panel class="relative">
            <template #top-right>
                <span class="text-white">System</span> {{ planet.system.name }}, <span class="text-white">Sector</span> {{ planet.system.sector_id }}.
            </template>

            <template #top-left>
                <nav class="space-x-1">
                    <filled-text-link :href="systemHref" active>[System]</filled-text-link>
                    <filled-text-link href="#" active disabled>[Scan]</filled-text-link>
                    <filled-text-link href="#" active disabled>[Land]</filled-text-link>
                </nav>
            </template>

<!--            <planet-tiles />-->
<!--            <pre class="pt-10">{{ planet }}</pre>-->
        </main-panel>
    </authenticated-layout>
</template>
