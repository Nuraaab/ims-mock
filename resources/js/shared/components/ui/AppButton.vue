<template>
    <button
        :type="type"
        :disabled="disabled"
        :class="buttonClass"
    >
        <slot />
    </button>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    type: {
        type: String,
        default: "button",
    },
    variant: {
        type: String,
        default: "primary",
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});

const buttonClass = computed(() => {
    if (props.variant === "danger") {
        return "rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-60";
    }

    if (props.variant === "soft") {
        return "ui-surface-soft ui-text rounded-xl px-4 py-2 text-sm font-medium";
    }

    return "ui-btn-primary";
});
</script>
