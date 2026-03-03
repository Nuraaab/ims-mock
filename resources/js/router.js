import { createRouter, createWebHistory } from "vue-router";
import sharedRoutes from "@/shared/routes";
import imsRoutes from "@ims/routes";

const routes = [
    ...sharedRoutes,
    ...imsRoutes,
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to) => {
    const token = localStorage.getItem("user_token");

    if (to.meta.requiresAuth && !token) {
        return { name: "auth.login" };
    }

    if (to.meta.guestOnly && token) {
        return { name: "dashboard" };
    }

    return true;
});

export default router;
