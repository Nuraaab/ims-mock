<template>
    <DashboardLayout>
        <section class="mx-auto w-full max-w-2xl">
            <div class="mb-6">
                <h2 class="ui-text text-2xl font-semibold tracking-tight">Create Role</h2>
                <p class="ui-muted mt-1 text-sm">Add a new role for your organization access control.</p>
            </div>

            <div class="ui-card p-6 md:p-7">
                <form class="space-y-4" @submit.prevent="submit">
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

                    <div class="flex items-center justify-between gap-3">
                        <p class="ui-muted text-xs">Names must be unique.</p>
                        <button
                            type="submit"
                            :disabled="loading || !form.name"
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
            </div>
        </section>
    </DashboardLayout>
</template>

<script setup>
import { computed, reactive, ref } from "vue";
import apiClient from "@/shared/auth/clients/apiClient";
import DashboardLayout from "@/shared/components/dashboard/DashboardLayout.vue";

const loading = ref(false);
const message = ref(null);
const error = ref(null);
const validationErrors = ref({});

const form = reactive({
    name: "",
});

const errorItems = computed(() => Object.values(validationErrors.value).flat());

function resetFeedback() {
    message.value = null;
    error.value = null;
    validationErrors.value = {};
}

async function submit() {
    loading.value = true;
    resetFeedback();

    try {
        const { data } = await apiClient.post("/roles", form);
        message.value = data?.message || "Role created successfully.";
        console.log("Created Role:", data?.data);
        form.name = "";
    } catch (err) {
        error.value = err?.response?.data?.message || "Failed to create role.";
        validationErrors.value = err?.response?.data?.errors || {};
    } finally {
        loading.value = false;
    }
}
</script>
