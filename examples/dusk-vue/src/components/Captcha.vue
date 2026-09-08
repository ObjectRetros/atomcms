<script setup lang="ts">
import { onMounted, onBeforeUnmount, ref, watch } from "vue";
import { session } from "../state";
type CaptchaApi = {
    render(
        element: HTMLElement,
        options: Record<string, unknown>
    ): string | number;
    reset(id: string | number): void;
    remove?(id: string | number): void;
};
const model = defineModel<Record<string, string>>({ required: true });
const props = defineProps<{ busy?: boolean }>();
const recaptchaElement = ref<HTMLElement | null>(null),
    turnstileElement = ref<HTMLElement | null>(null),
    error = ref("");
const widgets: { api: CaptchaApi; id: string | number }[] = [];
let alive = true;
async function loadScript(name: string, src: string): Promise<CaptchaApi> {
    const globals = window as unknown as Record<string, CaptchaApi>;
    if (globals[name]) return globals[name];
    await new Promise<void>((resolve, reject) => {
        let script = document.querySelector<HTMLScriptElement>(
            `script[data-captcha="${name}"]`
        );
        if (!script) {
            script = document.createElement("script");
            script.src = src;
            script.async = true;
            script.dataset.captcha = name;
            document.head.appendChild(script);
        }
        script.addEventListener("load", () => resolve(), { once: true });
        script.addEventListener(
            "error",
            () =>
                reject(
                    new Error(
                        "The verification service could not be loaded. Please reload this page."
                    )
                ),
            { once: true }
        );
    });
    return globals[name]!;
}
onMounted(async () => {
    const config = session.bootstrap.captcha;
    if (!config) return;
    try {
        for (const item of [
            {
                enabled: config.recaptcha_enabled,
                name: "grecaptcha",
                element: recaptchaElement,
                key: config.recaptcha_site_key,
                field: "g-recaptcha-response",
                url: "https://www.google.com/recaptcha/api.js?render=explicit",
            },
            {
                enabled: config.turnstile_enabled,
                name: "turnstile",
                element: turnstileElement,
                key: config.turnstile_site_key,
                field: "cf-turnstile-response",
                url: "https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit",
            },
        ]) {
            if (!item.enabled) continue;
            if (!item.key)
                throw new Error(
                    "Hotel verification is not configured. Please contact the hotel team."
                );
            const service = await loadScript(item.name, item.url);
            if (!alive || !item.element.value) return;
            const id = service.render(item.element.value, {
                sitekey: item.key,
                theme: "dark",
                callback: (token: string) => {
                    model.value = { ...model.value, [item.field]: token };
                },
                "expired-callback": () => {
                    model.value = { ...model.value, [item.field]: "" };
                },
            });
            widgets.push({ api: service, id });
        }
    } catch (failure) {
        error.value =
            failure instanceof Error
                ? failure.message
                : "Verification could not be loaded.";
    }
});
watch(
    () => props.busy,
    (value, old) => {
        if (!value && old) {
            widgets.forEach((widget) => widget.api.reset(widget.id));
            model.value = {};
        }
    }
);
onBeforeUnmount(() => {
    alive = false;
    widgets.forEach((widget) => widget.api.remove?.(widget.id));
    model.value = {};
});
</script>
<template>
    <div
        v-if="
            session.bootstrap.captcha?.recaptcha_enabled ||
            session.bootstrap.captcha?.turnstile_enabled
        "
        class="stack"
    >
        <div ref="recaptchaElement"></div>
        <div ref="turnstileElement"></div>
        <p v-if="error" class="notice error" role="alert">{{ error }}</p>
    </div>
</template>
