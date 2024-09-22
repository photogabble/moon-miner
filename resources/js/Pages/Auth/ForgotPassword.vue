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
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/Atoms/Form/InputError.vue';
import InputLabel from '@/Components/Atoms/Form/InputLabel.vue';
import PrimaryButton from '@/Components/Atoms/Button/PrimaryButton.vue';
import TextInput from '@/Components/Atoms/Form/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
import MainPanel from "@/Components/Atoms/MainPanel.vue";

defineProps<{
    status?: string;
}>();

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <Head title="Forgot Password" />

    <guest-layout>
        <main-panel centered>
            <div class="border border-ui-orange-500 py-2 px-3 border-x-8 w-1/3 space-y-3">
                <p class="mb-2 text-sm">
                    <strong class="text-white">Forgot your password?</strong> No problem.
                </p>
                <p class="mb-4 text-sm">
                    Just let us know your email address and we will email you a password reset
                    link that will allow you to choose a new one.
                </p>

                <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
                    {{ status }}
                </div>

                <form @submit.prevent="submit">
                    <div>
                        <input-label for="email" value="Email" />

                        <text-input
                            id="email"
                            type="email"
                            class="mt-1 block w-full"
                            v-model="form.email"
                            required
                            autofocus
                            autocomplete="username"
                        />

                        <input-error class="mt-2" :message="form.errors.email" />
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <primary-button class="w-full" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Email Password Reset Link
                        </primary-button>
                    </div>
                </form>
            </div>
        </main-panel>
    </guest-layout>
</template>
