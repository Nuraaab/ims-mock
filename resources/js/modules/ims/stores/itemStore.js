import { defineStore } from "pinia";
import { itemClient } from "@ims/clients/itemClient";

export const useItemStore = defineStore("imsItem", {
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
                const { data } = await itemClient.list();
                const raw = data?.items ?? data?.data ?? [];
                this.items = Array.isArray(raw) ? raw : [];
                return this.items;
            } catch (error) {
                this.setError(error, "Failed to load items.");
                throw error;
            } finally {
                this.loading = false;
            }
        },
        async create(payload) {
            this.saving = true;
            this.resetFeedback();
            try {
                const { data } = await itemClient.create(payload);
                this.message = data?.message || "Item created successfully.";
                await this.fetchAll();
                return data;
            } catch (error) {
                this.setError(error, "Failed to create item.");
                throw error;
            } finally {
                this.saving = false;
            }
        },
        async update(id, payload) {
            this.saving = true;
            this.resetFeedback();
            try {
                const { data } = await itemClient.update(id, payload);
                this.message = data?.message || "Item updated successfully.";
                await this.fetchAll();
                return data;
            } catch (error) {
                this.setError(error, "Failed to update item.");
                throw error;
            } finally {
                this.saving = false;
            }
        },
        async remove(id) {
            this.saving = true;
            this.resetFeedback();
            try {
                const { data } = await itemClient.remove(id);
                this.message = data?.message || "Item deleted successfully.";
                await this.fetchAll();
                return data;
            } catch (error) {
                this.setError(error, "Failed to delete item.");
                throw error;
            } finally {
                this.saving = false;
            }
        },
    },
});
