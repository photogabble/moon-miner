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

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, router, usePage} from '@inertiajs/vue3';
import MainPanel from "@/Components/Atoms/MainPanel.vue";
import PlayerShip from "@/Components/Organisms/Panel/PlayerShip.vue";
import Orbit from "@/Components/Atoms/Orbit.vue";
import SidebarPanel from "@/Components/Atoms/SidebarPanel.vue";
import {usePlayerState} from "@/Composables/usePlayerState";
import {useRoute} from "ziggy-js";
import EncounterModal from "@/Components/Organisms/Modal/EncounterModal.vue";
import PlayerShipCargo from "@/Components/Organisms/Panel/PlayerShipCargo.vue";
import TradeRoutes from "@/Components/Organisms/Panel/TradeRoutes.vue";
import SystemWarps from "@/Components/Organisms/Panel/SystemWarps.vue";
import PlayerPreset from "@/Components/Organisms/Panel/PlayerPreset.vue";
import TextButton from "@/Components/Atoms/Button/TextButton.vue";
import {computed} from "vue";
import {DashboardPageProps} from "@/types/dashboard";
import {WaypointResource, WaypointType} from "@/types/resources/waypoint.d.ts";
import FilledTextLink from "@/Components/Atoms/Button/FilledTextLink.vue";
import PaginationPrevNext from "@/Components/Atoms/Links/PaginationPrevNext.vue";
import PanelHeader from "@/Components/Atoms/PanelHeader.vue";
import PlanetIcon from "@/Components/Atoms/PlanetIcon.vue";

const route = useRoute();
const { ship, encounter, player } = usePlayerState();
const { system, navicom_view_mode } = usePage<DashboardPageProps>().props;

const all = usePage();

// When displaying waypoints, need to predefine a 2D plane that we are "drawing" to
// then each waypoint's orbital distance from its primary can be scaled to that
// planes dimensions and then the plane itself can be scaled so to act as a
// kind of zoom.

// @see https://www.trysmudford.com/blog/linear-interpolation-functions/
const lerp = (x: number, y: number, a: number) => x * (1 - a) + y * a;
const invlerp = (x: number, y: number, a: number) => clamp((a - x) / (y - x));
const clamp = (a: number, min = 0, max = 1) => Math.min(max, Math.max(min, a));
const range = (
    x1: number,
    y1: number,
    x2: number,
    y2: number,
    a: number
) => lerp(x2, y2, invlerp(x1, y1, a));

const waypoints = computed(() => {
    const waypoints = (system?.waypoints ?? [])
        .filter(waypoint => waypoint.primary_id === system.id && waypoint.type !== 'Star')
        .sort((a, b) => b.orbit.distance - a.orbit.distance);

    let min = 9999;
    let max = 0;

    waypoints.forEach((waypoint) => {
        if (waypoint.orbit.distance < min) min = waypoint.orbit.distance;
        if (waypoint.orbit.distance > max) max = waypoint.orbit.distance;
    });

    return waypoints.map(waypoint => {
        return {
            ...waypoint,
            orbitRadius: range(min, max, 70, 800, waypoint.orbit.distance)
        };
    });
})

const spawn = () => {
    router.visit(route('debug.spawn-encounter'), {
        method: 'post'
    });
};

const randomise = () => {
    router.visit(route('debug.randomise-system'), {
        method: 'post'
    });
}

const viewWaypoint = (waypoint: WaypointResource<WaypointType>) => {
    console.log('=== Waypoint clicked: ', waypoint);
    if (waypoint.type === WaypointType.Planet) {
        router.visit(route('navicom.planet', waypoint.id), {
            method: 'get'
        });
    }
}

const waypointLink = (waypoint: WaypointResource<WaypointType>) => {
    if (waypoint.type === WaypointType.Planet) return route('navicom.planet', waypoint.id);
    return '#';
}

</script>

