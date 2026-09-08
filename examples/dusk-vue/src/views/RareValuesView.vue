<script setup lang="ts">
import { t } from "../i18n";
import { onMounted, ref } from "vue";
import { useRoute } from "vue-router";
import { api, safeUrl } from "../api";
import type { Data } from "../api";
import { avatar } from "../state";
import { usePage } from "../usePage";
import Card from "../components/Card.vue";
import Notice from "../components/Notice.vue";
type Category = { id: number; name: string; values: Data<"RareValue">[] };
type Value = Data<"RareValue"> & {
    holdings: { user: Data<"PublicUser"> | null; count: number }[];
};
const route = useRoute();
const { busy, error, run } = usePage(),
    search = ref(""),
    categories = ref<Category[]>([]),
    value = ref<Value | null>(null);
async function load() {
    if (route.params.id)
        value.value = (
            await api<Value>(
                `/rare-values/${encodeURIComponent(String(route.params.id))}`
            )
        ).data;
    else
        categories.value = (
            await api<Category[]>(
                `/rare-values${
                    search.value
                        ? `?search=${encodeURIComponent(search.value)}`
                        : ""
                }`
            )
        ).data;
}
onMounted(() => run(load));
</script>
<template>
    <div class="page-intro">
        <h1>{{ t("Rare values") }}</h1>
        <p>{{ t("Discover the hotel's rare furniture collection.") }}</p>
    </div>
    <Notice :error="error" /><Card
        v-if="value"
        :title="value.name"
        icon="store_icon"
        ><img
            :src="safeUrl(value.icon)"
            :alt="value.name"
            style="max-height: 100px; width: auto; image-rendering: pixelated"
        />
        <p>
            {{ value.credit_value }} {{ t("credits ·") }}
            {{ value.currency_value }}
            {{ value.currency_type }}
        </p>
        <h3>{{ t("Collectors") }}</h3>
        <RouterLink
            v-for="(holder, index) in value.holdings"
            :key="holder.user?.id || index"
            class="user-row"
            :to="holder.user ? `/home/${holder.user.username}` : '/values'"
            ><img v-if="holder.user" :src="avatar(holder.user)" alt="" />
            <div>{{ holder.user?.username || "Unknown member" }}</div>
            <span>{{ holder.count }}</span></RouterLink
        ></Card
    >
    <div v-else class="stack">
        <form class="inline" @submit.prevent="run(load)">
            <label style="flex: 1">
                {{ t("Search furniture") }}
                <input
                    v-model="search"
                    :placeholder="t('Furniture name')" /></label
            ><button :disabled="busy">{{ t("Search") }}</button>
        </form>
        <section
            v-for="category in categories"
            :key="category.id"
            class="stack"
        >
            <h2>{{ category.name }}</h2>
            <div class="grid four-columns">
                <RouterLink
                    v-for="item in category.values"
                    :key="item.id"
                    :to="`/values/${item.id}`"
                    ><Card :title="item.name"
                        ><img
                            class="package-image"
                            :src="safeUrl(item.icon)"
                            :alt="item.name"
                        />
                        <p>{{ item.credit_value }} {{ t("credits") }}</p>
                        <p class="muted">
                            {{ item.currency_value }} {{ item.currency_type }}
                        </p></Card
                    ></RouterLink
                >
            </div>
        </section>
        <p v-if="!busy && !categories.length" class="empty">
            {{ t("No furniture matches your search.") }}
        </p>
    </div>
</template>
