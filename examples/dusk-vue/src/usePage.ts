import { ref } from "vue";
import { ApiError } from "./api";

export function usePage() {
    const busy = ref(false);
    const error = ref("");
    const fields = ref<Record<string, string[]>>({});
    const success = ref("");
    async function run(action: () => Promise<void>, message = "") {
        if (busy.value) return;
        busy.value = true;
        error.value = "";
        fields.value = {};
        success.value = "";
        try {
            await action();
            success.value = message;
        } catch (failure) {
            error.value =
                failure instanceof Error
                    ? failure.message
                    : "Something went wrong. Please try again.";
            if (failure instanceof ApiError) fields.value = failure.fields;
        } finally {
            busy.value = false;
        }
    }
    return { busy, error, fields, success, run };
}
