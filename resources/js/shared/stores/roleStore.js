import { defineStore } from "pinia";
import { roleClient } from "@/shared/clients/roleClient";

export const useRoleStore = defineStore("role", {
    state: () => ({
        loading: false,
        saving: false,
        message: null,
        error: null,
        validationErrors: {},
        roles: [],
        permissions: [],
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
        async fetchRolesAndPermissions() {
            this.loading = true;
            this.resetFeedback();
            try {
                const [{ data: roleData }, { data: permissionData }] = await Promise.all([
                    roleClient.list(),
                    roleClient.permissions(),
                ]);
                this.roles = roleData?.roles ?? [];
                this.permissions = permissionData?.permissions ?? [];
            } catch (error) {
                this.setErrorState(error, "Failed to load roles and permissions.");
                throw error;
            } finally {
                this.loading = false;
            }
        },
        async createRole(payload) {
            this.saving = true;
            this.resetFeedback();
            try {
                const { data } = await roleClient.create(payload);
                this.message = data?.message || "Role created successfully.";
                await this.fetchRolesAndPermissions();
                return data;
            } catch (error) {
                this.setErrorState(error, "Failed to create role.");
                throw error;
            } finally {
                this.saving = false;
            }
        },
        async updateRole(id, payload) {
            this.saving = true;
            this.resetFeedback();
            try {
                const { data } = await roleClient.update(id, payload);
                this.message = data?.message || "Role updated successfully.";
                await this.fetchRolesAndPermissions();
                return data;
            } catch (error) {
                this.setErrorState(error, "Failed to update role.");
                throw error;
            } finally {
                this.saving = false;
            }
        },
        async deleteRole(id) {
            this.saving = true;
            this.resetFeedback();
            try {
                const { data } = await roleClient.remove(id);
                this.message = data?.message || "Role deleted successfully.";
                await this.fetchRolesAndPermissions();
                return data;
            } catch (error) {
                this.setErrorState(error, "Failed to delete role.");
                throw error;
            } finally {
                this.saving = false;
            }
        },
    },
});
