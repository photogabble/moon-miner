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

import { useSlots } from "vue";
import NavigationColumn from "@/Components/Atoms/NavigationColumn.vue";
import {TeleportTarget} from "vue-safe-teleport";
import HelpPullOut from "@/Components/Organisms/HelpPullOut.vue";
import ModalBackdrop from "@/Components/Atoms/ModalBackdrop.vue";

const slots = useSlots();
</script>

<template>
    <div class="p-10 flex flex-col h-screen w-screen justify-center items-center">
        <!-- Page Content -->
        <main class="w-full max-h-screen font-mono text-ui-orange-500 relative overflow-hidden">
            <!-- Modals -->
            <help-pull-out />

            <teleport-target id="modal-target" />
            <modal-backdrop />

            <div class="grid grid-cols-[350px_minmax(350px,_1fr)_64px] w-full h-full">
                <!-- Left Sidebar -->
                <div v-if="slots.sidebar" class="flex flex-col h-full overflow-hidden">
                    <slot name="sidebar"/>
                </div>

                <!-- Middle Area -->
                <div :class="['w-full flex flex-col', {'col-span-2 pr-1': !slots.sidebar, 'px-1': slots.sidebar}]">
                    <div class="text-sm flex flex-row border-t border-ui-orange-500/50 justify-between">
                        <div class="border-t border-ui-orange-500 border-l border-partway-r p-1 px-2">
                            <span class="uppercase text-white">Turns Available:</span> X.001
                            <span class="text-ui-yellow">&middot;&nbsp;</span>
                            <span class="uppercase text-white">Turns Used:</span> X.002
                            <span class="text-ui-yellow">&middot;&nbsp;</span>
                            <span class="uppercase text-white">Credits</span> X.003
                        </div>

                        <div class="border-ui-orange-500 border-partway-t px-2 p-1">
                            <span class="uppercase text-white">Score</span> X.X
                        </div>
                    </div>
                    <slot/>
                </div>

                <navigation-column />
            </div>
        </main>
    </div>
</template>
