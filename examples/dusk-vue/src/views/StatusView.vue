<script setup lang="ts">
import { t } from "../i18n";
import { computed } from "vue";
import { useRoute } from "vue-router";
import Card from "../components/Card.vue";
const route = useRoute();
const content = computed(() =>
    route.path === "/maintenance"
        ? [
              "We will be right back",
              "The hotel is undergoing maintenance. Please check back soon.",
          ]
        : route.path === "/banned"
        ? [
              "Your account is restricted",
              "Please contact the help center if you need assistance with your account.",
          ]
        : ["Page not found", "We could not find the page you were looking for."]
);
</script>
<template>
    <Card :title="content[0] || ''" icon="exclamation-mark_icon"
        ><p>{{ content[1] }}</p>
        <div class="inline">
            <RouterLink class="button" to="/"> {{ t("Back home") }} </RouterLink
            ><RouterLink
                v-if="route.path === '/banned'"
                class="button secondary"
                to="/help-center"
            >
                {{ t("Help center") }}
            </RouterLink>
        </div></Card
    >
</template>
