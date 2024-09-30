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

import {useModal} from "@/Composables/useModal";
import PullOut from "@/Components/Atoms/PullOut.vue";
import {ref} from "vue";
import PlayerProfile from "@/Components/Molecules/Form/PlayerProfile.vue";
import UpdatePassword from "@/Components/Molecules/Form/UpdatePassword.vue";
import DeleteAccount from "@/Components/Organisms/DeleteAccount.vue";

const {isOpen} = useModal('profile');
const isOpened = ref<boolean>(false);
</script>

<template>
    <pull-out :is-visible="isOpen" @visible="isOpened = $event">
        <player-profile :focus="isOpened" />
        <update-password />
        <delete-account />
    </pull-out>
</template>
