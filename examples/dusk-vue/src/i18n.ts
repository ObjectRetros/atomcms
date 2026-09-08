import { ref } from "vue";

const dictionaries = import.meta.glob<{ default: Record<string, string> }>(
    "./locales/*.json"
);
export const locale = ref(localStorage.getItem("dusk-locale") || "en");
const messages = ref<Record<string, string>>({});
export async function setLocale(value: string): Promise<void> {
    const loader = dictionaries[`./locales/${value}.json`];
    if (!loader) return;
    messages.value = (await loader()).default;
    locale.value = value;
    localStorage.setItem("dusk-locale", value);
    document.documentElement.lang = value;
}
export function t(
    message: string,
    replacements: Record<string, string | number> = {}
): string {
    let result = messages.value[message] || message;
    for (const [key, value] of Object.entries(replacements))
        result = result.replaceAll(`:${key}`, String(value));
    return result;
}
