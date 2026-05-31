<template>
  <Head>
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  </Head>
  <div class="app-shell" @click="closeDropdowns">
    <!-- Sidebar -->
    <aside class="sidebar" :class="{ 'sidebar--open': sidebarOpen }">
      <!-- Brand -->
      <div class="sidebar__brand">
        <div class="brand-icon">
          <img src="/logo.png" alt="Logo" style="width: 100%; height: 100%; object-fit: contain; border-radius: var(--radius-sm);">
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
        <Link :href="route('tamu.index')" class="nav-link" :class="{ 'nav-link--active': isRoute('tamu.index') }">
          <i class="fa-solid fa-users nav-icon"></i>
          <span>Daftar Undangan</span>
        </Link>
        <span class="nav-label" style="margin-top: 15px;">Pengaturan</span>
        <Link :href="route('profile.edit')" class="nav-link" :class="{ 'nav-link--active': isRoute('profile.edit') }">
          <i class="fa-solid fa-user-gear nav-icon"></i>
          <span>Pengaturan Akun</span>
        </Link>
        <Link :href="route('logout')" method="post" as="button" class="nav-link" style="width: 100%; text-align: left;">
          <i class="fa-solid fa-arrow-right-from-bracket nav-icon"></i>
          <span>Keluar</span>
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

        <div class="topbar__right relative">
          <div class="topbar__greeting">
            <span>{{ greeting }}</span>
          </div>
          <span class="topbar__date hidden-mobile">{{ currentDate }}</span>
          
          <div class="user-dropdown-container">
            <button class="topbar__avatar" @click.stop="userMenuOpen = !userMenuOpen">
              <i class="fa-solid fa-user"></i>
            </button>
            
            <div v-if="userMenuOpen" class="user-dropdown">
              <div class="user-dropdown-header">
                <p class="user-dropdown-name">{{ $page.props.auth?.user?.name || 'Administrator' }}</p>
                <p class="user-dropdown-email">{{ $page.props.auth?.user?.email || 'admin@example.com' }}</p>
              </div>
              <div class="user-dropdown-divider"></div>
              <Link :href="route('profile.edit')" class="user-dropdown-item">
                <i class="fa-solid fa-user-pen"></i> Pengaturan Profil
              </Link>
              <Link :href="route('logout')" method="post" as="button" class="user-dropdown-item text-danger">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar
              </Link>
            </div>
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
import { Head, Link, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

const sidebarOpen = ref(false);
const userMenuOpen = ref(false);

const closeDropdowns = () => {
    userMenuOpen.value = false;
};

const page = usePage();

const routeMap = {
  'dashboard': 'Dashboard',
  'checklist.index': 'Checklist Pernikahan',
  'budget.index': 'Wedding Budget',
  'seserahan.index': 'List Seserahan',
  'dokumen-kua.index': 'Dokumen KUA',
  'tamu.index': 'Daftar Undangan',
  'profile.edit': 'Pengaturan Akun',
};

const pageTitle = computed(() => {
  const name = route().current();
  return routeMap[name] || null;
});

function isRoute(name) {
  return route().current() === name;
}

const greeting = computed(() => {
  const hour = new Date().getHours();
  if (hour >= 4 && hour < 10) return 'Selamat Pagi';
  if (hour >= 10 && hour < 14) return 'Selamat Siang';
  if (hour >= 14 && hour < 18) return 'Selamat Sore';
  return 'Selamat Malam';
});

const currentDate = computed(() => {
  const now = new Date();
  return now.toLocaleDateString('id-ID', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  });
});

// Removed onMounted that incorrectly opened mobile sidebar on desktop
</script>

<style>
/* === GOOGLE FONTS === */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:wght@500;600;700&display=swap');

