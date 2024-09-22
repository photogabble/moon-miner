<script setup lang="ts">
    import GuestLayout from '@/Layouts/GuestLayout.vue';
    import InputError from '@/Components/Atoms/Form/InputError.vue';
    import InputLabel from '@/Components/Atoms/Form/InputLabel.vue';
    import PrimaryButton from '@/Components/Atoms/Button/PrimaryButton.vue';
    import TextInput from '@/Components/Atoms/Form/TextInput.vue';
    import { Head, Link, useForm } from '@inertiajs/vue3';
    import MainPanel from "@/Components/Atoms/MainPanel.vue";

    const props = defineProps<{
        locale: string,
        locales: {[name: string]: {name: string, flag: string}},
    }>();

    const form = useForm({
        ship_name: '',
        character_name: '',
        email: '',
        password: '',
        password_confirmation: '',
        locale: props.locale,
    });

    const submit = () => {
        form.post(route('register'), {
            onFinish: () => {
                form.reset('password', 'password_confirmation');
            },
        });
    };
</script>

<template>
    <guest-layout>
        <Head title="Register" />

        <main-panel centered>
            <form @submit.prevent="submit" class="border border-ui-orange-500 py-2 px-3 border-x-8 w-1/3 space-y-3">
                <fieldset class="space-y-3">
                    <legend class="font-bold text-white">Character Details</legend>

                    <div>
                        <input-label for="name" :value="__('new.l_new_pname')" />
                        <text-input
                            id="name"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.character_name"
                            required

                        />
                        <input-error class="mt-2" :message="form.errors.character_name" />
                    </div>

                    <div>
                        <input-label for="ship_name" :value="__('new.l_new_shipname')" />
                        <text-input
                            id="ship_name"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.ship_name"
                        />
                        <input-error class="mt-2" :message="form.errors.ship_name" />
                    </div>

                </fieldset>

                <div class="mt-4">
                    <input-label for="locale" :value="__('options.l_opt_lang')" />
                    <select
                        id="locale"
                        name="locale"
                        v-model="form.locale"
                        class="border-orange-500 bg-gray-900 focus:border-orange-500 focus:ring-orange-500 rounded-sm shadow-sm w-full"
                    >
                        <option v-for="(locale, id) in locales" :value="id">
                            {{ locale.name }}
                        </option>
                    </select>
                    <input-error class="mt-2" :message="form.errors.locale" />
                </div>

                <fieldset class="space-y-3">
                    <legend class="font-bold text-white">Login Credentials</legend>

                    <div class="mt-4">
                        <input-label for="email" :value="__('login.l_login_email')" />
                        <text-input
                            id="email"
                            type="email"
                            class="mt-1 block w-full"
                            v-model="form.email"
                            required
                            autocomplete="username"
                        />
                        <input-error class="mt-2" :message="form.errors.email" />
                    </div>

                    <div class="mt-4">
                        <input-label for="password" value="Password" />

                        <text-input
                            id="password"
                            type="password"
                            class="mt-1 block w-full"
                            v-model="form.password"
                            required
                            autocomplete="new-password"
                        />

                        <input-error class="mt-2" :message="form.errors.password" />
                    </div>

                    <div class="mt-4">
                        <input-label for="password_confirmation" value="Confirm Password" />

                        <text-input
                            id="password_confirmation"
                            type="password"
                            class="mt-1 block w-full"
                            v-model="form.password_confirmation"
                            required
                            autocomplete="new-password"
                        />

                        <input-error class="mt-2" :message="form.errors.password_confirmation" />
                    </div>
                </fieldset>



                <div class="flex items-center justify-end mt-4">
                    <Link :href="route('login')" class="underline text-sm hover:text-white">
                    Already registered?
                    </Link>

                    <primary-button class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Register
                    </primary-button>
                </div>
            </form>
        </main-panel>
    </guest-layout>
</template>
