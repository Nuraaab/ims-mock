import { storeToRefs } from "pinia";
import { useThemeStore } from "@/shared/stores/themeStore";

export function useTheme() {
    const store = useThemeStore();
    const { current, isDark } = storeToRefs(store);
    store.initTheme();

    return {
        theme: current,
        isDark,
        toggleTheme: store.toggleTheme,
    };
}
