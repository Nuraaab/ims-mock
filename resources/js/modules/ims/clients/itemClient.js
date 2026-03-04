import apiClient from "@/shared/auth/clients/apiClient";

export const itemClient = {
    list(params = {}) {
        return apiClient.get("/v1/items", { params });
    },
    create(payload) {
        return apiClient.post("/v1/items", payload);
    },
    update(id, payload) {
        return apiClient.put(`/v1/items/${id}`, payload);
    },
    remove(id) {
        return apiClient.delete(`/v1/items/${id}`);
    },
};
