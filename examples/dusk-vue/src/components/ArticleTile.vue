<script setup lang="ts">
import { t } from "../i18n";
import { mediaUrl } from "../api";
import type { Data } from "../api";
defineProps<{ article: Data<"Article"> }>();
</script>
<template>
    <RouterLink
        class="article-tile"
        :to="`/community/article/${article.slug}`"
        :style="{
            backgroundImage: `url('${mediaUrl(article.image)}')`,
        }"
        ><div>
            <h2>{{ article.title }}</h2>
            <span>{{ article.author?.username }}</span
            ><small> {{ t("Read more →") }} </small>
        </div></RouterLink
    >
</template>
