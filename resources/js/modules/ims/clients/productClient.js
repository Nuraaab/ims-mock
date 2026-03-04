import apiClient from "@/shared/auth/clients/apiClient";

export const productClient = {
    list(params = {}) {
        return apiClient.get("/v1/products", { params });
    },
    create(payload) {
        return apiClient.post("/v1/products", payload);
    },
    update(id, payload) {
        return apiClient.put(`/v1/products/${id}`, payload);
    },
    remove(id) {
        return apiClient.delete(`/v1/products/${id}`);
    },
    listProductGroups(params = {}) {
        return apiClient.get("/v1/product-groups", { params });
    },
    listMeasurements(params = {}) {
        return apiClient.get("/v1/measurements", { params });
    },
    listItems(params = {}) {
        return apiClient.get("/v1/items", { params });
    },
};
