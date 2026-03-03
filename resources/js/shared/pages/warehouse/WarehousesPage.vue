<template>
    <DashboardLayout>
        <section class="mb-6 flex items-center justify-between gap-3">
            <div>
                <h2 class="ui-text text-2xl font-semibold tracking-tight">Warehouses</h2>
                <p class="ui-muted mt-1 text-sm">Create and manage organization warehouses.</p>
            </div>
            <AppButton @click="openCreate">Create Warehouse</AppButton>
        </section>

        <section class="ui-card overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="ui-surface-soft text-left">
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3">Branch</th>
                        <th class="px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in items" :key="item.id" class="border-t" :style="{ borderColor: 'var(--ui-border)' }">
                        <td class="px-4 py-3">{{ item.name }}</td>
                        <td class="px-4 py-3">{{ item.warehouse_type }}</td>
                        <td class="px-4 py-3">{{ branchName(item.branch_id) }}</td>
                        <td class="px-4 py-3">
                            <TableActions @edit="openEdit(item)" @delete="removeItem(item.id)" />
                        </td>
                    </tr>
                    <tr v-if="!items.length && !loading">
                        <td class="ui-muted px-4 py-4" colspan="4">No warehouses found.</td>
                    </tr>
                </tbody>
            </table>
        </section>

        <p v-if="message" class="ui-alert-success">{{ message }}</p>
        <p v-if="error" class="ui-alert-error">{{ error }}</p>
        <ul v-if="errorItems.length" class="mt-2 list-disc pl-5 text-sm text-red-700 dark:text-red-300">
            <li v-for="(item, index) in errorItems" :key="index">{{ item }}</li>
        </ul>

        <AppModal :open="modalOpen" :title="editingId ? 'Edit Warehouse' : 'Create Warehouse'" @close="closeModal">
            <form class="grid grid-cols-1 gap-3" @submit.prevent="submit">
                <div>
                    <label class="ui-label">Name</label>
                    <input v-model.trim="form.name" class="ui-input" required :disabled="saving" />
                </div>
                <div>
                    <label class="ui-label">Warehouse Type</label>
                    <input v-model.trim="form.warehouse_type" class="ui-input" required :disabled="saving" />
                </div>
                <div>
                    <label class="ui-label">Branch</label>
                    <select v-model.number="form.branch_id" class="ui-select" :disabled="saving">
                        <option :value="null">No branch</option>
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
import { useWarehouses } from "@/shared/composables/useWarehouses";

const { items: branches, fetchBranches } = useBranches();
const { items, loading, saving, message, error, validationErrors, fetchWarehouses, createWarehouse, updateWarehouse, deleteWarehouse } = useWarehouses();

const modalOpen = ref(false);
const editingId = ref(null);
const form = reactive({
    name: "",
    warehouse_type: "",
    branch_id: null,
});

const errorItems = computed(() => Object.values(validationErrors.value).flat());

onMounted(async () => {
    await fetchBranches();
    await fetchWarehouses();
});

function openCreate() {
    editingId.value = null;
    form.name = "";
    form.warehouse_type = "";
    form.branch_id = null;
    modalOpen.value = true;
}

function openEdit(item) {
    editingId.value = item.id;
    form.name = item.name || "";
    form.warehouse_type = item.warehouse_type || "";
    form.branch_id = item.branch_id || null;
    modalOpen.value = true;
}

function closeModal() {
    modalOpen.value = false;
}

async function submit() {
    const payload = {
        name: form.name,
        warehouse_type: form.warehouse_type,
        branch_id: form.branch_id || null,
    };

    if (editingId.value) {
        await updateWarehouse(editingId.value, payload);
    } else {
        await createWarehouse(payload);
    }

    closeModal();
}

async function removeItem(id) {
    await deleteWarehouse(id);
}

function branchName(branchId) {
    if (!branchId) return "-";
    return branches.value.find((branch) => branch.id === branchId)?.name || `#${branchId}`;
}
</script>
