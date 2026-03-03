import { storeToRefs } from "pinia";
import { useStaffStore } from "@/shared/stores/staffStore";

export function useStaff() {
    const store = useStaffStore();
    const { loading, saving, message, error, validationErrors, lookups, staff } = storeToRefs(store);

    return {
        loading,
        saving,
        message,
        error,
        validationErrors,
        lookups,
        staff,
        fetchLookups: store.fetchLookups,
        fetchStaff: store.fetchStaff,
        createStaff: store.createStaff,
        updateStaff: store.updateStaff,
        deleteStaff: store.deleteStaff,
        resetFeedback: store.resetFeedback,
    };
}
