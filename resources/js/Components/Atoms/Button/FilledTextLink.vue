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

import {Link as InertiaLink} from "@inertiajs/vue3";
import {computed} from "vue";

const props = withDefaults(defineProps<{
    disabled: boolean,
    active: boolean,
    href: string,
    method: 'get' | 'post' | 'put' | 'patch' | 'delete',
    data?: object,
}>(), {
    method: 'get',
    disabled: false,
    active: false,
    data: undefined,
});

const classList = computed(() => {
    let classes = 'uppercase p-0.5 text-ui-grey-900 disabled:opacity-50';

    if (props.active) {
        return `${classes} bg-ui-orange-500 hover:bg-ui-yellow`;
    } else {
        return `${classes} bg-white hover:bg-ui-yellow`;
    }
});

const as = computed(() => props.method === 'get'
    ? 'a'
    : 'button'
);

</script>

<template>
    <inertia-link
        v-if="as === 'a'"
        :href="href"
        :method="method"
        :class="classList"
    ><slot/></inertia-link>
    <inertia-link
        v-else
        :href="href"
        :method="method"
        :class="classList"
        :data="data"
        as="button"
        type="button"
    ><slot /></inertia-link>
</template>
