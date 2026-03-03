import { defineStore } from "pinia";
import { branchClient } from "@/shared/clients/branchClient";

export const useBranchStore = defineStore("branch", {
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
                const { data } = await branchClient.list();
                this.items = data?.data ?? [];
                return this.items;
            } catch (error) {
                this.setError(error, "Failed to load branches.");
                throw error;
            } finally {
                this.loading = false;
            }
        },
        async create(payload) {
            this.saving = true;
            this.resetFeedback();
            try {
                const { data } = await branchClient.create(payload);
                this.message = data?.message || "Branch created successfully.";
                await this.fetchAll();
                return data;
            } catch (error) {
                this.setError(error, "Failed to create branch.");
                throw error;
            } finally {
                this.saving = false;
            }
        },
        async update(id, payload) {
            this.saving = true;
            this.resetFeedback();
            try {
                const { data } = await branchClient.update(id, payload);
                this.message = data?.message || "Branch updated successfully.";
                await this.fetchAll();
                return data;
            } catch (error) {
                this.setError(error, "Failed to update branch.");
                throw error;
            } finally {
                this.saving = false;
            }
        },
        async remove(id) {
            this.saving = true;
            this.resetFeedback();
            try {
                const { data } = await branchClient.remove(id);
                this.message = data?.message || "Branch deleted successfully.";
                await this.fetchAll();
                return data;
            } catch (error) {
                this.setError(error, "Failed to delete branch.");
                throw error;
            } finally {
                this.saving = false;
            }
        },
    },
});