/* === GLOBAL RESET & BASE === */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
  /* === FONTS === */
  --font: 'Inter', sans-serif;
  --font-display: 'Playfair Display', serif;

  /* === SPACING (4px grid) === */
  --space-xs:  4px;
  --space-sm:  8px;
  --space-md:  12px;
  --space-lg:  16px;
  --space-xl:  20px;
  --space-2xl: 24px;
  --space-3xl: 32px;

  /* === ROSE GOLD COLOR PALETTE === */
  /* Warm cream backgrounds */
  --bg:          #faf6f3;
  --surface:     #fffdfb;
  --border:      #e8d5ce;

  /* Warm brown text */
  --text:        #4a2c2a;
  --text-muted:  #8b6f6a;
  --text-dim:    #b8a09a;

  /* Rose Gold accent */
  --rose:        #c4956a;
  --rose-light:  #e8c4a2;
  --rose-pale:   #faf0e8;

  /* Sidebar gradient colors */
  --sidebar-from: #3d1e2b;
  --sidebar-to:   #5a3a3a;

  /* Status colors — refined */
  --ok-bg:     #eef5f0;
  --ok-text:   #5a9e6f;
  --ok-border: #c3e0cb;

  --warn-bg:     #fdf6ea;
  --warn-text:   #c4953e;
  --warn-border: #f0d9a8;

  --danger-bg:     #fdf0f0;
  --danger-text:   #c75b5b;
  --danger-border: #f0c8c8;

  /* Legacy status var compat */
  --status-ok-bg:   var(--ok-bg);
  --status-ok-text: var(--ok-text);
  --status-warn-bg: var(--warn-bg);
  --status-warn-text: var(--warn-text);
  --status-danger-bg: var(--danger-bg);
  --status-danger-text: var(--danger-text);

  /* Stat card color accents */
  --stat-pink:     #fff0f3;
  --stat-peach:    #fff8f0;
  --stat-lavender: #f3f0ff;
  --stat-sage:     #f0f8f4;

  --radius-sm: 6px;
  --radius:    10px;
  --radius-lg: 14px;
  --sidebar-w: 230px;
  --topbar-h:  56px;

  --shadow-sm:  0 1px 3px rgba(74,44,42,0.06), 0 1px 2px rgba(74,44,42,0.04);
  --shadow:     0 2px 8px rgba(74,44,42,0.08), 0 1px 3px rgba(74,44,42,0.05);
  --shadow-lg:  0 8px 24px rgba(74,44,42,0.12), 0 3px 8px rgba(74,44,42,0.06);
  --shadow-hover: 0 6px 16px rgba(74,44,42,0.12), 0 2px 4px rgba(74,44,42,0.06);
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

/* === PAGE TRANSITION === */
.content > * {
  animation: pageFadeIn 0.3s ease-out;
}
@keyframes pageFadeIn {
  from { opacity: 0; transform: translateY(12px); }
  to   { opacity: 1; transform: translateY(0); }
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
  background: linear-gradient(180deg, var(--sidebar-from) 0%, var(--sidebar-to) 100%);
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
  gap: var(--space-md);
  padding: 0 18px;
  height: var(--topbar-h);
  border-bottom: 1px solid rgba(255,255,255,0.08);
}

.brand-icon {
  width: 36px;
  height: 36px;
  border-radius: var(--radius-sm);
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--rose-light);
  font-size: 14px;
  flex-shrink: 0;
}

.brand-title {
  display: block;
  color: rgba(255,255,255,0.92);
  font-size: 16px;
  font-weight: 600;
  font-family: var(--font-display);
  line-height: 1.2;
  letter-spacing: -0.01em;
}

.brand-sub {
  display: block;
  color: rgba(255,255,255,0.3);
  font-size: 11px;
  margin-top: 1px;
}

.sidebar__nav {
  flex: 1;
  padding: 14px 14px;
  overflow-y: auto;
}

.nav-label {
  display: block;
  font-size: 11px;
  font-weight: 600;
  letter-spacing: 0.06em;
  color: rgba(255,255,255,0.22);
  padding: 10px 10px 6px;
  text-transform: none;
}

.nav-link {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 14px;
  border-radius: 8px;
  font-size: 13.5px;
  font-weight: 500;
  color: rgba(255,255,255,0.5);
  text-decoration: none;
  transition: all 0.2s ease;
  margin-bottom: 1px;
}

.nav-link:hover {
  background: rgba(196,149,106,0.18);
  color: var(--rose-light);
}

.nav-link--active {
  background: rgba(196,149,106,0.22);
  color: rgba(255,255,255,0.95);
  font-weight: 600;
}

