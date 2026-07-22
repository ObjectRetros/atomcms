import "./bootstrap";
import "./external/flowbite";

import { Livewire, Alpine } from "livewire";

import Swiper from "swiper";
import { Navigation, Pagination } from "swiper/modules";
import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/pagination";

import "../../../js/components/HomeManager.js";

window.Alpine = Alpine;

const livewireStartedKey = Symbol.for("atomcms.livewire.started");
const startLivewire = () => {
    if (window[livewireStartedKey]) {
        return;
    }

    window[livewireStartedKey] = true;

    try {
        Livewire.start();
    } catch (error) {
        delete window[livewireStartedKey];
        throw error;
    }
};

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", startLivewire, {
        once: true,
    });
} else {
    startLivewire();
}

// Swiper Initialization
document.addEventListener("DOMContentLoaded", function () {
    const swiper = new Swiper(".swiper", {
        modules: [Navigation, Pagination],
        // Your Swiper options here
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
        pagination: {
            el: ".swiper-pagination",
        },
    });
});

console.log(
    "%cAtom CMS%c\n\nAtom CMS is a CMS for made for the community to enjoy. You can join our wonderful community at https://discord.gg/rX3aShUHdg\n\n",
    "color: #14619c; -webkit-text-stroke: 2px black; font-size: 32px; font-weight: bold;",
    ""
);
