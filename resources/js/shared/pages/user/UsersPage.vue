<template>
    <DashboardLayout>
        <section class="mb-6 flex items-center justify-between gap-3">
            <div>
                <h2 class="ui-text text-2xl font-semibold tracking-tight">Users</h2>
                <p class="ui-muted mt-1 text-sm">Create staff and assign role with scope.</p>
            </div>
            <AppButton :disabled="loading" @click="openCreate">Create User</AppButton>
        </section>

        <section class="ui-card overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="ui-surface-soft text-left">
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Role</th>
                        <th class="px-4 py-3">Scope</th>
                        <th class="px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="entry in staff" :key="entry.user.id" class="border-t" :style="{ borderColor: 'var(--ui-border)' }">
                        <td class="px-4 py-3">{{ entry.user.name }}</td>
                        <td class="px-4 py-3">{{ entry.user.email }}</td>
                        <td class="px-4 py-3">{{ entry.role.name }}</td>
                        <td class="px-4 py-3">{{ formatScope(entry.binding.scope) }}</td>
                        <td class="px-4 py-3">
                            <TableActions @edit="openEdit(entry)" @delete="removeStaff(entry.user.id)" />
                        </td>
                    </tr>
                    <tr v-if="!staff.length">
                        <td class="ui-muted px-4 py-4" colspan="5">No staff users found.</td>
                    </tr>
                </tbody>
            </table>
        </section>

        <AppModal :open="modalOpen" :title="editingUserId ? 'Edit Staff User' : 'Create Staff User'" @close="closeModal">
            <form class="grid grid-cols-1 gap-3 md:grid-cols-2" @submit.prevent="submit">
                <div>
                    <label class="ui-label">Full Name</label>
                    <input v-model.trim="form.name" class="ui-input" required :disabled="saving" />
                </div>
                <div>
                    <label class="ui-label">Email</label>
                    <input v-model.trim="form.email" type="email" class="ui-input" required :disabled="saving" />
                </div>
                <div>
                    <label class="ui-label">Phone</label>
                    <input v-model.trim="form.phone" class="ui-input" :disabled="saving" />
                </div>
                <div>
                    <label class="ui-label">National ID</label>
                    <input v-model.trim="form.national_id" class="ui-input" :disabled="saving" />
                </div>
                <div v-if="!editingUserId">
                    <label class="ui-label">Password</label>
                    <input v-model="form.password" type="password" class="ui-input" required :disabled="saving" />
                </div>
                <div v-if="!editingUserId">
                    <label class="ui-label">Confirm Password</label>
                    <input v-model="form.password_confirmation" type="password" class="ui-input" required :disabled="saving" />
                </div>
                <div>
                    <label class="ui-label">Role</label>
                    <select v-model.number="form.role_id" class="ui-select" required :disabled="saving || loading">
                        <option :value="null" disabled>Select role</option>
                        <option v-for="role in lookups.roles" :key="role.id" :value="role.id">
                            {{ role.name }}
                        </option>
                    </select>
                </div>
                <div>
                    <label class="ui-label">Scope</label>
                    <select v-model="form.scope" class="ui-select" required :disabled="saving || loading">
                        <option v-for="scope in lookups.scopes" :key="scope" :value="scope">
                            {{ formatScope(scope) }}
                        </option>
                    </select>
                </div>
                <div>
                    <label class="ui-label">Scope Target</label>
                    <select v-model.number="form.scope_id" class="ui-select" :disabled="saving || !scopeOptions.length">
                        <option :value="null" disabled>Select target</option>
                        <option v-for="target in scopeOptions" :key="target.id" :value="target.id">
                            {{ target.name }}
                        </option>
                    </select>
                </div>
                <label class="ui-muted mt-6 inline-flex items-center gap-2 text-sm">
                    <input v-model="form.include_descendents" type="checkbox" class="h-4 w-4 rounded border-slate-300" :disabled="saving" />
                    Include descendants
                </label>
                <div class="md:col-span-2 flex justify-end gap-2">
                    <AppButton variant="soft" @click="closeModal">Cancel</AppButton>
                    <AppButton type="submit" :disabled="saving">
                        {{ saving ? "Saving..." : editingUserId ? "Update" : "Create" }}
                    </AppButton>
                </div>

                <div class="md:col-span-2">
                    <p v-if="error" class="ui-alert-error">{{ error }}</p>
                    <ul v-if="errorItems.length" class="mt-2 list-disc pl-5 text-sm text-red-700 dark:text-red-300">
                        <li v-for="(item, index) in errorItems" :key="index">{{ item }}</li>
                    </ul>
                </div>
            </form>
        </AppModal>
    </DashboardLayout>
