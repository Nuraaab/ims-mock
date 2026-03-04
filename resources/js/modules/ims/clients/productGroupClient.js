import apiClient from "@/shared/auth/clients/apiClient";

export const productGroupClient = {
    list(params = {}) {
        return apiClient.get("/v1/product-groups", { params });
    },
    create(payload) {
        return apiClient.post("/v1/product-groups", payload);
    },
    update(id, payload) {
        return apiClient.put(`/v1/product-groups/${id}`, payload);
    },
    remove(id) {
        return apiClient.delete(`/v1/product-groups/${id}`);
    },
};
