<script setup lang="ts">
import { t } from "../i18n";
import { onMounted, ref } from "vue";
import { useRoute } from "vue-router";
import { api, safeUrl, ApiError } from "../api";
import type { Data } from "../api";
import { session } from "../state";
import { usePage } from "../usePage";
import Notice from "../components/Notice.vue";
const route = useRoute(),
    { busy, error, run } = usePage(),
    url = ref(""),
    voteUrl = ref("");
const flashRequested = route.params.client === "flash";
async function launch() {
    voteUrl.value = "";
    try {
        const result = await api<Data<"ClientLaunch">>(
            "/client/launch",
            "POST",
            { client: "nitro" }
        );
        url.value = safeUrl(result.data.url);
        if (!url.value)
            throw new Error("The hotel client is not configured yet.");
    } catch (failure) {
        if (failure instanceof ApiError && failure.code === "vote_required")
            voteUrl.value = safeUrl(failure.voteUrl);
        throw failure;
    }
}
onMounted(() => {
    if (!flashRequested) void run(launch);
});
</script>
<template>
    <div class="section-heading">
        <h1>{{ session.bootstrap.hotel_name }}</h1>
        <RouterLink to="/user/me"> {{ t("← Back to my account") }} </RouterLink>
    </div>
    <div v-if="flashRequested" class="card">
        <div class="card-body">
            <h2>{{ t("Flash client unavailable in this browser") }}</h2>
            <p>
                {{
                    t(
                        "This frontend supports the Nitro browser client. The legacy Flash client requires a separate supported Flash runtime."
                    )
                }}
            </p>
            <RouterLink class="button" to="/game/nitro">{{
                t("Open Nitro")
            }}</RouterLink>
        </div>
    </div>
    <Notice :error="error" /><a
        v-if="voteUrl"
        class="button gold"
        :href="voteUrl"
        target="_blank"
        rel="noopener noreferrer"
        >{{ t("Vote for the hotel") }}</a
    >
    <p v-if="busy" class="loading">{{ t("Connecting to the hotel…") }}</p>
    <iframe
        v-if="url"
        class="client-frame"
        :src="url"
        :title="t('Hotel game client')"
        allow="fullscreen; autoplay"
        referrerpolicy="no-referrer"
    ></iframe
    ><button v-else-if="!busy && !flashRequested" @click="run(launch)">
        {{ t("Try again") }}
    </button>
</template>
