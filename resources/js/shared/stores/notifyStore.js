import { defineStore } from "pinia";

let counter = 0;

export const useNotifyStore = defineStore("notify", {
    state: () => ({
        notifications: [],
    }),
    actions: {
        push({ type = "success", title = "", message = "", timeout = 3000 } = {}) {
            if (!message) return null;

            const id = ++counter;
            this.notifications.push({ id, type, title, message });

            if (timeout > 0) {
                window.setTimeout(() => this.remove(id), timeout);
            }

            return id;
        },
        remove(id) {
            this.notifications = this.notifications.filter((item) => item.id !== id);
        },
        success(message, title = "Success", timeout = 3000) {
            return this.push({ type: "success", title, message, timeout });
        },
        error(message, title = "Error", timeout = 4000) {
            return this.push({ type: "error", title, message, timeout });
        },
        info(message, title = "Info", timeout = 3000) {
            return this.push({ type: "info", title, message, timeout });
        },
    },
});
