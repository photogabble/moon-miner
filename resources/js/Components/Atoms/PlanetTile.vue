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

import {computed} from "vue";

const props = withDefaults(defineProps<{
    modifier?: 'none' | 'dead' | 'factory' | 'science' | 'organic'
}>(), {
    modifier: 'none',
})

const classes = computed(() => {
    const mapping = {
        'none': 'text-gray-100', // white: no bonus
        'dead': 'text-slate-500', // slate: dead zone
        'factory': 'text-red-500', // red: manufacturing bonus
        'science': 'text-blue-500', // blue: research bonus
        'organic': 'text-green-500' // green: agriculture bonus
    }

    return mapping[props.modifier];
});

</script>

<template>
    <svg width="20" height="20" :class="classes">
        <rect x="0" y="0" rx="2" ry="2" width="20" height="20" class="fill-current stroke-current stroke-2" style="fill-opacity:0.2"/>
    </svg>
</template>
