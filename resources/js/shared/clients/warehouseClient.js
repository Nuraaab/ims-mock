import apiClient from "@/shared/auth/clients/apiClient";

export const warehouseClient = {
    list(params = {}) {
        return apiClient.get("/warehouses", { params });
    },
    create(payload) {
        return apiClient.post("/warehouses", payload);
    },
    update(id, payload) {
        return apiClient.put(`/warehouses/${id}`, payload);
    },
    remove(id) {
        return apiClient.delete(`/warehouses/${id}`);
    },
};
