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

import {PresetResource} from "@/types/resources/preset";
import {ref} from "vue";
import SidebarPanel from "@/Components/Atoms/SidebarPanel.vue";
import TextButton from "@/Components/Atoms/Button/TextButton.vue";

const selectedSector = ref<number>(-1);
const editingPreset = ref<PresetResource|undefined>();
</script>

<template>
    <sidebar-panel>
        <template #heading>
            <span class="text-white">{{ __('common.l_realspace') }}</span>
        </template>
        <section class="flex flex-col">
            <ul>
                <!-- TODO -->
            </ul>
            <text-button @click="selectedSector = 0" class="self-end">[{{ __('main.l_main_other') }}]</text-button>
        </section>
    </sidebar-panel>
</template>
