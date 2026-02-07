import "./bootstrap";
import "./external/flowbite";

import "swiper/css";
import "swiper/css/pagination";

import Alpine from "alpinejs";
import Focus from "@alpinejs/focus";

import ThemeSwitcher from "./components/ThemeSwitcher.js";
import AtomSliders from "./components/AtomSliders.js";

ThemeSwitcher.init();
AtomSliders.init();
Alpine.plugin(Focus);
Alpine.start();

console.log(
    "%cAtom CMS%c\n\nAtom CMS is a CMS for made for the community to enjoy. You can join our wonderful community at https://discord.gg/rX3aShUHdg\n\n",
    "color: #14619c; -webkit-text-stroke: 2px black; font-size: 32px; font-weight: bold;",
    ""
);
