import apiClient from "@/shared/auth/clients/apiClient";

export const authClient = {
    registrationLookups() {
        return apiClient.get("/auth/registration-lookups");
    },
    registerOrganization(payload) {
        return apiClient.post("/auth/register-organization", payload);
    },
    login(payload) {
        return apiClient.post("/auth/login", payload);
    },
    logout() {
        return apiClient.post("/auth/logout");
    },
    me() {
        return apiClient.get("/auth/me");
    },
};
