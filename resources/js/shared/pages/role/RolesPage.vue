<template>
    <DashboardLayout>
        <section class="mb-6 flex items-center justify-between gap-3">
            <div>
                <h2 class="ui-text text-2xl font-semibold tracking-tight">Roles</h2>
                <p class="ui-muted mt-1 text-sm">Create roles and assign permissions.</p>
            </div>
            <AppButton :disabled="loading" @click="openCreate">Create Role</AppButton>
        </section>

        <section class="ui-card overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="ui-surface-soft text-left">
                        <th class="px-4 py-3">Role</th>
                        <th class="px-4 py-3">Permissions Count</th>
                        <th class="px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="role in roles" :key="role.id" class="border-t" :style="{ borderColor: 'var(--ui-border)' }">
                        <td class="px-4 py-3">{{ role.name }}</td>
                        <td class="px-4 py-3">{{ (role.permissions || []).length }}</td>
                        <td class="px-4 py-3">
                            <div v-if="!role.is_protected">
                                <TableActions @edit="openEdit(role)" @delete="removeRole(role.id)" />
                            </div>
                            <span v-else class="ui-muted text-xs">Protected</span>
                        </td>
                    </tr>
                    <tr v-if="!roles.length && !loading">
                        <td class="ui-muted px-4 py-4" colspan="3">No roles found.</td>
                    </tr>
                </tbody>
            </table>
        </section>

        <p v-if="message" class="ui-alert-success">{{ message }}</p>
        <p v-if="error" class="ui-alert-error">{{ error }}</p>
        <ul v-if="errorItems.length" class="mt-2 list-disc pl-5 text-sm text-red-700 dark:text-red-300">
            <li v-for="(item, index) in errorItems" :key="index">{{ item }}</li>
        </ul>

        <AppModal :open="modalOpen" :title="editingId ? 'Edit Role' : 'Create Role'" @close="closeModal">
            <form class="grid grid-cols-1 gap-3" @submit.prevent="submit">
                <div>
                    <label class="ui-label">Role Name</label>
                    <input v-model.trim="form.name" class="ui-input" required :disabled="saving" />
                </div>

                <div>
                    <p class="ui-label">Permissions</p>
                    <div class="ui-surface-soft max-h-64 space-y-2 overflow-y-auto p-3">
                        <section v-for="group in groupedPermissions" :key="group.name">
                            <p class="ui-muted mb-1 px-1 text-xs font-semibold uppercase tracking-wide">{{ group.name }}</p>
                            <label
                                v-for="permission in group.items"
                                :key="permission.id"
                                class="ui-text flex items-center gap-2 rounded-lg px-2 py-1.5 text-sm hover:bg-white/50"
                            >
                                <input v-model="form.permission_ids" :value="permission.id" type="checkbox" :disabled="saving || loading" />
                                <span>{{ permission.value }}</span>
                            </label>
                        </section>
                    </div>
                </div>

                <div class="flex justify-end gap-2">
                    <AppButton variant="soft" @click="closeModal">Cancel</AppButton>
                    <AppButton type="submit" :disabled="saving">
                        {{ saving ? "Saving..." : editingId ? "Update" : "Create" }}
                    </AppButton>
                </div>
            </form>
        </AppModal>
    </DashboardLayout>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from "vue";
import DashboardLayout from "@/shared/components/dashboard/DashboardLayout.vue";
import AppButton from "@/shared/components/ui/AppButton.vue";
import AppModal from "@/shared/components/ui/AppModal.vue";
import TableActions from "@/shared/components/ui/TableActions.vue";
import { useRoles } from "@/shared/composables/useRoles";

const { loading, saving, message, error, validationErrors, roles, permissions, fetchRolesAndPermissions, createRole, updateRole, deleteRole } = useRoles();

const modalOpen = ref(false);
const editingId = ref(null);
const form = reactive({
    name: "",
    permission_ids: [],
});

const errorItems = computed(() => Object.values(validationErrors.value).flat());
const groupedPermissions = computed(() => {
    const groups = new Map();
    for (const permission of permissions.value) {
        const prefix = permission.key?.split(".")?.[0] || "general";
        if (!groups.has(prefix)) groups.set(prefix, []);
        groups.get(prefix).push(permission);
    }
    return Array.from(groups.entries())
        .sort(([a], [b]) => a.localeCompare(b))
        .map(([name, items]) => ({ name, items: [...items].sort((a, b) => a.key.localeCompare(b.key)) }));
});

onMounted(fetchRolesAndPermissions);

function openCreate() {
    editingId.value = null;
    form.name = "";
    form.permission_ids = [];
    modalOpen.value = true;
}

function openEdit(role) {
    editingId.value = role.id;
    form.name = role.name;
    form.permission_ids = (role.permissions || []).map((permission) => permission.id);
    modalOpen.value = true;
}

function closeModal() {
    modalOpen.value = false;
}

async function submit() {
    const payload = {
        name: form.name,
        permission_ids: form.permission_ids,
    };

    if (editingId.value) {
        await updateRole(editingId.value, payload);
    } else {
        await createRole(payload);
    }

    form.name = "";
    form.permission_ids = [];
    editingId.value = null;
    closeModal();
}

async function removeRole(roleId) {
    await deleteRole(roleId);
}
</script>
