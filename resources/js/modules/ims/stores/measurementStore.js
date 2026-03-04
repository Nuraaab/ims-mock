import { defineStore } from "pinia";
import { measurementClient } from "@ims/clients/measurementClient";

export const useMeasurementStore = defineStore("imsMeasurement", {
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
                const { data } = await measurementClient.list();
                const raw = data?.measurements ?? data?.data ?? [];
                this.items = Array.isArray(raw) ? raw : [];
                return this.items;
            } catch (error) {
                this.setError(error, "Failed to load measurements.");
                throw error;
            } finally {
                this.loading = false;
            }
        },
        async create(payload) {
            this.saving = true;
            this.resetFeedback();
            try {
                const { data } = await measurementClient.create(payload);
                this.message = data?.message || "Measurement created successfully.";
                await this.fetchAll();
                return data;
            } catch (error) {
                this.setError(error, "Failed to create measurement.");
                throw error;
            } finally {
                this.saving = false;
            }
        },
        async update(id, payload) {
            this.saving = true;
            this.resetFeedback();
            try {
                const { data } = await measurementClient.update(id, payload);
                this.message = data?.message || "Measurement updated successfully.";
                await this.fetchAll();
                return data;
            } catch (error) {
                this.setError(error, "Failed to update measurement.");
                throw error;
            } finally {
                this.saving = false;
            }
        },
        async remove(id) {
            this.saving = true;
            this.resetFeedback();
            try {
                const { data } = await measurementClient.remove(id);
                this.message = data?.message || "Measurement deleted successfully.";
                await this.fetchAll();
                return data;
            } catch (error) {
                this.setError(error, "Failed to delete measurement.");
                throw error;
            } finally {
                this.saving = false;
            }
        },
    },
});
