<template>
    <DashboardLayout>
        <section class="mb-6">
            <h2 class="ui-text text-2xl font-semibold tracking-tight">Organization Dashboard</h2>
        
        </section>

        <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <article class="ui-card p-4">
                <p class="ui-muted text-xs uppercase tracking-wide">Total Users</p>
                <p class="ui-text mt-2 text-3xl font-semibold">24</p>
                <p class="mt-1 text-xs text-emerald-600">+3 this week</p>
            </article>
            <article class="ui-card p-4">
                <p class="ui-muted text-xs uppercase tracking-wide">Active Roles</p>
                <p class="ui-text mt-2 text-3xl font-semibold">6</p>
                <p class="ui-muted mt-1 text-xs">RBAC configured</p>
            </article>
            <article class="ui-card p-4">
                <p class="ui-muted text-xs uppercase tracking-wide">Products</p>
                <p class="ui-text mt-2 text-3xl font-semibold">138</p>
                <p class="mt-1 text-xs text-emerald-600">+12 this month</p>
            </article>
            <article class="ui-card p-4">
                <p class="ui-muted text-xs uppercase tracking-wide">Warehouses</p>
                <p class="ui-text mt-2 text-3xl font-semibold">4</p>
                <p class="ui-muted mt-1 text-xs">2 branches linked</p>
            </article>
        </section>

        <section class="mt-6 grid grid-cols-1 gap-4 lg:grid-cols-3">
            <article class="ui-card p-5 lg:col-span-2">
                <h3 class="ui-text text-base font-semibold">Recent Activity</h3>
                <ul class="ui-muted mt-4 space-y-3 text-sm">
                    <li class="ui-surface-soft p-3">New user created in Sales team.</li>
                    <li class="ui-surface-soft p-3">Role permissions updated for Store Manager.</li>
                    <li class="ui-surface-soft p-3">Inventory count synced for Main Warehouse.</li>
                </ul>
            </article>
            <article class="ui-card p-5">
                <h3 class="ui-text text-base font-semibold">Quick Actions</h3>
                <div class="mt-4 space-y-2">
                    <button type="button" class="ui-btn-primary w-full">Create User</button>
                    <RouterLink :to="{ name: 'roles.create' }" class="ui-btn-primary block w-full text-center">
                        Create Role
                    </RouterLink>
                </div>
            </article>
        </section>
    </DashboardLayout>
</template>

<script setup>
import DashboardLayout from "@/shared/components/dashboard/DashboardLayout.vue";
import { RouterLink } from "vue-router";
</script>
