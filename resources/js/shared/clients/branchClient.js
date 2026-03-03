import apiClient from "@/shared/auth/clients/apiClient";

export const branchClient = {
    list(params = {}) {
        return apiClient.get("/branches", { params });
    },
    create(payload) {
        return apiClient.post("/branches", payload);
    },
    update(id, payload) {
        return apiClient.put(`/branches/${id}`, payload);
    },
    remove(id) {
        return apiClient.delete(`/branches/${id}`);
    },
};
