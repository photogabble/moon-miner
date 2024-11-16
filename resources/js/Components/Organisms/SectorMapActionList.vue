<script setup lang="ts">
import {Link as InertiaLink} from "@inertiajs/vue3";
import type {SectorMapSystemResource} from "@/types/resources/sector";

defineProps<{
    system: SectorMapSystemResource,
}>();
</script>

<template>
    <div class="absolute top-10 left-10 border border-ui-orange-500 p-0.5 w-32 text-sm bg-ui-grey-900">
        <h3 class="bg-white w-full text-ui-grey-900 px-1">{{ system.name }} [{{ system.sector_id }}]</h3>
        <ul class="space-y-1 my-1">
            <li v-for="action in system.actions">
                <inertia-link
                    class="w-full block hover:text-ui-yellow"
                    :href="action.href"
                    :method="action?.method ?? 'get'"
                    :preserve-state="false"
                >{{ action.title }}</inertia-link>
            </li>
        </ul>
    </div>
</template>
