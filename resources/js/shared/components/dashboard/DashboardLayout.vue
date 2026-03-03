<template>
    <div class="min-h-screen">
        <div class="flex min-h-screen">
            <div class="hidden md:block">
                <DashboardSidebar />
            </div>

            <div class="fixed inset-0 z-30 md:hidden" v-if="mobileSidebarOpen">
                <button
                    type="button"
                    class="absolute inset-0 bg-black/40"
                    @click="mobileSidebarOpen = false"
                />
                <div class="relative z-40 h-full w-72">
                    <DashboardSidebar />
                </div>
            </div>

            <div class="flex min-h-screen flex-1 flex-col">
                <DashboardNavbar @toggle-sidebar="mobileSidebarOpen = true" />
                <main class="p-4 md:p-6">
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";
import DashboardNavbar from "@/shared/components/dashboard/DashboardNavbar.vue";
import DashboardSidebar from "@/shared/components/dashboard/DashboardSidebar.vue";

const mobileSidebarOpen = ref(false);
</script>

