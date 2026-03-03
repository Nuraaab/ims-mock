<template>
    <header
        class="flex items-center justify-between border-b px-4 py-3 backdrop-blur md:px-6"
        :style="{
            borderColor: 'var(--ui-border)',
            background: 'color-mix(in srgb, var(--ui-surface) 85%, transparent)',
        }"
    >
        <div class="flex items-center gap-3">
            <button
                type="button"
                class="ui-muted inline-flex h-9 w-9 items-center justify-center rounded-lg border hover:bg-slate-100/40 md:hidden"
                :style="{ borderColor: 'var(--ui-border)' }"
                @click="$emit('toggle-sidebar')"
                aria-label="Open sidebar"
            >
                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            <div>
                <h1 class="ui-text text-lg font-semibold tracking-tight">Dashboard</h1>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button
                type="button"
                class="ui-muted inline-flex h-9 w-9 items-center justify-center rounded-lg border hover:bg-slate-100/40"
                :style="{ borderColor: 'var(--ui-border)' }"
                @click="toggleTheme"
                aria-label="Toggle theme"
            >
                <svg
                    v-if="isDark"
                    viewBox="0 0 24 24"
                    class="h-5 w-5"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <circle cx="12" cy="12" r="4" />
                    <path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4" />
                </svg>
                <svg
                    v-else
                    viewBox="0 0 24 24"
                    class="h-5 w-5"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path d="M21 12.8A9 9 0 1 1 11.2 3 7 7 0 0 0 21 12.8z" />
                </svg>
            </button>

            <button
                type="button"
                class="ui-btn-primary"
                :disabled="loading"
                @click="onLogout"
            >
                {{ loading ? "Logging out..." : "Logout" }}
            </button>
        </div>
    </header>
</template>

<script setup>
import { useRouter } from "vue-router";
import { useAuth } from "@/shared/composables/useAuth";
import { useTheme } from "@/shared/composables/useTheme";

defineEmits(["toggle-sidebar"]);

const router = useRouter();
const { logout, loading } = useAuth();
const { isDark, toggleTheme } = useTheme();

async function onLogout() {
    try {
        await logout();
    } finally {
        await router.push({ name: "auth.login" });
    }
}
</script>

