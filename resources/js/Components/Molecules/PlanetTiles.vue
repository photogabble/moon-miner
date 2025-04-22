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

import PlanetTile from "@/Components/Atoms/PlanetTile.vue";
</script>

<template>
    <div class="grid-rows-9 grid-cols-9 grid" style="transform: rotateX(60deg) rotateY(0deg) rotateZ(-45deg); max-width: 70%;margin: 0 auto;">
        <div v-for="i in (9*9)" class="hover:bg-gray-500 hover:bg-opacity-60 cursor-pointer border border-opacity-40 aspect-square flex items-center justify-center">
            <planet-tile  />
        </div>
    </div>
</template>
