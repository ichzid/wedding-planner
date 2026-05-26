<script setup>
import { Link } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    </Head>
    <div
        class="flex min-h-screen flex-col items-center justify-center pt-6 px-4 sm:pt-0"
        style="background-color: var(--bg);"
    >
        <div class="mb-6">
            <Link href="/" class="flex flex-col items-center gap-3">
                <img src="/logo.png" alt="Logo" class="h-24 w-24 object-contain rounded-xl shadow-sm" />
                <div class="text-center">
                    <h1 style="font-family: var(--font-display); color: var(--text);" class="text-3xl font-bold tracking-tight">Wedding Planner</h1>
                    <p style="color: var(--text-muted);" class="text-sm mt-1">Hari Istimewamu</p>
                </div>
            </Link>
        </div>

        <div
            class="w-full overflow-hidden px-8 py-10 shadow-xl sm:max-w-md sm:rounded-2xl"
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

.btn-outline {
  display: inline-flex;
  justify-content: center;
  align-items: center;
  gap: 10px;
  width: 100%;
  background: transparent;
  color: var(--text);
  border: 1px solid var(--border);
  border-radius: 8px;
  padding: 10px 16px;
  font-weight: 600;
  font-size: 14px;
  transition: all 0.2s;
}
.btn-outline:hover {
  background: var(--bg);
  border-color: var(--text-dim);
  transform: translateY(-1px);
}

.auth-divider {
  display: flex;
  align-items: center;
  text-align: center;
  color: var(--text-dim);
  font-size: 13px;
  margin: 24px 0 16px;
}
.auth-divider::before, .auth-divider::after {
  content: '';
  flex: 1;
  border-bottom: 1px solid var(--border);
}
.auth-divider:not(:empty)::before { margin-right: 12px; }
.auth-divider:not(:empty)::after { margin-left: 12px; }
</style>
