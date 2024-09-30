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

import type {EncounterOption, EncounterResource} from "@/types/resources/encounter";
import ModalWithHeader from "@/Components/Molecules/ModalWithHeader.vue";
import {router} from "@inertiajs/vue3";
import {computed} from "vue";
import Message from "@/Components/Molecules/Encounter/Message.vue";

const props = defineProps<{
    encounter: EncounterResource & {messages?: Array<string>},
}>();

const canExit = computed(() => props.encounter.options.hasOwnProperty('complete'))
const exitAction = computed<EncounterOption>(() => props.encounter.options['complete']);

const exit = () => {
    if (!canExit.value) return;
    router.visit(exitAction.value.link, {
        method: 'post',
    });
};
</script>

<template>
    <modal-with-header
        @close="exit"
        :title="encounter.title"
        :close-button-text="exitAction?.text"
        :close-button="canExit"
        show>

        <!--
            <message> is the default encounter view, it parses and outputs the messages array, sometimes
            an encounter might require more complex display, such as dialogue between the player and a
            NPC, shop, piracy, etc.
        -->
        <message :encounter="encounter" />
    </modal-with-header>
</template>
