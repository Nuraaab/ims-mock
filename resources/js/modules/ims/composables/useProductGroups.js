import { storeToRefs } from "pinia";
import { useProductGroupStore } from "@ims/stores/productGroupStore";

export function useProductGroups() {
    const store = useProductGroupStore();
    const { items, loading, saving, message, error, validationErrors } = storeToRefs(store);

    return {
        items,
        loading,
        saving,
        message,
        error,
        validationErrors,
        fetchProductGroups: store.fetchAll,
        createProductGroup: store.create,
        updateProductGroup: store.update,
        deleteProductGroup: store.remove,
        resetProductGroupFeedback: store.resetFeedback,
    };
}
