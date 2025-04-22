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

withDefaults(defineProps<{
    name: string,
    ringCount?: number,
    ringInnerDiameter?: number
    satellites?: Array<{radius: number, name: string}>
}>(), {
    ringCount: 0,
    ringInnerDiameter: 33,
    satellites: () => [],
})
</script>

<template>
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 300">
        <g class="object" id="Saturn" style="transform: translateX(0px);">
            <g v-for="idx in ringCount" class="rings"><circle :r="33 + (2*idx)" cx="410" cy="150"></circle></g>
            <text class="" x="410" y="104">{{ name }}</text>
            <circle r="36" cx="410" cy="150"></circle>
            <line x1="410" y1="150" x2="410" :y2="186 + (10*satellites.length)"></line>
            <g v-for="(satellite, idx) in satellites" class="satellite">
                <circle :r="satellite.radius" cx="410" :cy="196 + (10*idx)"></circle>
                <text x="415" :y="198 + (10*idx)">{{ satellite.name }}</text>
            </g>
        </g>
    </svg>
</template>

<style scoped>
.object {
    fill: #fff;
    text-anchor: middle;
    font-size: .6em;
    cursor: pointer;
    transform-box: fill-box;
}

.rings {
    stroke: #fff;
    fill: none;
    transform: skew(-45deg);
    transform-origin: 50%;
}

.satellite {
    text-anchor: start;
    font-size: .7em;
}

line {
    stroke:#fff;
    stroke-width:0.5;
}
</style>
