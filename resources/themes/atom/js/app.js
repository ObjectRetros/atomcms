import "./bootstrap";
import { initFlowbite, initPopovers } from "flowbite";

import "swiper/css";
import "swiper/css/pagination";

import { Livewire, Alpine } from "livewire";

import ThemeSwitcher from "./components/ThemeSwitcher.js";
import AtomSliders from "./components/AtomSliders.js";

import "../../../js/components/HomeManager.js";

ThemeSwitcher.init();
AtomSliders.init();

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

document.addEventListener("turbolinks:load", () => initFlowbite());
document.addEventListener("reactions:loaded", () => initPopovers());

console.log(
    "%cAtom CMS%c\n\nAtom CMS is a CMS for made for the community to enjoy. You can join our wonderful community at https://discord.gg/rX3aShUHdg\n\n",
    "color: #14619c; -webkit-text-stroke: 2px black; font-size: 32px; font-weight: bold;",
    ""
);
