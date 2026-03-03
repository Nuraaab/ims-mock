<template>
    <AppLayout>
        <div class="ui-card mx-auto max-w-3xl p-6 md:p-7">
            <h2 class="ui-text mb-1 text-2xl font-semibold tracking-tight">
                Register Organization
            </h2>
            <p class="ui-muted mb-5 text-sm">Create your account and organization in one step.</p>

            <form class="grid grid-cols-1 gap-4 md:grid-cols-2" @submit.prevent="submit">
                <div class="md:col-span-2">
                    <h3 class="ui-muted mb-2 text-sm font-semibold uppercase tracking-wide">User</h3>
                </div>
                <input v-model="form.user.name" :disabled="loading" placeholder="Full name" required class="ui-input" />
                <input v-model="form.user.email" :disabled="loading" type="email" placeholder="Email" required class="ui-input" />
                <input v-model="form.user.phone" :disabled="loading" placeholder="Phone (optional)" class="ui-input" />
                <input v-model="form.user.national_id" :disabled="loading" placeholder="National ID (optional)" class="ui-input" />
                <input v-model="form.user.password" :disabled="loading" type="password" placeholder="Password" required class="ui-input" />
                <input v-model="form.user.password_confirmation" :disabled="loading" type="password" placeholder="Confirm password" required class="ui-input" />

                <div class="mt-2 md:col-span-2">
                    <h3 class="ui-muted mb-2 text-sm font-semibold uppercase tracking-wide">Organization</h3>
                </div>
                <input v-model="form.organization.name" :disabled="loading" placeholder="Organization name" required class="ui-input" />
                <input v-model="form.organization.tin_number" :disabled="loading" placeholder="TIN number" required class="ui-input" />
                <input v-model="form.organization.email" :disabled="loading" type="email" placeholder="Organization email" class="ui-input" />
                <input v-model="form.organization.phone" :disabled="loading" placeholder="Organization phone" class="ui-input" />
                <input v-model="form.organization.trade_name" :disabled="loading" placeholder="Trade name" class="ui-input" />
                <input v-model="form.organization.legal_name" :disabled="loading" placeholder="Legal name" class="ui-input" />

                <select v-model.number="form.organization.woreda_id" :disabled="loading" class="ui-select">
                    <option :value="null">Select Woreda</option>
                    <option v-for="item in registrationLookups.woredas" :key="item.id" :value="item.id">
                        {{ item.name }}
                    </option>
                </select>

                <select v-model.number="form.organization.kebele_id" class="ui-select" :disabled="loading || !form.organization.woreda_id">
                    <option :value="null">Select Kebele</option>
                    <option v-for="item in filteredKebeles" :key="item.id" :value="item.id">
                        {{ item.name }}
                    </option>
                </select>

                <select v-model.number="form.organization.locality_id" class="ui-select" :disabled="loading || !form.organization.kebele_id">
                    <option :value="null">Select Locality</option>
                    <option v-for="item in filteredLocalities" :key="item.id" :value="item.id">
                        {{ item.name }}
                    </option>
                </select>

                <select v-model.number="form.organization.tax_center_id" class="ui-select" :disabled="loading || !form.organization.woreda_id">
                    <option :value="null">Select Tax Center</option>
                    <option v-for="item in filteredTaxCenters" :key="item.id" :value="item.id">
                        {{ item.name }}
                    </option>
                </select>

                <div class="mt-2 md:col-span-2">
                    <button
                        type="submit"
                        :disabled="loading"
                        class="ui-btn-primary w-full"
                    >
                        {{ loading ? "Registering..." : "Register" }}
                    </button>
                </div>
            </form>

            <p v-if="message" class="ui-alert-success">{{ message }}</p>
            <p v-if="error" class="ui-alert-error">{{ error }}</p>
            <ul v-if="errorItems.length" class="mt-2 list-disc pl-5 text-sm text-red-700">
                <li v-for="(item, index) in errorItems" :key="index">{{ item }}</li>
            </ul>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed, onMounted, reactive, watch } from "vue";
import { useRouter } from "vue-router";
import AppLayout from "@/shared/components/AppLayout.vue";
import { useAuth } from "@/shared/composables/useAuth";

const router = useRouter();
const { registerOrganization, fetchRegistrationLookups, registrationLookups, loading, message, error, validationErrors, resetFeedback } = useAuth();

const form = reactive({
    user: {
        name: "",
        email: "",
        phone: "",
        national_id: "",
        password: "",
        password_confirmation: "",
    },
    organization: {
        name: "",
        tin_number: "",
        email: "",
        phone: "",
        trade_name: "",
        legal_name: "",
        woreda_id: null,
        kebele_id: null,
        locality_id: null,
        tax_center_id: null,
    },
});

const filteredKebeles = computed(() => {
    if (!form.organization.woreda_id) return [];
    return registrationLookups.value.kebeles.filter(
        (item) => item.woreda_id === Number(form.organization.woreda_id)
    );
});

const filteredLocalities = computed(() => {
    if (!form.organization.kebele_id) return [];
    return registrationLookups.value.localities.filter(
        (item) => item.kebele_id === Number(form.organization.kebele_id)
    );
});

const filteredTaxCenters = computed(() => {
    if (!form.organization.woreda_id) return [];
    return registrationLookups.value.tax_centers.filter(
        (item) => item.woreda_id === Number(form.organization.woreda_id)
    );
});

const errorItems = computed(() => Object.values(validationErrors.value).flat());

watch(
    () => form.organization.woreda_id,
    () => {
        form.organization.kebele_id = null;
        form.organization.locality_id = null;
        form.organization.tax_center_id = null;
    }
);

watch(
    () => form.organization.kebele_id,
    () => {
        form.organization.locality_id = null;
    }
);

onMounted(async () => {
    resetFeedback();
    try {
        await fetchRegistrationLookups();
    } catch {
        // Feedback handled by store state.
    }
});

async function submit() {
    try {
        await registerOrganization(form);
        await router.push({ name: "dashboard" });
    } catch {
        // Feedback handled by store state.
    }
}
</script>
