<script setup lang="ts">
import { computed } from "vue";
import DOMPurify from "dompurify";
const props = defineProps<{ html?: string | null }>();
const safe = computed(() =>
    DOMPurify.sanitize(props.html || "", {
        USE_PROFILES: { html: true },
        FORBID_TAGS: ["form", "input", "button", "style"],
        FORBID_ATTR: ["style"],
    })
);
</script>
<template><div class="rich-text" v-html="safe"></div></template>
