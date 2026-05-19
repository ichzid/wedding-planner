<template>
  <div class="app-shell">
    <!-- Sidebar -->
    <aside class="sidebar" :class="{ 'sidebar--open': sidebarOpen }">
      <!-- Brand -->
      <div class="sidebar__brand">
        <div class="brand-icon">
          <i class="fa-solid fa-rings-wedding"></i>
        </div>
        <div class="brand-text">
          <span class="brand-title">Wedding Planner</span>
          <span class="brand-sub">Hari Istimewamu</span>
        </div>
      </div>

      <!-- Nav -->
      <nav class="sidebar__nav">
        <span class="nav-label">Menu</span>
        <Link :href="route('dashboard')" class="nav-link" :class="{ 'nav-link--active': isRoute('dashboard') }">
          <i class="fa-solid fa-gauge-high nav-icon"></i>
          <span>Dashboard</span>
        </Link>
        <Link :href="route('checklist.index')" class="nav-link" :class="{ 'nav-link--active': isRoute('checklist.index') }">
          <i class="fa-solid fa-list-check nav-icon"></i>
          <span>Checklist</span>
        </Link>
        <Link :href="route('budget.index')" class="nav-link" :class="{ 'nav-link--active': isRoute('budget.index') }">
          <i class="fa-solid fa-wallet nav-icon"></i>
          <span>Budget</span>
        </Link>
        <Link :href="route('seserahan.index')" class="nav-link" :class="{ 'nav-link--active': isRoute('seserahan.index') }">
          <i class="fa-solid fa-gift nav-icon"></i>
          <span>Seserahan</span>
        </Link>
        <Link :href="route('dokumen-kua.index')" class="nav-link" :class="{ 'nav-link--active': isRoute('dokumen-kua.index') }">
          <i class="fa-solid fa-file-contract nav-icon"></i>
          <span>Dokumen KUA</span>
        </Link>
      </nav>
    </aside>

    <!-- Mobile overlay -->
    <div v-if="sidebarOpen" class="sidebar-overlay" @click="sidebarOpen = false"></div>

    <!-- Main area -->
    <div class="main-area">
      <!-- Topbar -->
      <header class="topbar">
        <button class="topbar__menu-btn lg-hidden" @click="sidebarOpen = !sidebarOpen">
          <i class="fa-solid fa-bars"></i>
        </button>

        <div class="topbar__breadcrumb">
          <Link :href="route('dashboard')" class="breadcrumb-home">
            <i class="fa-solid fa-house"></i>
          </Link>
          <i v-if="pageTitle" class="fa-solid fa-chevron-right breadcrumb-sep"></i>
          <span v-if="pageTitle" class="breadcrumb-page">{{ pageTitle }}</span>
        </div>

        <div class="topbar__right">
          <span class="topbar__date">{{ currentDate }}</span>
          <div class="topbar__avatar">
            <i class="fa-solid fa-user"></i>
          </div>
        </div>
      </header>

      <!-- Content -->
      <main class="content">
        <slot />
      </main>
    </div>

    <!-- Toast container -->
    <div id="toast-container" class="toast-container"></div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

const sidebarOpen = ref(false);

const page = usePage();

const routeMap = {
  'dashboard': 'Dashboard',
  'checklist.index': 'Checklist Pernikahan',
  'budget.index': 'Wedding Budget',
  'seserahan.index': 'List Seserahan',
  'dokumen-kua.index': 'Dokumen KUA',
};

const pageTitle = computed(() => {
  const name = page.props.routeName;
  return routeMap[name] || null;
});

function isRoute(name) {
  return page.props.routeName === name;
}

const currentDate = computed(() => {
  const now = new Date();
  return now.toLocaleDateString('id-ID', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  });
});

// Close sidebar on large screens
onMounted(() => {
  if (window.innerWidth >= 1024) {
    sidebarOpen.value = true;
  }
});
</script>

