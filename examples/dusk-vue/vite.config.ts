import { defineConfig, loadEnv } from "vite";
import vue from "@vitejs/plugin-vue";

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, ".", "");
    return {
        plugins: [vue()],
        server: {
            proxy: {
                "^/(api|sanctum|login|register|logout|two-factor-challenge|forgot-password|reset-password|user/confirm-password|user/settings/two-factor-authentication)(/|$)":
                    {
                        target: env.VITE_DEV_API_URL || "http://127.0.0.1:8000",
                        changeOrigin: false,
                        bypass(request) {
                            if (
                                request.method === "GET" &&
                                request.headers.accept?.includes("text/html")
                            )
                                return "/index.html";
                        },
                    },
            },
        },
    };
});
