<template>
    <DashboardLayout>
        <section class="mb-6 flex items-center justify-between gap-3">
            <div>
                <h2 class="ui-text text-2xl font-semibold tracking-tight">Products</h2>
                <p class="ui-muted mt-1 text-sm">Manage products and tracking configuration.</p>
            </div>
            <AppButton :disabled="loading" @click="openCreate">Create Product</AppButton>
        </section>

        <section class="ui-card overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="ui-surface-soft text-left">
                        <th class="px-4 py-3">Item</th>
                        <th class="px-4 py-3">Code</th>
                        <th class="px-4 py-3">Barcode</th>
                        <th class="px-4 py-3">Group</th>
                        <th class="px-4 py-3">Tracking</th>
                        <th class="px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in items" :key="item.id" class="border-t" :style="{ borderColor: 'var(--ui-border)' }">
                        <td class="px-4 py-3">{{ item.item?.name || `#${item.item_id}` }}</td>
                        <td class="px-4 py-3">{{ item.code || "-" }}</td>
                        <td class="px-4 py-3">{{ item.barcode || "-" }}</td>
                        <td class="px-4 py-3">{{ item.product_group?.name || "-" }}</td>
                        <td class="px-4 py-3">
                            <span class="ui-muted text-xs">
                                S: {{ item.track_stock ? "Y" : "N" }} | B: {{ item.track_batch ? "Y" : "N" }} | E: {{ item.track_expiry ? "Y" : "N" }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <TableActions @edit="openEdit(item)" @delete="removeItem(item.id)" />
                        </td>
                    </tr>
                    <tr v-if="!items.length && !loading">
                        <td class="ui-muted px-4 py-4" colspan="6">No products found.</td>
                    </tr>
                </tbody>
            </table>
        </section>

        <p v-if="error && !modalOpen" class="ui-alert-error">{{ error }}</p>

        <AppModal :open="modalOpen" :title="editingId ? 'Edit Product' : 'Create Product'" @close="closeModal">
            <form class="grid grid-cols-1 gap-3 md:grid-cols-2" @submit.prevent="submit">
                <div class="md:col-span-2">
                    <label class="ui-label">Item</label>
                    <template v-if="itemOptions.length">
                        <select v-model.number="form.item_id" class="ui-select" required :disabled="saving">
                            <option :value="null" disabled>Select item</option>
                            <option v-for="option in itemOptions" :key="option.id" :value="option.id">
                                {{ option.name }}
                            </option>
                        </select>
                    </template>
                    <template v-else>
                        <input v-model.number="form.item_id" type="number" min="1" class="ui-input" required :disabled="saving" placeholder="Enter item ID" />
                        <p class="ui-muted mt-1 text-xs">Items dropdown unavailable. Use existing item ID.</p>
                    </template>
                </div>
                <div>
                    <label class="ui-label">Code</label>
                    <input v-model.trim="form.code" class="ui-input" :disabled="saving" />
                </div>
                <div>
                    <label class="ui-label">Barcode</label>
                    <input v-model.trim="form.barcode" class="ui-input" :disabled="saving" />
                </div>
                <div>
                    <label class="ui-label">Product Group (Optional)</label>
                    <select v-model.number="form.product_group_id" class="ui-select" :disabled="saving">
                        <option :value="null">None</option>
                        <option v-for="group in productGroups" :key="group.id" :value="group.id">
                            {{ group.name }}
                        </option>
                    </select>
                </div>
                <div>
                    <label class="ui-label">Default Measurement (Optional)</label>
                    <select v-model.number="form.default_measurement_id" class="ui-select" :disabled="saving">
                        <option :value="null">None</option>
                        <option v-for="measurement in measurements" :key="measurement.id" :value="measurement.id">
                            {{ measurement.name }}
                        </option>
                    </select>
                </div>
                <label class="ui-muted inline-flex items-center gap-2 text-sm">
                    <input v-model="form.track_stock" type="checkbox" class="h-4 w-4 rounded border-slate-300" :disabled="saving" />
                    Track stock
                </label>
                <label class="ui-muted inline-flex items-center gap-2 text-sm">
                    <input v-model="form.track_batch" type="checkbox" class="h-4 w-4 rounded border-slate-300" :disabled="saving" />
                    Track batch
                </label>
                <label class="ui-muted inline-flex items-center gap-2 text-sm md:col-span-2">
                    <input v-model="form.track_expiry" type="checkbox" class="h-4 w-4 rounded border-slate-300" :disabled="saving" />
                    Track expiry
                </label>
                <div class="md:col-span-2 flex justify-end gap-2">
                    <AppButton variant="soft" @click="closeModal">Cancel</AppButton>
                    <AppButton type="submit" :disabled="saving">{{ saving ? "Saving..." : editingId ? "Update" : "Create" }}</AppButton>
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
import { computed, onMounted, reactive, ref } from "vue";
import DashboardLayout from "@/shared/components/dashboard/DashboardLayout.vue";
import AppButton from "@/shared/components/ui/AppButton.vue";
import AppModal from "@/shared/components/ui/AppModal.vue";
import TableActions from "@/shared/components/ui/TableActions.vue";
import { useProducts } from "@ims/composables/useProducts";
import { useNotifier } from "@/shared/composables/useNotifier";

const { items, productGroups, measurements, itemOptions, loading, saving, error, validationErrors, fetchProducts, fetchProductLookups, createProduct, updateProduct, deleteProduct, resetProductFeedback } =
    useProducts();
const { notifySuccess, notifyError, getErrorMessage, confirmDelete } = useNotifier();

const modalOpen = ref(false);
const editingId = ref(null);
const form = reactive({
    item_id: null,
    code: "",
    barcode: "",
    product_group_id: null,
    default_measurement_id: null,
    track_stock: true,
    track_batch: false,
    track_expiry: false,
});

const errorItems = computed(() => Object.values(validationErrors.value).flat());

onMounted(async () => {
    await Promise.all([fetchProducts(), fetchProductLookups()]);
});

function resetForm() {
    form.item_id = null;
    form.code = "";
    form.barcode = "";
    form.product_group_id = null;
    form.default_measurement_id = null;
    form.track_stock = true;
    form.track_batch = false;
    form.track_expiry = false;
    editingId.value = null;
}

function openCreate() {
    resetForm();
    resetProductFeedback();
    modalOpen.value = true;
}

function openEdit(item) {
    editingId.value = item.id;
    form.item_id = item.item_id || null;
    form.code = item.code || "";
    form.barcode = item.barcode || "";
    form.product_group_id = item.product_group_id ?? null;
    form.default_measurement_id = item.default_measurement_id ?? null;
    form.track_stock = !!item.track_stock;
    form.track_batch = !!item.track_batch;
    form.track_expiry = !!item.track_expiry;
    resetProductFeedback();
    modalOpen.value = true;
}

function closeModal() {
    modalOpen.value = false;
}

async function submit() {
    const payload = {
        item_id: form.item_id,
        code: form.code || null,
        barcode: form.barcode || null,
        product_group_id: form.product_group_id || null,
        default_measurement_id: form.default_measurement_id || null,
        track_stock: form.track_stock,
        track_batch: form.track_batch,
        track_expiry: form.track_expiry,
    };

    try {
        const data = editingId.value
            ? await updateProduct(editingId.value, payload)
            : await createProduct(payload);

        notifySuccess(data?.message || (editingId.value ? "Product updated successfully." : "Product created successfully."));
    } catch (apiError) {
        notifyError(getErrorMessage(apiError, "Failed to save product."));
        return;
    }

    closeModal();
    resetForm();
}

async function removeItem(id) {
    const confirmed = await confirmDelete({
        title: "Delete product?",
        text: "This will permanently remove this product.",
    });

    if (!confirmed) return;

    try {
        const data = await deleteProduct(id);
        notifySuccess(data?.message || "Product deleted successfully.");
    } catch (apiError) {
        notifyError(getErrorMessage(apiError, "Failed to delete product."));
    }
}
</script>
