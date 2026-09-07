<template>
  <Head :title="pageTitle">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  </Head>
  <div class="app-shell">
    <a href="#main-content" class="skip-link">Lewati ke konten utama</a>
    <header class="top-nav-wrap">
      <div id="primary-navigation" class="top-nav">
        <Link :href="route('dashboard')" class="top-nav__brand">
          <img src="/ruang-pasangan.svg" alt="" />
          <span class="brand-text">
            <span class="brand-title">Ruang Pasangan</span>
            <span class="brand-sub">Wedding Planner</span>
          </span>
        </Link>
        <nav class="top-nav__links" aria-label="Navigasi utama">
          <Link
            v-for="item in navigationGroups[0].items"
            :key="item.route"
            :href="route(item.route)"
            class="nav-link"
            :class="{ 'nav-link--active': isRoute(item.route) }"
          >
            <span>{{ item.label }}</span>
          </Link>
        </nav>
        <div ref="profileMenu" class="profile-menu">
          <button type="button" class="profile-trigger" :aria-expanded="profileMenuOpen" aria-controls="profile-dropdown" :aria-label="`Buka menu akun ${user.name}`" @click="profileMenuOpen = !profileMenuOpen">
            <img v-if="userAvatar && !avatarFailed" :src="userAvatar" :alt="`Foto profil ${user.name}`" class="profile-avatar" @error="avatarFailed = true" />
            <span v-else class="profile-avatar profile-avatar--fallback" aria-hidden="true">{{ userInitials }}</span>
          </button>
          <div v-if="profileMenuOpen" id="profile-dropdown" class="profile-dropdown">
            <div class="profile-dropdown__identity">
              <img v-if="userAvatar && !avatarFailed" :src="userAvatar" :alt="`Foto profil ${user.name}`" class="profile-avatar profile-avatar--large" @error="avatarFailed = true" />
              <span v-else class="profile-avatar profile-avatar--fallback profile-avatar--large" aria-hidden="true">{{ userInitials }}</span>
              <div><strong>{{ user.name }}</strong><small>{{ user.email }}</small></div>
            </div>
            <Link :href="route('profile.edit')" class="profile-dropdown__item" @click="profileMenuOpen = false"><Settings aria-hidden="true" /><span>Pengaturan</span></Link>
            <Link :href="route('subscription.index')" class="profile-dropdown__item" @click="profileMenuOpen = false"><CreditCard aria-hidden="true" /><span>Paket Langganan</span></Link>
            <Link :href="route('help.index')" class="profile-dropdown__item" @click="profileMenuOpen = false"><CircleHelp aria-hidden="true" /><span>Pusat Bantuan</span></Link>
            <Link :href="route('support.index')" class="profile-dropdown__item" @click="profileMenuOpen = false"><Headphones aria-hidden="true" /><span>Hubungi Dukungan</span></Link>
            <Link :href="route('logout')" method="post" as="button" class="profile-dropdown__item profile-dropdown__item--logout"><LogOut aria-hidden="true" /><span>Keluar</span></Link>
          </div>
        </div>
      </div>
    </header>

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
import { Head, Link, usePage } from '@inertiajs/vue3';
import { LayoutDashboard, ListChecks, WalletCards, Gift, FileCheck2, UsersRound, UserCog, Settings, CreditCard, CircleHelp, Headphones, LogOut, Ellipsis, X } from '@lucide/vue';
import { route } from 'ziggy-js';

const page = usePage();
const moreMenuOpen = ref(false);
const profileMenuOpen = ref(false);
const profileMenu = ref(null);
const avatarFailed = ref(false);
const user = computed(() => page.props.auth.user);
const userAvatar = computed(() => user.value?.avatar || null);
const userInitials = computed(() => {
  const nameParts = user.value?.name?.trim().split(/\s+/).filter(Boolean) || [];

  if (!nameParts.length) return 'U';

  return [nameParts[0], nameParts.at(-1)]
    .slice(0, nameParts.length > 1 ? 2 : 1)
    .map((part) => part.charAt(0))
    .join('')
    .toUpperCase();
});

