import { createRouter, createWebHistory } from "vue-router";
import imsRoutes from "@ims/routes";

const routes = [
    {
        path: "/",
        redirect: "/ims",
    },
    ...imsRoutes,
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
