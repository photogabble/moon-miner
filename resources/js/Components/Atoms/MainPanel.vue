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

import {computed, useSlots} from "vue";

withDefaults(defineProps<{
    centered?: boolean
}>(), {
    centered: false,
});

const slots = useSlots();

const hasIslands = computed(() => {
    return slots.topLeft || slots.topRight || slots.bottomLeft || slots.bottomRight;
})

</script>

<template>
    <div :class="`${hasIslands ? 'relative' : ''} overflow-hidden ${centered ? 'flex flex-row items-center justify-center' : ''} h-20 flex-grow border mt-1 border-ui-orange-500/50`">
        <div v-if="slots['top-left']" class="p-2 text-sm top-0 left-0 absolute z-20">
            <slot name="top-left"/>
        </div>
        <div v-if="slots['top-right']" class="p-2 text-sm top-0 right-0 absolute z-20">
            <slot name="top-right"/>
        </div>
        <slot/>
    </div>
</template>
