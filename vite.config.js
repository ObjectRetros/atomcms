import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import { bladeRefreshPlugin, postcssPlugins } from "./vite.shared.js";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/global.css",
                "resources/js/global.js",
            ],
        }),

        bladeRefreshPlugin(),
    ],
    resolve: {
        alias: {
            "@": "/resources/js",
        },
    },
    css: {
        postcss: {
            plugins: postcssPlugins(),
        },
    },
});
