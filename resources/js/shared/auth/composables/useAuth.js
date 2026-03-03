import { computed } from "vue";
import { useAuthStore } from "@/shared/auth/stores/authStore";

export function useAuth() {
    const store = useAuthStore();

    return {
        user: computed(() => store.state.user),
        token: computed(() => store.state.token),
        registrationLookups: computed(() => store.state.registrationLookups),
        loading: computed(() => store.state.loading),
        error: computed(() => store.state.error),
        login: store.login,
        fetchRegistrationLookups: store.fetchRegistrationLookups,
        registerOrganization: store.registerOrganization,
        logout: store.logout,
    };
}
