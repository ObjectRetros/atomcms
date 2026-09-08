<script setup lang="ts">
import { t } from "../i18n";
import { computed, ref } from "vue";
import { api } from "../api";
import type { Data } from "../api";
import { session } from "../state";
import { usePage } from "../usePage";
import Card from "../components/Card.vue";
import Notice from "../components/Notice.vue";
const { busy, error, success, run } = usePage();
const font = ref("atom"),
    text = ref(""),
    fonts = [
        "atom",
        "sunrise",
        "marine",
        "danlie",
        "habton",
        "habton_capitalized",
        "habbo_modern",
    ];
const letters = computed(() =>
    text.value
        .toLowerCase()
        .replace(/[^a-z ]/g, "")
        .split("")
);
async function render(): Promise<HTMLCanvasElement> {
    const images = await Promise.all(
        letters.value.map(
            (letter) =>
                new Promise<HTMLImageElement | null>((resolve, reject) => {
                    if (letter === " ") {
                        resolve(null);
                        return;
                    }
                    const image = new Image();
                    image.onload = () => resolve(image);
                    image.onerror = () =>
                        reject(new Error("A logo letter could not be loaded."));
                    image.src = `/assets/images/logo-generator/${font.value}/${letter}.png`;
                })
        )
    );
    const canvas = document.createElement("canvas");
    canvas.width = images.reduce(
        (sum, image) => sum + (image?.width || 15) + 2,
        0
    );
    canvas.height = Math.max(1, ...images.map((image) => image?.height || 0));
    const context = canvas.getContext("2d");
    let left = 0;
    for (const image of images) {
        if (image)
            context?.drawImage(image, left, canvas.height - image.height);
        left += (image?.width || 15) + 2;
    }
    return canvas;
}
async function generate(use: boolean) {
    await run(
        async () => {
            const canvas = await render();
            if (use) {
                const blob = await new Promise<Blob>((resolve, reject) =>
                    canvas.toBlob((value) =>
                        value
                            ? resolve(value)
                            : reject(new Error("Could not generate this logo."))
                    )
                );
                const data = new FormData();
                data.append("logo", blob, "logo.png");
                await api("/logo", "POST", data);
                session.bootstrap = (
                    await api<Data<"Bootstrap">>("/bootstrap")
                ).data;
            } else {
                const link = document.createElement("a");
                link.href = canvas.toDataURL("image/png");
                link.download = "hotel-logo.png";
                link.click();
            }
        },
        use ? "The hotel logo has been updated." : ""
    );
}
</script>
<template>
    <Card
        :title="t('Logo generator')"
        :subtitle="t('Generate your very own pixel logo')"
        icon="hotel_icon"
        ><Notice :error="error" :success="success" />
        <div class="inline">
            <button
                v-for="choice in fonts"
                :key="choice"
                class="secondary"
                :aria-pressed="font === choice"
                :aria-label="choice"
                @click="font = choice"
            >
                <img
                    :src="`/assets/images/logo-generator/${choice}/a.png`"
                    alt=""
                /><img
                    :src="`/assets/images/logo-generator/${choice}/b.png`"
                    alt=""
                /><img
                    :src="`/assets/images/logo-generator/${choice}/c.png`"
                    alt=""
                />
            </button>
        </div>
        <label>
            {{ t("Logo text") }}
            <input
                v-model="text"
                maxlength="60"
                :placeholder="t('Your hotel name')"
        /></label>
        <div
            class="inline"
            style="
                gap: 2px;
                overflow: auto;
                flex-wrap: nowrap;
                min-height: 100px;
            "
            :aria-label="t('Logo preview')"
        >
            <template v-for="(letter, index) in letters" :key="index"
                ><span v-if="letter === ' '" style="min-width: 15px"></span
                ><img
                    v-else
                    :src="`/assets/images/logo-generator/${font}/${letter}.png`"
                    :alt="letter"
                    style="image-rendering: pixelated"
            /></template>
        </div>
        <div class="inline">
            <button
                :disabled="busy || !letters.length"
                @click="generate(false)"
            >
                {{ t("Download logo") }}</button
            ><button
                v-if="session.user?.can_generate_logo"
                class="secondary"
                :disabled="busy || !letters.length"
                @click="generate(true)"
            >
                {{ t("Use as hotel logo") }}
            </button>
        </div></Card
    >
</template>
