import apiClient from "@/shared/auth/clients/apiClient";

export const roleClient = {
    list() {
        return apiClient.get("/roles");
    },
    permissions() {
        return apiClient.get("/permissions");
    },
    create(payload) {
        return apiClient.post("/roles", payload);
    },
    update(id, payload) {
        return apiClient.put(`/roles/${id}`, payload);
    },
    remove(id) {
        return apiClient.delete(`/roles/${id}`);
    },
};
