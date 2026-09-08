<script setup lang="ts">
import { displayDate } from "../date";
import { t } from "../i18n";
import { computed, onMounted, ref } from "vue";
import { useRoute } from "vue-router";
import { api, safeUrl } from "../api";
import type { Data } from "../api";
import { session, refreshUser } from "../state";
import { money } from "../money";
import { usePage } from "../usePage";
import Card from "../components/Card.vue";
import Notice from "../components/Notice.vue";
import RichText from "../components/RichText.vue";
const route = useRoute();
const { busy, error, fields, success, run } = usePage();
const catalog = ref<Data<"Shop">>({ categories: [], packages: [] }),
    purchases = ref<Data<"Purchase">[]>([]),
    selected = ref<Data<"ShopPackage"> | null>(null),
    receiver = ref(""),
    voucher = ref(""),
    amount = ref(5),
    order = ref<Data<"PaypalStatus"> | null>(null),
    termsAccepted = ref(false);
const history = computed(() => route.meta.history === true);
const page = ref(1),
    lastPage = ref(1);
const orderId = computed(() =>
    String(route.params.order || route.query.order || route.query.token || "")
);
const paymentHints: Record<string, string> = {
    completed:
        "PayPal has returned you to the hotel. Your balance updates after payment is verified.",
    pending: "Your payment is waiting for confirmation.",
    cancelled:
        "The PayPal checkout was cancelled. You can return to the store whenever you are ready.",
    failed: "We could not confirm your payment. Check its status before trying again.",
};
const paymentHint = computed(
    () => paymentHints[String(route.query.payment_status || "")] || ""
);
const orderMessage = computed(() => {
    const status = order.value?.status?.toUpperCase();
    if (["CANCELLED", "VOIDED"].includes(status || ""))
        return "This payment was cancelled.";
    if (status === "FAILED")
        return "This payment failed. You can check with PayPal for more details.";
    if (status === "REVIEW")
        return "This payment needs to be reviewed by the hotel team.";
    return "Payment confirmation can take a moment. Refresh to check for an update.";
});
async function load(nextPage = 1) {
    if (orderId.value)
        order.value = (
            await api<Data<"PaypalStatus">>(
                `/shop/paypal/orders/${encodeURIComponent(orderId.value)}`
            )
        ).data;
    else if (history.value) {
        const result = await api<Data<"Purchase">[]>(
            `/shop/purchases?page=${nextPage}`
        );
        purchases.value = result.data;
        page.value = result.meta?.current_page || nextPage;
        lastPage.value = result.meta?.last_page || 1;
    } else
        catalog.value = (
            await api<Data<"Shop">>(
                `/shop${
                    route.params.category
                        ? `?category=${encodeURIComponent(
                              String(route.params.category)
                          )}`
                        : ""
                }`
            )
        ).data;
}
onMounted(() => run(() => load()));
function keyFor(
    operation: string,
    payload: unknown
): { storage: string; key: string } {
    const storage = `dusk-pending:${
        session.user?.id
    }:${operation}:${JSON.stringify(payload)}`;
    const key = sessionStorage.getItem(storage) || crypto.randomUUID();
    sessionStorage.setItem(storage, key);
    return { storage, key };
}
async function purchase() {
    if (!selected.value) return;
    await run(async () => {
        const payload = {
            receiver:
                selected.value?.is_giftable && receiver.value
                    ? receiver.value
                    : null,
        };
        const attempt = keyFor(`package:${selected.value?.id}`, payload);
        await api(
            `/shop/packages/${selected.value?.id}/purchases`,
            "POST",
            payload,
            { "Idempotency-Key": attempt.key }
        );
        sessionStorage.removeItem(attempt.storage);
        selected.value = null;
        receiver.value = "";
        await refreshUser();
        await load();
    }, "Purchase complete. Your items will be delivered in the hotel.");
}
async function redeem() {
    await run(async () => {
        await api("/shop/vouchers", "POST", { code: voucher.value });
        voucher.value = "";
        await refreshUser();
    }, "Your voucher has been redeemed.");
}
async function topup() {
    await run(async () => {
        const payload = { amount: Number(amount.value) },
            attempt = keyFor("paypal", payload);
        const result = await api<Data<"PaypalOrder">>(
            "/shop/paypal/orders",
            "POST",
            payload,
            {
                "Idempotency-Key": attempt.key,
            }
        );
        const url = safeUrl(result.data.approval_url);
        if (!url)
            throw new Error(
                "PayPal did not return a valid approval link. Please try again."
            );
        sessionStorage.removeItem(attempt.storage);
        window.location.assign(url);
    });
}
</script>
<template>
    <div class="section-heading">
        <h1>
            {{
                t(
                    history
                        ? "Purchase history"
                        : orderId
                        ? "Payment status"
                        : "Hotel store"
                )
            }}
        </h1>
        <RouterLink :to="history ? '/shop' : '/shop/purchases'"
            >{{ t(history ? "Back to store" : "Your purchases") }} →</RouterLink
        >
    </div>
    <Notice :error="error" :fields="fields" :success="success" />
    <p v-if="paymentHint && !order?.credited_at" class="notice" role="status">
        {{ t(paymentHint) }}
    </p>
    <Card v-if="order" :title="t('Your PayPal payment')" icon="store_icon"
        ><p>{{ t("Order") }} {{ order.id }}</p>
        <p>
            {{ t("Status:") }} <strong>{{ order.status }}</strong>
        </p>
        <p v-if="order.credited_at" class="notice success">
            {{ t("Your hotel balance has been credited.") }}
        </p>
        <p v-else class="muted">
            {{ t(orderMessage) }}
        </p>
        <div>
            <button
                :disabled="busy"
                @click="
                    run(async () => {
                        await load();
                        await refreshUser();
                    })
                "
            >
                {{ t("Refresh status") }}
            </button>
        </div></Card
    >
    <Card v-else-if="history" :title="t('Your purchases')" icon="store_icon"
        ><div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>{{ t("Package") }}</th>
                        <th>{{ t("Recipient") }}</th>
                        <th>{{ t("Date") }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in purchases" :key="item.id">
                        <td>{{ item.package_name }}</td>
                        <td>
                            {{
                                item.recipient_username ||
                                session.user?.username
                            }}
                        </td>
                        <td>
                            {{ displayDate(item.created_at, true) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p v-if="!purchases.length && !busy" class="empty">
            {{ t("You haven't made any purchases yet.") }}
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
    <div v-else class="sidebar-layout">
        <aside class="stack">
            <nav class="sidebar-nav" :aria-label="t('Store categories')">
                <RouterLink to="/shop"> {{ t("All packages") }} </RouterLink
                ><RouterLink
                    v-for="category in catalog.categories"
                    :key="category.id"
                    :to="`/shop/category/${category.slug}`"
                    >{{ category.name }}</RouterLink
                >
            </nav>
            <Card :title="t('Your balance')" icon="store_icon"
                ><strong class="price">{{
                    money(session.user?.website_balance)
                }}</strong>
                <form @submit.prevent="topup">
                    <label>
                        {{ t("Top up (") }}
                        {{ session.bootstrap.currency }})<input
                            v-model.number="amount"
                            type="number"
                            min="1"
                            max="250"
                            required /></label
                    ><button :disabled="busy">
                        {{ t("Continue to PayPal") }}
                    </button>
                </form></Card
            ><Card :title="t('Redeem a voucher')"
                ><form @submit.prevent="redeem">
                    <label>
                        {{ t("Voucher code") }}
                        <input
                            v-model="voucher"
                            required
                            autocomplete="off" /></label
                    ><button :disabled="busy">{{ t("Redeem") }}</button>
                </form></Card
            >
        </aside>
        <div class="stack">
            <details class="card">
                <summary class="card-heading">
                    {{ t("Shop terms & conditions") }}
                </summary>
                <div class="card-body">
                    <p>
                        {{
                            t(
                                "Donations help keep the hotel running. In return, you receive in-game goods. Once received, donations and website balance cannot be refunded or exchanged for cash. Please spend responsibly and only use money you can afford."
                            )
                        }}
                    </p>
                </div>
            </details>
            <Card
                v-if="selected"
                :title="`Purchase ${selected.name}`"
                icon="store_icon"
                ><p>
                    {{ t("Total:") }}
                    <strong>{{ money(selected.price) }}</strong>
                </p>
                <form @submit.prevent="purchase">
                    <label v-if="selected.is_giftable">
                        {{ t("Gift recipient (optional)") }}
                        <input
                            v-model="receiver"
                            :placeholder="
                                t('Leave empty to buy for yourself')
                            " /></label
                    ><label class="check-label"
                        ><input
                            v-model="termsAccepted"
                            type="checkbox"
                            required
                        />
                        {{ t("I have read and accept the shop terms.") }}
                    </label>
                    <div class="inline">
                        <button :disabled="busy">
                            {{ t("Confirm purchase") }}</button
                        ><button
                            class="secondary"
                            type="button"
                            @click="selected = null"
                        >
                            {{ t("Cancel") }}
                        </button>
                    </div>
                </form></Card
            >
            <div class="grid two-columns">
                <Card
                    v-for="item in catalog.packages"
                    :key="item.id"
                    :title="item.name"
                    ><img
                        v-if="item.image"
                        class="package-image"
                        :src="safeUrl(item.image)"
                        :alt="item.name"
                    /><RichText :html="item.description" />
                    <div
                        v-for="product in item.items"
                        :key="product.id"
                        class="inline"
                    >
                        <img
                            v-if="product.image"
                            :src="safeUrl(product.image)"
                            alt=""
                            style="max-height: 45px"
                        /><span
                            >{{ product.quantity }} × {{ product.name }}</span
                        >
                    </div>
                    <strong class="price">{{ money(item.price) }}</strong>
                    <p v-if="item.stock !== null" class="muted">
                        {{ item.stock }} {{ t("remaining") }}
                    </p>
                    <button
                        :disabled="busy || !item.available"
                        @click="
                            selected = item;
                            receiver = '';
                            termsAccepted = false;
                        "
                    >
                        {{ t(item.available ? "Buy package" : "Unavailable") }}
                    </button></Card
                >
            </div>
            <p v-if="!busy && !catalog.packages?.length" class="empty">
                {{ t("There are no packages in this category yet.") }}
            </p>
        </div>
    </div>
</template>
