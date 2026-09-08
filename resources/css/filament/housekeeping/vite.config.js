import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/postcss";
import autoprefixer from "autoprefixer";

export default defineConfig({
    plugins: [laravel({
        input: ["resources/css/filament/housekeeping/theme.css"],
        buildDirectory: "build-housekeeping",
    })],
    css: { postcss: { plugins: [tailwindcss(), autoprefixer()] } },
});