const closeMenusOnEscape = (event) => {
  if (event.key === 'Escape') {
    moreMenuOpen.value = false;
    profileMenuOpen.value = false;
  }
};

const closeProfileMenuOutside = (event) => {
  if (!profileMenu.value?.contains(event.target)) profileMenuOpen.value = false;
};

onMounted(() => {
  window.addEventListener('keydown', closeMenusOnEscape);
  document.addEventListener('click', closeProfileMenuOutside);
});
onBeforeUnmount(() => {
  window.removeEventListener('keydown', closeMenusOnEscape);
  document.removeEventListener('click', closeProfileMenuOutside);
});

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
      { route: 'subscription.index', label: 'Paket Langganan', icon: CreditCard },
      { route: 'help.index', label: 'Pusat Bantuan', icon: CircleHelp },
      { route: 'support.index', label: 'Hubungi Dukungan', icon: Headphones },
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
  { route: 'subscription.index', label: 'Paket Langganan', icon: CreditCard },
  { route: 'help.index', label: 'Pusat Bantuan', icon: CircleHelp },
  { route: 'support.index', label: 'Hubungi Dukungan', icon: Headphones },
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

</script>

<style>
/* === GOOGLE FONTS === */
@import url('https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,800&family=Inter:wght@400;500;600;700;800&family=Roboto+Mono:wght@400&display=swap');

/* === GLOBAL RESET & BASE === */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }


html, body {
  height: 100%;
  margin: 0;
  padding: 0;
  background-color: #fcfaf5;
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
  from { opacity: 0; }
  to   { opacity: 1; }
}

/* === APP SHELL === */
.app-shell {
  display: flex;
  min-height: 100vh;
  overflow: hidden;
  flex-direction: column;
  background: #fcfaf5;
  color: #1a3300;
}

