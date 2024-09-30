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

import { Link } from '@inertiajs/vue3';
import {computed} from 'vue';
const props = defineProps({
    active: Boolean,
    title: String,
    href: String,
    disabled: Boolean,
    width: {
        type: String,
        default: '12',
    },
    height: {
        type: String,
        default: null
    }
});

const isSquare = computed(() => !props.height || (props.height === props.width) );
</script>

<template>
    <Link v-if="href && !disabled" :class="`${isSquare ? 'aspect-square' : ''} w-${props.width} ${(active ? 'router-link-active' : '')}`" :href="href" :aria-label="title">
        <span><slot/></span>
    </Link>
    <button v-else :class="`${isSquare ? 'aspect-square' : ''} w-${props.width} ${isSquare ? `h-${props.width}` : (props.height) ? `h-${props.height}` : '' } ${(active ? 'router-link-active' : '')}`" :disabled="disabled" :aria-label="title">
        <span><slot/></span>
    </button>
</template>

<style lang="postcss" scoped>
a, button{
    @apply block border p-0.5 disabled:opacity-50 border-ui-orange-500;
}

a span, button span {
    @apply flex h-full justify-center items-center ease-in-out duration-300;
}

a:not(:disabled), button:not(:disabled) {
    @apply border-ui-orange-500;
}

a:not(:disabled) span, button:not(:disabled) span {
    @apply bg-ui-orange-500/10 hover:bg-ui-orange-500 hover:text-ui-grey-900;
}

a.router-link-active:not(:disabled),
button.router-link-active:not(:disabled){
    @apply border-ui-salmon;
}

a.router-link-active:not(:disabled) span,
button.router-link-active:not(:disabled) span{
    @apply bg-ui-salmon text-ui-grey-900;
}

a:disabled, button:disabled{
    @apply border-ui-orange-500/50 cursor-default pointer-events-none
}

a:disabled span, button:disabled span {
    @apply bg-ui-orange-500/10 text-ui-orange-500/50;
}
</style>
