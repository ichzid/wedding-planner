<template>
  <Head :title="pageTitle">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  </Head>
  <div class="app-shell">
    <a href="#main-content" class="skip-link">Lewati ke konten utama</a>
    <!-- Sidebar -->
    <aside id="primary-navigation" class="sidebar" :class="{ 'sidebar--open': sidebarOpen }">
      <!-- Brand -->
      <div class="sidebar__brand">
        <div class="brand-icon" aria-hidden="true">
          <HeartHandshake />
        </div>
        <div class="brand-text">
          <span class="brand-title">Ruang Pasangan</span>
          <span class="brand-sub">Rencanakan Hari Bahagia</span>
        </div>
      </div>

      <!-- Nav -->
      <nav class="sidebar__nav" aria-label="Navigasi utama">
        <div
          v-for="group in navigationGroups"
          :key="group.label"
          class="nav-group"
          :class="{ 'nav-group--account': group.account }"
        >
          <span class="nav-label">{{ group.label }}</span>
          <Link
            v-for="item in group.items"
            :key="item.route"
            :href="route(item.route)"
            class="nav-link"
            :class="{ 'nav-link--active': isRoute(item.route) }"
          >
            <component :is="item.icon" class="nav-icon" aria-hidden="true" />
            <span>{{ item.label }}</span>
          </Link>
          <Link v-if="group.account" :href="route('logout')" method="post" as="button" class="nav-link nav-link--logout">
            <LogOut class="nav-icon" aria-hidden="true" />
            <span>Keluar</span>
          </Link>
        </div>
      </nav>
    </aside>

    <!-- Main area -->
    <div class="main-area">
      <!-- Content -->
      <main id="main-content" class="content" tabindex="-1">
        <div class="content__inner">
          <slot />
        </div>
      </main>
    </div>

    <div v-if="moreMenuOpen" class="mobile-sheet-backdrop" @click="moreMenuOpen = false"></div>
    <section v-if="moreMenuOpen" class="mobile-sheet" role="dialog" aria-modal="true" aria-labelledby="mobile-more-title">
      <div class="mobile-sheet__handle" aria-hidden="true"></div>
      <div class="mobile-sheet__header">
        <h2 id="mobile-more-title">Menu lainnya</h2>
        <button type="button" class="mobile-sheet__close" aria-label="Tutup menu lainnya" @click="moreMenuOpen = false">
          <X aria-hidden="true" />
        </button>
      </div>
      <nav class="mobile-sheet__menu" aria-label="Menu lainnya">
        <Link v-for="item in mobileMoreItems" :key="item.route" :href="route(item.route)" class="mobile-sheet__item" :class="{ 'mobile-sheet__item--active': isRoute(item.route) }" @click="moreMenuOpen = false">
          <span class="mobile-sheet__icon"><component :is="item.icon" aria-hidden="true" /></span>
          <span>{{ item.label }}</span>
        </Link>
        <Link :href="route('logout')" method="post" as="button" class="mobile-sheet__item mobile-sheet__item--logout">
          <span class="mobile-sheet__icon"><LogOut aria-hidden="true" /></span>
          <span>Keluar</span>
        </Link>
      </nav>
    </section>

    <nav class="mobile-bottom-nav" aria-label="Navigasi utama mobile">
      <Link v-for="item in mobilePrimaryItems" :key="item.route" :href="route(item.route)" class="mobile-bottom-nav__item" :class="{ 'mobile-bottom-nav__item--active': isRoute(item.route) }">
        <component :is="item.icon" class="mobile-bottom-nav__icon" aria-hidden="true" />
        <span>{{ item.mobileLabel }}</span>
      </Link>
      <button type="button" class="mobile-bottom-nav__item" :class="{ 'mobile-bottom-nav__item--active': moreMenuOpen || isMoreRoute }" :aria-expanded="moreMenuOpen" aria-controls="mobile-more-title" @click="moreMenuOpen = !moreMenuOpen">
        <Ellipsis class="mobile-bottom-nav__icon" aria-hidden="true" />
        <span>Lainnya</span>
      </button>
    </nav>

    <!-- Toast container -->
    <div id="toast-container" class="toast-container"></div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { HeartHandshake, LayoutDashboard, ListChecks, WalletCards, Gift, FileCheck2, UsersRound, UserCog, LogOut, Ellipsis, X } from '@lucide/vue';
