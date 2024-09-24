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

const emit = defineEmits(['visible']);

defineProps({
    isVisible: Boolean,
});

const onAfterEnter = () => emit('visible', true);
const onAfterLeave = () => emit('visible', false);
</script>

<template>
    <Transition @after-enter="onAfterEnter" @after-leave="onAfterLeave">
        <div v-if="isVisible" class="absolute w-[350px] right-0 top-0 border-l-4 p-2 border-double border-l-ui-orange-500 bg-ui-grey-900 z-50 h-full">
            <slot/>
        </div>
    </Transition>
</template>

<style scoped>
.v-enter-active,
.v-leave-active {
    right: 0;
    transition: right 250ms ease;
}

.v-enter-from,
.v-leave-to {
    right: -350px;
}
</style>
