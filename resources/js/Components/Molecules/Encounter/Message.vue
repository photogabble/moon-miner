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

import type {EncounterResource} from "@/types/resources/encounter";

const props = defineProps<{
    encounter: EncounterResource & {messages?: Array<string>},
}>();

const parseMessage = (message:string) => {
    const replace = {
        white: '<span class="text-white">',
        red: '<span class="text-red-600">',
        green: '<span class="text-green-600">',
    };

    [...message.matchAll('\\<(?<name>\\w+)(?<attributes>\\s+[^\\>]*|)\\>')].forEach((match) => {
        message = message.replace(match[0], replace[match.groups.name] ?? '<span>');
    });

    [...message.matchAll('\\<\\/(?<name>\\w+)>')].forEach((match) => {
        if (Object.keys(replace).includes(match.groups.name)) {
            message = message.replace(match[0], '</span>');
        }
    });

    return message;
};
</script>

<template>
    <p v-if="encounter.messages" v-for="message in encounter.messages" v-html="parseMessage(message)" />
</template>
