<template>
  <button
    class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-sm font-medium
           text-white shadow-sm transition focus:outline-none
           bg-indigo-600 hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500/50
           disabled:opacity-60 disabled:cursor-not-allowed
           dark:bg-indigo-500 dark:hover:bg-indigo-400 dark:focus:ring-indigo-400/40"
  >
    <slot />
  </button>
</template>
