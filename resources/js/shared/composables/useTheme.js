import { computed, ref } from "vue";

const THEME_KEY = "app_theme";
const theme = ref(localStorage.getItem(THEME_KEY) || "light");

function applyTheme(value) {
    document.documentElement.setAttribute("data-theme", value);
}

applyTheme(theme.value);

export function useTheme() {
    const isDark = computed(() => theme.value === "dark");

    function toggleTheme() {
        theme.value = isDark.value ? "light" : "dark";
        localStorage.setItem(THEME_KEY, theme.value);
        applyTheme(theme.value);
    }

    return {
        theme,
        isDark,
        toggleTheme,
    };
}

