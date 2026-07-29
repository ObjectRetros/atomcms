// Disconnected from client
function disconnected() {
    document.querySelector("#disconnected").style =
        "display: block !important;";
}

let frame = document.getElementById("nitro");
window.FlashExternalInterface = {};
window.FlashExternalInterface.disconnect = () => {
    disconnected();
};

if (frame && frame.contentWindow) {
    window.addEventListener("message", (ev) => {
        if (!frame || ev.source !== frame.contentWindow) return;
        const legacyInterface = "Nitro_LegacyExternalInterface";
        if (typeof ev.data !== "string") return;
        if (ev.data.startsWith(legacyInterface)) {
            const { method, params } = JSON.parse(
                ev.data.substr(legacyInterface.length)
            );
            if (!("FlashExternalInterface" in window)) return;
            const fn = window.FlashExternalInterface[method];
            if (!fn) return;
            fn(...params);
            return;
        }
    });
}
// Disconnected from client end

// Nitro is a single page app, so the frame only ever loads once. A later load means
// something inside it navigated away - "Go to hotel" on Nitro's own disconnected
// screen, for instance - which would leave the client buttons on top of the website.
if (frame) {
    const clientSource = new URL(
        frame.getAttribute("src") || "",
        window.location.href
    );
    const clientBase =
        clientSource.origin + clientSource.pathname.replace(/[^/]*$/, "");

    frame.addEventListener("load", () => {
        let destination;

        try {
            destination = frame.contentWindow.location.href;
        } catch (error) {
            // Nitro is hosted on another origin, so we cannot tell where it went.
            return;
        }

        if (
            !destination.startsWith("http") ||
            destination.startsWith(clientBase)
        ) {
            return;
        }

        window.location.replace(destination);
    });
}
