import { defineStore } from "pinia";
import { staffClient } from "@/shared/clients/staffClient";

export const useStaffStore = defineStore("staff", {
    state: () => ({
        loading: false,
        saving: false,
        message: null,
        error: null,
        validationErrors: {},
        lookups: {
            organization_id: null,
            scopes: [],
            roles: [],
            branches: [],
            warehouses: [],
            outlets: [],
        },
        staff: [],
    }),
    actions: {
        resetFeedback() {
            this.message = null;
            this.error = null;
            this.validationErrors = {};
        },
        setErrorState(error, fallbackMessage) {
            this.error = error?.response?.data?.message || fallbackMessage;
            this.validationErrors = error?.response?.data?.errors || {};
        },
        async fetchLookups(organizationId = null) {
            this.loading = true;
            this.resetFeedback();
            try {
                const { data } = await staffClient.lookups(organizationId);
                this.lookups = {
                    organization_id: data.organization_id ?? null,
                    scopes: data.scopes ?? [],
                    roles: data.roles ?? [],
                    branches: data.branches ?? [],
                    warehouses: data.warehouses ?? [],
                    outlets: data.outlets ?? [],
                };
                return this.lookups;
            } catch (error) {
                this.setErrorState(error, "Failed to load staff lookups.");
                throw error;
            } finally {
                this.loading = false;
            }
        },
        async createStaff(payload) {
            this.saving = true;
            this.resetFeedback();
            try {
                const { data } = await staffClient.create(payload);
                this.message = data?.message || "Staff user created successfully.";
                await this.fetchStaff(payload.organization_id ?? this.lookups.organization_id ?? null);
                return data;
            } catch (error) {
                this.setErrorState(error, "Failed to create staff user.");
                throw error;
            } finally {
                this.saving = false;
            }
        },
        async fetchStaff(organizationId = null) {
            this.loading = true;
            this.resetFeedback();
            try {
                const { data } = await staffClient.list(organizationId);
                this.staff = data?.staff ?? [];
                return this.staff;
            } catch (error) {
                this.setErrorState(error, "Failed to load staff users.");
                throw error;
            } finally {
                this.loading = false;
            }
        },
        async updateStaff(userId, payload) {
            this.saving = true;
            this.resetFeedback();
            try {
                const { data } = await staffClient.update(userId, payload);
                this.message = data?.message || "Staff user updated successfully.";
                await this.fetchStaff(payload.organization_id ?? this.lookups.organization_id ?? null);
                return data;
            } catch (error) {
                this.setErrorState(error, "Failed to update staff user.");
                throw error;
            } finally {
                this.saving = false;
            }
        },
        async deleteStaff(userId, organizationId = null) {
            this.saving = true;
            this.resetFeedback();
            try {
                const { data } = await staffClient.remove(userId);
                this.message = data?.message || "Staff user deleted successfully.";
                await this.fetchStaff(organizationId ?? this.lookups.organization_id ?? null);
                return data;
            } catch (error) {
                this.setErrorState(error, "Failed to delete staff user.");
                throw error;
            } finally {
                this.saving = false;
            }
        },
    },
});
