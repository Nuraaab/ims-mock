<template>
    <DashboardLayout>
        <section class="mb-6 flex items-center justify-between gap-3">
            <div>
                <h2 class="ui-text text-2xl font-semibold tracking-tight">Outlets</h2>
                <p class="ui-muted mt-1 text-sm">Create and manage organization outlets.</p>
            </div>
            <AppButton @click="openCreate">Create Outlet</AppButton>
        </section>

        <section class="ui-card overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="ui-surface-soft text-left">
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Outlet Type</th>
                        <th class="px-4 py-3">Branch</th>
                        <th class="px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in items" :key="item.id" class="border-t" :style="{ borderColor: 'var(--ui-border)' }">
                        <td class="px-4 py-3">{{ item.name }}</td>
                        <td class="px-4 py-3">{{ item.outlet_type }}</td>
                        <td class="px-4 py-3">{{ branchName(item.branch_id) }}</td>
                        <td class="px-4 py-3">
                            <TableActions @edit="openEdit(item)" @delete="removeItem(item.id)" />
                        </td>
                    </tr>
                    <tr v-if="!items.length && !loading">
                        <td class="ui-muted px-4 py-4" colspan="4">No outlets found.</td>
                    </tr>
                </tbody>
            </table>
        </section>

        <p v-if="error" class="ui-alert-error">{{ error }}</p>
        <ul v-if="errorItems.length" class="mt-2 list-disc pl-5 text-sm text-red-700 dark:text-red-300">
            <li v-for="(item, index) in errorItems" :key="index">{{ item }}</li>
        </ul>

        <AppModal :open="modalOpen" :title="editingId ? 'Edit Outlet' : 'Create Outlet'" @close="closeModal">
            <form class="grid grid-cols-1 gap-3" @submit.prevent="submit">
                <div>
                    <label class="ui-label">Name</label>
                    <input v-model.trim="form.name" class="ui-input" required :disabled="saving" />
                </div>
                <div>
                    <label class="ui-label">Outlet Type</label>
                    <input v-model.trim="form.outlet_type" class="ui-input" required :disabled="saving" />
                </div>
                <div>
                    <label class="ui-label">Branch</label>
                    <select v-model.number="form.branch_id" class="ui-select" required :disabled="saving">
                        <option :value="null" disabled>Select branch</option>
                        <option v-for="branch in branches" :key="branch.id" :value="branch.id">
                            {{ branch.name }}
                        </option>
                    </select>
                </div>
                <div class="flex justify-end gap-2">
                    <AppButton variant="soft" @click="closeModal">Cancel</AppButton>
                    <AppButton type="submit" :disabled="saving">{{ saving ? "Saving..." : editingId ? "Update" : "Create" }}</AppButton>
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
import { useBranches } from "@/shared/composables/useBranches";
import { useOutlets } from "@/shared/composables/useOutlets";
import { useNotifier } from "@/shared/composables/useNotifier";

const { items: branches, fetchBranches } = useBranches();
const { items, loading, saving, error, validationErrors, fetchOutlets, createOutlet, updateOutlet, deleteOutlet } = useOutlets();
const { notifySuccess, notifyError, getErrorMessage, confirmDelete } = useNotifier();

const modalOpen = ref(false);
const editingId = ref(null);
const form = reactive({
    name: "",
    outlet_type: "",
    branch_id: null,
});

const errorItems = computed(() => Object.values(validationErrors.value).flat());

onMounted(async () => {
    await fetchBranches();
    await fetchOutlets();
});

function openCreate() {
    editingId.value = null;
    form.name = "";
    form.outlet_type = "";
    form.branch_id = null;
    modalOpen.value = true;
}

function openEdit(item) {
    editingId.value = item.id;
    form.name = item.name || "";
    form.outlet_type = item.outlet_type || "";
    form.branch_id = item.branch_id || null;
    modalOpen.value = true;
}

function closeModal() {
    modalOpen.value = false;
}

async function submit() {
    const payload = {
        name: form.name,
        outlet_type: form.outlet_type,
        branch_id: form.branch_id,
    };

    try {
        const data = editingId.value
            ? await updateOutlet(editingId.value, payload)
            : await createOutlet(payload);
        notifySuccess(data?.message || (editingId.value ? "Outlet updated successfully." : "Outlet created successfully."));
    } catch (apiError) {
        notifyError(getErrorMessage(apiError, "Failed to save outlet."));
        return;
    }

    closeModal();
}

async function removeItem(id) {
    const confirmed = await confirmDelete({
        title: "Delete outlet?",
        text: "This will permanently remove this outlet.",
    });
    if (!confirmed) return;

    try {
        const data = await deleteOutlet(id);
        notifySuccess(data?.message || "Outlet deleted successfully.");
    } catch (apiError) {
        notifyError(getErrorMessage(apiError, "Failed to delete outlet."));
    }
}

function branchName(branchId) {
    return branches.value.find((branch) => branch.id === branchId)?.name || `#${branchId}`;
}
</script>