<template>
    <Head title="Dashboard" />

    <authenticated-layout>
        <template #sidebar>
            <player-ship v-if="ship" :ship="ship" />
            <div class="flex flex-col flex-grow overflow-y-scroll max-h-full space-y-2">
                <player-ship-cargo v-if="ship" :cargo="ship.cargo_holds" :energy="ship.energy" />

                <system-warps />
                <trade-routes />
                <player-preset />

                <sidebar-panel>
                    <template #heading>
                        <span class="text-white flex-grow">Encounter Debugging</span>
                        <text-button @click="spawn">[Spawn]</text-button>
                    </template>
                    <pre>{{ encounter }}</pre>
                </sidebar-panel>
                <sidebar-panel>
                    <template #heading>
                        <span class="text-white flex-grow">System Debugging</span>
                        <text-button @click="randomise">[Randomise]</text-button>
                    </template>
                    <pre>{{ all.props.alert }}</pre>
                </sidebar-panel>
            </div>

        </template>

        <main-panel class="relative">
            <template #top-right>
                <span class="text-white">System</span> {{ system.name }}, <span class="text-white">Sector</span> {{ system.sector_id }}.
            </template>
            <template #top-left>
                <nav class="space-x-1">
                    <filled-text-link
                        method="patch"
                        :href="route('navicom.set-view-mode')"
                        :data="{mode: 'map'}"
                        :active="navicom_view_mode === 'map'"
                        :only="['navicom_view_mode']"
                        :preserve-state="false"
                    >[Orbital Map]</filled-text-link>
                    <filled-text-link
                        method="patch"
                        :href="route('navicom.set-view-mode')"
                        :data="{mode: 'details'}"
                        :active="navicom_view_mode === 'details'"
                        :only="['navicom_view_mode']"
                        :preserve-state="false"
                    >[Orbit Details]</filled-text-link>
                </nav>
            </template>
            <div v-if="navicom_view_mode === 'map'" class="relative top-0 bottom-0 left-0 right-0 w-full h-full" :style="{zoom: 1}">
                <orbit
                    class="z-10"
                    v-for="waypoint in waypoints"
                    @click="viewWaypoint(waypoint)"
                    :waypoint="waypoint"
                    :name="`${waypoint.orbit.distance}AU`"
                    :angle="waypoint.orbit.angle"
                    :radius="waypoint.orbitRadius"
                    :planet-radius="20"
                />
                <button @click="console.log('Star')" class="relative sun z-0" />
            </div>

            <div v-else class="pt-9 grid grid-rows-6 divide-y-4 divide-double divide-ui-orange-500/50 h-full">
                <section>
                    <!-- TODO: display all planets as icons here... -->
                    <planet-icon name="Vulcan" :ring-count="6" :satellites="[{radius:2, name: 'Frank'}, {radius:1, name: 'Bob'}]" class="max-h-full" />
                </section>
                <section class="row-span-5">
                    <panel-header>
                        <span class="text-white">Orbital Bodies</span>
                        <template #actions>...</template>
                    </panel-header>

                    <table class="w-full">
                        <thead>
                        <tr class="border-b-2 border-double border-ui-orange-500 text-left">
                            <th class="p-1 w-20">Type</th>
                            <th class="p-1">Name</th>
                            <th>&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="waypoint in system?.waypoints ?? []" class="border-b border-ui-orange-500 text-left">
                            <td class="p-1">{{ waypoint.type.substring(0, 1) }}</td>
                            <td class="p-1">
                                {{ waypoint.name }} ({{ waypoint.id }})
                                <template v-if="waypoint.type === WaypointType.WarpGate">
                                    → {{ waypoint.properties.destination_system_name}}
                                    <span v-if="system.sector_id !== waypoint.properties.destination_system_sector_id" class="text-white">[S-{{ waypoint.properties.destination_system_sector_id }}]</span>
                                </template>
                            </td>
                            <td class="space-x-2 p-1">
                                <filled-text-link :href="waypointLink(waypoint)" :disabled="waypointLink(waypoint) === '#'" active>[Info]</filled-text-link>
                                <filled-text-link v-if="[WaypointType.WarpGate, WaypointType.Planet].includes(waypoint.type)" href="#" active disabled>[Scan]</filled-text-link>
                                <filled-text-link v-if="waypoint.type === WaypointType.WarpGate" method="post" :preserve-state="false" :href="route('ship.travel-through.gate', {ship: player.ship_id, gate: waypoint.id})" active :disabled="waypoint.properties.destination_system_id === null || player.ship_id === null">[Jump]</filled-text-link>
                                <filled-text-link v-if="waypoint.type === WaypointType.Planet" href="#" active disabled>[Land]</filled-text-link>
                                <filled-text-link v-if="waypoint.type === WaypointType.Port" href="#" active disabled>[Dock]</filled-text-link>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </section>

            </div>
        </main-panel>

        <encounter-modal v-if="encounter" :encounter="encounter" />

    </authenticated-layout>
</template>

<style scoped>
.sun {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);

    width: 30px;
    height: 30px;
    background: #fc8437;
    border-radius: 50%;
}
</style>
