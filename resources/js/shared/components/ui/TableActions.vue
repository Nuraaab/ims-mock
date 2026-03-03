<template>
    <div class="flex gap-2">
        <AppButton variant="soft" @click="$emit('edit')">Edit</AppButton>
        <AppButton variant="danger" @click="$emit('delete')">Delete</AppButton>
    </div>
</template>

<script setup>
import AppButton from "@/shared/components/ui/AppButton.vue";

defineEmits(["edit", "delete"]);
</script>
