<template>
    <DashboardLayout>
        <section class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <div class="mb-6">
                    <h2 class="ui-text text-2xl font-semibold tracking-tight">Create Role</h2>
                    <p class="ui-muted mt-1 text-sm">Add a role and assign permissions.</p>
                </div>

                <div class="ui-card p-6 md:p-7">
                    <form class="space-y-5" @submit.prevent="submit">
                        <div>
                            <label class="ui-label" for="role-name">Role Name</label>
                            <input
                                id="role-name"
                                v-model.trim="form.name"
                                type="text"
                                required
                                :disabled="loading"
                                class="ui-input"
                                placeholder="e.g. Store Manager"
                            />
                        </div>

                        <div>
                            <p class="ui-label">Permissions</p>
                            <div class="ui-surface-soft max-h-64 space-y-3 overflow-y-auto p-3">
                                <section v-for="group in groupedPermissions" :key="group.name">
                                    <p class="ui-muted mb-1 px-2 text-xs font-semibold uppercase tracking-wide">
                                        {{ group.name }}
                                    </p>
                                    <label
                                        v-for="permission in group.items"
                                        :key="permission.id"
                                        class="ui-text flex cursor-pointer items-center gap-2 rounded-lg px-2 py-1.5 text-sm hover:bg-white/50"
                                    >
                                        <input
                                            v-model="form.permission_ids"
                                            :value="permission.id"
                                            type="checkbox"
                                            :disabled="loading || bootLoading"
                                        />
                                        <span>{{ permission.value }}</span>
                                        <span class="ui-muted text-xs">({{ permission.key }})</span>
                                    </label>
                                </section>
                                <p v-if="!permissions.length && !bootLoading" class="ui-muted text-sm">
                                    No permissions found.
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between gap-3">
                            <p class="ui-muted text-xs">Role name must be unique.</p>
                            <button
                                type="submit"
                                :disabled="loading || bootLoading || !form.name"
                                class="ui-btn-primary"
                            >
                                {{ loading ? "Creating..." : "Create Role" }}
                            </button>
                        </div>
                    </form>

                    <p v-if="message" class="ui-alert-success">{{ message }}</p>
                    <p v-if="error" class="ui-alert-error">{{ error }}</p>
                    <ul v-if="errorItems.length" class="mt-2 list-disc pl-5 text-sm text-red-700">
                        <li v-for="(item, index) in errorItems" :key="index">{{ item }}</li>
                    </ul>
                    <p v-if="bootError" class="ui-alert-error">
                        {{ bootError }}
                    </p>
                </div>
            </div>

            <div class="ui-card h-fit p-4">
                <h3 class="ui-text text-base font-semibold">Existing Roles</h3>
                <p class="ui-muted mt-1 text-xs">Fetched from API</p>

                <div class="mt-4 space-y-2">
                    <p v-if="bootLoading" class="ui-muted text-sm">Loading roles...</p>
                    <div v-else-if="!roles.length" class="ui-muted text-sm">No roles created yet.</div>
                    <article
                        v-for="role in roles"
                        :key="role.id"
                        class="ui-surface-soft p-3"
                    >
                        <p class="ui-text text-sm font-semibold">{{ role.name }}</p>
                        <p class="ui-muted mt-1 text-xs">
                            {{ (role.permissions || []).length }} permission(s)
                        </p>
                    </article>
                </div>
            </div>
        </section>
    </DashboardLayout>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from "vue";
import apiClient from "@/shared/auth/clients/apiClient";
import DashboardLayout from "@/shared/components/dashboard/DashboardLayout.vue";

const loading = ref(false);
const bootLoading = ref(false);
const message = ref(null);
const error = ref(null);
const bootError = ref(null);
const validationErrors = ref({});
const roles = ref([]);
const permissions = ref([]);

const form = reactive({
    name: "",
    permission_ids: [],
});

const errorItems = computed(() => Object.values(validationErrors.value).flat());
const groupedPermissions = computed(() => {
    const groups = new Map();

    for (const permission of permissions.value) {
        const prefix = permission.key?.split(".")?.[0] || "general";
        if (!groups.has(prefix)) {
            groups.set(prefix, []);
        }
        groups.get(prefix).push(permission);
    }

    return Array.from(groups.entries())
        .sort(([a], [b]) => a.localeCompare(b))
        .map(([name, items]) => ({
            name,
            items: [...items].sort((a, b) => a.key.localeCompare(b.key)),
        }));
});

function resetFeedback() {
    message.value = null;
    error.value = null;
    validationErrors.value = {};
}

async function loadData() {
    bootLoading.value = true;
    bootError.value = null;

    try {
        const [{ data: roleData }, { data: permissionData }] = await Promise.all([
            apiClient.get("/roles"),
            apiClient.get("/permissions"),
        ]);

        roles.value = roleData?.roles ?? [];
        permissions.value = permissionData?.permissions ?? [];
    } catch (err) {
        bootError.value = err?.response?.data?.message || "Failed to load roles and permissions.";
    } finally {
        bootLoading.value = false;
    }
}

onMounted(loadData);

async function submit() {
    loading.value = true;
    resetFeedback();

    try {
        const payload = {
            name: form.name,
            permission_ids: form.permission_ids,
        };
        const { data } = await apiClient.post("/roles", payload);
        message.value = data?.message || "Role created successfully.";
        form.name = "";
        form.permission_ids = [];
        await loadData();
    } catch (err) {
        error.value = err?.response?.data?.message || "Failed to create role.";
        validationErrors.value = err?.response?.data?.errors || {};
    } finally {
        loading.value = false;
    }
}
</script>
