import { defineStore } from "pinia";
import { productGroupClient } from "@ims/clients/productGroupClient";

export const useProductGroupStore = defineStore("imsProductGroup", {
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
                const { data } = await productGroupClient.list();
                this.items = data?.data ?? [];
                return this.items;
            } catch (error) {
                this.setError(error, "Failed to load product groups.");
                throw error;
            } finally {
                this.loading = false;
            }
        },
        async create(payload) {
            this.saving = true;
            this.resetFeedback();
            try {
                const { data } = await productGroupClient.create(payload);
                this.message = data?.message || "Product group created successfully.";
                await this.fetchAll();
                return data;
            } catch (error) {
                this.setError(error, "Failed to create product group.");
                throw error;
            } finally {
                this.saving = false;
            }
        },
        async update(id, payload) {
            this.saving = true;
            this.resetFeedback();
            try {
                const { data } = await productGroupClient.update(id, payload);
                this.message = data?.message || "Product group updated successfully.";
                await this.fetchAll();
                return data;
            } catch (error) {
                this.setError(error, "Failed to update product group.");
                throw error;
            } finally {
                this.saving = false;
            }
        },
        async remove(id) {
            this.saving = true;
            this.resetFeedback();
            try {
                const { data } = await productGroupClient.remove(id);
                this.message = data?.message || "Product group deleted successfully.";
                await this.fetchAll();
                return data;
            } catch (error) {
                this.setError(error, "Failed to delete product group.");
                throw error;
            } finally {
                this.saving = false;
            }
        },
    },
});