.nav-icon {
  width: 16px;
  text-align: center;
  font-size: 13px;
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

/* === DROPDOWN MENU === */
.user-dropdown-container { position: relative; }
.user-dropdown { position: absolute; right: 0; top: 120%; background: var(--surface); border: 1px solid var(--border); border-radius: 12px; min-width: 200px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); z-index: 100; overflow: hidden; animation: slideDown 0.2s ease; }
@keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
.user-dropdown-header { padding: 12px 16px; background: var(--rose-pale); }
.user-dropdown-name { font-weight: 600; font-size: 14px; color: var(--text); margin: 0; }
.user-dropdown-email { font-size: 12px; color: var(--text-muted); margin: 0; }
.user-dropdown-divider { height: 1px; background: var(--border); }
.user-dropdown-item { display: flex; align-items: center; gap: 10px; width: 100%; text-align: left; padding: 12px 16px; font-size: 13.5px; color: var(--text); border: none; background: transparent; cursor: pointer; transition: background 0.15s ease; text-decoration: none; }
.user-dropdown-item:hover { background: var(--bg); color: var(--rose); }
.user-dropdown-item i { width: 16px; text-align: center; color: var(--text-dim); }
.user-dropdown-item:hover i { color: var(--rose); }
.user-dropdown-item.text-danger { color: var(--danger-text); }
.user-dropdown-item.text-danger:hover { background: var(--danger-bg); }
.user-dropdown-item.text-danger i { color: var(--danger-text); }
.topbar {
  height: var(--topbar-h);
  background: var(--surface);
  border-bottom: 1px solid var(--border);
  display: flex;
  align-items: center;
  padding: 0 var(--space-xl);
  gap: var(--space-md);
  flex-shrink: 0;
}

.topbar__menu-btn {
  background: none;
  border: none;
  cursor: pointer;
  color: var(--text-dim);
  font-size: 15px;
  padding: 4px;
  transition: color 0.15s;
}
.topbar__menu-btn:hover { color: var(--text); }

.topbar__breadcrumb {
  display: flex;
  align-items: center;
  gap: var(--space-sm);
  font-size: 13.5px;
  min-width: 0;
}

.breadcrumb-home {
  color: var(--text-dim);
  text-decoration: none;
  font-size: 12px;
  transition: color 0.15s;
  flex-shrink: 0;
}
.breadcrumb-home:hover { color: var(--rose); }

.breadcrumb-sep {
  color: var(--border);
  font-size: 9px;
  flex-shrink: 0;
}

.breadcrumb-page {
  color: var(--text);
  font-weight: 600;
  font-size: 13.5px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.topbar__greeting {
  margin-left: auto;
  font-size: 13px;
  font-weight: 500;
  color: var(--rose);
  white-space: nowrap;
}
@media (max-width: 768px) { .topbar__greeting { display: none; } }

.topbar__right {
  display: flex;
  align-items: center;
  gap: var(--space-md);
}

.topbar__date {
  font-size: 12px;
  color: var(--text-muted);
}

@media (max-width: 640px) { .topbar__date { display: none; } }

.topbar__avatar {
  width: 32px;
  height: 32px;
  background: var(--rose-pale);
  border-radius: var(--radius-sm);
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--rose);
  font-size: 12px;
}

.lg-hidden { display: none; }
@media (max-width: 1023px) { .lg-hidden { display: flex; } }

/* === CONTENT === */
.content {
  flex: 1;
  overflow-y: auto;
  padding: var(--space-3xl) var(--space-3xl);
}
@media (max-width: 640px) {
  .content {
    padding: var(--space-xl) var(--space-xl);
  }
}

/* === SCROLLBAR === */
::-webkit-scrollbar { width: 6px; height: 6px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }
::-webkit-scrollbar-thumb:hover { background: var(--text-dim); }

/* === CARDS === */
.card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  box-shadow: var(--shadow-sm);
  transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
}
.card--hoverable:hover {
  border-color: var(--rose-light);
  box-shadow: var(--shadow-hover);
  transform: translateY(-2px);
}

