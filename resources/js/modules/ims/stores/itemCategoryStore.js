import { defineStore } from "pinia";
import { itemCategoryClient } from "@ims/clients/itemCategoryClient";

export const useItemCategoryStore = defineStore("imsItemCategory", {
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
                const { data } = await itemCategoryClient.list();
                const raw = data?.item_categories?.data ?? data?.item_categories ?? data?.data ?? [];
                if (Array.isArray(raw)) {
                    this.items = raw;
                } else if (raw && typeof raw === "object") {
                    this.items = Array.isArray(raw.data)
                        ? raw.data
                        : Object.values(raw).filter((value) => value && typeof value === "object");
                } else {
                    this.items = [];
                }

                return this.items;
            } catch (error) {
                this.setError(error, "Failed to load item categories.");
                throw error;
            } finally {
                this.loading = false;
            }
        },
        async create(payload) {
            this.saving = true;
            this.resetFeedback();
            try {
                const { data } = await itemCategoryClient.create(payload);
                this.message = data?.message || "Item category created successfully.";
                await this.fetchAll();
                return data;
            } catch (error) {
                this.setError(error, "Failed to create item category.");
                throw error;
            } finally {
                this.saving = false;
            }
        },
        async update(id, payload) {
            this.saving = true;
            this.resetFeedback();
            try {
                const { data } = await itemCategoryClient.update(id, payload);
                this.message = data?.message || "Item category updated successfully.";
                await this.fetchAll();
                return data;
            } catch (error) {
                this.setError(error, "Failed to update item category.");
                throw error;
            } finally {
                this.saving = false;
            }
        },
        async remove(id) {
            this.saving = true;
            this.resetFeedback();
            try {
                const { data } = await itemCategoryClient.remove(id);
                this.message = data?.message || "Item category deleted successfully.";
                await this.fetchAll();
                return data;
            } catch (error) {
                this.setError(error, "Failed to delete item category.");
                throw error;
            } finally {
                this.saving = false;
            }
        },
    },
});
