import tailwindcss from "@tailwindcss/postcss";
import autoprefixer from "autoprefixer";

export function bladeRefreshPlugin() {
    return {
        name: "blade",
        handleHotUpdate({ file, server }) {
            if (file.endsWith(".blade.php")) {
                server.ws.send({
                    type: "full-reload",
                    path: "*",
                });
            }
        },
    };
}

export function postcssPlugins() {
    return [tailwindcss(), autoprefixer()];
}
