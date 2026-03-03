<template>
    <div class="min-h-screen bg-gray-50">
        <AppHeader />
        <main class="mx-auto max-w-6xl px-4 py-6">
            <slot />
        </main>
    </div>
</template>

<script setup>
import AppHeader from "@ims/components/AppHeader.vue";
</script>
