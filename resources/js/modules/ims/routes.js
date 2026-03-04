import ItemCategoriesPage from "@ims/pages/ItemCategoriesPage.vue";
import ProductGroupsPage from "@ims/pages/ProductGroupsPage.vue";
import MeasurementsPage from "@ims/pages/MeasurementsPage.vue";
import ItemsPage from "@ims/pages/ItemsPage.vue";
import ProductsPage from "@ims/pages/ProductsPage.vue";

const imsRoutes = [
    {
        path: "/dashboard/ims/item-categories",
        name: "ims.item-categories.index",
        component: ItemCategoriesPage,
        meta: { requiresAuth: true },
    },
    {
        path: "/dashboard/ims/product-groups",
        name: "ims.product-groups.index",
        component: ProductGroupsPage,
        meta: { requiresAuth: true },
    },
    {
        path: "/dashboard/ims/measurements",
        name: "ims.measurements.index",
        component: MeasurementsPage,
        meta: { requiresAuth: true },
    },
    {
        path: "/dashboard/ims/items",
        name: "ims.items.index",
        component: ItemsPage,
        meta: { requiresAuth: true },
    },
    {
        path: "/dashboard/ims/products",
        name: "ims.products.index",
        component: ProductsPage,
        meta: { requiresAuth: true },
    },
];

export default imsRoutes;
