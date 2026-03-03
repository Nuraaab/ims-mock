import apiClient from "@/shared/auth/clients/apiClient";

export const outletClient = {
    list(params = {}) {
        return apiClient.get("/outlets", { params });
    },
    create(payload) {
        return apiClient.post("/outlets", payload);
    },
    update(id, payload) {
        return apiClient.put(`/outlets/${id}`, payload);
    },
    remove(id) {
        return apiClient.delete(`/outlets/${id}`);
    },
};
