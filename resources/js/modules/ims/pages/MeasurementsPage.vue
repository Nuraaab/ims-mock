<template>
    <DashboardLayout>
        <section class="mb-6 flex items-center justify-between gap-3">
            <div>
                <h2 class="ui-text text-2xl font-semibold tracking-tight">Measurements</h2>
                <p class="ui-muted mt-1 text-sm">Manage unit names and symbols for IMS measurements.</p>
            </div>
            <AppButton :disabled="loading" @click="openCreate">Create Measurement</AppButton>
        </section>

        <section class="ui-card overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="ui-surface-soft text-left">
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Plural</th>
                        <th class="px-4 py-3">Symbol</th>
                        <th class="px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in items" :key="item.id" class="border-t" :style="{ borderColor: 'var(--ui-border)' }">
                        <td class="px-4 py-3">{{ item.name }}</td>
                        <td class="px-4 py-3">{{ item.name_plural || "-" }}</td>
                        <td class="px-4 py-3">{{ item.symbol || "-" }}</td>
                        <td class="px-4 py-3">
                            <TableActions @edit="openEdit(item)" @delete="removeItem(item.id)" />
                        </td>
                    </tr>
                    <tr v-if="!items.length && !loading">
                        <td class="ui-muted px-4 py-4" colspan="4">No measurements found.</td>
                    </tr>
                </tbody>
            </table>
        </section>

        <p v-if="error && !modalOpen" class="ui-alert-error">{{ error }}</p>

        <AppModal :open="modalOpen" :title="editingId ? 'Edit Measurement' : 'Create Measurement'" @close="closeModal">
            <form class="grid grid-cols-1 gap-3" @submit.prevent="submit">
                <div>
                    <label class="ui-label">Name</label>
                    <input v-model.trim="form.name" class="ui-input" required :disabled="saving" />
                </div>
                <div>
                    <label class="ui-label">Plural Name</label>
                    <input v-model.trim="form.name_plural" class="ui-input" :disabled="saving" />
                </div>
                <div>
                    <label class="ui-label">Symbol</label>
                    <input v-model.trim="form.symbol" class="ui-input" :disabled="saving" />
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
import { useMeasurements } from "@ims/composables/useMeasurements";
import { useNotifier } from "@/shared/composables/useNotifier";

const { items, loading, saving, error, validationErrors, fetchMeasurements, createMeasurement, updateMeasurement, deleteMeasurement, resetMeasurementFeedback } = useMeasurements();
const { notifySuccess, notifyError, getErrorMessage, confirmDelete } = useNotifier();

const modalOpen = ref(false);
const editingId = ref(null);
const form = reactive({
    name: "",
    name_plural: "",
    symbol: "",
});

const errorItems = computed(() => Object.values(validationErrors.value).flat());

onMounted(fetchMeasurements);

function resetForm() {
    form.name = "";
    form.name_plural = "";
    form.symbol = "";
    editingId.value = null;
}

function openCreate() {
    resetForm();
    resetMeasurementFeedback();
    modalOpen.value = true;
}

function openEdit(item) {
    editingId.value = item.id;
    form.name = item.name || "";
    form.name_plural = item.name_plural || "";
    form.symbol = item.symbol || "";
    resetMeasurementFeedback();
    modalOpen.value = true;
}

function closeModal() {
    modalOpen.value = false;
}

async function submit() {
    const payload = {
        name: form.name,
        name_plural: form.name_plural || null,
        symbol: form.symbol || null,
    };

    try {
        const data = editingId.value
            ? await updateMeasurement(editingId.value, payload)
            : await createMeasurement(payload);

        notifySuccess(data?.message || (editingId.value ? "Measurement updated successfully." : "Measurement created successfully."));
    } catch (apiError) {
        notifyError(getErrorMessage(apiError, "Failed to save measurement."));
        return;
    }

    closeModal();
    resetForm();
}

async function removeItem(id) {
    const confirmed = await confirmDelete({
        title: "Delete measurement?",
        text: "This will permanently remove this measurement.",
    });

    if (!confirmed) return;

    try {
        const data = await deleteMeasurement(id);
        notifySuccess(data?.message || "Measurement deleted successfully.");
    } catch (apiError) {
        notifyError(getErrorMessage(apiError, "Failed to delete measurement."));
    }
}
</script>
