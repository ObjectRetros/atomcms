<script setup lang="ts">
import { t } from "../i18n";
import { computed, onMounted, reactive, ref } from "vue";
import { useRoute, useRouter } from "vue-router";
import { api, request, safeUrl } from "../api";
import type { Data } from "../api";
import { refreshUser, session } from "../state";
import { usePage } from "../usePage";
import Notice from "../components/Notice.vue";
import Captcha from "../components/Captcha.vue";
const captcha = ref<Record<string, string>>({});
import ArticleTile from "../components/ArticleTile.vue";
const route = useRoute(),
    router = useRouter();
const { busy, error, fields, success, run } = usePage();
const kind = computed(() => String(route.meta.authKind || "login"));
const titles: Record<string, string> = {
    login: "Login",
    register: "Create a new account",
    challenge: "Two-factor authentication",
    forgot: "Forgot your password?",
    reset: "Choose a new password",
};
const form = reactive({
    username: "",
    mail: "",
    password: "",
    password_confirmation: "",
    terms: false,
    code: "",
    recovery_code: "",
    beta_code: "",
    referral_code: String(route.params.referral || route.query.referral || ""),
});
const recovery = ref(false),
    articles = ref<Data<"Article">[]>([]);
onMounted(async () => {
    try {
        articles.value = (await api<Data<"Article">[]>("/articles")).data.slice(
            0,
            2
        );
    } catch {
        /* The auth form remains usable when public news is unavailable. */
    }
});
async function submit() {
    await run(
        async () => {
            if (kind.value === "forgot") {
                await request("/forgot-password", "POST", {
                    mail: form.mail,
                    ...captcha.value,
                });
                return;
            }
            if (kind.value === "reset") {
                await request(
                    `/reset-password/${encodeURIComponent(
                        String(route.params.token)
                    )}`,
                    "POST",
                    {
                        password: form.password,
                        password_confirmation: form.password_confirmation,
                        ...captcha.value,
                    }
                );
                await router.push("/login");
                return;
            }
            const path =
                kind.value === "challenge"
                    ? "/two-factor-challenge"
                    : `/${kind.value}`;
            const payload =
                kind.value === "challenge"
                    ? recovery.value
                        ? { recovery_code: form.recovery_code }
                        : { code: form.code }
                    : { ...form, ...captcha.value };
            const result = await request(path, "POST", payload);
            form.password = "";
            form.password_confirmation = "";
            if (result.two_factor) {
                await router.push({
                    path: "/two-factor-challenge",
                    query: route.query,
                });
                return;
            }
            session.bootstrap = (
                await api<Data<"Bootstrap">>("/bootstrap")
            ).data;
            await refreshUser();
            await router.push(
                typeof route.query.next === "string" &&
                    route.query.next.startsWith("/") &&
                    !route.query.next.startsWith("//")
                    ? route.query.next
                    : "/user/me"
            );
        },
        kind.value === "forgot"
            ? "If an account matches that email address, a password reset link will arrive shortly."
            : ""
    );
}
</script>
<template>
    <div class="grid auth-hero">
        <section class="glass-panel">
            <h1>{{ t(titles[kind] || "Login") }}</h1>
            <Notice :error="error" :fields="fields" :success="success" />
            <p
                v-if="
                    kind === 'register' &&
                    session.bootstrap.registration?.enabled === false
                "
                class="notice error"
            >
                {{
                    t(
                        "Registration is currently closed. Please check back soon."
                    )
                }}
            </p>
            <form @submit.prevent="submit">
                <label
                    v-if="kind === 'login' || kind === 'register'"
                    class="auth-avatar"
                    ><span class="sr-only"> {{ t("Username") }} </span
                    ><input
                        v-model="form.username"
                        name="username"
                        autocomplete="username"
                        :placeholder="t('Enter your username')"
                        required /><img
                        v-if="kind === 'login'"
                        src="/assets/images/dusk/ghost.png"
                        alt=""
                /></label>
                <label v-if="kind === 'register' || kind === 'forgot'"
                    ><span class="sr-only"> {{ t("Email address") }} </span
                    ><input
                        v-model="form.mail"
                        name="mail"
                        type="email"
                        autocomplete="email"
                        :placeholder="t('Enter your email')"
                        required
                /></label>
                <label v-if="['login', 'register', 'reset'].includes(kind)"
                    ><span class="sr-only"> {{ t("Password") }} </span
                    ><input
                        v-model="form.password"
                        name="password"
                        type="password"
                        :autocomplete="
                            kind === 'login'
                                ? 'current-password'
                                : 'new-password'
                        "
                        :placeholder="t('Enter your password')"
                        required
                /></label>
                <label v-if="kind === 'register' || kind === 'reset'"
                    ><span class="sr-only"> {{ t("Confirm password") }} </span
                    ><input
                        v-model="form.password_confirmation"
                        name="password_confirmation"
                        type="password"
                        autocomplete="new-password"
                        :placeholder="t('Confirm your password')"
                        required
                /></label>
                <label
                    v-if="
                        kind === 'register' &&
                        session.bootstrap.registration?.requires_beta_code
                    "
                >
                    {{ t("Beta code") }}
                    <input v-model="form.beta_code" name="beta_code" required
                /></label>
                <label v-if="kind === 'register'" class="check-label"
                    ><input
                        v-model="form.terms"
                        type="checkbox"
                        required
                    /><span>
                        {{ t("I accept the") }}
                        <RouterLink to="/help-center/rules" target="_blank">
                            {{ t("hotel terms & rules") }} </RouterLink
                        >.</span
                    ></label
                >
                <template v-if="kind === 'challenge'"
                    ><p>
                        {{
                            t(
                                "Enter the code from your authenticator app, or use one of your saved recovery codes."
                            )
                        }}
                    </p>
                    <label v-if="!recovery">
                        {{ t("Authentication code") }}
                        <input
                            v-model="form.code"
                            name="code"
                            inputmode="numeric"
                            autocomplete="one-time-code"
                            required /></label
                    ><label v-else>
                        {{ t("Recovery code") }}
                        <input
                            v-model="form.recovery_code"
                            name="recovery_code"
                            autocomplete="off"
                            required /></label
                    ><button
                        class="text-button"
                        type="button"
                        @click="recovery = !recovery"
                    >
                        {{
                            t(
                                recovery
                                    ? "Use an authentication code"
                                    : "Use a recovery code"
                            )
                        }}
                    </button></template
                >
                <Captcha v-model="captcha" :busy="busy" />
                <div class="grid two-columns">
                    <button
                        class="gold"
                        :disabled="
                            busy ||
                            (kind === 'register' &&
                                session.bootstrap.registration?.enabled ===
                                    false)
                        "
                    >
                        {{
                            t(
                                busy
                                    ? "Please wait…"
                                    : kind === "challenge"
                                    ? "Verify"
                                    : kind === "forgot"
                                    ? "Send reset link"
                                    : kind === "reset"
                                    ? "Save password"
                                    : kind === "register"
                                    ? "Register"
                                    : "Login"
                            )
                        }}</button
                    ><RouterLink
                        class="button secondary"
                        :to="kind === 'login' ? '/register' : '/login'"
                        >{{
                            t(kind === "login" ? "Register" : "Back to login")
                        }}</RouterLink
                    >
                </div>
                <RouterLink
                    v-if="kind === 'login'"
                    class="muted"
                    to="/forgot-password"
                >
                    {{ t("Forgot your password?") }}
                </RouterLink>
            </form>
        </section>
        <aside class="stack">
            <ArticleTile
                v-for="article in articles"
                :key="article.id"
                :article="article"
            />
            <div v-if="!articles.length" class="glass-panel">
                <img src="/assets/images/dusk/hotel_icon.png" alt="" />
                <h2>
                    {{ t("Welcome to") }} {{ session.bootstrap.hotel_name }}
                </h2>
                <p>
                    {{
                        session.bootstrap.hotel_description ||
                        "Meet new friends, create your own rooms, and make yourself at home."
                    }}
                </p>
            </div>
        </aside>
    </div>
    <div
        v-if="session.bootstrap.latest_photos?.length"
        class="grid four-columns"
        style="margin-top: 18px"
    >
        <a
            v-for="photo in session.bootstrap.latest_photos"
            :key="photo.id"
            class="photo"
            :href="safeUrl(photo.url)"
            target="_blank"
            rel="noopener"
            ><img
                :src="safeUrl(photo.url)"
                :alt="`Photo by ${photo.author?.username || 'a hotel member'}`"
            />
            <figcaption>
                <img
                    src="/assets/images/dusk/author_camera_icon.png"
                    alt=""
                />{{ photo.author?.username }}
            </figcaption></a
        >
    </div>
</template>
