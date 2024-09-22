<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import GuestLayout from "@/Layouts/GuestLayout.vue";
import Terminal from "@/Components/Atoms/Terminal.vue";
import MainPanel from "@/Components/Atoms/MainPanel.vue";

defineProps<{
    canLogin?: boolean;
    canRegister?: boolean;
    laravelVersion: string;
    phpVersion: string;
}>();
</script>

<template>
    <Head title="Welcome" />
    <guest-layout>
        <template #sidebar>
            <div class="flex-grow h-20 flex flex-col">
                <!-- needs an initial height in order to grow to available screen height -->
                <div class="uppercase border-y border-ui-orange-500/50 p-2 mb-4 border-partway-tl" style="--border-part-t-height:25%;">
                    <span class="text-white">Welcome To &middot;</span> Moon Miner
                </div>

                <div class="space-y-5 px-2 overflow-y-auto">
                    <p>
                        Welcome to Space Mines a modernised fork of Black Nova Traders.
                    </p>
                </div>
            </div>
        </template>

        <main-panel class="relative" centered>
            <div class="scanlines w-full h-full z-10"/>

            <terminal class="absolute top-2 left-2 z-0" />
            <div class="z-20 border border-ui-orange-500 p-2 border-x-8">
                &rarr;&nbsp;<Link :href="route('login')" class="text-white hover:underline">{{ __('login.l_login_title')}}</Link>&nbsp;// <Link :href="route('register')" class="text-white hover:underline">{{ __('index.l_new_player') }}</Link> &larr;
            </div>
        </main-panel>
    </guest-layout>
</template>

<style scoped>
.scanlines{
    background: linear-gradient(to bottom,rgba(255,255,255,0),rgba(255,255,255,0) 50%,rgba(0,0,0,.2) 70%,rgba(0,0,0,.6));
    background-size: 100% 0.2rem;
    position: absolute;
}
</style>