<style>
/* === GLOBAL RESET & BASE === */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
  --font: 'Inter', sans-serif;

  /* Monochrome palette — warm-tinted blacks/grays */
  --ink-0:   #ffffff;
  --ink-50:  #f8f8f7;
  --ink-100: #f0f0ef;
  --ink-200: #e4e4e2;
  --ink-300: #c9c9c6;
  --ink-400: #9e9e9a;
  --ink-500: #747470;
  --ink-600: #555552;
  --ink-700: #3e3e3b;
  --ink-800: #2a2a28;
  --ink-900: #1a1a18;
  --ink-950: #0d0d0c;

  /* Semantic */
  --bg:         var(--ink-50);
  --surface:    var(--ink-0);
  --border:     var(--ink-200);
  --text:       var(--ink-900);
  --text-muted: var(--ink-500);
  --text-dim:   var(--ink-400);

  /* Status colors — muted */
  --status-ok-bg:   #f0f4f0;
  --status-ok-text: #3d6b3d;
  --status-warn-bg: #f5f2ec;
  --status-warn-text: #7a5a28;
  --status-danger-bg: #f5efef;
  --status-danger-text: #8a3535;

  --radius-sm: 6px;
  --radius:    10px;
  --radius-lg: 14px;
  --sidebar-w: 220px;
  --topbar-h:  56px;

  --shadow-sm: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
  --shadow:    0 2px 8px rgba(0,0,0,0.07), 0 1px 3px rgba(0,0,0,0.05);
  --shadow-lg: 0 8px 24px rgba(0,0,0,0.10), 0 3px 8px rgba(0,0,0,0.06);
}

html, body { 
  height: 100%; 
  margin: 0; 
  padding: 0; 
  background-color: var(--bg); 
  color: var(--text); 
}
body { 
  font-family: var(--font); 
  -webkit-font-smoothing: antialiased; 
  -moz-osx-font-smoothing: grayscale;
}

/* === APP SHELL === */
.app-shell {
  display: flex;
  height: 100vh;
  overflow: hidden;
}

/* === SIDEBAR === */
.sidebar {
  width: var(--sidebar-w);
  background: var(--ink-950);
  display: flex;
  flex-direction: column;
  flex-shrink: 0;
  transition: transform 0.25s ease;
  z-index: 50;
}

@media (max-width: 1023px) {
  .sidebar {
    position: fixed;
    inset-y: 0;
    left: 0;
    transform: translateX(-100%);
  }
  .sidebar--open {
    transform: translateX(0);
  }
}

.sidebar-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.5);
  z-index: 40;
}

.sidebar__brand {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 18px 16px 16px;
  border-bottom: 1px solid rgba(255,255,255,0.07);
}

.brand-icon {
  width: 32px;
  height: 32px;
  background: rgba(255,255,255,0.1);
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: rgba(255,255,255,0.7);
  font-size: 13px;
  flex-shrink: 0;
}

.brand-title {
  display: block;
  color: rgba(255,255,255,0.9);
  font-size: 13px;
  font-weight: 600;
  line-height: 1.2;
  letter-spacing: -0.01em;
}

.brand-sub {
  display: block;
  color: rgba(255,255,255,0.3);
  font-size: 10.5px;
  margin-top: 1px;
}

.sidebar__nav {
  flex: 1;
  padding: 10px 10px;
  overflow-y: auto;
}

.nav-label {
  display: block;
  font-size: 9.5px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.12em;
  color: rgba(255,255,255,0.25);
  padding: 10px 8px 6px;
}

.nav-link {
  display: flex;
  align-items: center;
  gap: 9px;
  padding: 8px 10px;
  border-radius: 7px;
  font-size: 13px;
  font-weight: 500;
  color: rgba(255,255,255,0.45);
  text-decoration: none;
  transition: all 0.15s ease;
  margin-bottom: 1px;
}

.nav-link:hover {
  background: rgba(255,255,255,0.07);
  color: rgba(255,255,255,0.75);
}

