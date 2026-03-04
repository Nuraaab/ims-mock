<template>
    <aside class="flex h-full w-72 flex-col border-r px-4 py-5" :style="sidebarStyle">
        <div class="mb-6 flex items-center gap-3 rounded-xl border px-3 py-3" :style="panelStyle">
            <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-xs font-bold text-white" :style="{ background: 'var(--ui-primary)' }">ER</span>
            <div>
                <p class="ui-text text-sm font-semibold leading-tight">ERP Platform</p>
                <p class="ui-muted text-xs">Operations Console</p>
            </div>
        </div>

        <nav class="space-y-2 overflow-y-auto pr-1">
            <RouterLink :to="{ name: 'dashboard' }" class="flex w-full items-center gap-2 rounded-xl px-3 py-2.5 text-left text-sm font-medium transition" :class="linkClass('dashboard')">
                <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 13h8V3H3v10Zm10 8h8v-6h-8v6Zm0-10h8V3h-8v8ZM3 21h8v-6H3v6Z" />
                </svg>
                Dashboard
            </RouterLink>

            <div v-if="showUserManagement" class="rounded-xl border p-1.5" :style="panelStyle">
                <button type="button" class="ui-text flex w-full items-center justify-between rounded-lg px-2.5 py-2 text-left text-sm font-semibold transition hover:bg-white/40 dark:hover:bg-slate-800/60" @click="userMenuOpen = !userMenuOpen">
                    <span class="flex items-center gap-2">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                        </svg>
                        User Management
                    </span>
                    <span class="ui-muted text-xs">{{ userMenuOpen ? "▾" : "▸" }}</span>
                </button>

                <div v-if="userMenuOpen" class="mt-1 space-y-1 pl-2">
                    <RouterLink v-if="canViewUsers" :to="{ name: 'users.index' }" class="block w-full rounded-lg px-2.5 py-1.5 text-left text-sm transition" :class="linkClass(['users.index'])">
                        Users
                    </RouterLink>
                    <RouterLink v-if="canViewRoles" :to="{ name: 'roles.create' }" class="block w-full rounded-lg px-2.5 py-1.5 text-left text-sm transition" :class="linkClass(['roles.create'])">
                        Roles
                    </RouterLink>
                </div>
            </div>

            <div v-if="showBranchManagement" class="rounded-xl border p-1.5" :style="panelStyle">
                <button type="button" class="ui-text flex w-full items-center justify-between rounded-lg px-2.5 py-2 text-left text-sm font-semibold transition hover:bg-white/40 dark:hover:bg-slate-800/60" @click="branchMenuOpen = !branchMenuOpen">
                    <span class="flex items-center gap-2">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 21h18" />
                            <path d="M5 21V7l7-4 7 4v14" />
                            <path d="M9 9h6" />
                            <path d="M9 13h6" />
                        </svg>
                        Branch Management
                    </span>
                    <span class="ui-muted text-xs">{{ branchMenuOpen ? "▾" : "▸" }}</span>
                </button>

                <div v-if="branchMenuOpen" class="mt-1 space-y-1 pl-2">
                    <RouterLink v-if="canViewBranches" :to="{ name: 'branches.index.page' }" class="block w-full rounded-lg px-2.5 py-1.5 text-left text-sm transition" :class="linkClass(['branches.index.page'])">
                        Branch
                    </RouterLink>
                    <RouterLink
                        v-if="canViewWarehouses"
                        :to="{ name: 'warehouses.index.page' }"
                        class="block w-full rounded-lg px-2.5 py-1.5 text-left text-sm transition"
                        :class="linkClass(['warehouses.index.page'])"
                    >
                        Warehouse
                    </RouterLink>
                    <RouterLink v-if="canViewOutlets" :to="{ name: 'outlets.index.page' }" class="block w-full rounded-lg px-2.5 py-1.5 text-left text-sm transition" :class="linkClass(['outlets.index.page'])">
                        Outlet
                    </RouterLink>
                </div>
            </div>

            <div v-if="showImsManagement" class="rounded-xl border p-1.5" :style="panelStyle">
                <button type="button" class="ui-text flex w-full items-center justify-between rounded-lg px-2.5 py-2 text-left text-sm font-semibold transition hover:bg-white/40 dark:hover:bg-slate-800/60" @click="imsMenuOpen = !imsMenuOpen">
                    <span class="flex items-center gap-2">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z" />
                            <path d="M3.3 7 12 12l8.7-5" />
                            <path d="M12 22V12" />
                        </svg>
                        IMS
                    </span>
                    <span class="ui-muted text-xs">{{ imsMenuOpen ? "▾" : "▸" }}</span>
                </button>

                <div v-if="imsMenuOpen" class="mt-1 space-y-1 pl-2">
                    <div v-if="showItemManagement" class="rounded-lg border p-1.5" :style="panelStyle">
                        <button type="button" class="ui-text flex w-full items-center justify-between rounded-lg px-2.5 py-1.5 text-left text-sm font-medium transition hover:bg-white/40 dark:hover:bg-slate-800/60" @click="itemManagementMenuOpen = !itemManagementMenuOpen">
                            <span class="flex items-center gap-2">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="6" rx="1" />
                                    <rect x="3" y="14" width="18" height="6" rx="1" />
                                </svg>
                                Item Management
                            </span>
                            <span class="ui-muted text-xs">{{ itemManagementMenuOpen ? "▾" : "▸" }}</span>
                        </button>

                        <div v-if="itemManagementMenuOpen" class="mt-1 space-y-1 pl-2">
                            <RouterLink
                                v-if="canViewItemCategories"
                                :to="{ name: 'ims.item-categories.index' }"
                                class="block w-full rounded-lg px-2.5 py-1.5 text-left text-sm transition"
                                :class="linkClass(['ims.item-categories.index'])"
                            >
                                Item Category
                            </RouterLink>
                            <RouterLink
                                v-if="canViewItems"
                                :to="{ name: 'ims.items.index' }"
                                class="block w-full rounded-lg px-2.5 py-1.5 text-left text-sm transition"
                                :class="linkClass(['ims.items.index'])"
                            >
                                Item
                            </RouterLink>
                            <RouterLink
                                v-if="canViewProductGroups"
                                :to="{ name: 'ims.product-groups.index' }"
                                class="block w-full rounded-lg px-2.5 py-1.5 text-left text-sm transition"
                                :class="linkClass(['ims.product-groups.index'])"
                            >
                                Product Group
                            </RouterLink>
                            <RouterLink
                                v-if="canViewMeasurements"
                                :to="{ name: 'ims.measurements.index' }"
                                class="block w-full rounded-lg px-2.5 py-1.5 text-left text-sm transition"
                                :class="linkClass(['ims.measurements.index'])"
                            >
                                Measurement
                            </RouterLink>
                            <RouterLink
                                v-if="canViewProducts"
                                :to="{ name: 'ims.products.index' }"
                                class="block w-full rounded-lg px-2.5 py-1.5 text-left text-sm transition"
                                :class="linkClass(['ims.products.index'])"
                            >
                                Product
                            </RouterLink>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </aside>
