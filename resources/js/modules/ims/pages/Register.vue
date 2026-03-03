<template>
    <IMSLayout>
        <div class="mx-auto max-w-2xl rounded-lg border border-gray-200 bg-white p-6">
            <h2 class="mb-4 text-xl font-semibold text-gray-900">
                Register Organization
            </h2>

            <form class="grid grid-cols-1 gap-4 md:grid-cols-2" @submit.prevent="submit">
                <div class="md:col-span-2">
                    <h3 class="mb-2 text-sm font-semibold text-gray-700">User</h3>
                </div>
                <input v-model="form.user.name" placeholder="Full name" required class="rounded-md border border-gray-300 px-3 py-2" />
                <input v-model="form.user.email" type="email" placeholder="Email" required class="rounded-md border border-gray-300 px-3 py-2" />
                <input v-model="form.user.phone" placeholder="Phone (optional)" class="rounded-md border border-gray-300 px-3 py-2" />
                <input v-model="form.user.national_id" placeholder="National ID (optional)" class="rounded-md border border-gray-300 px-3 py-2" />
                <input v-model="form.user.password" type="password" placeholder="Password" required class="rounded-md border border-gray-300 px-3 py-2" />
                <input v-model="form.user.password_confirmation" type="password" placeholder="Confirm password" required class="rounded-md border border-gray-300 px-3 py-2" />

                <div class="mt-2 md:col-span-2">
                    <h3 class="mb-2 text-sm font-semibold text-gray-700">Organization</h3>
                </div>
                <input v-model="form.organization.name" placeholder="Organization name" required class="rounded-md border border-gray-300 px-3 py-2" />
                <input v-model="form.organization.tin_number" placeholder="TIN number" required class="rounded-md border border-gray-300 px-3 py-2" />
                <input v-model="form.organization.email" type="email" placeholder="Organization email" class="rounded-md border border-gray-300 px-3 py-2" />
                <input v-model="form.organization.phone" placeholder="Organization phone" class="rounded-md border border-gray-300 px-3 py-2" />
                <input v-model="form.organization.trade_name" placeholder="Trade name" class="rounded-md border border-gray-300 px-3 py-2" />
                <input v-model="form.organization.legal_name" placeholder="Legal name" class="rounded-md border border-gray-300 px-3 py-2" />

                <select v-model.number="form.organization.woreda_id" class="rounded-md border border-gray-300 px-3 py-2">
                    <option :value="null">Select Woreda</option>
                    <option v-for="item in registrationLookups.woredas" :key="item.id" :value="item.id">
                        {{ item.name }}
                    </option>
                </select>

                <select v-model.number="form.organization.kebele_id" class="rounded-md border border-gray-300 px-3 py-2" :disabled="!form.organization.woreda_id">
                    <option :value="null">Select Kebele</option>
                    <option v-for="item in filteredKebeles" :key="item.id" :value="item.id">
                        {{ item.name }}
                    </option>
                </select>

                <select v-model.number="form.organization.locality_id" class="rounded-md border border-gray-300 px-3 py-2" :disabled="!form.organization.kebele_id">
                    <option :value="null">Select Locality</option>
                    <option v-for="item in filteredLocalities" :key="item.id" :value="item.id">
                        {{ item.name }}
                    </option>
                </select>

                <select v-model.number="form.organization.tax_center_id" class="rounded-md border border-gray-300 px-3 py-2" :disabled="!form.organization.woreda_id">
                    <option :value="null">Select Tax Center</option>
                    <option v-for="item in filteredTaxCenters" :key="item.id" :value="item.id">
                        {{ item.name }}
                    </option>
                </select>

                <div class="mt-2 md:col-span-2">
                    <button
                        type="submit"
                        class="w-full rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
                    >
                        Register
                    </button>
                </div>
            </form>

            <p v-if="error" class="mt-4 text-sm text-red-600">{{ error }}</p>
        </div>
    </IMSLayout>
</template>

<script setup>
import { computed, onMounted, reactive, watch } from "vue";
import { useRouter } from "vue-router";
import IMSLayout from "@ims/components/IMSLayout.vue";
import { useAuth } from "@/shared/auth/composables/useAuth";

const router = useRouter();
const { registerOrganization, fetchRegistrationLookups, registrationLookups, error } = useAuth();

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
    await fetchRegistrationLookups();
});

async function submit() {
    await registerOrganization(form);
    await router.push({ name: "ims.home" });
}
</script>