.nav-link--active {
  background: rgba(255,255,255,0.1);
  color: rgba(255,255,255,0.92);
  font-weight: 600;
}

.nav-icon {
  width: 14px;
  text-align: center;
  font-size: 12px;
  flex-shrink: 0;
  opacity: 0.8;
}

/* === MAIN AREA === */
.main-area {
  flex: 1;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  min-width: 0;
}

/* === TOPBAR === */
.topbar {
  height: var(--topbar-h);
  background: var(--surface);
  border-bottom: 1px solid var(--border);
  display: flex;
  align-items: center;
  padding: 0 20px;
  gap: 12px;
  flex-shrink: 0;
}

.topbar__menu-btn {
  background: none;
  border: none;
  cursor: pointer;
  color: var(--ink-400);
  font-size: 15px;
  padding: 4px;
  transition: color 0.15s;
}
.topbar__menu-btn:hover { color: var(--ink-900); }

.topbar__breadcrumb {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  min-width: 0;
}

.breadcrumb-home {
  color: var(--ink-400);
  text-decoration: none;
  font-size: 12px;
  transition: color 0.15s;
  flex-shrink: 0;
}
.breadcrumb-home:hover { color: var(--ink-900); }

.breadcrumb-sep {
  color: var(--ink-300);
  font-size: 9px;
  flex-shrink: 0;
}

.breadcrumb-page {
  color: var(--ink-700);
  font-weight: 600;
  font-size: 13px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.topbar__right {
  margin-left: auto;
  display: flex;
  align-items: center;
  gap: 12px;
}

.topbar__date {
  font-size: 12px;
  color: var(--ink-400);
}

@media (max-width: 640px) { .topbar__date { display: none; } }

.topbar__avatar {
  width: 30px;
  height: 30px;
  background: var(--ink-900);
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: rgba(255,255,255,0.6);
  font-size: 11px;
}

.lg-hidden { display: none; }
@media (max-width: 1023px) { .lg-hidden { display: flex; } }

/* === CONTENT === */
.content {
  flex: 1;
  overflow-y: auto;
  padding: 24px 24px;
}

/* === SCROLLBAR === */
::-webkit-scrollbar { width: 5px; height: 5px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: var(--ink-300); border-radius: 3px; }

/* === CARDS === */
.card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
}

/* === TABLE === */
.tbl { width: 100%; border-collapse: collapse; }
.tbl th {
  padding: 10px 14px;
  text-align: left;
  font-size: 10.5px;
  font-weight: 700;
  color: var(--ink-400);
  text-transform: uppercase;
  letter-spacing: 0.07em;
  background: var(--ink-50);
  border-bottom: 1px solid var(--border);
  white-space: nowrap;
}
.tbl td {
  padding: 11px 14px;
  font-size: 13.5px;
  color: var(--ink-800);
  border-bottom: 1px solid var(--ink-100);
  vertical-align: middle;
}
.tbl tbody tr:last-child td { border-bottom: none; }
.tbl tbody tr:hover td { background: var(--ink-50); }

/* === BADGE / CHIP === */
.chip {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 2px 8px;
  border-radius: 20px;
  font-size: 11px;
  font-weight: 600;
  letter-spacing: 0.01em;
}
.chip--ok      { background: var(--status-ok-bg); color: var(--status-ok-text); }
.chip--warn    { background: var(--status-warn-bg); color: var(--status-warn-text); }
.chip--danger  { background: var(--status-danger-bg); color: var(--status-danger-text); }
.chip--neutral { background: var(--ink-100); color: var(--ink-600); }
.chip--outline { background: transparent; border: 1px solid var(--border); color: var(--ink-600); }

/* === BUTTONS === */
.btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 8px 16px;
  border-radius: var(--radius-sm);
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.15s ease;
  border: none;
  text-decoration: none;
  white-space: nowrap;
}
.btn:disabled { opacity: 0.55; cursor: not-allowed; }

.btn--dark {
  background: var(--ink-900);
  color: #fff;
}
.btn--dark:hover:not(:disabled) { background: var(--ink-800); }

