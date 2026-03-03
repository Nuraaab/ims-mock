import { storeToRefs } from "pinia";
import { useWarehouseStore } from "@/shared/stores/warehouseStore";

export function useWarehouses() {
    const store = useWarehouseStore();
    const { items, loading, saving, message, error, validationErrors } = storeToRefs(store);

    return {
        items,
        loading,
        saving,
        message,
        error,
        validationErrors,
        fetchWarehouses: store.fetchAll,
        createWarehouse: store.create,
        updateWarehouse: store.update,
        deleteWarehouse: store.remove,
        resetWarehouseFeedback: store.resetFeedback,
    };
}
