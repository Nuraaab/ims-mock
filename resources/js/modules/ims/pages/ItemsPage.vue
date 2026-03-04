<template>
    <DashboardLayout>
        <section class="mb-6 flex items-center justify-between gap-3">
            <div>
                <h2 class="ui-text text-2xl font-semibold tracking-tight">Items</h2>
                <p class="ui-muted mt-1 text-sm">Manage items and assign them to item categories.</p>
            </div>
            <AppButton :disabled="loading" @click="openCreate">Create Item</AppButton>
        </section>

        <section class="ui-card overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="ui-surface-soft text-left">
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Category</th>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in items" :key="item.id" class="border-t" :style="{ borderColor: 'var(--ui-border)' }">
                        <td class="px-4 py-3">{{ item.name }}</td>
                        <td class="px-4 py-3">{{ item.category?.name || "-" }}</td>
                        <td class="px-4 py-3">{{ item.item_type || "-" }}</td>
                        <td class="px-4 py-3">
                            <span :class="item.is_active ? 'text-emerald-700 dark:text-emerald-300' : 'ui-muted'">
                                {{ item.is_active ? "Active" : "Inactive" }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <TableActions @edit="openEdit(item)" @delete="removeItem(item.id)" />
                        </td>
                    </tr>
                    <tr v-if="!items.length && !loading">
                        <td class="ui-muted px-4 py-4" colspan="5">No items found.</td>
                    </tr>
                </tbody>
            </table>
        </section>

        <p v-if="error && !modalOpen" class="ui-alert-error">{{ error }}</p>

        <AppModal :open="modalOpen" :title="editingId ? 'Edit Item' : 'Create Item'" @close="closeModal">
            <form class="grid grid-cols-1 gap-3" @submit.prevent="submit">
                <div>
                    <label class="ui-label">Name</label>
                    <input v-model.trim="form.name" class="ui-input" required :disabled="saving" />
                </div>
                <div>
                    <label class="ui-label">Category</label>
                    <select v-model.number="form.item_category_id" class="ui-select" :disabled="saving">
                        <option :value="null">Unassigned</option>
                        <option v-for="category in itemCategories" :key="category.id" :value="category.id">
                            {{ category.name }}
                        </option>
                    </select>
                </div>
                <div>
                    <label class="ui-label">Item Type</label>
                    <input v-model.trim="form.item_type" class="ui-input" :disabled="saving" placeholder="product" />
                </div>
                <label class="ui-muted inline-flex items-center gap-2 text-sm">
                    <input v-model="form.is_active" type="checkbox" :disabled="saving" />
                    Active
                </label>
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
import { useItems } from "@ims/composables/useItems";
import { useItemCategories } from "@ims/composables/useItemCategories";
import { useNotifier } from "@/shared/composables/useNotifier";

const { items, loading, saving, error, validationErrors, fetchItems, createItem, updateItem, deleteItem, resetItemFeedback } = useItems();
const { items: itemCategories, fetchItemCategories } = useItemCategories();
const { notifySuccess, notifyError, getErrorMessage, confirmDelete } = useNotifier();

const modalOpen = ref(false);
const editingId = ref(null);
const form = reactive({
    name: "",
    item_category_id: null,
    item_type: "product",
    is_active: true,
});

const errorItems = computed(() => Object.values(validationErrors.value).flat());

onMounted(async () => {
    await Promise.all([fetchItems(), fetchItemCategories()]);
});

function resetForm() {
    form.name = "";
    form.item_category_id = null;
    form.item_type = "product";
    form.is_active = true;
    editingId.value = null;
}

function openCreate() {
    resetForm();
    resetItemFeedback();
    modalOpen.value = true;
}

function openEdit(item) {
    editingId.value = item.id;
    form.name = item.name || "";
    form.item_category_id = item.item_category_id ?? null;
    form.item_type = item.item_type || "product";
    form.is_active = Boolean(item.is_active);
    resetItemFeedback();
    modalOpen.value = true;
}

function closeModal() {
    modalOpen.value = false;
}

async function submit() {
    const payload = {
        name: form.name,
        item_category_id: form.item_category_id || null,
        item_type: form.item_type || "product",
        is_active: Boolean(form.is_active),
    };

    try {
        const data = editingId.value
            ? await updateItem(editingId.value, payload)
            : await createItem(payload);

        notifySuccess(data?.message || (editingId.value ? "Item updated successfully." : "Item created successfully."));
    } catch (apiError) {
        notifyError(getErrorMessage(apiError, "Failed to save item."));
        return;
    }

    closeModal();
    resetForm();
}

async function removeItem(id) {
    const confirmed = await confirmDelete({
        title: "Delete item?",
        text: "This will permanently remove this item.",
    });

    if (!confirmed) return;

    try {
        const data = await deleteItem(id);
        notifySuccess(data?.message || "Item deleted successfully.");
    } catch (apiError) {
        notifyError(getErrorMessage(apiError, "Failed to delete item."));
    }
}
</script>
