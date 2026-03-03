<template>
    <IMSLayout>
        <div class="mx-auto max-w-md rounded-lg border border-gray-200 bg-white p-6">
            <h2 class="mb-4 text-xl font-semibold text-gray-900">Login</h2>

            <form class="space-y-4" @submit.prevent="submit">
                <div>
                    <label class="mb-1 block text-sm text-gray-700">Email</label>
                    <input
                        v-model="form.email"
                        type="email"
                        required
                        class="w-full rounded-md border border-gray-300 px-3 py-2"
                    />
                </div>

                <div>
                    <label class="mb-1 block text-sm text-gray-700">Password</label>
                    <input
                        v-model="form.password"
                        type="password"
                        required
                        class="w-full rounded-md border border-gray-300 px-3 py-2"
                    />
                </div>

                <button
                    type="submit"
                    class="w-full rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
                >
                    Login
                </button>
            </form>

            <p v-if="error" class="mt-4 text-sm text-red-600">{{ error }}</p>

            <p class="mt-6 text-sm text-gray-600">
                New organization?
                <RouterLink :to="{ name: 'ims.auth.register' }" class="text-blue-600 hover:underline">
                    Register here
                </RouterLink>
            </p>
        </div>
    </IMSLayout>
</template>

<script setup>
import { reactive } from "vue";
import { useRouter, RouterLink } from "vue-router";
import IMSLayout from "@ims/components/IMSLayout.vue";
import { useAuth } from "@/shared/auth/composables/useAuth";

const router = useRouter();
const { login, error } = useAuth();

const form = reactive({
    email: "",
    password: "",
    device_name: "web",
});

async function submit() {
    await login(form);
    await router.push({ name: "ims.home" });
}
</script>
