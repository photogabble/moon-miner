<script setup lang="ts">
import {useModal} from "@/Composables/useModal";
const {isOpen, closeModal} = useModal();
</script>

<template>
    <Transition>
        <div v-if="isOpen" @click="closeModal" class="absolute w-full h-full top-0 left-0 bg-ui-grey-900/80 z-40"/>
    </Transition>
</template>

<style scoped>
.v-enter-active,
.v-leave-active {
    transition: opacity 250ms ease;
}

.v-enter-from,
.v-leave-to {
    opacity: 0;
}
</style>
