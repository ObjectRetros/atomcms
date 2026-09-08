<script setup lang="ts">
import { t } from "../i18n";
import { computed, onMounted, reactive, ref } from "vue";
import { useRoute } from "vue-router";
import { api, request, ApiError } from "../api";
import type { RecordData, Data } from "../api";
import { session, refreshUser } from "../state";
import { usePage } from "../usePage";
import Card from "../components/Card.vue";
import Notice from "../components/Notice.vue";
import Captcha from "../components/Captcha.vue";
import DOMPurify from "dompurify";
const captcha = ref<Record<string, string>>({});
const route = useRoute();
const tab = computed(() => String(route.params.tab || "account"));
const { busy, error, fields, success, run } = usePage();
const form = reactive({
    mail: session.user?.mail || "",
    motto: session.user?.motto || "",
    username: session.user?.username || "",
    current_password: "",
    password: "",
    password_confirmation: "",
    code: "",
});
const twoFactor = ref<Partial<Data<"TwoFactor">>>({}),
    sessions = ref<Data<"Session">[]>([]),
    passwordRequired = ref(false);
const tabs = [
    ["Account settings", "account"],
    ["Change password", "password"],
    ["Two-factor authentication", "two-factor"],
    ["Session logs", "session-logs"],
];
const qr = computed(() =>
    DOMPurify.sanitize(twoFactor.value.qr_code || "", {
        USE_PROFILES: { svg: true },
        FORBID_TAGS: ["foreignObject", "style"],
        FORBID_ATTR: ["style"],
    })
);
async function loadTwoFactor() {
    try {
        twoFactor.value = (await api<Data<"TwoFactor">>("/me/two-factor")).data;
        passwordRequired.value = false;
    } catch (failure) {
        if (failure instanceof ApiError && failure.status === 423)
            passwordRequired.value = true;
        else throw failure;
    }
}
onMounted(() =>
    run(async () => {
        if (tab.value === "two-factor") await loadTwoFactor();
        if (tab.value === "session-logs")
            sessions.value = (
                await api<Data<"Session">[]>("/me/sessions")
            ).data;
    })
);
async function save() {
    await run(async () => {
        if (tab.value === "account") {
            const body: RecordData = {
                mail: form.mail,
                motto: form.motto,
                current_password: form.current_password,
                ...captcha.value,
            };
            if (session.user?.can_change_name) body.username = form.username;
            await api("/me/account", "PUT", body);
            await refreshUser();
        } else {
            await api("/me/password", "PUT", {
                current_password: form.current_password,
                password: form.password,
                password_confirmation: form.password_confirmation,
                ...captcha.value,
            });
            form.password = "";
            form.password_confirmation = "";
        }
        form.current_password = "";
    }, "Your settings have been updated.");
}
async function confirmPassword() {
    await run(async () => {
        await request("/user/confirm-password", "POST", {
            password: form.current_password,
        });
        form.current_password = "";
        await loadTwoFactor();
    });
}
async function changeTwoFactor(action: "enable" | "confirm" | "disable") {
    await run(
        async () => {
            const endpoint = "/user/settings/two-factor-authentication";
            if (action === "confirm") {
                await request(`${endpoint}/confirm`, "POST", {
                    code: form.code,
                });
                form.code = "";
                await refreshUser();
            } else
                await request(
                    endpoint,
                    action === "enable" ? "POST" : "DELETE",
                    { current_password: form.current_password }
                );
            form.current_password = "";
            await loadTwoFactor();
            await refreshUser();
        },
        action === "confirm"
            ? "Two-factor authentication is enabled. Keep your recovery codes safe."
            : action === "disable"
            ? "Two-factor authentication is disabled."
            : ""
    );
}
</script>
<template>
    <div class="sidebar-layout">
        <aside class="sidebar-nav">
            <RouterLink
                v-for="[label, path] in tabs"
                :key="path"
                :to="`/user/settings/${path}`"
                ><img
                    src="/assets/images/dusk/usersettings_wheel_icon.png"
                    alt=""
                />
                {{ t(label) }}
            </RouterLink>
        </aside>
        <Card
            :title="
                tabs.find((item) => item[1] === tab)?.[0] || 'Account settings'
            "
            icon="usersettings_wheel_icon"
            ><Notice :error="error" :fields="fields" :success="success" />
            <form
                v-if="tab === 'account' || tab === 'password'"
                @submit.prevent="save"
            >
                <template v-if="tab === 'account'"
                    ><label>
                        {{ t("Email address") }}
                        <input
                            v-model="form.mail"
                            type="email"
                            autocomplete="email"
                            required /></label
                    ><label v-if="session.user?.can_change_name">
                        {{ t("Username") }}
                        <input
                            v-model="form.username"
                            autocomplete="username"
                            required /></label
                    ><label>
                        {{ t("Motto") }} <input v-model="form.motto" /></label
                ></template>
                <label>
                    {{ t("Current password") }}
                    <input
                        v-model="form.current_password"
                        type="password"
                        autocomplete="current-password"
                        :required="tab === 'password'"
                    /><small v-if="tab === 'account'">
                        {{ t("Required when changing your email address.") }}
                    </small></label
                >
                <template v-if="tab === 'password'"
                    ><label>
                        {{ t("New password") }}
                        <input
                            v-model="form.password"
                            type="password"
                            autocomplete="new-password"
                            required /></label
                    ><label>
                        {{ t("Confirm new password") }}
                        <input
                            v-model="form.password_confirmation"
                            type="password"
                            autocomplete="new-password"
                            required /></label
                ></template>
                <Captcha v-model="captcha" :busy="busy" />
                <div>
                    <button :disabled="busy">{{ t("Update settings") }}</button>
                </div>
            </form>
            <template v-else-if="tab === 'two-factor'">
                <p>
                    {{
                        t(
                            "Protect your account with a code from your authenticator app every time you log in."
                        )
                    }}
                </p>
                <form v-if="passwordRequired" @submit.prevent="confirmPassword">
                    <p>
                        {{
                            t(
                                "Confirm your password to view your security settings."
                            )
                        }}
                    </p>
                    <label>
                        {{ t("Current password") }}
                        <input
                            v-model="form.current_password"
                            type="password"
                            autocomplete="current-password"
                            required
                    /></label>
                    <div>
                        <button :disabled="busy">
                            {{ t("Confirm password") }}
                        </button>
                    </div>
                </form>
                <template v-else>
                    <template v-if="twoFactor.qr_code"
                        ><p>
                            {{
                                t(
                                    "Scan this QR code with your authenticator app, then enter the six-digit code below."
                                )
                            }}
                        </p>
                        <div class="qr" v-html="qr"></div
                    ></template>
                    <template v-if="twoFactor.recovery_codes?.length"
                        ><p>
                            {{
                                t(
                                    "Save these recovery codes somewhere safe. Each can be used once if you lose access to your authenticator."
                                )
                            }}
                        </p>
                        <ul class="recovery-codes">
                            <li
                                v-for="code in twoFactor.recovery_codes"
                                :key="code"
                            >
                                {{ code }}
                            </li>
                        </ul></template
                    >
                    <form
                        v-if="twoFactor.qr_code && !twoFactor.enabled"
                        @submit.prevent="changeTwoFactor('confirm')"
                    >
                        <label>
                            {{ t("Authentication code") }}
                            <input
                                v-model="form.code"
                                inputmode="numeric"
                                autocomplete="one-time-code"
                                required
                        /></label>
                        <div>
                            <button :disabled="busy">
                                {{ t("Verify and enable") }}
                            </button>
                        </div>
                    </form>
                    <form
                        v-else
                        @submit.prevent="
                            changeTwoFactor(
                                twoFactor.enabled ? 'disable' : 'enable'
                            )
                        "
                    >
                        <p v-if="twoFactor.enabled" class="notice success">
                            {{ t("Two-factor authentication is enabled.") }}
                        </p>
                        <label>
                            {{ t("Current password") }}
                            <input
                                v-model="form.current_password"
                                type="password"
                                autocomplete="current-password"
                                required
                        /></label>
                        <div>
                            <button
                                :class="{ danger: twoFactor.enabled }"
                                :disabled="busy"
                            >
                                {{
                                    t(
                                        twoFactor.enabled
                                            ? "Disable 2FA"
                                            : "Activate 2FA"
                                    )
                                }}
                            </button>
                        </div>
                    </form>
                </template>
            </template>
            <template v-else-if="tab === 'session-logs'"
                ><p>{{ t("Recent sign-ins to your account.") }}</p>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>{{ t("Device") }}</th>
                                <th>{{ t("IP address") }}</th>
                                <th>{{ t("Last active") }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in sessions" :key="index">
                                <td>
                                    {{
                                        item.agent?.browser ||
                                        item.agent?.platform ||
                                        t("Unknown device")
                                    }}
                                    <span
                                        v-if="item.is_current_device"
                                        class="badge"
                                    >
                                        {{ t("This device") }}
                                    </span>
                                </td>
                                <td>{{ item.ip_address }}</td>
                                <td>
                                    {{ item.last_active }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p v-if="!sessions.length" class="empty">
                    {{ t("No session history is available.") }}
                </p></template
            >
        </Card>
    </div>
</template>
