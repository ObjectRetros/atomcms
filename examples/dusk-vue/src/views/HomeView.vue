<script setup lang="ts">
import { t } from "../i18n";
import { computed, onMounted, ref } from "vue";
import { useRoute, onBeforeRouteLeave } from "vue-router";
import { api, safeUrl } from "../api";
import type { Data } from "../api";
import { avatar, session } from "../state";
import { usePage } from "../usePage";
import Card from "../components/Card.vue";
import Notice from "../components/Notice.vue";
import HomeWidget from "../components/HomeWidget.vue";
const route = useRoute(),
    username = encodeURIComponent(String(route.params.username));
const { busy, error, fields, success, run } = usePage();
const home = ref<Data<"Home"> | null>(null),
    items = ref<Data<"HomeItem">[]>([]),
    inventory = ref<Data<"HomeItem">[]>([]),
    shop = ref<Data<"HomeShop">>({ categories: [], items: [] }),
    editing = ref(false),
    dirty = ref(false),
    backgroundId = ref(0);
const owner = computed(() => home.value?.user?.id === session.user?.id);
const background = computed(
    () =>
        [...items.value, ...inventory.value].find(
            (item) => item.id === backgroundId.value
        )?.definition?.image ||
        home.value?.active_background?.definition?.image ||
        ""
);
const selectedItem = ref<Data<"HomeItem"> | null>(null);
async function load() {
    home.value = (await api<Data<"Home">>(`/homes/${username}`)).data;
    items.value = home.value.items || [];
    backgroundId.value = home.value.active_background?.id || 0;
    if (owner.value)
        inventory.value = (
            await api<Data<"HomeItem">[]>(`/homes/${username}/inventory`)
        ).data;
}
onMounted(() => run(load));
onBeforeRouteLeave(
    () =>
        !dirty.value || window.confirm("Leave without saving your home layout?")
);
async function edit() {
    await run(async () => {
        shop.value = (await api<Data<"HomeShop">>("/home-shop")).data;
        editing.value = true;
    });
}
async function save() {
    await run(async () => {
        await api(`/homes/${username}`, "PUT", {
            items: items.value.map((item) => ({
                id: item.id,
                x: item.x,
                y: item.y,
                z: item.z,
                placed: item.placed,
                is_reversed: item.is_reversed,
                theme: item.theme,
                extra_data: item.extra_data,
            })),
            backgroundId: backgroundId.value,
        });
        dirty.value = false;
        editing.value = false;
        selectedItem.value = null;
        await load();
    }, "Your home has been saved.");
}
async function cancel() {
    await run(async () => {
        await load();
        dirty.value = false;
        editing.value = false;
        selectedItem.value = null;
    });
}
function place(item: Data<"HomeItem">) {
    if (item.definition?.type === "b") {
        backgroundId.value = item.id;
        dirty.value = true;
        return;
    } else {
        const existing = items.value.find((entry) => entry.id === item.id);
        if (existing) existing.placed = true;
        else
            items.value.push({
                ...item,
                x: 30,
                y: 30,
                z: items.value.length + 1,
                placed: true,
            });
    }
    inventory.value = inventory.value.filter((entry) => entry.id !== item.id);
    dirty.value = true;
}
function remove(item: Data<"HomeItem">) {
    item.placed = false;
    inventory.value.push(item);
    selectedItem.value = null;
    dirty.value = true;
}
function drag(event: PointerEvent, item: Data<"HomeItem">) {
    if (
        !editing.value ||
        (event.target as HTMLElement).closest("button,a,input,textarea,select")
    )
        return;
    selectedItem.value = item;
    const element = event.currentTarget as HTMLElement;
    const startX = event.clientX,
        startY = event.clientY,
        initialX = Number(item.x),
        initialY = Number(item.y);
    element.setPointerCapture(event.pointerId);
    const move = (next: PointerEvent) => {
        item.x = Math.round(
            Math.max(0, Math.min(2000, initialX + next.clientX - startX))
        );
        item.y = Math.round(
            Math.max(0, Math.min(2000, initialY + next.clientY - startY))
        );
        dirty.value = true;
    };
    const end = () => {
        element.removeEventListener("pointermove", move);
        element.removeEventListener("pointerup", end);
        element.removeEventListener("pointercancel", end);
    };
    element.addEventListener("pointermove", move);
    element.addEventListener("pointerup", end);
    element.addEventListener("pointercancel", end);
}
async function buy(item: Data<"HomeDefinition">) {
    if (!window.confirm(`Buy ${item.name} for ${item.price} ${item.currency}?`))
        return;
    await run(async () => {
        await api(`/homes/${username}/purchases`, "POST", {
            item_id: item.id,
            quantity: 1,
        });
        inventory.value = (
            await api<Data<"HomeItem">[]>(`/homes/${username}/inventory`)
        ).data;
    }, "Added to your home inventory.");
}
</script>
<template>
    <Notice :error="error" :fields="fields" :success="success" />
    <div v-if="home" class="stack">
        <div class="section-heading">
            <div class="inline">
                <img :src="avatar(home.user)" alt="" style="height: 90px" />
                <div>
                    <h1>{{ home.user.username }} {{ t("'s Home") }}</h1>
                    <p class="muted">{{ home.user.motto }}</p>
                </div>
            </div>
            <div v-if="owner" class="inline">
                <button v-if="!editing" :disabled="busy" @click="edit">
                    {{ t("Edit home") }}</button
                ><template v-else
                    ><button :disabled="busy" @click="save">
                        {{ t("Save home") }}</button
                    ><button class="secondary" :disabled="busy" @click="cancel">
                        {{ t("Cancel") }}
                    </button></template
                >
            </div>
        </div>
        <Card v-if="editing && selectedItem" :title="t('Selected item')"
            ><div class="grid four-columns">
                <label>
                    {{ t("X position") }}
                    <input
                        v-model.number="selectedItem.x"
                        type="number"
                        min="0"
                        max="2000"
                        @input="dirty = true" /></label
                ><label>
                    {{ t("Y position") }}
                    <input
                        v-model.number="selectedItem.y"
                        type="number"
                        min="0"
                        max="2000"
                        @input="dirty = true" /></label
                ><label>
                    {{ t("Layer") }}
                    <input
                        v-model.number="selectedItem.z"
                        type="number"
                        min="0"
                        max="1000"
                        @input="dirty = true" /></label
                ><label class="check-label"
                    ><input
                        v-model="selectedItem.is_reversed"
                        type="checkbox"
                        @change="dirty = true"
                    />
                    {{ t("Flip image") }}
                </label>
            </div>
            <label v-if="selectedItem.definition?.type === 'n'">
                {{ t("Note text") }}
                <textarea
                    v-model="selectedItem.extra_data"
                    maxlength="2000"
                    @input="dirty = true"
                ></textarea>
            </label>
            <div>
                <button class="small danger" @click="remove(selectedItem)">
                    {{ t("Return to inventory") }}
                </button>
            </div></Card
        >
        <div
            class="home-stage"
            :style="{
                backgroundImage: background
                    ? `url('${safeUrl(background)}')`
                    : undefined,
            }"
        >
            <template v-for="item in items" :key="item.id"
                ><section
                    v-if="item.placed && item.definition?.type !== 'b'"
                    :class="['home-widget', { editable: editing }]"
                    :style="{
                        left: `${item.x}px`,
                        top: `${item.y}px`,
                        zIndex: item.z,
                        width: item.definition?.type === 's' ? 'auto' : '250px',
                        background:
                            item.definition?.type === 's'
                                ? 'transparent'
                                : undefined,
                        border:
                            item.definition?.type === 's' ? 'none' : undefined,
                    }"
                    @pointerdown="drag($event, item)"
                >
                    <h3 v-if="item.definition?.type !== 's'">
                        {{ item.definition?.name }}
                    </h3>
                    <HomeWidget
                        v-if="item.definition?.type === 'w'"
                        :username="username"
                        :item="item"
                        :visitor="!!session.user && !owner"
                        :editing="editing"
                    />
                    <p
                        v-else-if="item.definition?.type === 'n'"
                        style="white-space: pre-wrap"
                    >
                        {{ item.extra_data }}
                    </p>
                    <img
                        v-else-if="item.definition?.image"
                        :src="safeUrl(item.definition.image)"
                        :alt="item.definition.name"
                        :style="{
                            transform: item.is_reversed
                                ? 'scaleX(-1)'
                                : undefined,
                            pointerEvents: 'none',
                        }"
                    /><button
                        v-if="editing"
                        class="small secondary"
                        @click="selectedItem = item"
                    >
                        {{ t("Select") }}
                    </button>
                </section></template
            >
            <p v-if="!items.filter((item) => item.placed).length" class="empty">
                {{
                    t(
                        owner
                            ? "Make this space your own. Edit your home to place items."
                            : "This home has not been decorated yet."
                    )
                }}
            </p>
        </div>
        <div v-if="editing" class="grid two-columns">
            <Card
                :title="t('Your inventory')"
                :subtitle="t('Choose an item to place it in your home')"
                icon="home_icon"
                ><div class="inline">
                    <button
                        v-for="item in inventory"
                        :key="item.id"
                        class="small secondary"
                        @click="place(item)"
                    >
                        {{ item.definition?.name }}
                    </button>
                </div>
                <p v-if="!inventory.length" class="muted">
                    {{ t("Your inventory is empty.") }}
                </p></Card
            ><Card :title="t('Home shop')" icon="store_icon"
                ><div
                    v-for="item in shop.items || []"
                    :key="item.id"
                    class="user-row"
                >
                    <img v-if="item.image" :src="safeUrl(item.image)" alt="" />
                    <div>
                        <strong>{{ item.name }}</strong>
                        <p class="muted">
                            {{ item.price }} {{ item.currency }}
                        </p>
                    </div>
                    <button class="small" :disabled="busy" @click="buy(item)">
                        {{ t("Buy") }}
                    </button>
                </div></Card
            >
        </div>
    </div>
</template>
