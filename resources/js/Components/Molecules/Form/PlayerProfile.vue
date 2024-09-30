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

import {useForm} from "@inertiajs/vue3";
import {ref, watch} from "vue";
import {usePlayerState} from "@/Composables/usePlayerState";
import BorderedHeading from "@/Components/Atoms/BorderedHeading.vue";
import InputLabel from "@/Components/Atoms/Form/InputLabel.vue";
import TextInput from "@/Components/Atoms/Form/TextInput.vue";
import InputError from "@/Components/Atoms/Form/InputError.vue";
import PrimaryButton from "@/Components/Atoms/Button/PrimaryButton.vue";

const {player} = usePlayerState();

const username = ref<HTMLInputElement | null>(null);

const form = useForm({
    name: player.name,
    email: player.email,
});

const props = defineProps<{
    focus: boolean,
}>();

watch(() => props.focus, (a, b) => {
    console.log(a, b);
    if (username.value !== null && a) username.value.focus();
});

</script>

<template>
    <section>
        <bordered-heading>
            <span class="text-white">Your Profile &middot;</span> <span aria-hidden="true">2023.0422XX</span>
        </bordered-heading>

        <form @submit.prevent="form.patch(route('profile.update'))" class="space-y-4 mb-5 flex flex-col">
            <div>
                <input-label for="email" value="Email"/>
                <text-input id="email" ref="username" type="email" v-model="form.email" required autocomplete="username" />
                <input-error class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <input-label for="name" :value="__('new.l_new_pname')"/>
                <text-input id="name" type="text" v-model="form.name" required autocomplete="name"/>
                <input-error class="mt-2" :message="form.errors.name" />
            </div>

            <div class="flex items-center gap-4">
                <primary-button :disabled="form.processing" class="h-8">Save</primary-button>
                <transition enter-from-class="opacity-0" leave-to-class="opacity-0" class="transition ease-in-out">
                    <p v-if="form.recentlySuccessful" class="text-sm text-ui-orange-500">Changes Saved.</p>
                </transition>
            </div>
        </form>
    </section>
</template>
