import { defineStore } from "pinia";
import { productClient } from "@ims/clients/productClient";

export const useProductStore = defineStore("imsProduct", {
    state: () => ({
        items: [],
        productGroups: [],
        measurements: [],
        itemOptions: [],
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
                const { data } = await productClient.list();
                const raw = data?.products?.data ?? data?.products ?? data?.data ?? [];
                this.items = Array.isArray(raw) ? raw : [];
                return this.items;
            } catch (error) {
                this.setError(error, "Failed to load products.");
                throw error;
            } finally {
                this.loading = false;
            }
        },
        async fetchLookups() {
            try {
                const [groupsRes, measurementsRes, itemsRes] = await Promise.allSettled([
                    productClient.listProductGroups({ per_page: 200 }),
                    productClient.listMeasurements({ per_page: 200 }),
                    productClient.listItems({ per_page: 200 }),
                ]);

                if (groupsRes.status === "fulfilled") {
                    const groupsRaw = groupsRes.value?.data?.data ?? groupsRes.value?.data?.product_groups ?? [];
                    this.productGroups = Array.isArray(groupsRaw) ? groupsRaw : [];
                } else {
                    this.productGroups = [];
                }

                if (measurementsRes.status === "fulfilled") {
                    const measurementRaw = measurementsRes.value?.data?.measurements ?? measurementsRes.value?.data?.data ?? [];
                    this.measurements = Array.isArray(measurementRaw) ? measurementRaw : [];
                } else {
                    this.measurements = [];
                }

                // Items endpoint may not exist yet; keep graceful fallback.
                if (itemsRes.status === "fulfilled") {
                    const itemRaw = itemsRes.value?.data?.items?.data ?? itemsRes.value?.data?.items ?? itemsRes.value?.data?.data ?? [];
                    this.itemOptions = Array.isArray(itemRaw) ? itemRaw : [];
                } else {
                    this.itemOptions = [];
                }
            } catch {
                this.productGroups = [];
                this.measurements = [];
                this.itemOptions = [];
            }
        },
        async create(payload) {
            this.saving = true;
            this.resetFeedback();
            try {
                const { data } = await productClient.create(payload);
                this.message = data?.message || "Product created successfully.";
                await this.fetchAll();
                return data;
            } catch (error) {
                this.setError(error, "Failed to create product.");
                throw error;
            } finally {
                this.saving = false;
            }
        },
        async update(id, payload) {
            this.saving = true;
            this.resetFeedback();
            try {
                const { data } = await productClient.update(id, payload);
                this.message = data?.message || "Product updated successfully.";
                await this.fetchAll();
                return data;
            } catch (error) {
                this.setError(error, "Failed to update product.");
                throw error;
            } finally {
                this.saving = false;
            }
        },
        async remove(id) {
            this.saving = true;
            this.resetFeedback();
            try {
                const { data } = await productClient.remove(id);
                this.message = data?.message || "Product deleted successfully.";
                await this.fetchAll();
                return data;
            } catch (error) {
                this.setError(error, "Failed to delete product.");
                throw error;
            } finally {
                this.saving = false;
            }
        },
    },
});
