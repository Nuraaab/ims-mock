import HomePage from "@/shared/pages/Home.vue";
import DashboardPage from "@/shared/pages/Dashboard.vue";
import LoginPage from "@/shared/pages/auth/Login.vue";
import RegisterPage from "@/shared/pages/auth/Register.vue";
import RolePage from "@/shared/pages/auth/Role.vue";
import UsersPage from "@/shared/pages/users/Users.vue";

const sharedRoutes = [
    {
        path: "/",
        name: "home",
        component: HomePage,
        meta: { guestOnly: true },
    },
    {
        path: "/dashboard",
        name: "dashboard",
        component: DashboardPage,
        meta: { requiresAuth: true },
    },
    {
        path: "/auth/login",
        name: "auth.login",
        component: LoginPage,
        meta: { guestOnly: true },
    },
    {
        path: "/auth/register",
        name: "auth.register",
        component: RegisterPage,
        meta: { guestOnly: true },
    },
    {
        path: "/dashboard/roles",
        name: "roles.create",
        component: RolePage,
        meta: { requiresAuth: true },
    },
    {
        path: "/dashboard/users",
        name: "users.index",
        component: UsersPage,
        meta: { requiresAuth: true },
    },
];

export default sharedRoutes;
