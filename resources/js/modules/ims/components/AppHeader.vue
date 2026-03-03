<template>
    <header class="border-b border-gray-200 bg-white">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3">
            <RouterLink
                :to="{ name: 'ims.home' }"
                class="text-lg font-semibold text-gray-900"
            >
                IMS Module
            </RouterLink>

            <RouterLink
                :to="{ name: 'ims.auth.login' }"
                class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
            >
                Login
            </RouterLink>
        </div>
    </header>
</template>

<script setup>
import { RouterLink } from "vue-router";
</script>

