<script setup lang="ts">/**
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
import {nextTick, ref} from "vue";
import {useForm} from "@inertiajs/vue3";
import BorderedHeading from "@/Components/Atoms/BorderedHeading.vue";
import InputLabel from "@/Components/Atoms/Form/InputLabel.vue";
import TextInput from "@/Components/Atoms/Form/TextInput.vue";
import InputError from "@/Components/Atoms/Form/InputError.vue";
import PrimaryButton from "@/Components/Atoms/Button/PrimaryButton.vue";
import DangerButton from "@/Components/Atoms/Button/DangerButton.vue";
import Modal from "@/Components/Atoms/Modal.vue";

const confirmingUserDeletion = ref(false);
const passwordInput = ref<HTMLInputElement | null>(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;
    nextTick(() => passwordInput.value?.focus());
};

const closeModal = () => {
    confirmingUserDeletion.value = false;
    form.reset();
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value?.focus(),
        onFinish: () => form.reset(),
    });
};

</script>

<template>
    <section>
        <bordered-heading>
            <span class="text-white">{{ __('self_destruct.l_die_title') }}</span>
        </bordered-heading>

        <p class="mt-1 mb-5 text-sm text-red-600">
            {{ __('self_destruct.l_die_rusure') }}
        </p>

        <danger-button @click="confirmUserDeletion" class="w-full">{{__('self_destruct.l_die_goodbye')}}</danger-button>

        <modal :show="confirmingUserDeletion" @close="closeModal" danger>
            <div class="p-6 w-2/3 border border-ui-orange-500 bg-ui-grey-900/90 border-x-4">
                <h2 class="text-lg font-medium text-ui-yellow">
                    {{__('self_destruct.l_die_check')}}
                </h2>
                <p class="mt-1 text-sm text-white">
                    Once you account is deleted, an in game news obituary will be generated and then all your data
                    will be permanently deleted. Please enter your password to confirm you would like
                    to permanently delete your account.
                </p>

                <div class="mt-6">
                    <input-label for="password" :value="__('login.l_login_pw')"/>
                    <text-input id="password" ref="passwordInput" type="password" v-model="form.password"
                                @keyup.enter="deleteUser"/>
                    <input-error class="mt-2" :message="form.errors.password"/>

                    <div class="flex w-full space-x-2 mt-6 justify-end">
                        <primary-button class="h-8" @click="closeModal">{{ __('common.l_cancel') }} [ESC]</primary-button>
                        <danger-button class="h-8" @click="deleteUser" :disabled="form.processing" :aria-disabled="form.processing">
                            {{ __('common.l_confirm') }}
                        </danger-button>
                    </div>
                </div>
            </div>
        </modal>
    </section>
</template>