/* === TABLE === */
.tbl { width: 100%; border-collapse: collapse; }
.tbl thead { position: sticky; top: 0; z-index: 2; }
.tbl th {
  padding: 10px 14px;
  text-align: left;
  font-size: 12px;
  font-weight: 600;
  color: var(--text-muted);
  background: var(--rose-pale);
  border-bottom: 1px solid var(--border);
  white-space: nowrap;
  text-transform: none;
  letter-spacing: 0.02em;
}
.tbl td {
  padding: 11px 14px;
  font-size: 13.5px;
  color: var(--text);
  border-bottom: 1px solid #f5ece8;
  vertical-align: middle;
  background: var(--surface);
}
.tbl tbody tr:last-child td { border-bottom: none; }
.tbl tbody tr:hover td { background: #fdf9f6; }

/* === BADGE / CHIP === */
.chip {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 3px 10px;
  border-radius: 20px;
  font-size: 11.5px;
  font-weight: 600;
  letter-spacing: 0.01em;
}
.chip--ok      { background: var(--ok-bg); color: var(--ok-text); border: 1px solid var(--ok-border); }
.chip--warn    { background: var(--warn-bg); color: var(--warn-text); border: 1px solid var(--warn-border); }
.chip--danger  { background: var(--danger-bg); color: var(--danger-text); border: 1px solid var(--danger-border); }
.chip--neutral { background: var(--rose-pale); color: var(--rose); }
.chip--outline { background: transparent; border: 1px solid var(--border); color: var(--text-muted); }
.chip--pink    { background: var(--stat-pink); color: #c4956a; }
.chip--soft    { background: #faf6f3; color: var(--text-muted); border: 1px solid var(--border); }

/* === BUTTONS === */
.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  padding: 8px 16px;
  height: 38px;
  border-radius: var(--radius-sm);
  font-size: 13.5px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  border: none;
  text-decoration: none;
  white-space: nowrap;
  outline: none;
}
.btn:focus-visible {
  outline: 2px solid var(--rose);
  outline-offset: 2px;
}
.btn:disabled { opacity: 0.55; cursor: not-allowed; }

.btn--primary {
  background: var(--rose);
  color: #fff;
}
.btn--primary:hover:not(:disabled) {
  background: #b8845c;
  transform: scale(1.02);
}

.btn--dark {
  background: var(--rose);
  color: #fff;
}
.btn--dark:hover:not(:disabled) {
  background: #b8845c;
  transform: scale(1.02);
}

.btn--outline {
  background: var(--surface);
  color: var(--text);
  border: 1px solid var(--border);
  height: 38px;
}
.btn--outline:hover:not(:disabled) {
  background: var(--rose-pale);
  border-color: var(--rose-light);
  transform: scale(1.02);
}

.btn--ghost {
  background: transparent;
  color: var(--text-muted);
}
.btn--ghost:hover:not(:disabled) {
  background: var(--rose-pale);
  color: var(--rose);
}

.btn--danger-ghost {
  background: transparent;
  color: var(--text-dim);
}
.btn--danger-ghost:hover:not(:disabled) {
  background: var(--danger-bg);
  color: var(--danger-text);
}

.btn--sm { padding: 5px 10px; font-size: 12px; gap: 4px; }
.btn--icon { padding: 6px; width: 32px; height: 32px; justify-content: center; }

/* === FORM INPUTS === */
.form-input {
  width: 100%;
  border: 1px solid var(--border);
  border-radius: var(--radius-sm);
  padding: 8px 11px;
  height: 38px;
  font-size: 14px;
  font-family: var(--font);
  color: var(--text);
  background: var(--surface);
  outline: none;
  transition: border-color 0.15s, box-shadow 0.15s;
  appearance: none;
}
.form-input:focus {
  border-color: var(--rose);
  box-shadow: 0 0 0 3px rgba(196,149,106,0.12);
}
.form-input::placeholder { color: var(--text-dim); font-size: 14px; }
select.form-input {
  cursor: pointer;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%238b6f6a' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 12px center;
  background-size: 14px;
  padding-right: 36px;
}
textarea.form-input { resize: none; height: auto; min-height: 80px; }
.search-input { padding-left: 30px; height: 38px; }

.form-label {
  display: block;
  font-size: 12px;
  font-weight: 600;
  color: var(--text-muted);
  margin-bottom: 5px;
  letter-spacing: 0.01em;
}

.form-error { font-size: 11.5px; color: var(--danger-text); margin-top: 4px; }

/* === PROGRESS === */
.prog-track { height: 6px; background: var(--rose-pale); border-radius: 99px; overflow: hidden; }
.prog-fill {
  height: 100%;
  border-radius: 99px;
  background: linear-gradient(90deg, var(--rose) 0%, #d4a878 100%);
  transition: width 0.6s ease-out;
}

/* === MODAL === */
.modal-backdrop {
  position: fixed;
  inset: 0;
  z-index: 100;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: var(--space-lg);
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
  animation: modalEnter 0.2s ease-out;
}
@keyframes modalEnter {
  from { opacity: 0; transform: scale(0.95); }
  to   { opacity: 1; transform: scale(1); }
}
.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: var(--space-lg) var(--space-xl);
  border-bottom: 1px solid var(--border);
  position: sticky;
  top: 0;
  background: var(--surface);
  z-index: 1;
  border-radius: var(--radius-lg) var(--radius-lg) 0 0;
}
.modal-title { font-size: 15px; font-weight: 700; color: var(--text); font-family: var(--font-display); }
.modal-body { padding: var(--space-2xl); }
.modal-footer {
  display: flex;
  gap: var(--space-sm);
  padding: var(--space-lg) var(--space-xl);
}

/* === TOAST === */
.toast-container {
  position: fixed;
  top: var(--space-xl);
  right: var(--space-xl);
  z-index: 9999;
  display: flex;
  flex-direction: column;
  gap: var(--space-sm);
  pointer-events: none;
  width: min(360px, calc(100vw - 32px));
}
.toast {
  pointer-events: all;
  display: flex;
  align-items: center;
  gap: 10px;
  width: 100%;
  padding: 11px 14px;
  border-radius: var(--radius);
  border: 1px solid var(--border);
  background: var(--surface);
  color: var(--text);
  font-size: 13.5px;
  font-weight: 500;
  line-height: 1.45;
  box-shadow: var(--shadow-lg);
  opacity: 0;
  transform: translateX(18px);
  transition: opacity 0.24s ease, transform 0.24s ease;
}
.toast--show { opacity: 1; transform: translateX(0); }
.toast__icon {
  width: 24px;
  height: 24px;
  border-radius: 999px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  font-size: 12px;
}
.toast__message { flex: 1; }
.toast--success { border-color: var(--ok-border); background: var(--ok-bg); color: var(--ok-text); }
.toast--success .toast__icon { background: var(--surface); color: var(--ok-text); }
.toast--error { border-color: var(--danger-border); background: var(--danger-bg); color: var(--danger-text); }
.toast--error .toast__icon { background: var(--surface); color: var(--danger-text); }
.toast--warning { border-color: var(--warn-border); background: var(--warn-bg); color: var(--warn-text); }
.toast--warning .toast__icon { background: var(--surface); color: var(--warn-text); }
.toast--info { border-color: var(--border); background: var(--rose-pale); color: var(--text-muted); }
.toast--info .toast__icon { background: var(--surface); color: var(--rose); }
@media (max-width: 640px) {
  .toast-container { top: var(--space-lg); right: var(--space-lg); left: var(--space-lg); width: auto; }
}

/* === CHECKBOX === */
input[type=checkbox] {
  accent-color: var(--rose);
  cursor: pointer;
}

/* === INERTIA PROGRESS === */
#nprogress .bar { background: var(--rose) !important; height: 2px !important; }

/* === PAGE HEADER === */
.page-header {
  margin-bottom: var(--space-2xl);
}
.page-title {
  font-size: 24px;
  font-weight: 700;
  color: var(--text);
  letter-spacing: -0.02em;
  font-family: var(--font-display);
}
.page-sub {
  font-size: 14px;
  color: var(--text-muted);
  margin-top: 4px;
}

/* === EMPTY STATE === */
.empty-state {
  text-align: center;
  padding: 56px var(--space-2xl);
}
.empty-state__icon {
  font-size: 40px;
  color: var(--text-dim);
  margin-bottom: var(--space-lg);
  opacity: 0.5;
}
.empty-state__text {
  font-size: 14px;
  color: var(--text-muted);
  margin-bottom: var(--space-xl);
}

/* === SECTION HEADER === */
.section-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: var(--space-lg) var(--space-xl);
  border-bottom: 1px solid var(--border);
}
.section-title {
  font-size: 15px;
  font-weight: 600;
  color: var(--text);
  letter-spacing: -0.01em;
}

/* === UTILITY === */
.action-icon { font-size: 13px; }
.action-icon--copy { color: var(--text-dim); }
.action-icon--edit { color: var(--text-dim); }
.action-icon--delete { color: var(--text-dim); }
.mt-2 { margin-top: var(--space-sm); }
.mt-3 { margin-top: var(--space-md); }
.mt-4 { margin-top: var(--space-lg); }
.text-center { text-align: center; }
.toolbar__select { max-width: 180px; height: 38px; }
</style>