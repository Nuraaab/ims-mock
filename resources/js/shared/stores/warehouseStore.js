import { defineStore } from "pinia";
import { warehouseClient } from "@/shared/clients/warehouseClient";

export const useWarehouseStore = defineStore("warehouse", {
    state: () => ({
        items: [],
        loading: false,
        saving: false,
        message: null,
        error: null,
        validationErrors: {},
    }),

    actions: {
        resetFeedback() {
            this.message = null;
            this.error = null;
            this.validationErrors = {};
        },
        setError(error, fallback) {
            this.error = error?.response?.data?.message || fallback;
            this.validationErrors = error?.response?.data?.errors || {};
        },
        async fetchAll() {
            this.loading = true;
            this.resetFeedback();
            try {
                const { data } = await warehouseClient.list();
                this.items = data?.data ?? [];
                return this.items;
            } catch (error) {
                this.setError(error, "Failed to load warehouses.");
                throw error;
            } finally {
                this.loading = false;
            }
        },
        async create(payload) {
            this.saving = true;
            this.resetFeedback();
            try {
                const { data } = await warehouseClient.create(payload);
                this.message = data?.message || "Warehouse created successfully.";
                await this.fetchAll();
                return data;
            } catch (error) {
                this.setError(error, "Failed to create warehouse.");
                throw error;
            } finally {
                this.saving = false;
            }
        },
        async update(id, payload) {
            this.saving = true;
            this.resetFeedback();
            try {
                const { data } = await warehouseClient.update(id, payload);
                this.message = data?.message || "Warehouse updated successfully.";
                await this.fetchAll();
                return data;
            } catch (error) {
                this.setError(error, "Failed to update warehouse.");
                throw error;
            } finally {
                this.saving = false;
            }
        },
        async remove(id) {
            this.saving = true;
            this.resetFeedback();
            try {
                const { data } = await warehouseClient.remove(id);
                this.message = data?.message || "Warehouse deleted successfully.";
                await this.fetchAll();
                return data;
            } catch (error) {
                this.setError(error, "Failed to delete warehouse.");
                throw error;
            } finally {
                this.saving = false;
            }
        },
    },
});
