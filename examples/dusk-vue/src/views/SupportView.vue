<script setup lang="ts">
import { displayDate } from "../date";
import { t } from "../i18n";
import { onMounted, reactive, ref } from "vue";
import { useRoute, useRouter } from "vue-router";
import { api, safeUrl } from "../api";
import type { Data } from "../api";
import { session } from "../state";
import { usePage } from "../usePage";
import Card from "../components/Card.vue";
import Notice from "../components/Notice.vue";
import RichText from "../components/RichText.vue";
const route = useRoute(),
    router = useRouter();
const kind = String(route.meta.support || "index"),
    id = route.params.id ? encodeURIComponent(String(route.params.id)) : "";
const { busy, error, fields, success, run } = usePage();
const categories = ref<Data<"SupportCategory">[]>([]),
    tickets = ref<Data<"Ticket">[]>([]),
    ticket = ref<Data<"Ticket"> | null>(null),
    rules = ref<Data<"RuleCategory">[]>([]);
const form = reactive({ category_id: "", title: "", content: "" }),
    reply = ref(""),
    editing = ref(false);
const page = ref(1),
    lastPage = ref(1);
async function load(nextPage = 1) {
    if (kind === "rules") {
        rules.value = (await api<Data<"RuleCategory">[]>("/rules")).data;
        return;
    }
    categories.value = (await api<Data<"SupportCategory">[]>("/support")).data;
    if (id) {
        ticket.value = (
            await api<Data<"Ticket">>(`/support/tickets/${id}`)
        ).data;
        form.category_id = String(ticket.value.category_id);
        form.title = ticket.value.title;
        form.content = ticket.value.content;
    } else if (kind !== "create") {
        const result = await api<Data<"Ticket">[]>(
            `/support/tickets?page=${nextPage}`
        );
        tickets.value = result.data;
        page.value = result.meta?.current_page || nextPage;
        lastPage.value = result.meta?.last_page || 1;
    }
}
onMounted(() => run(() => load()));
async function save() {
    await run(async () => {
        const result = await api(
            `/support/tickets${id ? `/${id}` : ""}`,
            id ? "PUT" : "POST",
            { ...form, category_id: Number(form.category_id) }
        );
        if (id) {
            editing.value = false;
            await load();
        } else await router.push(`/help-center/tickets/${result.data.id}`);
    }, "Your ticket has been saved.");
}
async function postReply() {
    await run(async () => {
        await api(`/support/tickets/${id}/replies`, "POST", {
            content: reply.value,
        });
        reply.value = "";
        await load();
    }, "Your reply has been sent.");
}
async function toggle() {
    await run(async () => {
        await api(`/support/tickets/${id}/toggle-status`, "POST");
        await load();
    });
}
async function remove() {
    if (!window.confirm("Delete this support ticket and its replies?")) return;
    await run(async () => {
        await api(`/support/tickets/${id}`, "DELETE");
        await router.push("/help-center");
    });
}
async function removeReply(replyId: number) {
    await run(async () => {
        await api(`/support/replies/${replyId}`, "DELETE");
        await load();
    });
}
</script>
<template>
    <div class="section-heading">
        <div>
            <h1>{{ t(kind === "rules" ? "Hotel rules" : "Help center") }}</h1>
            <p class="muted">
                {{
                    t(
                        kind === "rules"
                            ? "A few rules to help everyone feel at home."
                            : "Our team is here to help."
                    )
                }}
            </p>
        </div>
        <RouterLink
            v-if="kind !== 'rules' && kind !== 'create'"
            class="button"
            to="/help-center/tickets/create"
        >
            {{ t("Create a ticket") }}
        </RouterLink>
    </div>
    <Notice :error="error" :fields="fields" :success="success" />
    <div v-if="kind === 'rules'" class="stack">
        <Card
            v-for="category in rules"
            :key="category.id"
            :title="category.name"
            :subtitle="category.description"
            icon="rules_icon"
            ><div v-for="rule in category.rules" :key="rule.id">
                <h3>{{ rule.paragraph }}</h3>
                <RichText :html="rule.rule" /></div
        ></Card>
    </div>
    <Card
        v-else-if="kind === 'create' || editing"
        :title="editing ? 'Edit ticket' : 'Create a ticket'"
        icon="speechbubble_icon"
        ><form @submit.prevent="save">
            <label>
                {{ t("Category") }}
                <select v-model="form.category_id" required>
                    <option disabled value="">
                        {{ t("Choose a category") }}
                    </option>
                    <option
                        v-for="category in categories"
                        :key="category.id"
                        :value="String(category.id)"
                    >
                        {{ category.name }}
                    </option>
                </select></label
            ><label>
                {{ t("Title") }}
                <input
                    v-model="form.title"
                    required
                    minlength="10"
                    maxlength="255" /></label
            ><label>
                {{ t("Tell us what happened") }}
                <textarea
                    v-model="form.content"
                    required
                    minlength="10"
                    maxlength="65000"
                ></textarea>
            </label>
            <div class="inline">
                <button :disabled="busy">
                    {{ t(editing ? "Save changes" : "Send ticket") }}</button
                ><button
                    v-if="editing"
                    class="secondary"
                    type="button"
                    @click="editing = false"
                >
                    {{ t("Cancel") }}
                </button>
            </div>
        </form></Card
    >
    <div v-else-if="ticket" class="stack">
        <RouterLink to="/help-center"> {{ t("← Your tickets") }} </RouterLink
        ><Card
            :title="ticket.title"
            :subtitle="ticket.open ? 'Open ticket' : 'Closed ticket'"
            icon="speechbubble_icon"
            ><p style="white-space: pre-wrap">{{ ticket.content }}</p>
            <div class="inline">
                <button class="small secondary" @click="editing = true">
                    {{ t("Edit ticket") }}</button
                ><button
                    class="small secondary"
                    :disabled="busy"
                    @click="toggle"
                >
                    {{
                        t(ticket.open ? "Close ticket" : "Reopen ticket")
                    }}</button
                ><button class="small danger" :disabled="busy" @click="remove">
                    {{ t("Delete ticket") }}
                </button>
            </div></Card
        ><Card :title="t('Conversation')" icon="speechbubble_icon"
            ><article
                v-for="item in ticket.replies"
                :key="item.id"
                class="comment"
            >
                <div class="inline">
                    <strong>{{ item.author?.username }}</strong
                    ><small>{{ displayDate(item.created_at, true) }}</small>
                </div>
                <p>{{ item.content }}</p>
                <button
                    v-if="item.author?.id === session.user?.id"
                    class="text-button"
                    :disabled="busy"
                    @click="removeReply(item.id)"
                >
                    {{ t("Delete reply") }}
                </button>
            </article>
            <form v-if="ticket.open" @submit.prevent="postReply">
                <label>
                    {{ t("Your reply") }}
                    <textarea
                        v-model="reply"
                        required
                        minlength="10"
                        maxlength="65000"
                    ></textarea>
                </label>
                <div>
                    <button :disabled="busy">{{ t("Send reply") }}</button>
                </div>
            </form></Card
        >
    </div>
    <div v-else class="stack">
        <div class="grid three-columns">
            <Card
                v-for="category in categories"
                :key="category.id"
                :title="category.name"
                icon="community_icon"
                ><RichText :html="category.content" /><a
                    v-if="safeUrl(category.button_url)"
                    :href="safeUrl(category.button_url)"
                    rel="noopener"
                    target="_blank"
                    >{{ category.button_text || "Read more" }} →</a
                ></Card
            >
        </div>
        <Card :title="t('Your support tickets')" icon="speechbubble_icon"
            ><RouterLink
                v-for="item in tickets"
                :key="item.id"
                class="user-row"
                :to="`/help-center/tickets/${item.id}`"
                ><div>
                    <strong>{{ item.title }}</strong>
                    <p class="muted">
                        {{ displayDate(item.created_at) }}
                    </p>
                </div>
                <span class="badge">{{
                    t(item.open ? "Open" : "Closed")
                }}</span></RouterLink
            >
            <p v-if="!busy && !tickets.length" class="empty">
                {{ t("You haven't opened any tickets.") }}
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
            </div></Card
        >
    </div>
</template>
