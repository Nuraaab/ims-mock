import apiClient from "@/shared/auth/clients/apiClient";

export const staffClient = {
    list(organizationId = null) {
        const params = organizationId ? { organization_id: organizationId } : {};
        return apiClient.get("/users/staff", { params });
    },
    lookups(organizationId = null) {
        const params = organizationId ? { organization_id: organizationId } : {};
        return apiClient.get("/users/staff/lookups", { params });
    },
    create(payload) {
        return apiClient.post("/users/staff", payload);
    },
    update(userId, payload) {
        return apiClient.put(`/users/staff/${userId}`, payload);
    },
    remove(userId) {
        return apiClient.delete(`/users/staff/${userId}`);
    },
};
