import { ref } from 'vue'

const STORAGE_KEY = 'theme' // 'dark' | 'light'
export const isDark = ref(false)

function applyTheme(dark) {
    isDark.value = dark
    const root = document.documentElement
    if (dark) root.classList.add('dark')
    else root.classList.remove('dark')
}

export function initTheme() {
    // 1) preferencia guardada
    const saved = localStorage.getItem(STORAGE_KEY)
    if (saved === 'dark' || saved === 'light') {
        applyTheme(saved === 'dark')
        return
    }
    // 2) preferencia del sistema
    const prefers = window.matchMedia?.('(prefers-color-scheme: dark)').matches
    applyTheme(!!prefers)
}

export function toggleTheme() {
    applyTheme(!isDark.value)
    localStorage.setItem(STORAGE_KEY, isDark.value ? 'dark' : 'light')
}
