<script setup lang="ts">
import { t } from "../i18n";
import { computed, onMounted, ref } from "vue";
import { api, safeUrl } from "../api";
import type { Data } from "../api";
import { avatar, session } from "../state";
import { usePage } from "../usePage";
import Notice from "./Notice.vue";
const props = defineProps<{
    username: string;
    item: Data<"HomeItem">;
    visitor: boolean;
    editing: boolean;
}>();
const { busy, error, run } = usePage();
const widget = ref<Data<"HomeWidget"> | null>(null),
    message = ref(""),
    rating = ref(5);
const pagination = computed(() =>
    widget.value && ["my-friends", "my-badges"].includes(widget.value.type)
        ? (
              widget.value as Extract<
                  Data<"HomeWidget">,
                  { type: "my-friends" | "my-badges" }
              >
          ).content
        : null
);
async function load(page = 1) {
    widget.value = (
        await api<Data<"HomeWidget">>(
            `/homes/${props.username}/widgets/${props.item.id}?friends_page=${page}&badges_page=${page}`
        )
    ).data;
}
onMounted(() => run(() => load()));
async function post() {
    await run(async () => {
        await api(`/homes/${props.username}/messages`, "POST", {
            content: message.value,
        });
        message.value = "";
        await load();
    });
}
async function rate() {
    await run(async () => {
        await api(`/homes/${props.username}/ratings`, "POST", {
            rating: Number(rating.value),
        });
        await load();
    });
}
</script>
<template>
    <Notice :error="error" /><template v-if="widget">
        <p v-if="widget.supported === false" class="muted">
            {{ t("This widget is not available for this hotel.") }}
        </p>
        <template v-else-if="widget.type === 'my-profile'"
            ><img
                :src="avatar(widget.content)"
                :alt="widget.content?.username"
            /><strong>{{ widget.content?.username }}</strong>
            <p>{{ widget.content?.motto }}</p></template
        >
        <template v-else-if="widget.type === 'my-rooms'"
            ><div v-for="room in widget.content" :key="room.id">
                <strong>{{ room.name }}</strong>
                <p class="muted">{{ room.description }}</p>
            </div>
            <p v-if="!widget.content?.length">
                {{ t("No rooms yet.") }}
            </p></template
        >
        <template v-else-if="widget.type === 'my-badges'"
            ><div class="inline">
                <img
                    v-for="badge in widget.content?.items"
                    :key="badge.code"
                    :src="
                        safeUrl(
                            `${session.bootstrap.assets?.badge || ''}/${
                                badge.code
                            }.gif`
                        )
                    "
                    :alt="badge.code"
                /></div
        ></template>
        <template v-else-if="widget.type === 'my-friends'"
            ><RouterLink
                v-for="friend in widget.content.items.filter(
                    (friend) => friend !== null
                )"
                :key="friend.id"
                class="user-row"
                :to="`/home/${friend.username}`"
                ><img :src="avatar(friend)" alt="" /><strong>{{
                    friend.username
                }}</strong></RouterLink
            ></template
        >
        <template v-else-if="widget.type === 'my-rating'"
            ><p>
                ★ {{ Number(widget.content?.average || 0).toFixed(1) }} ·
                {{ widget.content?.total || 0 }} {{ t("ratings") }}
            </p>
            <form v-if="visitor && !editing" @submit.prevent="rate">
                <label>
                    {{ t("Your rating") }}
                    <select v-model="rating">
                        <option v-for="value in 5" :key="value" :value="value">
                            {{ value }} {{ t("stars") }}
                        </option>
                    </select></label
                ><button class="small" :disabled="busy">
                    {{ t("Rate home") }}
                </button>
            </form></template
        >
        <template v-else-if="widget.type === 'my-guestbook'"
            ><article
                v-for="entry in widget.content"
                :key="entry.id"
                class="comment"
            >
                <strong>{{ entry.author?.username }}</strong>
                <p>{{ entry.content }}</p>
            </article>
            <form v-if="visitor && !editing" @submit.prevent="post">
                <label>
                    {{ t("Leave a message") }}
                    <textarea
                        v-model="message"
                        required
                        maxlength="2000"
                    ></textarea></label
                ><button class="small" :disabled="busy">
                    {{ t("Post message") }}
                </button>
            </form></template
        >
        <div v-if="pagination && pagination.last_page > 1" class="pagination">
            <button
                class="small"
                :disabled="busy || pagination.current_page === 1"
                @click="run(() => load((pagination?.current_page || 1) - 1))"
            >
                ←</button
            ><span
                >{{ pagination.current_page }}/{{ pagination.last_page }}</span
            ><button
                class="small"
                :disabled="
                    busy || pagination.current_page === pagination.last_page
                "
                @click="run(() => load((pagination?.current_page || 1) + 1))"
            >
                →
            </button>
        </div>
    </template>
</template>
