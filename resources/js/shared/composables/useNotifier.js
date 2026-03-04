import { useNotifyStore } from "@/shared/stores/notifyStore";
import Swal from "sweetalert2";

export function useNotifier() {
    const store = useNotifyStore();

    function notifySuccess(message, title = "Success") {
        store.success(message, title);
    }

    function notifyError(message, title = "Error") {
        store.error(message, title);
    }

    function getErrorMessage(error, fallback = "Something went wrong.") {
        return error?.response?.data?.message || fallback;
    }

    async function confirmDelete({
        title = "Delete record?",
        text = "This action cannot be undone.",
        confirmText = "Delete",
        cancelText = "Cancel",
    } = {}) {
        const result = await Swal.fire({
            icon: "warning",
            title,
            text,
            showCancelButton: true,
            confirmButtonText: confirmText,
            cancelButtonText: cancelText,
            reverseButtons: true,
            confirmButtonColor: "#dc2626",
            cancelButtonColor: "#475569",
        });

        return !!result.isConfirmed;
    }

    return {
        notifySuccess,
        notifyError,
        getErrorMessage,
        confirmDelete,
    };
}