.top-nav-wrap { position: fixed; z-index: 60; top: 0; right: 0; left: 0; padding: 16px 20px 0; background: transparent; pointer-events: none; }
.top-nav {
  display: grid;
  width: min(1200px, calc(100% - 48px));
  max-width: 1200px;
  height: 64px;
  margin: 0 auto;
  padding: 8px 10px 8px 12px;
  grid-template-columns: auto 1fr auto;
  align-items: center;
  gap: 28px;
  border: 1px solid #1a3300;
  border-radius: 16px;
  background: rgba(252,250,245,.96);
  box-shadow: 0 18px 50px rgba(255,233,92,.22);
  pointer-events: auto;
}
.top-nav__brand { display: flex; align-items: center; gap: 10px; color: #1a3300; text-decoration: none; }
.top-nav__brand img { width: 40px; height: 40px; background: transparent; }
.brand-text { display: flex; flex-direction: column; line-height: 1.08; }
.brand-title { color: #1a3300; font-family: Inter, system-ui, sans-serif; font-size: 18px; font-weight: 800; white-space: nowrap; }
.brand-sub { margin-top: 4px; color: #1a3300; font-family: ui-monospace, monospace; font-size: 8px; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; white-space: nowrap; }
.top-nav__links { display: flex; align-items: center; justify-content: center; gap: 24px; }
.nav-link { display: inline-flex; padding: 0; border: 0; align-items: center; background: transparent; color: #1a3300; font: 500 14px Inter, system-ui, sans-serif; text-decoration: none; cursor: pointer; }
.nav-link:hover, .nav-link--active, .nav-link--active:hover { background: linear-gradient(transparent 56%, #ffe95c 56%); color: #1a3300; }
.profile-menu { position: relative; justify-self: end; }
.profile-trigger { display: grid; width: 44px; height: 44px; padding: 3px; border: 0; border-radius: 50%; background: transparent; color: #1a3300; place-items: center; cursor: pointer; transition: background .18s ease; }
.profile-trigger:hover, .profile-trigger[aria-expanded="true"] { background: #e7e3da; }
.profile-avatar { width: 36px; height: 36px; border: 1px solid #1a3300; border-radius: 50%; object-fit: cover; flex-shrink: 0; }
.profile-avatar--fallback { display: grid; background: #a8e5e5; color: #1a3300; font-size: 14px; font-weight: 800; place-items: center; }
.profile-trigger[aria-expanded="true"] .profile-avatar { box-shadow: 0 0 0 2px #fcfaf5, 0 0 0 4px #1a3300; }
.profile-dropdown { position: absolute; z-index: 100; top: calc(100% + 10px); right: 0; width: 270px; padding: 8px; border: 1px solid #1a3300; border-radius: 12px; background: #fcfaf5; box-shadow: 5px 5px 0 rgba(26,51,0,.14); animation: profileDropdownIn .16s ease-out; }
.profile-dropdown__identity { display: flex; padding: 10px; align-items: center; gap: 11px; border-bottom: 1px solid #b6b6b6; }
.profile-avatar--large { width: 40px; height: 40px; }
.profile-dropdown__identity div { min-width: 0; }
.profile-dropdown__identity strong, .profile-dropdown__identity small { display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.profile-dropdown__identity strong { font-size: 14px; }.profile-dropdown__identity small { margin-top: 3px; color: #526159; font-size: 11px; }
.profile-dropdown__item { display: flex; width: 100%; min-height: 42px; margin-top: 5px; padding: 9px 10px; border: 0; border-radius: 6px; align-items: center; gap: 10px; background: transparent; color: #1a3300; font-family: var(--font); font-size: 14px; font-weight: 600; text-decoration: none; cursor: pointer; }
.profile-dropdown__item:hover { background: #d5f5c2; }.profile-dropdown__item--logout:hover { background: #f6d0ff; }
.profile-dropdown__item svg { width: 17px; height: 17px; }
@keyframes profileDropdownIn { from { opacity: 0; transform: translateY(-6px); } to { opacity: 1; transform: translateY(0); } }

@media (max-width: 1023px) {
  .top-nav-wrap { display: none; }
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
    font-size: 11px;
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

  .mobile-sheet__header h2 { font-size: 18px; font-weight: 700; }
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
  padding: 96px 0 0;
}

@media (max-width: 1023px) {
  .content {
    padding: 0 0 calc(76px + env(safe-area-inset-bottom));
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
  font-size: 14px;
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
  font-size: 12px;
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
  font-size: 14px;
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

.form-error { font-size: 12px; color: var(--danger-text); margin-top: 4px; }

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
.modal-title { font-size: 16px; font-weight: 700; color: var(--text); font-family: var(--font); }
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
  font-size: 14px;
}
.toast__content { display: grid; gap: 3px; min-width: 0; padding-top: 1px; }
.toast__title { color: var(--text); font-size: 14px; font-weight: 700; line-height: 1.25; }
.toast__message { color: var(--text-muted); font-size: 12px; font-weight: 400; line-height: 1.45; overflow-wrap: anywhere; }
.toast__close { width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border: 0; border-radius: 8px; background: transparent; color: var(--text-dim); cursor: pointer; }
.toast__close:hover { background: var(--surface-soft); color: var(--text); }
.toast__progress { position: absolute; left: 0; right: 0; bottom: 0; height: 2px; background: var(--toast-color); transform-origin: left; animation: toastProgress 4.2s linear forwards; opacity: .65; }
.toast--success {
  --toast-color: #df37a7;
  --toast-soft: #fff1f8;
  border-color: rgba(223, 55, 167, .28);
  background: linear-gradient(135deg, #fff 0%, #fffafd 100%);
  box-shadow:
    0 16px 42px rgba(36, 0, 41, .12),
    0 3px 10px rgba(223, 55, 167, .08);
}
.toast--success .toast__icon {
  border: 1px solid rgba(223, 55, 167, .18);
  border-radius: 12px;
  box-shadow: inset 0 0 0 1px rgba(255, 255, 255, .8);
}
.toast--success .toast__title { color: #240029; }
.toast--success .toast__message { color: #6d526d; }
.toast--success .toast__close:hover {
  background: #fff1f8;
  color: #df37a7;
}
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
  font-size: 16px;
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
.action-icon { font-size: 14px; }
.action-icon--copy { color: var(--text-dim); }
.action-icon--edit { color: var(--text-dim); }
.action-icon--delete { color: var(--text-dim); }
.mt-2 { margin-top: var(--space-sm); }
.mt-3 { margin-top: var(--space-md); }
.mt-4 { margin-top: var(--space-lg); }
.text-center { text-align: center; }
.toolbar__select { max-width: 180px; height: 38px; }

@media (prefers-reduced-motion: reduce) {
  .content > * { animation: none; }
}

/* DESIGN.md - Sunset Marketplace shell */
html, body { background:#fff; color:#240029; }
.app-shell { background:#fff; color:#240029; }
.top-nav { border-color:rgba(36,0,41,.2); border-radius:14px; background:rgba(255,255,255,.94); box-shadow:0 0 0 1px rgba(41,0,41,.05),0 12px 32px rgba(32,0,36,.08); backdrop-filter:blur(16px); }
.top-nav__brand,.brand-title { color:#240029; }
.brand-title { font-family:'Bricolage Grotesque','Inter',sans-serif; font-weight:800; letter-spacing:-.025em; }
.brand-sub { color:#6d526d; font-family:'JetBrains Mono',ui-monospace,monospace; font-weight:400; }
.nav-link { position:relative; color:#6d526d; }
.nav-link::after { position:absolute; right:0; bottom:-8px; left:0; height:2px; border-radius:999px; background:#df37a7; content:""; opacity:0; transform:scaleX(.35); transition:opacity .16s ease,transform .16s ease; }
.nav-link:hover,.nav-link--active,.nav-link--active:hover { background:none; color:#240029; }
.nav-link:hover::after,.nav-link--active::after { opacity:1; transform:scaleX(1); }
.profile-trigger:hover,.profile-trigger[aria-expanded="true"] { background:#f8eaf4; }
.profile-avatar { border-color:#6d526d; }
.profile-avatar--fallback { background:#f8eaf4; color:#240029; }
.profile-trigger[aria-expanded="true"] .profile-avatar { box-shadow:0 0 0 2px #fff,0 0 0 4px #df37a7; }
.profile-dropdown { border-color:#d4ccd4; border-radius:14px; background:#fff; box-shadow:0 0 0 1px rgba(41,0,41,.06),0 16px 36px rgba(32,0,36,.12); }
.profile-dropdown__identity { border-color:#d4ccd4; }
.profile-dropdown__identity small { color:#6d526d; }
.profile-dropdown__item { color:#240029; }
.profile-dropdown__item:hover { background:#f8eaf4; color:#df37a7; }
.profile-dropdown__item--logout:hover { background:#fff0f0; color:#ef4444; }
.content { background:#fff; }
.mobile-bottom-nav { border-top-color:#d4ccd4; box-shadow:0 -8px 24px rgba(32,0,36,.08); }
.mobile-bottom-nav__item { color:#767676; }
.mobile-bottom-nav__item--active { background:#f8eaf4; color:#df37a7; }
.mobile-sheet-backdrop { background:rgba(36,0,41,.35); }
.mobile-sheet { border:1px solid #d4ccd4; border-bottom:0; background:#fff; box-shadow:0 -16px 48px rgba(32,0,36,.18); }
.mobile-sheet__handle { background:#bbadbb; }
.mobile-sheet__item { color:#240029; }
.mobile-sheet__item:hover,.mobile-sheet__item--active { background:#f8eaf4; color:#df37a7; }
</style>