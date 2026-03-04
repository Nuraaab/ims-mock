<template>
    <DashboardLayout>
        <section class="mb-6 flex items-center justify-between gap-3">
            <div>
                <h2 class="ui-text text-2xl font-semibold tracking-tight">Item Categories</h2>
                <p class="ui-muted mt-1 text-sm">Manage hierarchical item categories for IMS.</p>
            </div>
            <AppButton :disabled="loading" @click="openCreate">Create Category</AppButton>
        </section>

        <section class="ui-card overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="ui-surface-soft text-left">
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Code</th>
                        <th class="px-4 py-3">Level</th>
                        <th class="px-4 py-3">Parent</th>
                        <th class="px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in items" :key="item.id" class="border-t" :style="{ borderColor: 'var(--ui-border)' }">
                        <td class="px-4 py-3">{{ item.name }}</td>
                        <td class="px-4 py-3">{{ item.code || "-" }}</td>
                        <td class="px-4 py-3">{{ item.level }}</td>
                        <td class="px-4 py-3">{{ item.parent?.name || "-" }}</td>
                        <td class="px-4 py-3">
                            <TableActions @edit="openEdit(item)" @delete="removeItem(item.id)" />
                        </td>
                    </tr>
                    <tr v-if="!items.length && !loading">
                        <td class="ui-muted px-4 py-4" colspan="5">No item categories found.</td>
                    </tr>
                </tbody>
            </table>
        </section>

        <p v-if="error && !modalOpen" class="ui-alert-error">{{ error }}</p>

        <AppModal :open="modalOpen" :title="editingId ? 'Edit Item Category' : 'Create Item Category'" @close="closeModal">
            <form class="grid grid-cols-1 gap-3" @submit.prevent="submit">
                <div>
                    <label class="ui-label">Name</label>
                    <input v-model.trim="form.name" class="ui-input" required :disabled="saving" />
                </div>
                <div>
                    <label class="ui-label">Code</label>
                    <input v-model.trim="form.code" class="ui-input" :disabled="saving" />
                </div>
                <div>
                    <label class="ui-label">Parent Category</label>
                    <select v-model.number="form.parent_id" class="ui-select" :disabled="saving">
                        <option :value="null">None (Root)</option>
                        <option v-for="category in parentOptions" :key="category.id" :value="category.id">
                            {{ category.name }}
                        </option>
                    </select>
                </div>
                <div class="flex justify-end gap-2">
                    <AppButton variant="soft" @click="closeModal">Cancel</AppButton>
                    <AppButton type="submit" :disabled="saving">{{ saving ? "Saving..." : editingId ? "Update" : "Create" }}</AppButton>
                </div>

                <div>
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
import { computed, onMounted, reactive, ref } from "vue";
import DashboardLayout from "@/shared/components/dashboard/DashboardLayout.vue";
import AppButton from "@/shared/components/ui/AppButton.vue";
import AppModal from "@/shared/components/ui/AppModal.vue";
import TableActions from "@/shared/components/ui/TableActions.vue";
import { useItemCategories } from "@ims/composables/useItemCategories";
import { useNotifier } from "@/shared/composables/useNotifier";

const { items, loading, saving, error, validationErrors, fetchItemCategories, createItemCategory, updateItemCategory, deleteItemCategory, resetItemCategoryFeedback } =
    useItemCategories();
const { notifySuccess, notifyError, getErrorMessage, confirmDelete } = useNotifier();

const modalOpen = ref(false);
const editingId = ref(null);
const form = reactive({
    name: "",
    code: "",
    parent_id: null,
});

const errorItems = computed(() => Object.values(validationErrors.value).flat());
const parentOptions = computed(() => items.value.filter((item) => item.id !== editingId.value));

onMounted(fetchItemCategories);

function resetForm() {
    form.name = "";
    form.code = "";
    form.parent_id = null;
    editingId.value = null;
}

function openCreate() {
    resetForm();
    resetItemCategoryFeedback();
    modalOpen.value = true;
}

function openEdit(item) {
    editingId.value = item.id;
    form.name = item.name || "";
    form.code = item.code || "";
    form.parent_id = item.parent_id ?? null;
    resetItemCategoryFeedback();
    modalOpen.value = true;
}

function closeModal() {
    modalOpen.value = false;
}

async function submit() {
    const payload = {
        name: form.name,
        code: form.code || null,
        parent_id: form.parent_id || null,
    };

    try {
        const data = editingId.value
            ? await updateItemCategory(editingId.value, payload)
            : await createItemCategory(payload);

        notifySuccess(data?.message || (editingId.value ? "Item category updated successfully." : "Item category created successfully."));
    } catch (apiError) {
        notifyError(getErrorMessage(apiError, "Failed to save item category."));
        return;
    }

    closeModal();
    resetForm();
}

async function removeItem(id) {
    const confirmed = await confirmDelete({
        title: "Delete item category?",
        text: "This will permanently remove this category.",
    });

    if (!confirmed) return;

    try {
        const data = await deleteItemCategory(id);
        notifySuccess(data?.message || "Item category deleted successfully.");
    } catch (apiError) {
        notifyError(getErrorMessage(apiError, "Failed to delete item category."));
    }
}
</script>
