import apiClient from "@/shared/auth/clients/apiClient";

export const staffClient = {
    lookups(organizationId = null) {
        const params = organizationId ? { organization_id: organizationId } : {};
        return apiClient.get("/users/staff/lookups", { params });
    },
    create(payload) {
        return apiClient.post("/users/staff", payload);
    },
};
