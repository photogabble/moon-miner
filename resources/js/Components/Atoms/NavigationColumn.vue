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

import NavigationLink from "@/Components/Atoms/NavigationLink.vue";
import DashboardIcon from "@/Components/Atoms/Icons/DashboardIcon.vue";
import ExploreIcon from "@/Components/Atoms/Icons/ExploreIcon.vue";
import ResearchIcon from "@/Components/Atoms/Icons/ResearchIcon.vue";
import HarvestingIcon from "@/Components/Atoms/Icons/HarvestingIcon.vue";
import ManufacturingIcon from "@/Components/Atoms/Icons/ManufacturingIcon.vue";
import MarketplaceIcon from "@/Components/Atoms/Icons/MarketplaceIcon.vue";
import RankingIcon from "@/Components/Atoms/Icons/RankingIcon.vue";
import UserProfileIcon from "@/Components/Atoms/Icons/UserProfileIcon.vue";
import HelpIcon from "@/Components/Atoms/Icons/HelpIcon.vue";
import LogoutIcon from "@/Components/Atoms/Icons/LogoutIcon.vue";
import {useModal} from "@/Composables/useModal";
import {useRoute} from "ziggy-js";

const route = useRoute();

const {openModal: openProfilePanel} = useModal('profile');
const {openModal: openHelpPanel} = useModal('help');

withDefaults(defineProps<{
    isLoggedIn?: boolean;
}>(), {
    isLoggedIn: false,
});

const isHelpPage = false; // TODO: computed<Boolean>(() => route.path.includes('help/'));
</script>

<template>
    <div class="bg-slate-700/10 w-16 p-2 border-partway-y flex flex-col" style="--border-part-b-height: 12px; --border-part-t-height: 12px;">
        <div class="flex flex-col space-y-2 mb-2 flex-grow">
            <div class="space-y-1 text-2xl">
                <navigation-link :href="isLoggedIn ? route('navicom') : '/'" :active="route().current('navicom')" title="Overview"><dashboard-icon/></navigation-link> <!-- Overview -->
                <navigation-link :href="route('explore')" :active="route().current('explore')" title="Explore universe" :disabled="!isLoggedIn"><explore-icon/></navigation-link> <!-- Explore Universe -->
                <navigation-link href="/research" title="Research and Development" :disabled="!isLoggedIn"><research-icon/></navigation-link> <!-- Research -->
                <navigation-link href="/harvesting" title="Manage Resource Harvesting" :disabled="!isLoggedIn"><harvesting-icon/></navigation-link> <!-- Resource Harvesting -->
                <navigation-link href="/manufacturing" title="Manage Manufacturing" :disabled="!isLoggedIn"><manufacturing-icon/></navigation-link> <!-- Manufacturing -->
            </div>

            <hr class="border-ui-orange-500"/>

            <div class="space-y-1 text-2xl">
                <navigation-link href="/market" title="Buy/Sell in the Marketplace" :disabled="!isLoggedIn"><marketplace-icon/></navigation-link> <!-- Marketplace -->
                <navigation-link href="/ranking" title="View Player Rankings" :active="route().current('ranking')"><ranking-icon/></navigation-link> <!-- Player Ranking -->
            </div>

            <!-- Decoration -->
            <div aria-hidden="true" class="decoration flex-grow border-partway-y p-1 relative text-xs overflow-hidden min-h-[100px]" style="--border-part-b-height: 12px; --border-part-t-height: 12px;">
                <small class="block right-1 top-2 absolute h-[240px]" style="writing-mode: vertical-rl;">X000.69 //////////////////////////////...</small>
                <small class="animate-pulse block absolute bottom-0 left-1">&middot;&middot;&middot;</small>
            </div>
            <!-- ./ decoration -->
        </div>

        <div class="space-y-1 text-2xl">
            <navigation-link @click="openProfilePanel" title="View and modify your profile" :disabled="!isLoggedIn"><user-profile-icon/></navigation-link> <!-- Player Profile -->
            <navigation-link @click="openHelpPanel" :active="isHelpPage" title="How to play"><help-icon/></navigation-link> <!-- Player Feedback & Help -->
            <navigation-link :href="route('logout')" as="button" method="post" title="Logout" :disabled="!isLoggedIn"><logout-icon/></navigation-link> <!-- Logout -->
        </div>
    </div>
</template>

<style scoped>
@media screen and (max-height: 700px)
{
    .decoration{
        display:none;
    }
}
</style>
