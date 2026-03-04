import apiClient from "@/shared/auth/clients/apiClient";

export const itemCategoryClient = {
    list(params = {}) {
        return apiClient.get("/v1/item-categories", { params });
    },
    create(payload) {
        return apiClient.post("/v1/item-categories", payload);
    },
    update(id, payload) {
        return apiClient.put(`/v1/item-categories/${id}`, payload);
    },
    remove(id) {
        return apiClient.delete(`/v1/item-categories/${id}`);
    },
};
