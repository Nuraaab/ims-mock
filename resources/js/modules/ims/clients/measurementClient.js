import apiClient from "@/shared/auth/clients/apiClient";

export const measurementClient = {
    list(params = {}) {
        return apiClient.get("/v1/measurements", { params });
    },
    create(payload) {
        return apiClient.post("/v1/measurements", payload);
    },
    update(id, payload) {
        return apiClient.put(`/v1/measurements/${id}`, payload);
    },
    remove(id) {
        return apiClient.delete(`/v1/measurements/${id}`);
    },
};
