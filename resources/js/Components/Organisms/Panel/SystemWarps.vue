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

import SidebarPanel from "@/Components/Atoms/SidebarPanel.vue";
import TextButton from "@/Components/Atoms/Button/TextButton.vue";
import {ref} from "vue";

const selectedSector = ref<number>(-1);
</script>

<template>
    <sidebar-panel>
        <template #heading>
            <span class="text-white flex-grow">System Warps</span>
            <text-button @click="selectedSector = 0">[Nav Computer]</text-button>
        </template>

        <section class="flex flex-col">
            <ul>
                <!-- TODO -->

                <li class="text-red-600">
                    No warps found
                </li>
            </ul>
            <text-button class="self-end">[Full Scan]</text-button>
        </section>
    </sidebar-panel>
</template>