import { route } from 'ziggy-js';

const moreMenuOpen = ref(false);

const closeMoreMenuOnEscape = (event) => {
  if (event.key === 'Escape') moreMenuOpen.value = false;
};

onMounted(() => window.addEventListener('keydown', closeMoreMenuOnEscape));
onBeforeUnmount(() => window.removeEventListener('keydown', closeMoreMenuOnEscape));

const navigationGroups = [
  {
    label: 'Perencanaan',
    items: [
      { route: 'dashboard', label: 'Ringkasan', icon: LayoutDashboard },
      { route: 'checklist.index', label: 'Daftar Persiapan', icon: ListChecks },
      { route: 'budget.index', label: 'Anggaran', icon: WalletCards },
      { route: 'seserahan.index', label: 'Seserahan', icon: Gift },
      { route: 'dokumen-kua.index', label: 'Dokumen KUA', icon: FileCheck2 },
      { route: 'tamu.index', label: 'Daftar Undangan', icon: UsersRound },
    ],
  },
  {
    label: 'Akun',
    account: true,
    items: [
      { route: 'profile.edit', label: 'Pengaturan', icon: UserCog },
    ],
  },
];

const mobilePrimaryItems = [
  { route: 'dashboard', mobileLabel: 'Ringkasan', icon: LayoutDashboard },
  { route: 'checklist.index', mobileLabel: 'Persiapan', icon: ListChecks },
  { route: 'budget.index', mobileLabel: 'Anggaran', icon: WalletCards },
  { route: 'tamu.index', mobileLabel: 'Undangan', icon: UsersRound },
];

const mobileMoreItems = [
  { route: 'seserahan.index', label: 'Seserahan', icon: Gift },
  { route: 'dokumen-kua.index', label: 'Dokumen KUA', icon: FileCheck2 },
  { route: 'profile.edit', label: 'Pengaturan', icon: UserCog },
];

const isMoreRoute = computed(() => mobileMoreItems.some((item) => isRoute(item.route)));

const pageTitle = computed(() => {
  const currentRoute = route().current();
  return navigationGroups.flatMap((group) => group.items)
    .find((item) => item.route === currentRoute)?.label ?? null;
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
  background: var(--bg);
  border-right: 1px solid var(--border);
  display: flex;
  flex-direction: column;
  flex-shrink: 0;
  transition: transform 0.25s ease;
  z-index: 50;
}

@media (max-width: 1023px) {
  .sidebar { display: none; }
}

.sidebar__brand {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 18px 18px 16px;
  min-height: 78px;
}

.brand-icon {
  width: 40px;
  height: 40px;
  border: 1px solid var(--rose-light);
  border-radius: 12px;
  background: var(--accent-soft);
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--accent-hover);
  flex-shrink: 0;
}

.brand-icon svg {
  width: 21px;
  height: 21px;
  stroke-width: 1.8;
}

.brand-text {
  min-width: 0;
}

.brand-title {
  display: block;
  color: var(--text);
  font-size: 15px;
  font-weight: 700;
  font-family: var(--font);
  line-height: 1.2;
  letter-spacing: -0.02em;
  white-space: nowrap;
}

.brand-sub {
  display: block;
  color: #66736a;
  font-size: 10.5px;
  line-height: 1.35;
  margin-top: 3px;
  white-space: nowrap;
}

.sidebar__nav {
  flex: 1;
  display: flex;
  flex-direction: column;
  padding: 16px 12px 14px;
  overflow-y: auto;
}

