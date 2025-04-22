<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    required: true,
});

const select = ref(null);

onMounted(() => {
    if (select.value.hasAttribute('autofocus')) {
        select.value.focus();
    }
});

defineExpose({ focus: () => select.value.focus() });
</script>

<template>
    <select
        class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 w-full"
        v-model="model"
        ref="select"
    >
        <slot></slot>
    </select>
</template>