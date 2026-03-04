import { storeToRefs } from "pinia";
import { useItemCategoryStore } from "@ims/stores/itemCategoryStore";

export function useItemCategories() {
    const store = useItemCategoryStore();
    const { items, loading, saving, message, error, validationErrors } = storeToRefs(store);

    return {
        items,
        loading,
        saving,
        message,
        error,
        validationErrors,
        fetchItemCategories: store.fetchAll,
        createItemCategory: store.create,
        updateItemCategory: store.update,
        deleteItemCategory: store.remove,
        resetItemCategoryFeedback: store.resetFeedback,
    };
}
