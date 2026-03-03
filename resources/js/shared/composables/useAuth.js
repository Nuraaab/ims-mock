import { storeToRefs } from "pinia";
import { useAuthStore } from "@/shared/auth/stores/authStore";

export function useAuth() {
    const store = useAuthStore();
    const {
        user,
        token,
        permissions,
        registrationLookups,
        loading,
        message,
        error,
        validationErrors,
    } = storeToRefs(store);

    return {
        user,
        token,
        permissions,
        registrationLookups,
        loading,
        message,
        error,
        validationErrors,
        login: store.login,
        fetchMe: store.fetchMe,
        hasPermission: store.hasPermission,
        fetchRegistrationLookups: store.fetchRegistrationLookups,
        registerOrganization: store.registerOrganization,
        logout: store.logout,
        resetFeedback: store.resetFeedback,
    };
}