.nav-group {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.nav-group--account {
  margin-top: auto;
  padding-top: 24px;
}

.nav-label {
  display: block;
  font-size: 11px;
  font-weight: 650;
  letter-spacing: 0.06em;
  color: #7b887f;
  padding: 6px 10px 8px;
  text-transform: uppercase;
}

.nav-link {
  position: relative;
  display: flex;
  align-items: center;
  gap: 10px;
  min-height: 42px;
  padding: 10px 14px;
  border: 0;
  border-radius: 8px 0 0 8px !important;
  font-size: 13.5px;
  font-weight: 500;
  color: #526159;
  text-decoration: none;
  transition: background-color 0.16s ease, color 0.16s ease;
  margin: 0;
  cursor: pointer;
}

.nav-link:hover {
  background: transparent;
  color: var(--accent-hover);
}

.nav-link--active:hover {
  background: var(--surface-soft);
  color: var(--accent-hover);
}

.nav-link--active {
  background: var(--surface-soft);
  color: var(--text-muted);
  font-weight: 600;
}

.nav-link--active::before {
  content: '';
  position: absolute;
  top: 0;
  bottom: 0;
  left: 0;
  width: 3px;
  background: var(--accent);
}

.nav-link--logout {
  background: transparent;
  box-shadow: none;
}

.nav-link--logout:hover {
  background: transparent;
  color: var(--danger-text);
}

.nav-icon {
  width: 17px;
  height: 17px;
  flex-shrink: 0;
  stroke-width: 1.8;
  opacity: 0.9;
}

/* === MAIN AREA === */
.main-area {
  flex: 1;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  min-width: 0;
}

/* === MOBILE NAVIGATION === */
.mobile-bottom-nav,
.mobile-sheet,
.mobile-sheet-backdrop { display: none; }

@media (max-width: 1023px) {
  .mobile-bottom-nav {
    position: fixed;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 70;
    display: grid;
    grid-template-columns: repeat(5, minmax(0, 1fr));
    padding: 8px 6px calc(8px + env(safe-area-inset-bottom));
    background: rgba(255, 255, 255, 0.96);
    border-top: 1px solid var(--border);
    box-shadow: 0 -8px 24px rgba(48, 63, 53, 0.08);
    backdrop-filter: blur(14px);
  }

  .mobile-bottom-nav__item {
    display: flex;
    min-width: 0;
    min-height: 50px;
    padding: 5px 2px;
    border: 0;
    border-radius: 10px;
    background: transparent;
    color: var(--text-dim);
    align-items: center;
    justify-content: center;
    flex-direction: column;
    gap: 4px;
    font-family: var(--font);
    font-size: 10px;
    font-weight: 600;
    text-decoration: none;
  }

  .mobile-bottom-nav__item--active {
    color: var(--accent-hover);
    background: var(--accent-soft);
  }

  .mobile-bottom-nav__icon {
    width: 21px;
    height: 21px;
    stroke-width: 1.9;
  }

  .mobile-sheet-backdrop {
    position: fixed;
    inset: 0;
    z-index: 75;
    display: block;
    background: rgba(30, 39, 31, 0.42);
    backdrop-filter: blur(2px);
  }

  .mobile-sheet {
    position: fixed;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 80;
    display: block;
    padding: 8px 18px calc(22px + env(safe-area-inset-bottom));
    background: var(--surface);
    border-radius: 22px 22px 0 0;
    box-shadow: 0 -16px 48px rgba(38, 51, 36, 0.2);
    animation: mobileSheetEnter 0.2s ease-out;
  }

  .mobile-sheet__handle {
    width: 38px;
    height: 4px;
    margin: 0 auto 12px;
    border-radius: 99px;
    background: var(--border-strong);
  }

  .mobile-sheet__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 10px;
  }

  .mobile-sheet__header h2 { font-size: 17px; font-weight: 700; }
  .mobile-sheet__close {
    display: grid;
    width: 36px;
    height: 36px;
    border: 0;
    border-radius: 10px;
    background: var(--surface-soft);
    color: var(--text-muted);
    place-items: center;
  }
  .mobile-sheet__close svg { width: 19px; height: 19px; }
  .mobile-sheet__menu { display: grid; gap: 6px; }
  .mobile-sheet__item {
    display: flex;
    width: 100%;
    min-height: 52px;
    padding: 8px 10px;
    border: 0;
    border-radius: 12px;
    background: transparent;
    color: var(--text);
    align-items: center;
    gap: 12px;
    font-family: var(--font);
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
  }
  .mobile-sheet__item--active { background: var(--accent-soft); color: var(--accent-hover); }
  .mobile-sheet__item--logout { color: var(--danger-text); }
  .mobile-sheet__icon {
    display: grid;
    width: 36px;
    height: 36px;
    border-radius: 10px;
    background: var(--surface-soft);
    place-items: center;
  }
  .mobile-sheet__icon svg { width: 19px; height: 19px; stroke-width: 1.8; }
}

@keyframes mobileSheetEnter {
  from { transform: translateY(100%); }
  to { transform: translateY(0); }
}

/* === CONTENT === */
.content {
  flex: 1;
  overflow-y: auto;
  padding: 0;
}

