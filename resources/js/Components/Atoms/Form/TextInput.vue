<script setup lang="ts">
import { onMounted, ref } from 'vue';

const model = defineModel<string>({ required: true });

const input = ref<HTMLInputElement | null>(null);

onMounted(() => {
    if (input.value?.hasAttribute('autofocus')) {
        input.value?.focus();
    }
});

defineExpose({ focus: () => input.value?.focus() });
</script>

<template>
    <input
        class="border-orange-500 bg-gray-900 focus:border-orange-500 focus:ring-orange-500 rounded-sm shadow-sm w-full"
        v-model="model"
        ref="input"
    />
</template>
