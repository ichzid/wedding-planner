<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center pt-6 sm:justify-center sm:pt-0"
        style="background-color: var(--bg);"
    >
        <div>
            <Link href="/" class="flex flex-col items-center gap-2">
                <img src="/logo.png" alt="Logo" class="h-20 w-20 object-contain rounded-lg shadow-sm" />
                <div class="text-center">
                    <h1 style="font-family: var(--font-display); color: var(--text);" class="text-2xl font-bold tracking-tight">Wedding Planner</h1>
                    <p style="color: var(--text-muted);" class="text-sm">Hari Istimewamu</p>
                </div>
            </Link>
        </div>

        <div
            class="mt-8 w-full overflow-hidden px-6 py-8 shadow-lg sm:max-w-md sm:rounded-xl"
            style="background-color: var(--surface); border: 1px solid var(--border);"
        >
            <slot />
        </div>
    </div>
</template>

<style>
/* Memastikan variabel CSS global tersedia */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:wght@500;600;700&display=swap');

:root {
  --font: 'Inter', sans-serif;
  --font-display: 'Playfair Display', serif;
  --bg: #faf6f3;
  --surface: #fffdfb;
  --border: #e8d5ce;
  --text: #4a2c2a;
  --text-muted: #8b6f6a;
  --text-dim: #b8a09a;
  --rose: #c4956a;
  --rose-light: #e8c4a2;
  --rose-pale: #faf0e8;
}

body {
  font-family: var(--font);
  color: var(--text);
  background-color: var(--bg);
}

.form-input {
  width: 100%;
  border: 1px solid var(--border);
  border-radius: 8px;
  padding: 10px 14px;
  font-size: 14px;
  background: var(--surface);
  color: var(--text);
  transition: all 0.2s;
}
.form-input:focus {
  outline: none;
  border-color: var(--rose);
  box-shadow: 0 0 0 3px var(--rose-pale);
}

.btn-primary {
  display: inline-flex;
  justify-content: center;
  align-items: center;
  width: 100%;
  background: var(--rose);
  color: white;
  border: none;
  border-radius: 8px;
  padding: 10px 16px;
  font-weight: 600;
  font-size: 14px;
  transition: all 0.2s;
}
.btn-primary:hover {
  background: #b8845c;
  transform: translateY(-1px);
}
</style>
