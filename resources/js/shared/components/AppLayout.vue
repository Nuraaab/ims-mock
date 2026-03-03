<template>
    <div class="min-h-screen">
        <AppHeader />
        <main class="ui-container py-8 md:py-10">
            <slot />
        </main>
    </div>
</template>

<script setup>
import AppHeader from "@/shared/components/AppHeader.vue";
</script>

