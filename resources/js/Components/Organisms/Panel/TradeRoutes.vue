<script setup lang="ts">
import SidebarPanel from "@/Components/Atoms/SidebarPanel.vue";
</script>

<template>
    <sidebar-panel>
        <template #heading>
            <span class="text-white">Trade Routes</span>
        </template>
        <section class="flex flex-col">
            <ul>
                <!-- TODO -->
                <li class="flex"><span class="flex-grow">None</span> <a href="#" class="self-end">[Trade Control]</a></li>
            </ul>
        </section>
    </sidebar-panel>
</template>
