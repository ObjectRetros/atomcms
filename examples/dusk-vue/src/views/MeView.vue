<script setup lang="ts">
import { t } from "../i18n";
import { onMounted, ref } from "vue";
import { api, safeUrl } from "../api";
import type { Data } from "../api";
import { session, avatar, refreshUser } from "../state";
import { usePage } from "../usePage";
import Card from "../components/Card.vue";
import Notice from "../components/Notice.vue";
import ArticleTile from "../components/ArticleTile.vue";
const { busy, error, success, run } = usePage();
const frontendOrigin = window.location.origin;
const articles = ref<Data<"Article">[]>([]),
    photos = ref<Data<"Bootstrap">["latest_photos"]>([]);
onMounted(() =>
    run(async () => {
        await refreshUser();
        articles.value = (await api<Data<"Article">[]>("/articles")).data;
        if (session.bootstrap.features?.includes("camera-photos"))
            photos.value = (
                await api<Data<"Bootstrap">["latest_photos"]>("/photos")
            ).data.slice(0, 4);
    })
);
async function claim() {
    await run(async () => {
        await api("/me/referral-claim", "POST");
        await refreshUser();
    }, "Referral reward claimed.");
}
</script>
<template>
    <Notice :error="error" :success="success" />
    <div v-if="session.user" class="stack">
        <div class="grid dashboard-top">
            <section class="me-hero">
                <RouterLink
                    class="me-avatar"
                    :to="`/home/${session.user.username}`"
                    ><img
                        :src="avatar(session.user, true)"
                        :alt="session.user.username"
                /></RouterLink>
                <div class="me-greeting">
                    <div>
                        <h1>
                            {{
                                t("Hey :username!", {
                                    username: session.user.username,
                                })
                            }}
                        </h1>
                        <p>{{ session.user.motto }}</p>
                    </div>
                    <RouterLink class="button gold" to="/game/nitro">
                        {{
                            t("Go to :hotel", {
                                hotel: session.bootstrap.hotel_name,
                            })
                        }}</RouterLink
                    >
                </div>
                <div class="balances">
                    <div
                        v-for="currency in ['credits', 'duckets', 'diamonds']"
                        :key="currency"
                        class="balance"
                    >
                        <img
                            :src="`/assets/images/icons/currency/${currency}.png`"
                            alt=""
                        /><strong>{{
                            Number(
                                session.user.balances?.[currency] || 0
                            ).toLocaleString()
                        }}</strong
                        ><span class="label">{{
                            t(
                                currency.charAt(0).toUpperCase() +
                                    currency.slice(1)
                            )
                        }}</span>
                    </div>
                </div>
            </section>
            <ArticleTile v-if="articles[0]" :article="articles[0]" />
        </div>
        <div class="grid three-columns">
            <Card
                :title="t('Your friends')"
                :subtitle="t('See who\'s online')"
                icon="community_icon"
                ><RouterLink
                    v-for="friend in session.user.online_friends || []"
                    :key="friend.id"
                    class="user-row"
                    :to="`/home/${friend.username}`"
                    ><img :src="avatar(friend)" alt="" />
                    <div>
                        <strong>{{ friend.username }}</strong>
                        <p class="muted">{{ friend.motto }}</p>
                    </div>
                    <span class="online-dot"></span
                ></RouterLink>
                <p v-if="!session.user.online_friends?.length" class="muted">
                    {{ t("Your friends are offline right now.") }}
                </p></Card
            ><Card
                :title="t('Invite your friends')"
                :subtitle="t('Good company makes a great hotel')"
                icon="speechbubble_icon"
                ><p>{{ t("Share your invitation link with a friend.") }}</p>
                <input
                    :value="`${frontendOrigin}/register/${
                        session.user.referral_code || ''
                    }`"
                    readonly
                    :aria-label="t('Your invitation link')"
                />
                <p class="muted">
                    {{ session.user.referrals_needed }}
                    {{ t("referrals needed for your next reward.") }}
                </p>
                <button :disabled="busy" @click="claim">
                    {{ t("Claim referral reward") }}
                </button></Card
            ><Card
                :title="t('Your account')"
                :subtitle="t('Make yourself at home')"
                icon="usersettings_wheel_icon"
                ><RouterLink to="/user/settings/account">
                    {{ t("Update your profile →") }} </RouterLink
                ><RouterLink to="/user/settings/two-factor">
                    {{ t("Secure your account →") }} </RouterLink
                ><RouterLink to="/help-center">
                    {{ t("Need a hand? Visit the help center →") }}
                </RouterLink></Card
            >
        </div>
        <div v-if="photos.length">
            <div class="section-heading">
                <h2>{{ t("Latest hotel moments") }}</h2>
                <RouterLink to="/community/photos">
                    {{ t("View all photos →") }}
                </RouterLink>
            </div>
            <div class="grid four-columns">
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
        </div>
        <div v-if="articles.length > 1" class="grid three-columns">
            <ArticleTile
                v-for="article in articles.slice(1, 4)"
                :key="article.id"
                :article="article"
            />
        </div>
    </div>
</template>
