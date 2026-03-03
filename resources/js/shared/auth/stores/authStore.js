import { defineStore } from "pinia";
import { authClient } from "@/shared/auth/clients/authClient";

export const useAuthStore = defineStore("auth", {
    state: () => ({
        user: null,
        token: localStorage.getItem("user_token"),
        permissions: [],
        registrationLookups: {
            woredas: [],
            kebeles: [],
            localities: [],
            tax_centers: [],
        },
        loading: false,
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

        setErrorState(error, fallbackMessage) {
            this.error = error?.response?.data?.message || fallbackMessage;
            this.validationErrors = error?.response?.data?.errors || {};
        },

        async fetchRegistrationLookups() {
            this.loading = true;
            this.resetFeedback();
            try {
                const { data } = await authClient.registrationLookups();
                this.registrationLookups = {
                    woredas: data.woredas ?? [],
                    kebeles: data.kebeles ?? [],
                    localities: data.localities ?? [],
                    tax_centers: data.tax_centers ?? [],
                };
                return this.registrationLookups;
            } catch (error) {
                this.setErrorState(error, "Failed to load registration options.");
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async login(payload) {
            this.loading = true;
            this.resetFeedback();
            try {
                const { data } = await authClient.login(payload);
                this.user = data.user ?? null;
                this.token = data.token ?? null;
                this.permissions = data.permissions ?? [];
                this.message = data.message || "Login successful.";
                if (this.token) {
                    localStorage.setItem("user_token", this.token);
                }
                return data;
            } catch (error) {
                this.setErrorState(error, "Login failed.");
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async registerOrganization(payload) {
            this.loading = true;
            this.resetFeedback();
            try {
                const { data } = await authClient.registerOrganization(payload);
                this.user = data.user ?? null;
                this.token = data.token ?? null;
                this.permissions = data.permissions ?? [];
                this.message = data.message || "Registration successful.";
                if (this.token) {
                    localStorage.setItem("user_token", this.token);
                }
                return data;
            } catch (error) {
                this.setErrorState(error, "Registration failed.");
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async fetchMe() {
            this.loading = true;
            this.resetFeedback();
            try {
                const { data } = await authClient.me();
                this.user = data.user ?? null;
                this.permissions = data.permissions ?? [];
                return data;
            } catch (error) {
                this.setErrorState(error, "Failed to load current user.");
                throw error;
            } finally {
                this.loading = false;
            }
        },

        hasPermission(permissionKey) {
            return this.permissions.includes(permissionKey);
        },

        async logout() {
            this.loading = true;
            this.resetFeedback();
            try {
                await authClient.logout();
                this.user = null;
                this.token = null;
                this.permissions = [];
                this.message = "Logged out successfully.";
                localStorage.removeItem("user_token");
            } catch (error) {
                this.setErrorState(error, "Logout failed.");
                throw error;
            } finally {
                this.loading = false;
            }
        },
    },
});
