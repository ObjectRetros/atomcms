<script setup lang="ts">
defineProps<{ title: string; subtitle?: string | null; icon?: string }>();
</script>
<template>
    <section class="card">
        <header class="card-heading">
            <img v-if="icon" :src="`/assets/images/dusk/${icon}.png`" alt="" />
            <div>
                <h2>{{ title }}</h2>
                <p v-if="subtitle">{{ subtitle }}</p>
            </div>
        </header>
        <div class="card-body"><slot /></div>
    </section>
</template>
