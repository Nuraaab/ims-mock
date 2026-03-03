import { storeToRefs } from "pinia";
import { useBranchStore } from "@/shared/stores/branchStore";

export function useBranches() {
    const store = useBranchStore();
    const { items, loading, saving, message, error, validationErrors } = storeToRefs(store);

    return {
        items,
        loading,
        saving,
        message,
        error,
        validationErrors,
        fetchBranches: store.fetchAll,
        createBranch: store.create,
        updateBranch: store.update,
        deleteBranch: store.remove,
        resetBranchFeedback: store.resetFeedback,
    };
}
