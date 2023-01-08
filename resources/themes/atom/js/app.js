/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import './external/flowbite';
import "./bootstrap";


import 'swiper/css';
import 'swiper/css/pagination';
import Alpine from "alpinejs";
import ThemeSwitcher from "./components/ThemeSwitcher.js";
import ArticleReactions from './components/ArticleReactions.js';
import AtomSliders from './components/AtomSliders.js';

ThemeSwitcher.init();
ArticleReactions.init();
AtomSliders.init();
Alpine.start();

