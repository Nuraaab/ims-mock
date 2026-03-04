import { storeToRefs } from "pinia";
import { useProductStore } from "@ims/stores/productStore";

export function useProducts() {
    const store = useProductStore();
    const { items, productGroups, measurements, itemOptions, loading, saving, message, error, validationErrors } = storeToRefs(store);

    return {
        items,
        productGroups,
        measurements,
        itemOptions,
        loading,
        saving,
        message,
        error,
        validationErrors,
        fetchProducts: store.fetchAll,
        fetchProductLookups: store.fetchLookups,
        createProduct: store.create,
        updateProduct: store.update,
        deleteProduct: store.remove,
        resetProductFeedback: store.resetFeedback,
    };
}
