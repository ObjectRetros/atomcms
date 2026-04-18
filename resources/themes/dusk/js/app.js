import "./bootstrap";
import "./external/flowbite";

import Alpine from "alpinejs";
import Focus from "@alpinejs/focus";

import Swiper from "swiper";
import { Navigation, Pagination } from "swiper/modules";
import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/pagination";

import "../../../js/components/HomeManager.js";

Alpine.plugin(Focus);
Alpine.start();

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
