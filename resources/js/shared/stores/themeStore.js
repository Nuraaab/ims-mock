import { defineStore } from "pinia";

const THEME_KEY = "app_theme";

export const useThemeStore = defineStore("theme", {
    state: () => ({
        current: "light",
        initialized: false,
    }),
    getters: {
        isDark: (state) => state.current === "dark",
    },
    actions: {
        applyTheme(theme) {
            document.documentElement.setAttribute("data-theme", theme);
        },
        initTheme() {
            if (this.initialized) return;
            const stored = localStorage.getItem(THEME_KEY);
            this.current = stored === "dark" ? "dark" : "light";
            this.applyTheme(this.current);
            this.initialized = true;
        },
        toggleTheme() {
            this.current = this.current === "dark" ? "light" : "dark";
            localStorage.setItem(THEME_KEY, this.current);
            this.applyTheme(this.current);
        },
    },
});
