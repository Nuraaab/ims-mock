import { storeToRefs } from "pinia";
import { useOutletStore } from "@/shared/stores/outletStore";

export function useOutlets() {
    const store = useOutletStore();
    const { items, loading, saving, message, error, validationErrors } = storeToRefs(store);

    return {
        items,
        loading,
        saving,
        message,
        error,
        validationErrors,
        fetchOutlets: store.fetchAll,
        createOutlet: store.create,
        updateOutlet: store.update,
        deleteOutlet: store.remove,
        resetOutletFeedback: store.resetFeedback,
    };
}
