import Swiper from "swiper";
import { Autoplay, Pagination } from "swiper/modules";

const AtomSliders = {
    init() {
        document.addEventListener("turbolinks:load", () => {
            this.initArticleSlider();
        });
    },

    initArticleSlider() {
        if (!document.querySelector(".article-slider")) return;

        new Swiper(".articles-slider", {
            modules: [Autoplay, Pagination],
            slidesPerView: 1,
            loop: true,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
                pauseOnMouseEnter: true,
            },
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
        });
    },
};

export { AtomSliders as default };
