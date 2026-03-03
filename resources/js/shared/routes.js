import HomePage from "@/shared/pages/Home.vue";
import DashboardPage from "@/shared/pages/Dashboard.vue";
import LoginPage from "@/shared/pages/auth/Login.vue";
import RegisterPage from "@/shared/pages/auth/Register.vue";
import RolePage from "@/shared/pages/role/RolesPage.vue";
import UsersPage from "@/shared/pages/user/UsersPage.vue";
import BranchesPage from "@/shared/pages/branch/BranchesPage.vue";
import WarehousesPage from "@/shared/pages/warehouse/WarehousesPage.vue";
import OutletsPage from "@/shared/pages/outlet/OutletsPage.vue";

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
    { path: "/dashboard/branches", name: "branches.index.page", component: BranchesPage, meta: { requiresAuth: true } },
    { path: "/dashboard/warehouses", name: "warehouses.index.page", component: WarehousesPage, meta: { requiresAuth: true } },
    { path: "/dashboard/outlets", name: "outlets.index.page", component: OutletsPage, meta: { requiresAuth: true } },
];

export default sharedRoutes;
