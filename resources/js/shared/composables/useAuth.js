import { storeToRefs } from "pinia";
import { useAuthStore } from "@/shared/auth/stores/authStore";

export function useAuth() {
    const store = useAuthStore();
    const {
        user,
        token,
        registrationLookups,
        loading,
        message,
        error,
        validationErrors,
    } = storeToRefs(store);

    return {
        user,
        token,
        registrationLookups,
        loading,
        message,
        error,
        validationErrors,
        login: store.login,
        fetchRegistrationLookups: store.fetchRegistrationLookups,
        registerOrganization: store.registerOrganization,
        logout: store.logout,
        resetFeedback: store.resetFeedback,
    };
}
