<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center rounded-lg border border-[var(--border)] bg-[var(--surface)] px-4 py-2 text-xs font-semibold uppercase tracking-widest text-[var(--text)] shadow-sm transition duration-150 ease-in-out hover:bg-[var(--rose-pale)] focus:outline-none focus:ring-2 focus:ring-[var(--rose)] focus:ring-offset-2 disabled:opacity-25"
    >
        <slot />
    </button>
</template>
