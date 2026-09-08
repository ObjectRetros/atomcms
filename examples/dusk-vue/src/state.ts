import { reactive } from "vue";
import { api, safeUrl, ApiError } from "./api";
import type { Data } from "./api";

export const session = reactive({
    user: null as
        | (Pick<Data<"Me">, "id" | "username"> &
              Partial<Data<"Me">> &
              Partial<NonNullable<Data<"Bootstrap">["viewer"]>>)
        | null,
    bootstrap: {} as Data<"Bootstrap">,
    ready: false,
    restriction: "",
});
export async function initialize() {
    const result = await api<Data<"Bootstrap">>("/bootstrap");
    session.bootstrap = result.data;
    session.user = result.data.viewer;
    await refreshUser();
    session.ready = true;
}
export async function refreshUser() {
    try {
        const result = await api<Data<"Me">>("/me");
        session.user = result.data;
        session.restriction = "";
        if (session.bootstrap.viewer)
            session.bootstrap.viewer.requires_two_factor = false;
    } catch (error) {
        if (error instanceof ApiError && error.status === 401)
            session.user = null;
        else if (
            error instanceof ApiError &&
            ["two_factor_required", "account_banned", "maintenance"].includes(
                error.code
            )
        ) {
            session.bootstrap = (
                await api<Data<"Bootstrap">>("/bootstrap")
            ).data;
            session.user = session.bootstrap.viewer;
        } else throw error;
    }
}
export function avatar(
    user?: Partial<Data<"PublicUser">> | null,
    large = false
): string {
    if (!user?.look) return "/assets/images/dusk/ghost.png";
    return safeUrl(
        `${session.bootstrap.assets?.avatar || ""}${encodeURIComponent(
            user.look
        )}&direction=2&head_direction=3&gesture=sml&size=${large ? "l" : "m"}`
    );
}
window.addEventListener("session-ended", () => {
    session.user = null;
    session.bootstrap.viewer = null;
    session.restriction = "";
});
window.addEventListener("access-restricted", (event) => {
    session.restriction = (event as CustomEvent<string>).detail;
});
