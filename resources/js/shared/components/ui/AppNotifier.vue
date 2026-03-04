<template>
    <div class="pointer-events-none fixed right-4 top-4 z-[1200] flex w-full max-w-sm flex-col gap-3">
        <article
            v-for="item in notifications"
            :key="item.id"
            class="pointer-events-auto rounded-xl border px-4 py-3 shadow-lg backdrop-blur"
            :class="cardClass(item.type)"
        >
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm font-semibold">{{ item.title }}</p>
                    <p class="mt-1 text-sm">{{ item.message }}</p>
                </div>
                <button class="rounded p-1 text-xs opacity-70 transition hover:opacity-100" type="button" @click="remove(item.id)">x</button>
            </div>
        </article>
    </div>
</template>

<script setup>
import { storeToRefs } from "pinia";
import { useNotifyStore } from "@/shared/stores/notifyStore";

const store = useNotifyStore();
const { notifications } = storeToRefs(store);

function remove(id) {
    store.remove(id);
}

function cardClass(type) {
    if (type === "error") {
        return "border-red-300 bg-red-50 text-red-800 dark:border-red-900 dark:bg-red-950/70 dark:text-red-200";
    }
    if (type === "info") {
        return "border-sky-300 bg-sky-50 text-sky-800 dark:border-sky-900 dark:bg-sky-950/70 dark:text-sky-200";
    }
    return "border-emerald-300 bg-emerald-50 text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950/70 dark:text-emerald-200";
}
</script>
