import { createRouter, createWebHistory } from "vue-router";
import { session } from "./state";
import AuthView from "./views/AuthView.vue";
import MeView from "./views/MeView.vue";
import ArticlesView from "./views/ArticlesView.vue";

export const router = createRouter({
    history: createWebHistory(),
    scrollBehavior: () => ({ top: 0 }),
    routes: [
        { path: "/", component: AuthView, meta: { authKind: "login" } },
        { path: "/login", component: AuthView, meta: { authKind: "login" } },
        {
            path: "/register/:referral?",
            component: AuthView,
            meta: { authKind: "register" },
        },
        {
            path: "/two-factor-challenge",
            component: AuthView,
            meta: { authKind: "challenge" },
        },
        {
            path: "/forgot-password",
            component: AuthView,
            meta: { authKind: "forgot" },
        },
        {
            path: "/reset-password/:token",
            component: AuthView,
            meta: { authKind: "reset" },
        },
        {
            path: "/user/me",
            alias: "/me",
            component: MeView,
            meta: { auth: true },
        },
        {
            path: "/user/settings/:tab(account|password|two-factor|session-logs)",
            alias: "/settings/:tab(account|password|two-factor|session-logs)",
            component: () => import("./views/SettingsView.vue"),
            meta: { auth: true },
        },
        { path: "/community/articles", component: ArticlesView },
        { path: "/community/article/:slug", component: ArticlesView },
        {
            path: "/community/:section(staff|teams|photos|staff-applications|team-applications)/:id?",
            component: () => import("./views/CommunityView.vue"),
            meta: { auth: true },
        },
        {
            path: "/leaderboard",
            component: () => import("./views/CommunityView.vue"),
            meta: { auth: true, section: "leaderboard" },
        },
        {
            path: "/shop",
            component: () => import("./views/ShopView.vue"),
            meta: { auth: true },
        },
        {
            path: "/shop/category/:category",
            component: () => import("./views/ShopView.vue"),
            meta: { auth: true },
        },
        {
            path: "/shop/purchases",
            component: () => import("./views/ShopView.vue"),
            meta: { auth: true, history: true },
        },
        {
            path: "/shop/orders/:order",
            component: () => import("./views/ShopView.vue"),
            meta: { auth: true },
        },
        {
            path: "/paypal/:status",
            component: () => import("./views/ShopView.vue"),
            meta: { auth: true },
        },
        {
            path: "/home/:username",
            component: () => import("./views/HomeView.vue"),
        },
        {
            path: "/help-center",
            component: () => import("./views/SupportView.vue"),
            meta: { auth: true, support: "index" },
        },
        {
            path: "/help-center/tickets/create",
            component: () => import("./views/SupportView.vue"),
            meta: { auth: true, support: "create" },
        },
        {
            path: "/help-center/tickets/:id",
            component: () => import("./views/SupportView.vue"),
            meta: { auth: true, support: "ticket" },
        },
        {
            path: "/help-center/rules",
            component: () => import("./views/SupportView.vue"),
            meta: { support: "rules" },
        },
        {
            path: "/draw-badge",
            component: () => import("./views/BadgeView.vue"),
            meta: { auth: true },
        },
        {
            path: "/logo-generator",
            component: () => import("./views/LogoView.vue"),
            meta: { auth: true },
        },
        {
            path: "/values/:id?",
            component: () => import("./views/RareValuesView.vue"),
            meta: { auth: true },
        },
        {
            path: "/game/:client(nitro|flash)",
            component: () => import("./views/ClientView.vue"),
            meta: { auth: true },
        },
        {
            path: "/:pathMatch(.*)*",
            component: () => import("./views/StatusView.vue"),
        },
    ],
});
router.beforeEach((to) => {
    document
        .querySelectorAll("details.nav-menu[open]")
        .forEach((menu) => menu.removeAttribute("open"));
    if (!session.ready) return;
    if (
        session.restriction === "account_banned" &&
        to.path !== "/banned" &&
        !to.path.startsWith("/help-center")
    )
        return "/banned";
    if (
        session.restriction === "maintenance" &&
        session.user &&
        !["/login", "/two-factor-challenge", "/maintenance"].includes(to.path)
    )
        return "/maintenance";
    if (to.meta.auth && !session.user)
        return { path: "/login", query: { next: to.fullPath } };
    if (
        session.user?.requires_two_factor &&
        !["/user/settings/two-factor", "/settings/two-factor"].includes(to.path)
    )
        return "/user/settings/two-factor";
    if (session.user && ["/", "/login", "/register"].includes(to.path))
        return "/user/me";
});
