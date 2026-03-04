import { storeToRefs } from "pinia";
import { useMeasurementStore } from "@ims/stores/measurementStore";

export function useMeasurements() {
    const store = useMeasurementStore();
    const { items, loading, saving, message, error, validationErrors } = storeToRefs(store);

    return {
        items,
        loading,
        saving,
        message,
        error,
        validationErrors,
        fetchMeasurements: store.fetchAll,
        createMeasurement: store.create,
        updateMeasurement: store.update,
        deleteMeasurement: store.remove,
        resetMeasurementFeedback: store.resetFeedback,
    };
}
