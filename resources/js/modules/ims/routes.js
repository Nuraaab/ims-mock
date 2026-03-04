import ItemCategoriesPage from "@ims/pages/ItemCategoriesPage.vue";

const imsRoutes = [
    {
        path: "/dashboard/ims/item-categories",
        name: "ims.item-categories.index",
        component: ItemCategoriesPage,
        meta: { requiresAuth: true },
    },
];

export default imsRoutes;