</template>

<script setup>
import { computed, onMounted, reactive, ref, watch } from "vue";
import DashboardLayout from "@/shared/components/dashboard/DashboardLayout.vue";
import AppButton from "@/shared/components/ui/AppButton.vue";
import AppModal from "@/shared/components/ui/AppModal.vue";
import TableActions from "@/shared/components/ui/TableActions.vue";
import { useStaff } from "@/shared/composables/useStaff";
import { useNotifier } from "@/shared/composables/useNotifier";

const { loading, saving, lookups, staff, error, validationErrors, fetchLookups, fetchStaff, createStaff, updateStaff, deleteStaff, resetFeedback } = useStaff();
const { notifySuccess, notifyError, getErrorMessage, confirmDelete } = useNotifier();

const modalOpen = ref(false);
const editingUserId = ref(null);
const form = reactive({
    name: "",
    email: "",
    phone: "",
    national_id: "",
    password: "",
    password_confirmation: "",
    role_id: null,
    scope: "organization",
    scope_id: null,
    include_descendents: false,
});

const errorItems = computed(() => Object.values(validationErrors.value).flat());
const scopeOptions = computed(() => {
    if (form.scope === "organization") {
        return lookups.value.organization_id ? [{ id: lookups.value.organization_id, name: "Current Organization" }] : [];
    }
    if (form.scope === "branch") return lookups.value.branches;
    if (form.scope === "warehouse") return lookups.value.warehouses;
    if (form.scope === "outlet") return lookups.value.outlets;
    return [];
});

watch(
    () => form.scope,
    (scope) => {
        form.scope_id = scope === "organization" ? lookups.value.organization_id : null;
    }
);

onMounted(async () => {
    await fetchLookups();
    await fetchStaff();
    form.scope_id = lookups.value.organization_id;
});

function openCreate() {
    editingUserId.value = null;
    resetForm();
    resetFeedback();
    modalOpen.value = true;
}

function closeModal() {
    resetFeedback();
    modalOpen.value = false;
}

function openEdit(entry) {
    editingUserId.value = entry.user.id;
    form.name = entry.user.name;
    form.email = entry.user.email;
    form.phone = entry.user.phone || "";
    form.national_id = entry.user.national_id || "";
    form.password = "";
    form.password_confirmation = "";
    form.role_id = entry.role.id;
    form.scope = entry.binding.scope;
    form.scope_id = entry.binding.scope_id;
    form.include_descendents = !!entry.binding.include_descendents;
    resetFeedback();
    modalOpen.value = true;
}

function formatScope(scope) {
    return scope ? `${scope.charAt(0).toUpperCase()}${scope.slice(1)}` : "";
}

async function submit() {
    const payload = { ...form };
    if (lookups.value.organization_id) payload.organization_id = lookups.value.organization_id;
    if (!payload.phone) delete payload.phone;
    if (!payload.national_id) delete payload.national_id;
    if (!payload.password) {
        delete payload.password;
        delete payload.password_confirmation;
    }

    let data;
    try {
        data = editingUserId.value
            ? await updateStaff(editingUserId.value, payload)
            : await createStaff(payload);
        notifySuccess(data?.message || (editingUserId.value ? "User updated successfully." : "User created successfully."));
    } catch (apiError) {
        notifyError(getErrorMessage(apiError, "Failed to save user."));
        return;
    }

    if (!data?.user) return;

    editingUserId.value = null;
    resetForm();
    closeModal();
}

async function removeStaff(userId) {
    const confirmed = await confirmDelete({
        title: "Delete user?",
        text: "The selected user will be removed from this organization.",
    });
    if (!confirmed) return;

    try {
        const data = await deleteStaff(userId, lookups.value.organization_id);
        notifySuccess(data?.message || "User deleted successfully.");
    } catch (apiError) {
        notifyError(getErrorMessage(apiError, "Failed to delete user."));
    }
}

function resetForm() {
    form.name = "";
    form.email = "";
    form.phone = "";
    form.national_id = "";
    form.password = "";
    form.password_confirmation = "";
    form.role_id = null;
    form.scope = "organization";
    form.scope_id = lookups.value.organization_id;
    form.include_descendents = false;
}
</script>
