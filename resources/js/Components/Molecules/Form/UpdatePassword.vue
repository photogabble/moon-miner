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
import BorderedHeading from "@/Components/Atoms/BorderedHeading.vue";
import {ref} from "vue";
import {useForm} from "@inertiajs/vue3";
import InputError from "@/Components/Atoms/Form/InputError.vue";
import InputLabel from "@/Components/Atoms/Form/InputLabel.vue";
import TextInput from "@/Components/Atoms/Form/TextInput.vue";
import PrimaryButton from "@/Components/Atoms/Button/PrimaryButton.vue";

const passwordInput = ref<HTMLInputElement | null>(null);
const currentPasswordInput = ref<HTMLInputElement | null>(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value?.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value?.focus();
            }
        },
    });
};

</script>

<template>
    <section>
        <bordered-heading>
            <span class="text-white">Update Password</span>
        </bordered-heading>

        <form @submit.prevent="updatePassword" class="space-y-4 mb-5 flex flex-col">
            <div>
                <input-label for="current_password" value="Current Password"/>
                <text-input id="current_password" ref="currentPasswordInput" type="password" v-model="form.current_password" required autocomplete="current-password" />
                <input-error class="mt-2" :message="form.errors.current_password" />
            </div>

            <div>
                <input-label for="password" value="New Password"/>
                <text-input id="password" ref="passwordInput" type="password" v-model="form.password" required autocomplete="new-password"/>
                <input-error class="mt-2" :message="form.errors.password" />
            </div>

            <div>
                <input-label for="password_confirmation" value="Confirm Password"/>
                <text-input id="password_confirmation" type="password" v-model="form.password_confirmation" required autocomplete="new-password"/>
                <input-error class="mt-2" :message="form.errors.password_confirmation" />
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
