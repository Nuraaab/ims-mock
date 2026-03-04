<template>
    <Teleport to="body">
        <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <button
                type="button"
                class="absolute inset-0 bg-black/45"
                aria-label="Close modal backdrop"
                @click="$emit('close')"
            />
            <div class="ui-card relative z-10 w-full max-w-lg p-5 md:p-6">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="ui-text text-lg font-semibold">{{ title }}</h3>
                    <button
                        type="button"
                        class="ui-muted rounded-md p-1.5 text-lg leading-none transition hover:bg-slate-100 dark:hover:bg-slate-800"
                        aria-label="Close modal"
                        @click="$emit('close')"
                    >
                        &times;
                    </button>
                </div>
                <slot />
            </div>
        </div>
    </Teleport>
</template>

<script setup>
defineProps({
    open: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        default: "",
    },
});

defineEmits(["close"]);
</script>
