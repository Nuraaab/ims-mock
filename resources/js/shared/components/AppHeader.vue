<template>
    <header class="border-b backdrop-blur" :style="{ borderColor: 'var(--ui-border)', background: 'color-mix(in srgb, var(--ui-surface) 85%, transparent)' }">
        <div class="ui-container flex items-center justify-between py-3.5">
            <RouterLink
                :to="{ name: 'home' }"
                class="group inline-flex items-center gap-2"
            >
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-teal-700 text-xs font-bold text-white">
                    ER
                </span>
                <span class="ui-text text-lg font-semibold tracking-tight">ERP Platform</span>
            </RouterLink>

            <RouterLink
                :to="{ name: 'auth.login' }"
                class="ui-btn-primary"
            >
                Login
            </RouterLink>
        </div>
    </header>
</template>

<script setup>
import { RouterLink } from "vue-router";
</script>
