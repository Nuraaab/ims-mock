import { defineStore } from "pinia";
import { outletClient } from "@/shared/clients/outletClient";

export const useOutletStore = defineStore("outlet", {
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
                const { data } = await outletClient.list();
                this.items = data?.data ?? [];
                return this.items;
            } catch (error) {
                this.setError(error, "Failed to load outlets.");
                throw error;
            } finally {
                this.loading = false;
            }
        },
        async create(payload) {
            this.saving = true;
            this.resetFeedback();
            try {
                const { data } = await outletClient.create(payload);
                this.message = data?.message || "Outlet created successfully.";
                await this.fetchAll();
                return data;
            } catch (error) {
                this.setError(error, "Failed to create outlet.");
                throw error;
            } finally {
                this.saving = false;
            }
        },
        async update(id, payload) {
            this.saving = true;
            this.resetFeedback();
            try {
                const { data } = await outletClient.update(id, payload);
                this.message = data?.message || "Outlet updated successfully.";
                await this.fetchAll();
                return data;
            } catch (error) {
                this.setError(error, "Failed to update outlet.");
                throw error;
            } finally {
                this.saving = false;
            }
        },
        async remove(id) {
            this.saving = true;
            this.resetFeedback();
            try {
                const { data } = await outletClient.remove(id);
                this.message = data?.message || "Outlet deleted successfully.";
                await this.fetchAll();
                return data;
            } catch (error) {
                this.setError(error, "Failed to delete outlet.");
                throw error;
            } finally {
                this.saving = false;
            }
        },
    },
});
