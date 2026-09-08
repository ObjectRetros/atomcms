import type { components } from "./api-schema";
export type Data<Name extends keyof components["schemas"]> =
    components["schemas"][Name];
import { locale } from "./i18n";
export type RecordData = Record<string, unknown>;
export interface Envelope<T> {
    data: T;
    meta?: { current_page: number; last_page: number; total: number };
    links?: Record<string, string | null>;
}
export class ApiError extends Error {
    constructor(
        public status: number,
        public code: string,
        message: string,
        public fields: Record<string, string[]> = {},
        public voteUrl: string | null = null
    ) {
        super(message);
    }
}

const origin = (import.meta.env.VITE_API_URL || "").replace(/\/$/, "");
let csrfReady: Promise<void> | undefined;
export function backendUrl(path: string): string {
    return `${origin}${path.startsWith("/") ? path : `/${path}`}`;
}
export function mediaUrl(path?: string | null): string {
    if (!path) return "";
    if (/^https?:\/\//i.test(path)) return path;
    return backendUrl(path);
}
export function safeUrl(path?: string | null): string {
    if (!path) return "";
    try {
        const url = new URL(mediaUrl(path), window.location.origin);
        return ["https:", "http:"].includes(url.protocol) ? url.href : "";
    } catch {
        return "";
    }
}
async function csrf(): Promise<void> {
    csrfReady ??= fetch(backendUrl("/sanctum/csrf-cookie"), {
        credentials: "include",
        headers: { Accept: "application/json" },
    })
        .then((response) => {
            if (!response.ok)
                throw new ApiError(
                    response.status,
                    "csrf_failed",
                    "Could not start a secure session. Please try again."
                );
        })
        .catch((error) => {
            csrfReady = undefined;
            throw error;
        });
    await csrfReady;
}
export async function request<T = RecordData>(
    path: string,
    method = "GET",
    body?: unknown,
    extraHeaders: Record<string, string> = {}
): Promise<T> {
    const write = !["GET", "HEAD"].includes(method);
    if (write) await csrf();
    const headers: Record<string, string> = {
        Accept: "application/json",
        "Accept-Language": locale.value,
        "X-Requested-With": "XMLHttpRequest",
        ...extraHeaders,
    };
    if (write) {
        if (!(body instanceof FormData))
            headers["Content-Type"] = "application/json";
        const token = document.cookie
            .split("; ")
            .find((value) => value.startsWith("XSRF-TOKEN="))
            ?.slice(11);
        if (token) headers["X-XSRF-TOKEN"] = decodeURIComponent(token);
    }
    const response = await fetch(backendUrl(path), {
        method,
        credentials: "include",
        headers,
        body:
            body === undefined
                ? undefined
                : body instanceof FormData
                ? body
                : JSON.stringify(body),
    });
    const json =
        response.status === 204 ? {} : await response.json().catch(() => ({}));
    if (!response.ok) {
        if (response.status === 419) csrfReady = undefined;
        if (response.status === 401)
            window.dispatchEvent(new Event("session-ended"));
        const error = json.error || {};
        const code = error.code || json.code || "request_failed";
        if (
            ["account_banned", "maintenance", "two_factor_required"].includes(
                code
            )
        ) {
            window.dispatchEvent(
                new CustomEvent("access-restricted", { detail: code })
            );
        }
        throw new ApiError(
            response.status,
            code,
            error.message ||
                json.message ||
                (response.status === 419
                    ? "Your session expired. Please submit the form again."
                    : `The request failed (${response.status}).`),
            error.fields || json.errors || {},
            typeof json.vote_url === "string" ? json.vote_url : null
        );
    }
    return json as T;
}
export async function api<T = RecordData>(
    path: string,
    method = "GET",
    body?: unknown,
    headers?: Record<string, string>
): Promise<Envelope<T>> {
    return request<Envelope<T>>(`/api/v1${path}`, method, body, headers);
}