@media (max-width: 1023px) {
  .content {
    padding-bottom: calc(76px + env(safe-area-inset-bottom));
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
.tbl tbody tr:hover > td { background: #fafcf9; }

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
  top: 20px;
  right: 20px;
  z-index: 9999;
  display: flex;
  flex-direction: column;
  gap: 10px;
  pointer-events: none;
}

.toast {
  --toast-color: var(--accent);
  --toast-soft: var(--accent-soft);
  position: relative;
  display: grid;
  grid-template-columns: 36px minmax(0, 1fr) 32px;
  align-items: start;
  gap: 11px;
  padding: 14px 12px 14px 14px;
  overflow: hidden;
  background: rgba(255, 255, 255, 0.98);
  border: 1px solid var(--border);
  border-radius: 14px;
  box-shadow: 0 14px 36px rgba(48, 63, 53, 0.14);
  width: min(390px, calc(100vw - 40px));
  transform: translateY(-12px) scale(.98);
  opacity: 0;
  transition: transform .22s ease, opacity .22s ease;
  pointer-events: auto;
}
.toast--show { transform: translateY(0) scale(1); opacity: 1; }
.toast__icon {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  background: var(--toast-soft);
  color: var(--toast-color);
  font-size: 13px;
}
.toast__content { display: grid; gap: 3px; min-width: 0; padding-top: 1px; }
.toast__title { color: var(--text); font-size: 13px; font-weight: 700; line-height: 1.25; }
.toast__message { color: var(--text-muted); font-size: 12.5px; font-weight: 400; line-height: 1.45; overflow-wrap: anywhere; }
.toast__close { width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border: 0; border-radius: 8px; background: transparent; color: var(--text-dim); cursor: pointer; }
.toast__close:hover { background: var(--surface-soft); color: var(--text); }
.toast__progress { position: absolute; left: 0; right: 0; bottom: 0; height: 2px; background: var(--toast-color); transform-origin: left; animation: toastProgress 4.2s linear forwards; opacity: .65; }
.toast--success { --toast-color: var(--ok-text); --toast-soft: var(--ok-bg); }
.toast--error { --toast-color: var(--danger-text); --toast-soft: var(--danger-bg); }
.toast--warning { --toast-color: var(--warn-text); --toast-soft: var(--warn-bg); }
.toast--info { --toast-color: var(--accent); --toast-soft: var(--accent-soft); }
@keyframes toastProgress { to { transform: scaleX(0); } }

@media (max-width: 640px) {
  .toast-container { left: 12px; right: 12px; top: 12px; }
  .toast { width: 100%; grid-template-columns: 34px minmax(0, 1fr) 32px; padding: 12px 10px 12px 12px; }
  .toast__icon { width: 34px; height: 34px; }
}

/* === CHECKBOX === */
input[type=checkbox] {
  accent-color: var(--rose);
  cursor: pointer;
}

/* === INERTIA PROGRESS === */
#nprogress .bar { background: var(--accent) !important; height: 2px !important; }

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

/* === GLOBAL TOOLTIP === */
[data-tooltip] {
  position: relative;
}

[data-tooltip]::before,
[data-tooltip]::after {
  position: absolute;
  left: 50%;
  z-index: 1000;
  pointer-events: none;
  opacity: 0;
  visibility: hidden;
  transition: opacity 0.15s ease, transform 0.15s ease, visibility 0.15s ease;
}

[data-tooltip]::before {
  content: attr(data-tooltip);
  bottom: calc(100% + 8px);
  transform: translate(-50%, 4px);
  padding: 5px 8px;
  border-radius: 6px;
  background: var(--text);
  color: var(--bg);
  font-size: 11px;
  font-weight: 600;
  line-height: 1.2;
  white-space: nowrap;
  box-shadow: 0 4px 12px rgba(20, 31, 24, 0.16);
}

[data-tooltip]::after {
  content: '';
  bottom: calc(100% + 3px);
  transform: translate(-50%, 4px);
  border: 5px solid transparent;
  border-top-color: var(--text);
  border-bottom: 0;
}

[data-tooltip]:hover::before,
[data-tooltip]:hover::after,
[data-tooltip]:focus-visible::before,
[data-tooltip]:focus-visible::after {
  opacity: 1;
  visibility: visible;
  transform: translate(-50%, 0);
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