<template>
    <DashboardLayout>
        <section class="mb-6 flex items-center justify-between gap-3">
            <div>
                <h2 class="ui-text text-2xl font-semibold tracking-tight">Branches</h2>
                <p class="ui-muted mt-1 text-sm">Create and manage organization branches.</p>
            </div>
            <AppButton @click="openCreate">Create Branch</AppButton>
        </section>

        <section class="ui-card overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="ui-surface-soft text-left">
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Sub TIN</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Phone</th>
                        <th class="px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in items" :key="item.id" class="border-t" :style="{ borderColor: 'var(--ui-border)' }">
                        <td class="px-4 py-3">{{ item.name }}</td>
                        <td class="px-4 py-3">{{ item.sub_tin || "-" }}</td>
                        <td class="px-4 py-3">{{ item.email || "-" }}</td>
                        <td class="px-4 py-3">{{ item.phone || "-" }}</td>
                        <td class="px-4 py-3">
                            <TableActions @edit="openEdit(item)" @delete="removeItem(item.id)" />
                        </td>
                    </tr>
                    <tr v-if="!items.length && !loading">
                        <td class="ui-muted px-4 py-4" colspan="5">No branches found.</td>
                    </tr>
                </tbody>
            </table>
        </section>

        <p v-if="error" class="ui-alert-error">{{ error }}</p>
        <ul v-if="errorItems.length" class="mt-2 list-disc pl-5 text-sm text-red-700 dark:text-red-300">
            <li v-for="(item, index) in errorItems" :key="index">{{ item }}</li>
        </ul>

        <AppModal :open="modalOpen" :title="editingId ? 'Edit Branch' : 'Create Branch'" @close="closeModal">
            <form class="grid grid-cols-1 gap-3" @submit.prevent="submit">
                <div>
                    <label class="ui-label">Name</label>
                    <input v-model.trim="form.name" class="ui-input" required :disabled="saving" />
                </div>
                <div>
                    <label class="ui-label">Sub TIN</label>
                    <input v-model.trim="form.sub_tin" class="ui-input" :disabled="saving" />
                </div>
                <div>
                    <label class="ui-label">Email</label>
                    <input v-model.trim="form.email" type="email" class="ui-input" :disabled="saving" />
                </div>
                <div>
                    <label class="ui-label">Phone</label>
                    <input v-model.trim="form.phone" class="ui-input" :disabled="saving" />
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
import { useNotifier } from "@/shared/composables/useNotifier";

const { items, loading, saving, error, validationErrors, fetchBranches, createBranch, updateBranch, deleteBranch } = useBranches();
const { notifySuccess, notifyError, getErrorMessage, confirmDelete } = useNotifier();

const modalOpen = ref(false);
const editingId = ref(null);
const form = reactive({
    name: "",
    sub_tin: "",
    email: "",
    phone: "",
});

const errorItems = computed(() => Object.values(validationErrors.value).flat());

onMounted(async () => {
    await fetchBranches();
});

function openCreate() {
    editingId.value = null;
    form.name = "";
    form.sub_tin = "";
    form.email = "";
    form.phone = "";
    modalOpen.value = true;
}

function openEdit(item) {
    editingId.value = item.id;
    form.name = item.name || "";
    form.sub_tin = item.sub_tin || "";
    form.email = item.email || "";
    form.phone = item.phone || "";
    modalOpen.value = true;
}

function closeModal() {
    modalOpen.value = false;
}

async function submit() {
    const payload = {
        name: form.name,
        sub_tin: form.sub_tin || null,
        email: form.email || null,
        phone: form.phone || null,
    };

    try {
        const data = editingId.value
            ? await updateBranch(editingId.value, payload)
            : await createBranch(payload);
        notifySuccess(data?.message || (editingId.value ? "Branch updated successfully." : "Branch created successfully."));
    } catch (apiError) {
        notifyError(getErrorMessage(apiError, "Failed to save branch."));
        return;
    }

    closeModal();
}

async function removeItem(id) {
    const confirmed = await confirmDelete({
        title: "Delete branch?",
        text: "This will permanently remove this branch.",
    });
    if (!confirmed) return;

    try {
        const data = await deleteBranch(id);
        notifySuccess(data?.message || "Branch deleted successfully.");
    } catch (apiError) {
        notifyError(getErrorMessage(apiError, "Failed to delete branch."));
    }
}
</script>
