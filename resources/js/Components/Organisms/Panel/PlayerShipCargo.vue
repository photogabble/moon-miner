<script setup lang="ts">
import type {CurrentMax, ShipCargo} from "@/types/resources/ship";
import {useFormattedNumber} from "@/Composables/useFormattedNumber";
import SidebarPanel from "@/Components/Atoms/SidebarPanel.vue";

defineProps<{
    cargo: ShipCargo;
    energy: CurrentMax;
}>();

const numberFormatter = useFormattedNumber();
</script>

<template>
    <sidebar-panel>
        <template #heading>
            <span class="text-white flex-grow">Ship Cargo</span>
            <span>C: {{ numberFormatter(cargo.holds_used) }}/{{ numberFormatter(cargo.holds_max) }}</span>
        </template>

        <ul class="grid grid-cols-2">
            <li>Ore: <span class="text-white">{{ numberFormatter(cargo.ore) }}</span></li>
            <li>Organics: <span class="text-white">{{ numberFormatter(cargo.organics) }}</span></li>
            <li>Goods: <span class="text-white">{{ numberFormatter(cargo.goods) }}</span></li>
            <li>Colonists: <span class="text-white">{{ numberFormatter(cargo.colonists) }}</span></li>
            <!-- TODO: move energy to own panel with progress bar -->
            <li>Energy: <span class="text-white">{{ numberFormatter(energy.current) }}/{{ numberFormatter(energy.max) }}</span></li>
        </ul>
    </sidebar-panel>
</template>
