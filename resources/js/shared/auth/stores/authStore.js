import { reactive } from "vue";
import { authClient } from "@/shared/auth/clients/authClient";

const state = reactive({
    user: null,
    token: localStorage.getItem("user_token"),
    registrationLookups: {
        woredas: [],
        kebeles: [],
        localities: [],
        tax_centers: [],
    },
    loading: false,
    error: null,
});

async function fetchRegistrationLookups() {
    state.loading = true;
    state.error = null;
    try {
        const { data } = await authClient.registrationLookups();
        state.registrationLookups = {
            woredas: data.woredas ?? [],
            kebeles: data.kebeles ?? [],
            localities: data.localities ?? [],
            tax_centers: data.tax_centers ?? [],
        };
        return state.registrationLookups;
    } catch (error) {
        state.error = error?.response?.data?.message || "Failed to load registration options.";
        throw error;
    } finally {
        state.loading = false;
    }
}

async function login(payload) {
    state.loading = true;
    state.error = null;
    try {
        const { data } = await authClient.login(payload);
        state.user = data.user ?? null;
        state.token = data.token ?? null;
        if (state.token) {
            localStorage.setItem("user_token", state.token);
        }
        return data;
    } catch (error) {
        state.error = error?.response?.data?.message || "Login failed.";
        throw error;
    } finally {
        state.loading = false;
    }
}

async function registerOrganization(payload) {
    state.loading = true;
    state.error = null;
    try {
        const { data } = await authClient.registerOrganization(payload);
        state.user = data.user ?? null;
        state.token = data.token ?? null;
        if (state.token) {
            localStorage.setItem("user_token", state.token);
        }
        return data;
    } catch (error) {
        state.error = error?.response?.data?.message || "Registration failed.";
        throw error;
    } finally {
        state.loading = false;
    }
}

async function logout() {
    state.loading = true;
    state.error = null;
    try {
        await authClient.logout();
        state.user = null;
        state.token = null;
        localStorage.removeItem("user_token");
    } catch (error) {
        state.error = error?.response?.data?.message || "Logout failed.";
        throw error;
    } finally {
        state.loading = false;
    }
}

export function useAuthStore() {
    return {
        state,
        login,
        fetchRegistrationLookups,
        registerOrganization,
        logout,
    };
}
