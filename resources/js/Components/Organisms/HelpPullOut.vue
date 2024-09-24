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
import {useModal} from "@/Composables/useModal";
import PullOut from "@/Components/Atoms/PullOut.vue";
import BorderedHeading from "@/Components/Atoms/BorderedHeading.vue";

const {isOpen} = useModal('help');
</script>

<template>
    <pull-out :is-visible="isOpen">
        <bordered-heading><span class="text-white">How to play</span></bordered-heading>
        <nav>
            <ol class="space-y-2 divide-y divide-solid divide-ui-orange-500 text-white">
                <li class="pt-2"><Link href="/help/introduction">Introduction to Space Mines</Link></li>
                <li class="pt-2"><Link href="/help/building-structures">Building structures</Link></li>
                <li class="pt-2"><Link href="/help/population-needs">What your population needs</Link></li>
                <li class="pt-2"><Link href="/help/resource-harvesting">Resource Harvesting</Link></li>
                <li class="pt-2"><Link href="/help/manufacturing">Manufacturing</Link></li>
                <li class="pt-2"><Link href="/help/marketplace">Buying and Selling</Link></li>
            </ol>
        </nav>
    </pull-out>
</template>
