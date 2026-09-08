<script setup lang="ts">
import { t } from "../i18n";
import { computed, onMounted, ref } from "vue";
import { useRoute } from "vue-router";
import { api, safeUrl } from "../api";
import type { Data } from "../api";
import { avatar } from "../state";
import { usePage } from "../usePage";
import Card from "../components/Card.vue";
import Notice from "../components/Notice.vue";
import Captcha from "../components/Captcha.vue";
const captcha = ref<Record<string, string>>({});
import RichText from "../components/RichText.vue";
const route = useRoute();
const section = String(route.meta.section || route.params.section || "staff");
const { busy, error, fields, success, run } = usePage();
const groups = ref<Data<"StaffGroup">[]>([]),
    boards = ref<Partial<Data<"Leaderboards">>>({}),
    photos = ref<Data<"Photo">[]>([]),
    positions = ref<Data<"Position">[]>([]),
    position = ref<Data<"Position"> | null>(null),
    application = ref("");
const page = ref(1),
    lastPage = ref(1);
const titles: Record<string, string> = {
    staff: "Meet the staff",
    teams: "Our teams",
    leaderboard: "Leaderboard",
    photos: "Hotel photos",
    "staff-applications": "Staff applications",
    "team-applications": "Team applications",
};
const isApplications = computed(() => section.endsWith("applications"));
const boardNames: Record<string, string> = {
    credits: "Credits",
    duckets: "Duckets",
    diamonds: "Diamonds",
    mostOnline: "Time online",
    respectsReceived: "Respects received",
    achievementScores: "Achievement scores",
};
async function load(nextPage = 1) {
    if (section === "staff" || section === "teams")
        groups.value = (await api<Data<"StaffGroup">[]>(`/${section}`)).data;
    if (section === "leaderboard")
        boards.value = (await api<Data<"Leaderboards">>("/leaderboards")).data;
    if (section === "photos") {
        const result = await api<Data<"Photo">[]>(`/photos?page=${nextPage}`);
        photos.value = result.data;
        page.value = result.meta?.current_page || nextPage;
        lastPage.value = result.meta?.last_page || 1;
    }
    if (isApplications.value) {
        if (route.params.id)
            position.value = (
                await api<Data<"Position">>(
                    `/applications/${encodeURIComponent(
                        String(route.params.id)
                    )}`
                )
            ).data;
        else
            positions.value = (
                await api<Data<"Position">[]>(
                    `/applications?kind=${
                        section === "staff-applications" ? "rank" : "team"
                    }`
                )
            ).data;
    }
}
onMounted(() => run(() => load()));
async function apply() {
    await run(async () => {
        await api(`/applications/${position.value?.id}`, "POST", {
            content: application.value,
            ...captcha.value,
        });
        application.value = "";
    }, "Your application has been submitted.");
}
</script>
<template>
    <div class="page-intro">
        <h1>{{ t(titles[section] || "Community") }}</h1>
        <p>
            {{
                t(
                    section === "staff"
                        ? "The people who keep our community safe and welcoming."
                        : section === "photos"
                        ? "Small moments from life in the hotel."
                        : "Get involved in your hotel community."
                )
            }}
        </p>
    </div>
    <Notice :error="error" :fields="fields" :success="success" />
    <div v-if="section === 'staff' || section === 'teams'" class="stack">
        <section v-for="group in groups" :key="group.id">
            <div class="section-heading">
                <div>
                    <h2>{{ group.name }}</h2>
                    <p class="muted">{{ group.description }}</p>
                </div>
            </div>
            <div class="grid four-columns">
                <Card
                    v-for="user in group.users"
                    :key="user.id"
                    class="staff-card"
                    :title="user.username"
                    ><RouterLink :to="`/home/${user.username}`"
                        ><img :src="avatar(user)" :alt="user.username"
                    /></RouterLink>
                    <p>{{ user.motto }}</p>
                    <span class="badge">{{
                        t(user.online ? "Online" : "Offline")
                    }}</span></Card
                >
            </div>
            <p v-if="!group.users?.length" class="empty">
                {{ t("There are no members in this group yet.") }}
            </p>
        </section>
        <p v-if="!busy && !groups.length" class="empty">
            {{ t("No") }} {{ section }} {{ t("groups are available yet.") }}
        </p>
    </div>
    <div v-else-if="section === 'leaderboard'" class="grid three-columns">
        <template v-for="(users, key) in boards" :key="key"
            ><Card
                v-if="users"
                :title="boardNames[String(key)] || String(key)"
                icon="leaderboard_icon"
                ><RouterLink
                    v-for="(entry, index) in users"
                    :key="entry.user.id"
                    class="user-row"
                    :to="`/home/${entry.user.username}`"
                    ><strong>{{ Number(index) + 1 }}</strong
                    ><img :src="avatar(entry.user)" alt="" />
                    <div>
                        <strong>{{ entry.user.username }}</strong>
                        <p class="muted">
                            {{ Number(entry.value).toLocaleString() }}
                        </p>
                    </div></RouterLink
                >
                <p v-if="!users.length" class="muted">
                    {{ t("No rankings yet.") }}
                </p></Card
            ></template
        >
    </div>
    <template v-else-if="section === 'photos'"
        ><div class="grid four-columns">
            <a
                v-for="photo in photos"
                :key="photo.id"
                class="photo"
                :href="safeUrl(photo.url)"
                target="_blank"
                rel="noopener"
                ><img
                    :src="safeUrl(photo.url)"
                    :alt="`Photo by ${
                        photo.author?.username || 'a hotel member'
                    }`"
                />
                <figcaption>
                    <img
                        src="/assets/images/dusk/author_camera_icon.png"
                        alt=""
                    />{{ photo.author?.username }}
                </figcaption></a
            >
        </div>
        <p v-if="!busy && !photos.length" class="empty">
            {{ t("No photos have been shared yet.") }}
        </p>
        <div v-if="lastPage > 1" class="pagination">
            <button
                :disabled="busy || page === 1"
                @click="run(() => load(page - 1))"
            >
                {{ t("Previous") }}</button
            ><span>{{ page }} / {{ lastPage }}</span
            ><button
                :disabled="busy || page === lastPage"
                @click="run(() => load(page + 1))"
            >
                {{ t("Next") }}
            </button>
        </div></template
    >
    <template v-else-if="isApplications"
        ><Card
            v-if="position"
            :title="position.name || 'Position'"
            icon="community_icon"
            ><RichText :html="position.description" />
            <form @submit.prevent="apply">
                <label>
                    {{ t("Your application") }}
                    <textarea
                        v-model="application"
                        required
                        minlength="10"
                        maxlength="5000"
                        :placeholder="
                            t(
                                'Tell us about yourself and why you\'d like to join.'
                            )
                        "
                    ></textarea></label
                ><Captcha v-model="captcha" :busy="busy" />
                <div>
                    <button :disabled="busy">
                        {{ t("Submit application") }}
                    </button>
                </div>
            </form></Card
        >
        <div v-else class="grid two-columns">
            <Card
                v-for="item in positions"
                :key="item.id"
                :title="item.name || 'Position'"
                icon="community_icon"
                ><RichText :html="item.description" /><RouterLink
                    class="button"
                    :to="`/community/${section}/${item.id}`"
                >
                    {{ t("Apply now") }}
                </RouterLink></Card
            >
            <p v-if="!busy && !positions.length" class="empty">
                {{
                    t("There are no open positions right now. Check back soon.")
                }}
            </p>
        </div></template
    >
</template>
