import { storeToRefs } from "pinia";
import { useStaffStore } from "@/shared/users/stores/staffStore";

export function useStaff() {
    const store = useStaffStore();
    const { loading, saving, message, error, validationErrors, lookups, createdStaff } = storeToRefs(store);

    return {
        loading,
        saving,
        message,
        error,
        validationErrors,
        lookups,
        createdStaff,
        fetchLookups: store.fetchLookups,
        createStaff: store.createStaff,
        resetFeedback: store.resetFeedback,
    };
}
