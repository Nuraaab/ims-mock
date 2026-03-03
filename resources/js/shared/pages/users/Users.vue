<template>
    <DashboardLayout>
        <section class="mb-6 flex items-center justify-between gap-3">
            <div>
                <h2 class="ui-text text-2xl font-semibold tracking-tight">Users</h2>
                <p class="ui-muted mt-1 text-sm">
                    Create organization staff and assign role with scope.
                </p>
            </div>
        </section>

        <section class="grid grid-cols-1 gap-4 xl:grid-cols-3">
            <article class="ui-card p-5 xl:col-span-2">
                <h3 class="ui-text text-base font-semibold">Create Staff User</h3>
                <form class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-2" @submit.prevent="submit">
                    <div>
                        <label class="ui-label" for="staff-name">Full Name</label>
                        <input id="staff-name" v-model.trim="form.name" type="text" class="ui-input" required :disabled="saving" />
                    </div>

                    <div>
                        <label class="ui-label" for="staff-email">Email</label>
                        <input id="staff-email" v-model.trim="form.email" type="email" class="ui-input" required :disabled="saving" />
                    </div>

                    <div>
                        <label class="ui-label" for="staff-phone">Phone</label>
                        <input id="staff-phone" v-model.trim="form.phone" type="text" class="ui-input" :disabled="saving" />
                    </div>

                    <div>
                        <label class="ui-label" for="staff-national-id">National ID</label>
                        <input id="staff-national-id" v-model.trim="form.national_id" type="text" class="ui-input" :disabled="saving" />
                    </div>

                    <div>
                        <label class="ui-label" for="staff-password">Password</label>
                        <input id="staff-password" v-model="form.password" type="password" class="ui-input" required :disabled="saving" />
                    </div>

                    <div>
                        <label class="ui-label" for="staff-password-confirm">Confirm Password</label>
                        <input
                            id="staff-password-confirm"
                            v-model="form.password_confirmation"
                            type="password"
                            class="ui-input"
                            required
                            :disabled="saving"
                        />
                    </div>

                    <div>
                        <label class="ui-label" for="staff-role">Role</label>
                        <select id="staff-role" v-model.number="form.role_id" class="ui-select" required :disabled="saving || loading">
                            <option :value="null" disabled>Select role</option>
                            <option v-for="role in lookups.roles" :key="role.id" :value="role.id">
                                {{ role.name }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="ui-label" for="staff-scope">Scope</label>
                        <select id="staff-scope" v-model="form.scope" class="ui-select" required :disabled="saving || loading">
                            <option :value="null" disabled>Select scope</option>
                            <option v-for="scope in lookups.scopes" :key="scope" :value="scope">
                                {{ formatScope(scope) }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="ui-label" for="staff-scope-id">Scope Target</label>
                        <select id="staff-scope-id" v-model.number="form.scope_id" class="ui-select" :disabled="saving || loading || !scopeOptions.length">
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

                    <div class="md:col-span-2 flex items-center justify-end">
                        <button type="submit" class="ui-btn-primary" :disabled="saving || loading">
                            {{ saving ? "Creating..." : "Create Staff" }}
                        </button>
                    </div>
                </form>

                <p v-if="message" class="ui-alert-success">{{ message }}</p>
                <p v-if="error" class="ui-alert-error">{{ error }}</p>
                <ul v-if="errorItems.length" class="mt-2 list-disc pl-5 text-sm text-red-700 dark:text-red-300">
                    <li v-for="(item, index) in errorItems" :key="index">{{ item }}</li>
                </ul>
            </article>

            <article class="ui-card p-5">
                <h3 class="ui-text text-base font-semibold">Created Staff</h3>
                <p class="ui-muted mt-1 text-sm">Recent users created in this session.</p>
                <ul class="mt-4 space-y-2">
                    <li v-for="entry in createdStaff" :key="entry.user.id" class="ui-surface-soft p-3">
                        <p class="ui-text text-sm font-medium">{{ entry.user.name }}</p>
                        <p class="ui-muted text-xs">{{ entry.user.email }}</p>
                        <p class="ui-muted mt-1 text-xs">
                            {{ entry.role.name }} • {{ formatScope(entry.binding.scope) }}
                        </p>
                    </li>
                    <li v-if="!createdStaff.length" class="ui-muted text-sm">No staff created yet.</li>
                </ul>
            </article>
        </section>
    </DashboardLayout>
</template>

<script setup>
import { computed, onMounted, reactive, watch } from "vue";
import DashboardLayout from "@/shared/components/dashboard/DashboardLayout.vue";
import { useStaff } from "@/shared/composables/useStaff";

const { loading, saving, lookups, createdStaff, message, error, validationErrors, fetchLookups, createStaff } = useStaff();
console.log('lookups:', lookups.value);
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

const scopeOptions = computed(() => {
    if (form.scope === "organization") {
        return lookups.value.organization_id ? [{ id: lookups.value.organization_id, name: "Current Organization" }] : [];
    }
    if (form.scope === "branch") {
        return lookups.value.branches;
    }
    if (form.scope === "warehouse") {
        return lookups.value.warehouses;
    }
    if (form.scope === "outlet") {
        return lookups.value.outlets;
    }
    return [];
});

const errorItems = computed(() => Object.values(validationErrors.value).flat());

watch(
    () => form.scope,
    (scope) => {
        if (scope === "organization") {
            form.scope_id = lookups.value.organization_id;
            return;
        }
        form.scope_id = null;
    }
);

onMounted(async () => {
    await fetchLookups();
    form.scope_id = lookups.value.organization_id;
});

function formatScope(scope) {
    if (!scope) return "";
    return scope.charAt(0).toUpperCase() + scope.slice(1);
}

function buildPayload() {
    const payload = {
        ...form,
    };

    if (lookups.value.organization_id) {
        payload.organization_id = lookups.value.organization_id;
    }

    if (!payload.phone) delete payload.phone;
    if (!payload.national_id) delete payload.national_id;

    return payload;
}

async function submit() {
    const data = await createStaff(buildPayload());
    if (!data?.user) return;

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
