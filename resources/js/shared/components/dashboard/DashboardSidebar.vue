<template>
    <aside
        class="flex h-full w-72 flex-col border-r p-4"
        :style="{ borderColor: 'var(--ui-border)', background: 'color-mix(in srgb, var(--ui-surface) 90%, transparent)' }"
    >
        <div class="mb-6 flex items-center gap-2 px-2">
            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-teal-700 text-xs font-bold text-white">
                ER
            </span>
            <div>
                <p class="ui-text text-sm font-semibold">ERP Platform</p>
            </div>
        </div>

        <nav class="space-y-2">
            <RouterLink
                :to="{ name: 'dashboard' }"
                class="ui-text flex w-full items-center rounded-xl px-3 py-2 text-left text-sm font-medium hover:bg-slate-100/40 dark:hover:bg-slate-800/50"
            >
                Dashboard
            </RouterLink>

            <div class="ui-surface-soft p-2">
                <button
                    type="button"
                    class="ui-text flex w-full items-center justify-between rounded-lg px-2 py-2 text-left text-sm font-medium hover:bg-white/40 dark:hover:bg-slate-800/40"
                    @click="userMenuOpen = !userMenuOpen"
                >
                    <span>User Management</span>
                    <span class="ui-muted text-xs">{{ userMenuOpen ? "▲" : "▼" }}</span>
                </button>

                <div v-if="userMenuOpen" class="mt-1 space-y-1 pl-2">
                    <RouterLink
                        :to="{ name: 'users.index' }"
                        class="ui-muted block w-full rounded-lg px-2 py-1.5 text-left text-sm hover:bg-white/40 dark:hover:bg-slate-800/40"
                    >
                        Users
                    </RouterLink>
                    <RouterLink
                        :to="{ name: 'roles.create' }"
                        class="ui-muted block w-full rounded-lg px-2 py-1.5 text-left text-sm hover:bg-white/40 dark:hover:bg-slate-800/40"
                    >
                        Roles
                    </RouterLink>
                </div>
            </div>
        </nav>
    </aside>
</template>

<script setup>
import { ref } from "vue";
import { RouterLink } from "vue-router";

const userMenuOpen = ref(true);
</script>
