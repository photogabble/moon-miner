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

import {onMounted, onUnmounted } from 'vue';
import {SafeTeleport} from "vue-safe-teleport";

const props = withDefaults(
    defineProps<{
        show?: boolean;
        closeable?: boolean;
        danger?: boolean;
    }>(), {
        show: false,
        closeable: true,
        danger: false
    }
);

const emit = defineEmits(['close']);

const close = () => {
    if (props.closeable) emit('close');
};

const closeOnEscape = (e: KeyboardEvent) => {
    if (e.key === 'Escape' && props.show) {
        close();
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));

onUnmounted(() => {
    document.removeEventListener('keydown', closeOnEscape);
    document.body.style.overflow = 'visible';
});

</script>

<template>
    <safe-teleport to="#modal-target">
        <div v-if="show" :class="['absolute top-0 left-0 w-full h-full z-50 flex justify-center items-center', (danger) ? 'bg-[#260202d6]' : 'bg-ui-grey-900/80']">
            <slot/>
        </div>
    </safe-teleport>
</template>
