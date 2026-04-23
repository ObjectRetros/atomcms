import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import path from "path";
import { bladeRefreshPlugin, postcssPlugins } from "../../../vite.shared.js";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                path.resolve(__dirname, "css/app.css"),
                path.resolve(__dirname, "js/app.js"),
                "resources/js/global.js",
                "resources/css/global.css",
            ],
        }),

        bladeRefreshPlugin(),
    ],
    resolve: {
        alias: {
            "@": path.resolve(__dirname, "js/app.js"),
        },
    },
    css: {
        postcss: {
            plugins: postcssPlugins(),
        },
    },
});