.btn--outline {
  background: var(--surface);
  color: var(--ink-700);
  border: 1px solid var(--border);
}
.btn--outline:hover:not(:disabled) { background: var(--ink-50); border-color: var(--ink-300); }

.btn--ghost {
  background: transparent;
  color: var(--ink-500);
}
.btn--ghost:hover:not(:disabled) { background: var(--ink-100); color: var(--ink-800); }

.btn--danger-ghost {
  background: transparent;
  color: var(--ink-400);
}
.btn--danger-ghost:hover:not(:disabled) { background: var(--status-danger-bg); color: var(--status-danger-text); }

.btn--sm { padding: 5px 10px; font-size: 12px; gap: 4px; }
.btn--icon { padding: 6px; width: 30px; height: 30px; justify-content: center; }

/* === FORM INPUTS === */
.form-input {
  width: 100%;
  border: 1px solid var(--border);
  border-radius: var(--radius-sm);
  padding: 8px 11px;
  font-size: 13.5px;
  font-family: var(--font);
  color: var(--text);
  background: var(--surface);
  outline: none;
  transition: border-color 0.15s, box-shadow 0.15s;
  appearance: none;
}
.form-input:focus {
  border-color: var(--ink-600);
  box-shadow: 0 0 0 3px rgba(10,10,10,0.06);
}
.form-input::placeholder { color: var(--ink-300); }
select.form-input {
  cursor: pointer;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23747470' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 12px center;
  background-size: 14px;
  padding-right: 36px;
}
textarea.form-input { resize: none; }

.form-label {
  display: block;
  font-size: 11.5px;
  font-weight: 600;
  color: var(--ink-500);
  margin-bottom: 5px;
  letter-spacing: 0.01em;
}

.form-error { font-size: 11.5px; color: var(--status-danger-text); margin-top: 4px; }

/* === PROGRESS === */
.prog-track { height: 5px; background: var(--ink-100); border-radius: 99px; overflow: hidden; }
.prog-fill { height: 100%; border-radius: 99px; background: var(--ink-900); transition: width 0.5s ease; }

/* === MODAL === */
.modal-backdrop {
  position: fixed;
  inset: 0;
  z-index: 100;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 16px;
  background: rgba(0,0,0,0.45);
  backdrop-filter: blur(5px);
}
.modal-box {
  background: var(--surface);
  border-radius: var(--radius-lg);
  width: 100%;
  max-width: 520px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: var(--shadow-lg);
}
.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 20px;
  border-bottom: 1px solid var(--border);
  position: sticky;
  top: 0;
  background: var(--surface);
  z-index: 1;
}
.modal-title { font-size: 14px; font-weight: 700; color: var(--ink-900); }
.modal-body { padding: 20px; }
.modal-footer {
  display: flex;
  gap: 8px;
  padding: 16px 20px;
  border-top: 1px solid var(--border);
}

/* === TOAST === */
.toast-container {
  position: fixed;
  top: 20px;
  right: 20px;
  z-index: 9999;
  display: flex;
  flex-direction: column;
  gap: 8px;
  pointer-events: none;
}

/* === CHECKBOX === */
input[type=checkbox] { accent-color: var(--ink-900); cursor: pointer; }

/* === INERTIA PROGRESS === */
#nprogress .bar { background: var(--ink-900) !important; height: 2px !important; }

/* === PAGE HEADER === */
.page-header { margin-bottom: 24px; }
.page-title { font-size: 20px; font-weight: 700; color: var(--ink-900); letter-spacing: -0.02em; }
.page-sub { font-size: 13px; color: var(--ink-400); margin-top: 3px; }

/* === EMPTY STATE === */
.empty-state {
  text-align: center;
  padding: 48px 20px;
}
.empty-state__icon { font-size: 32px; color: var(--ink-300); margin-bottom: 12px; }
.empty-state__text { font-size: 14px; color: var(--ink-400); }
</style>