</template>

<script setup>
import { computed, onMounted, ref } from "vue";
import { RouterLink, useRoute } from "vue-router";
import { useAuth } from "@/shared/composables/useAuth";

const userMenuOpen = ref(true);
const branchMenuOpen = ref(true);
const imsMenuOpen = ref(true);
const itemManagementMenuOpen = ref(true);
const route = useRoute();

const { token, permissions, fetchMe, hasPermission } = useAuth();

const canViewUsers = computed(() => hasPermission("users.view"));
const canViewRoles = computed(() => hasPermission("roles.view"));
const canViewBranches = computed(() => hasPermission("branches.view"));
const canViewWarehouses = computed(() => hasPermission("warehouses.view"));
const canViewOutlets = computed(() => hasPermission("outlets.view"));
const canViewItemCategories = computed(() => hasPermission("item_categories.view"));
const canViewItems = computed(() => hasPermission("items.view"));
const canViewProductGroups = computed(() => hasPermission("product-groups.view"));
const canViewMeasurements = computed(() => hasPermission("measurements.view"));
const canViewProducts = computed(() => hasPermission("products.view"));

const showUserManagement = computed(() => canViewUsers.value || canViewRoles.value);
const showBranchManagement = computed(() => canViewBranches.value || canViewWarehouses.value || canViewOutlets.value);
const showImsManagement = computed(() => canViewItemCategories.value || canViewProductGroups.value || canViewMeasurements.value || canViewProducts.value);
const showItemManagement = computed(() => canViewItemCategories.value || canViewProductGroups.value || canViewMeasurements.value || canViewProducts.value);

const sidebarStyle = computed(() => ({
    borderColor: "var(--ui-border)",
    background: "color-mix(in srgb, var(--ui-surface) 95%, transparent)",
}));

const panelStyle = computed(() => ({
    borderColor: "var(--ui-border)",
    background: "var(--ui-surface-soft)",
}));

function linkClass(names) {
    const matched = Array.isArray(names) ? names.includes(route.name) : route.name === names;

    return matched
        ? "text-teal-700 bg-teal-50 dark:bg-teal-900/40 dark:text-teal-200"
        : "ui-muted hover:bg-white/60 dark:hover:bg-slate-800/70";
}

onMounted(async () => {
    if (token.value && !permissions.value.length) {
        try {
            await fetchMe();
        } catch {}
    }
});
</script>
