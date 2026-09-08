<script setup lang="ts">
defineProps<{
    error?: string;
    success?: string;
    fields?: Record<string, string[]>;
}>();
</script>
<template>
    <div v-if="error" class="notice error" role="alert">
        {{ error }}
        <ul v-if="fields && Object.keys(fields).length">
            <li v-for="(messages, field) in fields" :key="field">
                {{ messages.join(" ") }}
            </li>
        </ul>
    </div>
    <div v-if="success" class="notice success" role="status">{{ success }}</div>
</template>
