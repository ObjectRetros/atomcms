<script setup lang="ts">
import { t, locale, setLocale } from "./i18n";
import { computed, onMounted, watch, ref } from "vue";
import { useRouter, useRoute } from "vue-router";
import { initialize, session } from "./state";
import { request, safeUrl } from "./api";
import { usePage } from "./usePage";
import Notice from "./components/Notice.vue";
const router = useRouter(),
    route = useRoute();
watch(
    () => [session.ready, session.user, session.restriction],
    () => {
        if (!session.ready) return;
        if (
            session.restriction === "account_banned" &&
            route.path !== "/banned" &&
            !route.path.startsWith("/help-center")
        ) {
            void router.replace("/banned");
            return;
        }
        if (
            session.restriction === "maintenance" &&
            session.user &&
            !["/login", "/two-factor-challenge", "/maintenance"].includes(
                route.path
            )
        ) {
            void router.replace("/maintenance");
            return;
        }
        if (
            (session.user?.requires_two_factor ||
                session.restriction === "two_factor_required") &&
            route.path !== "/user/settings/two-factor"
        )
            void router.replace("/user/settings/two-factor");
        else if (route.meta.auth && !session.user)
            void router.replace({
                path: "/login",
                query: { next: route.fullPath },
            });
        else if (
            session.user &&
            ["/", "/login", "/register"].includes(route.path)
        )
            void router.replace("/user/me");
    }
);
const { busy, error, run } = usePage();
const hotel = computed(() => session.bootstrap.hotel_name || "Atom Hotel");
const logoFailed = ref(false);
const logo = computed(() =>
    logoFailed.value
        ? "/assets/images/logo.png"
        : safeUrl(session.bootstrap.assets?.logo) || "/assets/images/logo.png"
);
watch(
    () => session.bootstrap.assets?.logo,
    () => {
        logoFailed.value = false;
    }
);
watch(
    hotel,
    (value) => {
        document.title = value;
    },
    { immediate: true }
);
const community = [
    ["Staff", "/community/staff"],
    ["Teams", "/community/teams"],
    ["Team applications", "/community/team-applications"],
    ["Staff applications", "/community/staff-applications"],
    ["Rare values", "/values"],
    ["Help center", "/help-center"],
    ["Photos", "/community/photos"],
];
async function logout() {
    await run(async () => {
        await request("/logout", "POST");
        session.user = null;
        session.bootstrap.viewer = null;
        session.restriction = "";
        await router.push("/");
    });
}
onMounted(() =>
    run(async () => {
        await setLocale(locale.value);
        await initialize();
    })
);
async function changeLocale(event: Event) {
    await run(async () => {
        await setLocale((event.target as HTMLSelectElement).value);
        await initialize();
    });
}
</script>
<template>
    <div class="site-shell">
        <header class="nav-header">
            <nav class="nav-inner" :aria-label="t('Main navigation')">
                <RouterLink
                    class="brand"
                    :to="session.user ? '/user/me' : '/'"
                    :aria-label="`${hotel} home`"
                    ><img :src="logo" :alt="hotel" @error="logoFailed = true"
                /></RouterLink>
                <div class="nav-links">
                    <details class="nav-menu">
                        <summary>
                            <img
                                src="/assets/images/dusk/community_icon.png"
                                alt=""
                            />
                            {{ t("Community") }}
                        </summary>
                        <div class="dropdown">
                            <RouterLink
                                v-for="[label, path] in community"
                                :key="path"
                                :to="path"
                            >
                                {{ t(label) }}
                            </RouterLink>
                        </div>
                    </details>
                    <RouterLink to="/leaderboard"
                        ><img
                            src="/assets/images/dusk/leaderboard_icon.png"
                            alt=""
                        />
                        {{ t("Leaderboard") }}
                    </RouterLink>
                    <RouterLink to="/community/articles"
                        ><img src="/assets/images/dusk/news_icon.png" alt="" />
                        {{ t("News") }}
                    </RouterLink>
                    <RouterLink to="/shop"
                        ><img src="/assets/images/dusk/store_icon.png" alt="" />
                        {{ t("Store") }}
                    </RouterLink>
                    <details class="nav-menu">
                        <summary>
                            <img
                                src="/assets/images/dusk/home_icon.png"
                                alt=""
                            />
                            {{ t("Home") }}
                        </summary>
                        <div class="dropdown">
                            <template v-if="session.user"
                                ><RouterLink to="/user/me">{{
                                    session.user.username
                                }}</RouterLink
                                ><RouterLink
                                    :to="`/home/${session.user.username}`"
                                >
                                    {{ t("My Home") }} </RouterLink
                                ><RouterLink to="/draw-badge">
                                    {{ t("Badge Drawer") }} </RouterLink
                                ><RouterLink to="/user/settings/account">
                                    {{ t("Account settings") }} </RouterLink
                                ><button @click="logout">
                                    {{ t("Logout") }}
                                </button></template
                            >
                            <template v-else
                                ><RouterLink to="/login">
                                    {{ t("Login") }} </RouterLink
                                ><RouterLink to="/register">
                                    {{ t("Register") }}
                                </RouterLink></template
                            >
                        </div>
                    </details>
                </div>
            </nav>
        </header>
        <div class="sub-header container">
            <div class="inline">
                <select
                    :value="locale"
                    :disabled="busy"
                    :aria-label="t('Language')"
                    class="locale-selector"
                    @change="changeLocale"
                >
                    <option
                        v-for="language in session.bootstrap.locales || [
                            { name: 'English', locale: 'en' },
                        ]"
                        :key="language.locale"
                        :value="language.locale"
                    >
                        {{ language.name }}
                    </option>
                </select>
                <span class="online-dot"></span
                ><span
                    >{{ session.bootstrap.online_count ?? 0 }}
                    {{ t("online") }} </span
                ><a
                    v-if="safeUrl(undefined)"
                    :href="safeUrl(undefined)"
                    target="_blank"
                    rel="noopener"
                >
                    {{ t("Discord") }}
                </a>
            </div>
            <div class="inline">
                <RouterLink
                    to="/help-center/rules"
                    :aria-label="t('Hotel rules')"
                    ><img
                        src="/assets/images/dusk/rules_icon.png"
                        alt="" /></RouterLink
                ><RouterLink to="/logo-generator">
                    {{ t("Logo generator") }} </RouterLink
                ><a
                    v-if="
                        session.user &&
                        session.bootstrap.viewer?.can_access_housekeeping &&
                        safeUrl(session.bootstrap.housekeeping_url)
                    "
                    :href="safeUrl(session.bootstrap.housekeeping_url)"
                >
                    {{ t("Housekeeping") }}
                </a>
            </div>
        </div>
        <div class="site-bg" aria-hidden="true"></div>
        <main class="container main-content">
            <Notice :error="error" />
            <div v-if="!session.ready && !error" class="loading" role="status">
                {{ t("Opening the hotel…") }}
            </div>
            <button v-else-if="!session.ready" @click="run(initialize)">
                {{ t("Try again") }}</button
            ><RouterView v-else :key="$route.fullPath" />
        </main>
    </div>
    <footer>
        © {{ new Date().getFullYear() }} {{ hotel }}
        {{
            t(
                "is a not for profit educational project & is in no way affiliated with Sulake Corporation Oy."
            )
        }}
    </footer>
</template>
