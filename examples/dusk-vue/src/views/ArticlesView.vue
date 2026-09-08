<script setup lang="ts">
import { displayDate } from "../date";
import { t } from "../i18n";
import { onMounted, reactive, ref } from "vue";
import { useRoute } from "vue-router";
import { api, mediaUrl } from "../api";
import type { Data, Envelope } from "../api";
import { session } from "../state";
import { usePage } from "../usePage";
import Card from "../components/Card.vue";
import Notice from "../components/Notice.vue";
import ArticleTile from "../components/ArticleTile.vue";
import RichText from "../components/RichText.vue";
const route = useRoute(),
    slug = route.params.slug
        ? encodeURIComponent(String(route.params.slug))
        : "";
const { busy, error, fields, success, run } = usePage();
const articles = ref<Data<"Article">[]>([]),
    article = ref<Data<"Article"> | null>(null),
    comments = ref<Data<"Comment">[]>([]),
    comment = ref("");
const pagination = reactive({ page: 1, last: 1 }),
    commentPagination = reactive({ page: 1, last: 1 });
const reactions = ref<Record<string, number>>({}),
    myReactions = ref<string[]>([]);
async function load(page = 1) {
    if (slug) {
        const result = (await api<Data<"Article">>(
            `/articles/${slug}`
        )) as Envelope<Data<"Article">> & {
            reactions: Record<string, number>;
            my_reactions: string[];
        };
        article.value = result.data;
        reactions.value = result.reactions || {};
        myReactions.value = result.my_reactions || [];
        await loadComments();
    } else {
        const result = await api<Data<"Article">[]>(`/articles?page=${page}`);
        articles.value = result.data;
        pagination.page = result.meta?.current_page || page;
        pagination.last = result.meta?.last_page || 1;
    }
}
async function loadComments(page = 1) {
    const result = await api<Data<"Comment">[]>(
        `/articles/${slug}/comments?page=${page}`
    );
    comments.value = result.data;
    commentPagination.page = result.meta?.current_page || page;
    commentPagination.last = result.meta?.last_page || 1;
}
onMounted(() => run(() => load()));
async function postComment() {
    await run(async () => {
        await api(`/articles/${slug}/comments`, "POST", {
            comment: comment.value,
        });
        comment.value = "";
        await loadComments();
    }, "Your comment has been posted.");
}
async function removeComment(id: number) {
    await run(async () => {
        await api(`/comments/${id}`, "DELETE");
        await loadComments();
    }, "Comment deleted.");
}
async function react(reaction: string) {
    await run(async () => {
        await api(
            `/articles/${slug}/reactions/${encodeURIComponent(reaction)}`,
            myReactions.value.includes(reaction) ? "DELETE" : "PUT"
        );
        await load();
    });
}
</script>
<template>
    <Notice :error="error" :fields="fields" :success="success" />
    <template v-if="!slug"
        ><div class="page-intro">
            <h1>{{ t("Latest news") }}</h1>
            <p>{{ t("Everything happening around the hotel.") }}</p>
        </div>
        <div class="grid three-columns">
            <ArticleTile
                v-for="item in articles"
                :key="item.id"
                :article="item"
            />
        </div>
        <p v-if="!busy && !articles.length" class="empty">
            {{ t("There are no articles yet.") }}
        </p>
        <div v-if="pagination.last > 1" class="pagination">
            <button
                :disabled="busy || pagination.page === 1"
                @click="run(() => load(pagination.page - 1))"
            >
                {{ t("Previous") }}</button
            ><span>{{ pagination.page }} / {{ pagination.last }}</span
            ><button
                :disabled="busy || pagination.page === pagination.last"
                @click="run(() => load(pagination.page + 1))"
            >
                {{ t("Next") }}
            </button>
        </div></template
    >
    <div v-else-if="article" class="stack">
        <RouterLink to="/community/articles"> {{ t("← All news") }} </RouterLink
        ><img
            v-if="article.image"
            class="article-banner"
            :src="mediaUrl(article.image)"
            alt=""
        /><Card
            :title="article.title"
            :subtitle="`By ${article.author?.username || ''}`"
            icon="news_icon"
            ><RichText :html="article.full_story" />
            <div class="inline">
                <button
                    v-for="reaction in session.bootstrap.reactions || []"
                    :key="reaction"
                    class="small secondary"
                    :aria-pressed="myReactions.includes(reaction)"
                    :disabled="busy || !session.user"
                    @click="react(reaction)"
                >
                    <img
                        :src="`/assets/images/icons/reactions/${reaction}.png`"
                        :alt="reaction"
                        style="width: 22px; height: 22px; object-fit: contain"
                    />
                    {{ reactions[reaction] || 0 }}
                </button>
            </div></Card
        ><Card :title="t('Comments')" icon="speechbubble_icon"
            ><article v-for="item in comments" :key="item.id" class="comment">
                <div class="inline">
                    <RouterLink :to="`/home/${item.author?.username}`"
                        ><strong>{{
                            item.author?.username
                        }}</strong></RouterLink
                    ><small>{{ displayDate(item.created_at, true) }}</small>
                </div>
                <p>{{ item.comment }}</p>
                <button
                    v-if="item.author?.id === session.user?.id"
                    class="text-button"
                    :disabled="busy"
                    @click="removeComment(item.id)"
                >
                    {{ t("Delete comment") }}
                </button>
            </article>
            <p v-if="!comments.length" class="muted">
                {{ t("Be the first to join the conversation.") }}
            </p>
            <div v-if="commentPagination.last > 1" class="pagination">
                <button
                    :disabled="busy || commentPagination.page === 1"
                    @click="run(() => loadComments(commentPagination.page - 1))"
                >
                    {{ t("Previous") }}</button
                ><span
                    >{{ commentPagination.page }} /
                    {{ commentPagination.last }}</span
                ><button
                    :disabled="
                        busy ||
                        commentPagination.page === commentPagination.last
                    "
                    @click="run(() => loadComments(commentPagination.page + 1))"
                >
                    {{ t("Next") }}
                </button>
            </div>
            <form
                v-if="session.user && article.can_comment"
                @submit.prevent="postComment"
            >
                <label>
                    {{ t("Your comment") }}
                    <textarea
                        v-model="comment"
                        required
                        maxlength="5000"
                    ></textarea>
                </label>
                <div>
                    <button :disabled="busy">{{ t("Post comment") }}</button>
                </div>
            </form>
            <RouterLink v-else-if="!session.user" to="/login">
                {{ t("Log in to join the conversation →") }}
            </RouterLink>
            <p v-else class="muted">
                {{ t("Comments are closed for this article.") }}
            </p></Card
        >
    </div>
</template>
