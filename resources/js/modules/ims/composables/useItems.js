import { storeToRefs } from "pinia";
import { useItemStore } from "@ims/stores/itemStore";

export function useItems() {
    const store = useItemStore();
    const { items, loading, saving, message, error, validationErrors } = storeToRefs(store);

    return {
        items,
        loading,
        saving,
        message,
        error,
        validationErrors,
        fetchItems: store.fetchAll,
        createItem: store.create,
        updateItem: store.update,
        deleteItem: store.remove,
        resetItemFeedback: store.resetFeedback,
    };
}
