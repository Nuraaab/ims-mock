<template>
    <AppLayout>
        <div class="ui-card mx-auto max-w-md p-6 md:p-7">
            <h2 class="ui-text mb-1 text-2xl font-semibold tracking-tight">Login</h2>
            <p class="ui-muted mb-5 text-sm">Access your organization workspace.</p>

            <form class="space-y-4" @submit.prevent="submit">
                <div>
                    <label class="ui-label">Email</label>
                    <input
                        v-model="form.email"
                        type="email"
                        required
                        :disabled="loading"
                        class="ui-input"
                    />
                </div>

                <div>
                    <label class="ui-label">Password</label>
                    <input
                        v-model="form.password"
                        type="password"
                        required
                        :disabled="loading"
                        class="ui-input"
                    />
                </div>

                <button
                    type="submit"
                    :disabled="loading"
                    class="ui-btn-primary w-full"
                >
                    {{ loading ? "Logging in..." : "Login" }}
                </button>
            </form>

            <p v-if="message" class="ui-alert-success">{{ message }}</p>
            <p v-if="error" class="ui-alert-error">{{ error }}</p>
            <ul v-if="errorItems.length" class="mt-2 list-disc pl-5 text-sm text-red-700">
                <li v-for="(item, index) in errorItems" :key="index">{{ item }}</li>
            </ul>

            <p class="ui-muted mt-6 text-sm">
                New organization?
                <RouterLink :to="{ name: 'auth.register' }" class="font-medium text-teal-700 hover:underline">
                    Register here
                </RouterLink>
            </p>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed, onMounted, reactive } from "vue";
import { useRouter, RouterLink } from "vue-router";
import AppLayout from "@/shared/components/AppLayout.vue";
import { useAuth } from "@/shared/composables/useAuth";

const router = useRouter();
const { login, loading, message, error, validationErrors, resetFeedback } = useAuth();

const form = reactive({
    email: "",
    password: "",
    device_name: "web",
});

const errorItems = computed(() => Object.values(validationErrors.value).flat());

onMounted(() => {
    resetFeedback();
});

async function submit() {
    try {
        await login(form);
        await router.push({ name: "dashboard" });
    } catch {
        // Feedback handled by store state.
    }
}
</script>
