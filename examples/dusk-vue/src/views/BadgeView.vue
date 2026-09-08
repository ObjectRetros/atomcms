<script setup lang="ts">
import { t } from "../i18n";
import { onMounted, ref } from "vue";
import { api, safeUrl } from "../api";
import { badgeGif } from "../badge";
import { usePage } from "../usePage";
import Card from "../components/Card.vue";
import Notice from "../components/Notice.vue";
type BadgeConfig = { cost?: number; currency?: string; badge_url?: string };
const { busy, error, fields, success, run } = usePage();
const canvas = ref<HTMLCanvasElement | null>(null),
    color = ref("#957cc3"),
    eraser = ref(false),
    badgeName = ref(""),
    description = ref(""),
    config = ref<BadgeConfig>({}),
    history = ref<ImageData[]>([]);
const palette = [
    "#000000",
    "#ffffff",
    "#957cc3",
    "#eab308",
    "#e36577",
    "#64b17b",
    "#69a3da",
    "#c44aac",
    "#805736",
    "#d4b197",
    "#697283",
    "#dc743c",
];
onMounted(() =>
    run(async () => {
        config.value = (await api<BadgeConfig>("/badges")).data;
    })
);
function snapshot() {
    const context = canvas.value?.getContext("2d");
    if (context) history.value.push(context.getImageData(0, 0, 40, 40));
    if (history.value.length > 30) history.value.shift();
}
function paint(event: PointerEvent) {
    const context = canvas.value?.getContext("2d");
    if (!context || !canvas.value) return;
    const bounds = canvas.value.getBoundingClientRect(),
        x = Math.floor(((event.clientX - bounds.left) * 40) / bounds.width),
        y = Math.floor(((event.clientY - bounds.top) * 40) / bounds.height);
    if (eraser.value) context.clearRect(x, y, 1, 1);
    else {
        context.fillStyle = color.value;
        context.fillRect(x, y, 1, 1);
    }
}
function start(event: PointerEvent) {
    snapshot();
    canvas.value?.setPointerCapture(event.pointerId);
    paint(event);
}
function move(event: PointerEvent) {
    if (event.buttons === 1) paint(event);
}
function undo() {
    const previous = history.value.pop();
    if (previous) canvas.value?.getContext("2d")?.putImageData(previous, 0, 0);
}
function clear() {
    snapshot();
    canvas.value?.getContext("2d")?.clearRect(0, 0, 40, 40);
}
async function importImage(event: Event) {
    const file = (event.target as HTMLInputElement).files?.[0];
    if (!file) return;
    await run(async () => {
        const image = await createImageBitmap(file);
        snapshot();
        const context = canvas.value?.getContext("2d");
        if (context) {
            context.clearRect(0, 0, 40, 40);
            context.imageSmoothingEnabled = false;
            context.drawImage(image, 0, 0, 40, 40);
        }
        image.close();
    });
}
function encoded(): Uint8Array {
    const pixels = canvas.value
        ?.getContext("2d")
        ?.getImageData(0, 0, 40, 40).data;
    if (!pixels) throw new Error("The badge canvas is unavailable.");
    return badgeGif(pixels);
}
function download() {
    const url = URL.createObjectURL(
        new Blob([new Uint8Array(encoded())], { type: "image/gif" })
    );
    const link = document.createElement("a");
    link.href = url;
    link.download = `${badgeName.value || "badge"}.gif`;
    link.click();
    setTimeout(() => URL.revokeObjectURL(url), 1000);
}
async function buy() {
    if (
        !window.confirm(
            `Buy this badge for ${config.value.cost} ${config.value.currency}?`
        )
    )
        return;
    await run(async () => {
        const badge_data = `data:image/gif;base64,${btoa(
            String.fromCharCode(...encoded())
        )}`;
        const result = await api<{ badge_url: string }>("/badges", "POST", {
            badge_data,
            badge_name: badgeName.value,
            badge_description: description.value,
        });
        config.value.badge_url = result.data.badge_url;
    }, "Your badge has been purchased.");
}
</script>
<template>
    <div class="page-intro">
        <h1>{{ t("Badge Drawer") }}</h1>
        <p>{{ t("Draw your very own 40 × 40 pixel badge.") }}</p>
    </div>
    <Notice :error="error" :fields="fields" :success="success" />
    <div class="grid two-columns">
        <Card :title="t('Your canvas')" icon="hotel_icon"
            ><div class="inline">
                <label>
                    {{ t("Choose color") }}
                    <input
                        v-model="color"
                        type="color"
                        @input="eraser = false" /></label
                ><button
                    class="small secondary"
                    :aria-pressed="eraser"
                    @click="eraser = !eraser"
                >
                    {{ t(eraser ? "Eraser on" : "Eraser") }}</button
                ><button
                    class="small secondary"
                    :disabled="!history.length"
                    @click="undo"
                >
                    {{ t("Undo") }}</button
                ><button class="small secondary" @click="clear">
                    {{ t("Clear") }}
                </button>
            </div>
            <canvas
                ref="canvas"
                class="pixel-canvas"
                width="40"
                height="40"
                :aria-label="t('Badge drawing canvas')"
                @pointerdown="start"
                @pointermove="move"
            ></canvas>
            <div class="inline">
                <button
                    v-for="swatch in palette"
                    :key="swatch"
                    :aria-label="`Use color ${swatch}`"
                    :style="{
                        background: swatch,
                        borderColor: color === swatch ? '#fff' : '#596071',
                        padding: '14px',
                    }"
                    @click="
                        color = swatch;
                        eraser = false;
                    "
                ></button>
            </div>
            <label>
                {{ t("Import a picture") }}
                <input
                    type="file"
                    accept="image/png,image/gif"
                    @change="importImage"
            /></label>
            <div>
                <button class="secondary" @click="download">
                    {{ t("Download GIF") }}
                </button>
            </div></Card
        ><Card :title="t('Make it yours')" icon="store_icon"
            ><form @submit.prevent="buy">
                <label>
                    {{ t("Badge name") }}
                    <input
                        v-model="badgeName"
                        required
                        maxlength="255" /></label
                ><label>
                    {{ t("Description") }}
                    <textarea
                        v-model="description"
                        required
                        maxlength="255"
                    ></textarea>
                </label>
                <p>
                    {{ t("Price:") }}
                    <strong>{{ config.cost }} {{ config.currency }}</strong>
                </p>
                <button :disabled="busy || config.cost === undefined">
                    {{ t("Buy badge") }}
                </button>
            </form>
            <a
                v-if="safeUrl(config.badge_url)"
                :href="safeUrl(config.badge_url)"
                target="_blank"
                rel="noopener"
            >
                {{ t("View your badge") }}
            </a></Card
        >
    </div>
</template>
