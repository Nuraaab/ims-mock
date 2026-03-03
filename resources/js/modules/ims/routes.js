import IMSHome from "@ims/pages/Home.vue";
import LoginPage from "@ims/pages/Login.vue";
import RegisterPage from "@ims/pages/Register.vue";

const imsRoutes = [
    {
        path: "/ims",
        name: "ims.home",
        component: IMSHome,
    },
    {
        path: "/ims/auth/login",
        name: "ims.auth.login",
        component: LoginPage,
    },
    {
        path: "/ims/auth/register",
        name: "ims.auth.register",
        component: RegisterPage,
    },
];

export default imsRoutes;
