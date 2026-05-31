<template>
  <AppLayout>
    <Head title="Dashboard" />

    <!-- Greeting Card -->
    <div class="greeting-card card">
      <div class="greeting-card__inner">
        <div class="greeting-card__text">
          <h1 class="greeting-card__title">{{ greetingText }}</h1>
          <p class="greeting-card__sub">Yuk, lanjutkan persiapan pernikahan impianmu ✨</p>
          <p v-if="countdownText" class="greeting-card__countdown">
            <i class="fa-solid fa-heart"></i> {{ countdownText }}
          </p>
        </div>
        <div class="quick-actions">
          <Link :href="route('checklist.index')" class="btn btn--primary btn--sm">
            <i class="fa-solid fa-list-check fa-xs"></i> Checklist
          </Link>
          <Link :href="route('budget.index')" class="btn btn--outline btn--sm">
            <i class="fa-solid fa-wallet fa-xs"></i> Budget
          </Link>
          <Link :href="route('tamu.index')" class="btn btn--outline btn--sm">
            <i class="fa-solid fa-users fa-xs"></i> Tamu
          </Link>
        </div>
      </div>
    </div>

    <!-- Stat Cards — row 1: original 4 -->
    <div class="stats-grid">
      <Link :href="route('checklist.index')" class="stat-card stat-card--link stat-card--pink">
        <div class="stat-card__header">
          <div class="stat-card__icon"><i class="fa-solid fa-list-check"></i></div>
          <i class="fa-solid fa-arrow-right stat-card__arrow"></i>
        </div>
        <p class="stat-card__value">{{ props.doneChecklist }}<span class="stat-card__total">/{{ props.totalChecklist }}</span></p>
        <p class="stat-card__label">Checklist Selesai</p>
        <div class="prog-track mt-3">
          <div class="prog-fill" :style="{ width: props.progressChecklist + '%' }"></div>
        </div>
      </Link>

      <Link :href="route('budget.index')" class="stat-card stat-card--link stat-card--peach">
        <div class="stat-card__header">
          <div class="stat-card__icon"><i class="fa-solid fa-wallet"></i></div>
          <i class="fa-solid fa-arrow-right stat-card__arrow"></i> 
        </div>
        <p class="stat-card__value">{{ props.progressBudget }}<span class="stat-card__total">%</span></p>
        <p class="stat-card__label">Budget Terbayar</p>
        <div class="prog-track mt-3">
          <div class="prog-fill" :style="{ width: props.progressBudget + '%' }"></div>
        </div>
      </Link>

      <Link :href="route('seserahan.index')" class="stat-card stat-card--link stat-card--lavender">
        <div class="stat-card__header">
          <div class="stat-card__icon"><i class="fa-solid fa-gift"></i></div>
          <i class="fa-solid fa-arrow-right stat-card__arrow"></i>
        </div>
        <p class="stat-card__value">{{ props.sudahBeli }}<span class="stat-card__total">/{{ props.totalSeserahan }}</span></p>
        <p class="stat-card__label">Seserahan Dibeli</p>
        <div class="prog-track mt-3">
          <div class="prog-fill" :style="{ width: (props.totalSeserahan > 0 ? Math.round(props.sudahBeli/props.totalSeserahan*100) : 0) + '%' }"></div>
        </div>
      </Link>

      <Link :href="route('dokumen-kua.index')" class="stat-card stat-card--link stat-card--sage">
        <div class="stat-card__header">
          <div class="stat-card__icon"><i class="fa-solid fa-file-contract"></i></div>
          <i class="fa-solid fa-arrow-right stat-card__arrow"></i>
        </div>
        <p class="stat-card__value">{{ props.doneKua }}<span class="stat-card__total">/{{ props.totalKua }}</span></p>
        <p class="stat-card__label">Dokumen KUA Selesai</p>
        <div class="prog-track mt-3">
          <div class="prog-fill" :style="{ width: (props.totalKua > 0 ? Math.round(props.doneKua/props.totalKua*100) : 0) + '%' }"></div>
        </div>
      </Link>
    </div>

    <!-- Info Pernikahan + Tamu row -->
    <div class="info-row">

      <!-- Info Pernikahan -->
      <div class="card info-card">
        <div class="section-header">
          <h2 class="section-title"><i class="fa-solid fa-heart" style="color:var(--rose);margin-right:8px;"></i>Info Pernikahan</h2>
        </div>
        <div class="info-list">
          <div class="info-item">
            <span class="info-label"><i class="fa-solid fa-ring"></i> Mempelai</span>
            <span class="info-val">{{ props.namaCpw || '-' }} & {{ props.namaCpp || '-' }}</span>
          </div>
          <div class="info-item">
            <span class="info-label"><i class="fa-solid fa-calendar-days"></i> Tanggal</span>
            <span class="info-val">{{ formattedDate }}</span>
          </div>
          <div class="info-item">
            <span class="info-label"><i class="fa-solid fa-mosque"></i> Akad</span>
            <span class="info-val">{{ props.lokasiAkad || '-' }}</span>
          </div>
          <div class="info-item">
            <span class="info-label"><i class="fa-solid fa-champagne-glasses"></i> Resepsi</span>
            <span class="info-val">{{ props.lokasiResepsi || '-' }}</span>
          </div>
        </div>
      </div>

      <!-- Ringkasan Tamu -->
      <div class="card tamu-card">
        <div class="section-header">
          <h2 class="section-title"><i class="fa-solid fa-users" style="color:var(--rose);margin-right:8px;"></i>Daftar Undangan</h2>
          <Link :href="route('tamu.index')" class="btn btn--ghost btn--sm">
            Lihat semua <i class="fa-solid fa-arrow-right fa-xs"></i>
          </Link>
        </div>
        <div class="tamu-stats">
          <div class="tamu-stat">
            <div class="tamu-stat__val">{{ props.totalTamu }}</div>
            <div class="tamu-stat__label">Total Tamu</div>
          </div>
          <div class="tamu-stat tamu-stat--cpw">
            <div class="tamu-stat__val">{{ props.tamuCpw }}</div>
            <div class="tamu-stat__label">Pihak Wanita</div>
          </div>
          <div class="tamu-stat tamu-stat--cpp">
            <div class="tamu-stat__val">{{ props.tamuCpp }}</div>
            <div class="tamu-stat__label">Pihak Pria</div>
          </div>
          <div class="tamu-stat tamu-stat--hadir">
            <div class="tamu-stat__val">{{ props.hadir }}</div>
            <div class="tamu-stat__label">Hadir</div>
          </div>
        </div>
        <!-- Progress hadir -->
        <div style="padding: 0 20px 16px;">
          <div style="display:flex; justify-content:space-between; font-size:12px; color:var(--text-muted); margin-bottom:6px;">
            <span>Konfirmasi Hadir</span>
            <span>{{ props.totalTamu > 0 ? Math.round(props.hadir/props.totalTamu*100) : 0 }}%</span>
          </div>
          <div class="prog-track">
            <div class="prog-fill" :style="{ width: (props.totalTamu > 0 ? Math.round(props.hadir/props.totalTamu*100) : 0) + '%' }"></div>
          </div>
        </div>
      </div>

      <!-- Dokumen KUA Progress -->
      <div class="card kua-card">
        <div class="section-header">
          <h2 class="section-title"><i class="fa-solid fa-file-contract" style="color:var(--rose);margin-right:8px;"></i>Dokumen KUA</h2>
          <Link :href="route('dokumen-kua.index')" class="btn btn--ghost btn--sm">
            Lihat <i class="fa-solid fa-arrow-right fa-xs"></i>
          </Link>
        </div>
        <div class="kua-progress">
          <div class="kua-progress-item">
            <div class="kua-prog-header">
              <span class="kua-prog-name"><i class="fa-solid fa-venus kua-icon--cpw"></i> {{ props.namaCpw }}</span>
              <span class="kua-prog-count">{{ props.doneCpw }}/{{ props.totalKua }}</span>
            </div>
            <div class="prog-track">
              <div class="prog-fill prog-fill--cpw" :style="{ width: (props.totalKua > 0 ? Math.round(props.doneCpw/props.totalKua*100) : 0) + '%' }"></div>
            </div>
          </div>
          <div class="kua-progress-item">
            <div class="kua-prog-header">
              <span class="kua-prog-name"><i class="fa-solid fa-mars kua-icon--cpp"></i> {{ props.namaCpp }}</span>
              <span class="kua-prog-count">{{ props.doneCpp }}/{{ props.totalKua }}</span>
            </div>
            <div class="prog-track">
              <div class="prog-fill prog-fill--cpp" :style="{ width: (props.totalKua > 0 ? Math.round(props.doneCpp/props.totalKua*100) : 0) + '%' }"></div>
            </div>
          </div>
          <div class="kua-progress-item">
            <div class="kua-prog-header">
              <span class="kua-prog-name"><i class="fa-solid fa-circle-check" style="color:var(--ok-text)"></i> Selesai Keduanya</span>
              <span class="kua-prog-count">{{ props.doneKua }}/{{ props.totalKua }}</span>
            </div>
            <div class="prog-track">
              <div class="prog-fill" :style="{ width: (props.totalKua > 0 ? Math.round(props.doneKua/props.totalKua*100) : 0) + '%' }"></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Bottom: Budget summary + Category + Pending Checklist -->
    <div class="dashboard-bottom">
      <!-- Budget Summary -->
      <div class="card budget-summary">
        <div class="section-header">
          <h2 class="section-title"><i class="fa-solid fa-wallet" style="color:var(--rose);margin-right:8px;"></i>Ringkasan Budget</h2>
          <Link :href="route('budget.index')" class="btn btn--ghost btn--sm">
            Lihat semua <i class="fa-solid fa-arrow-right fa-xs"></i>
          </Link>
        </div>
        <div class="budget-figures">
          <div class="budget-figure">
            <p class="budget-figure__label">Total Estimasi</p>
            <p class="budget-figure__value">{{ formatRp(props.totalEstimasi) }}</p>
          </div>
          <div class="budget-figure">
            <p class="budget-figure__label">Sudah Dibayar</p>
            <p class="budget-figure__value">{{ formatRp(props.totalAktual) }}</p>
          </div>
          <div class="budget-figure">
            <p class="budget-figure__label">Sisa</p>
            <p class="budget-figure__value budget-figure__value--muted">{{ formatRp(props.totalEstimasi - props.totalAktual) }}</p>
          </div>
        </div>
        <div class="prog-track mx-4 mt-4">
          <div class="prog-fill" :style="{ width: props.progressBudget + '%' }"></div>
        </div>
        <p class="prog-label">{{ props.progressBudget }}% terbayar</p>
      </div>

      <!-- Budget by category -->
      <div class="card kat-card">
        <div class="section-header">
          <h2 class="section-title"><i class="fa-solid fa-chart-pie" style="color:var(--rose);margin-right:8px;"></i>Budget per Kategori</h2>
        </div>
        <div class="kat-list">
          <div v-for="(data, kat) in props.budgetByKategori" :key="kat" class="kat-row">
            <span class="kat-name">{{ kat }}</span>
            <div class="kat-bar-wrap">
              <div class="prog-track" style="flex: 1">
                <div class="prog-fill" :style="{ width: getPct(data) + '%' }"></div>
              </div>
            </div>
            <span class="kat-amount">{{ formatRpShort(data.estimasi) }}</span>
            <span class="kat-pct">{{ getPct(data) }}%</span>
          </div>
          <div v-if="!Object.keys(props.budgetByKategori).length" class="empty-state">
            <i class="fa-solid fa-wallet empty-state__icon"></i>
            <p class="empty-state__text">Belum ada data budget</p>
          </div>
        </div>
      </div>

      <!-- Pending Checklist -->
      <div class="card pending-card">
        <div class="section-header">
          <h2 class="section-title"><i class="fa-solid fa-clock" style="color:var(--rose);margin-right:8px;"></i>Persiapan Belum Selesai</h2>
          <Link :href="route('checklist.index')" class="btn btn--ghost btn--sm">
            Lihat <i class="fa-solid fa-arrow-right fa-xs"></i>
          </Link>
        </div>
        <div class="pending-list">
          <div v-if="props.pendingChecklists.length === 0" class="empty-state" style="padding:24px;">
            <i class="fa-solid fa-circle-check empty-state__icon" style="color:var(--ok-text)"></i>
            <p class="empty-state__text">Semua checklist sudah selesai! 🎉</p>
          </div>
          <div v-for="item in props.pendingChecklists" :key="item.id" class="pending-item">
            <div class="pending-dot"></div>
            <div class="pending-body">
              <div class="pending-task">{{ item.persiapan }}</div>
              <div class="pending-range">{{ item.bulan_range }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  totalChecklist:    Number,
  doneChecklist:     Number,
  progressChecklist: Number,
  pendingChecklists: Array,
  totalEstimasi:     Number,
  totalAktual:       Number,
  progressBudget:    Number,
  totalSeserahan:    Number,
  sudahBeli:         Number,
  belumBeli:         Number,
  totalKua:          Number,
  doneKua:           Number,
  doneCpw:           Number,
  doneCpp:           Number,
  budgetByKategori:  Object,
  totalTamu:         Number,
  hadir:             Number,
  tamuCpw:           Number,
  tamuCpp:           Number,
  weddingDate:       { type: String, default: '' },
  namaCpw:           { type: String, default: '' },
  namaCpp:           { type: String, default: '' },
  lokasiAkad:        { type: String, default: '' },
  lokasiResepsi:     { type: String, default: '' },
});

const greetingText = computed(() => {
  const hour = new Date().getHours();
  if (hour >= 4 && hour < 10) return 'Selamat Pagi, Calon Pengantin 🌅';
  if (hour >= 10 && hour < 14) return 'Selamat Siang, Calon Pengantin ☀️';
  if (hour >= 14 && hour < 18) return 'Selamat Sore, Calon Pengantin 🌤️';
  return 'Selamat Malam, Calon Pengantin 🌙';
});

const countdownText = computed(() => {
  const wd = props.weddingDate;
  if (!wd) return '';
  const target = new Date(wd);
  if (isNaN(target.getTime())) return '';
  const now  = new Date();
  const diff = target.getTime() - now.getTime();
  if (diff <= 0) return 'Hari bahagia telah tiba! 🎉💍';
  const days = Math.ceil(diff / (1000 * 60 * 60 * 24));
  return `H-${days} hari menuju hari bahagia 💍`;
});

const formattedDate = computed(() => {
  if (!props.weddingDate) return '-';
  const d = new Date(props.weddingDate);
  return d.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
});

function formatRp(n) {
  return 'Rp' + Number(n || 0).toLocaleString('id-ID');
}

function formatRpShort(n) {
  if (!n) return 'Rp0';
  const juta = n / 1_000_000;
  if (juta >= 1) return 'Rp' + juta.toFixed(0) + 'jt';
  return 'Rp' + Number(n).toLocaleString('id-ID');
}

function getPct(data) {
  if (!data.estimasi) return 0;
  return Math.min(100, Math.round((data.aktual / data.estimasi) * 100));
}
</script>

<style scoped>
/* === GREETING CARD === */
.greeting-card {
  margin-bottom: var(--space-2xl);
  overflow: hidden;
  background: linear-gradient(135deg, #fffdfb 0%, #faf0e8 100%);
  border: 1px solid var(--rose-light);
}
.greeting-card__inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: var(--space-xl);
  padding: var(--space-xl) var(--space-2xl);
  flex-wrap: wrap;
}
.greeting-card__title {
  font-size: 22px;
  font-weight: 700;
  color: var(--text);
  font-family: var(--font-display);
  letter-spacing: -0.02em;
}
.greeting-card__sub {
  font-size: 14px;
  color: var(--text-muted);
  margin-top: 4px;
}
.greeting-card__countdown {
  font-size: 14px;
  font-weight: 600;
  color: var(--rose);
  margin-top: var(--space-sm);
  display: flex;
  align-items: center;
  gap: 6px;
}
.greeting-card__countdown i { color: #d48b8b; }
.quick-actions {
  display: flex;
  gap: var(--space-sm);
  flex-wrap: wrap;
  flex-shrink: 0;
}

/* === STATS GRID === */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: var(--space-lg);
  margin-bottom: var(--space-2xl);
}
@media (min-width: 768px) {
  .stats-grid { grid-template-columns: repeat(4, 1fr); }
}

/* === STAT CARD === */
.stat-card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: var(--space-xl);
  transition: all 0.25s ease;
}
.stat-card--link { text-decoration: none; display: block; cursor: pointer; }
.stat-card--link:hover {
  border-color: var(--rose-light);
  box-shadow: var(--shadow-hover);
  transform: translateY(-2px);
}
.stat-card--pink     { background: var(--stat-pink); border-color: #f5d8df; }
.stat-card--peach    { background: var(--stat-peach); border-color: #f5e4d0; }
.stat-card--lavender { background: var(--stat-lavender); border-color: #e3ddf5; }
.stat-card--sage     { background: var(--stat-sage); border-color: #d4e8da; }

.stat-card__header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 14px;
}
.stat-card__icon {
  width: 36px; height: 36px;
  background: rgba(196,149,106,0.15);
  border-radius: var(--radius-sm);
  display: flex; align-items: center; justify-content: center;
  color: var(--rose); font-size: 14px;
  transition: all 0.25s ease;
}
.stat-card--link:hover .stat-card__icon { background: var(--rose); color: #fff; }
.stat-card__arrow { color: var(--text-dim); font-size: 11px; transition: color 0.2s; }
.stat-card--link:hover .stat-card__arrow { color: var(--rose); }
.stat-card__value {
  font-size: 28px; font-weight: 800; color: var(--text);
  letter-spacing: -0.03em; line-height: 1;
}
.stat-card__total { font-size: 16px; font-weight: 500; color: var(--text-dim); margin-left: 2px; }
.stat-card__label {
  font-size: 12px; font-weight: 600; color: var(--text-muted);
  margin-top: 6px; letter-spacing: 0.02em;
}

/* === INFO ROW === */
.info-row {
  display: grid;
  grid-template-columns: 1fr;
  gap: var(--space-lg);
  margin-bottom: var(--space-lg);
}
@media (min-width: 768px) {
  .info-row { grid-template-columns: 1fr 1fr 1fr; }
}

/* === INFO CARD === */
.info-card { overflow: hidden; }
.info-list { padding: 14px 20px 16px; display: flex; flex-direction: column; gap: 10px; }
.info-item {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 8px;
  font-size: 13.5px;
}
.info-label {
  color: var(--text-muted);
  font-weight: 500;
  flex-shrink: 0;
  display: flex;
  align-items: center;
  gap: 6px;
  min-width: 100px;
  font-size: 12.5px;
}
.info-label i { width: 14px; text-align: center; color: var(--rose); }
.info-val { color: var(--text); font-weight: 600; text-align: right; }

/* === TAMU CARD === */
.tamu-card { overflow: hidden; }
.tamu-stats {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: var(--space-md);
  padding: 0 var(--space-xl) var(--space-lg);
}
.tamu-stat { text-align: center; padding: 8px 4px; }
.tamu-stat__val { font-size: 22px; font-weight: 800; color: var(--text); line-height: 1; }
.tamu-stat__label { font-size: 11px; color: var(--text-muted); margin-top: 4px; }
.tamu-stat--cpw .tamu-stat__val  { color: #c4719e; }
.tamu-stat--cpp .tamu-stat__val  { color: #5a82c4; }
.tamu-stat--hadir .tamu-stat__val { color: var(--ok-text); }

/* === KUA CARD === */
.kua-card { overflow: hidden; }
.kua-progress { padding: 14px 20px 16px; display: flex; flex-direction: column; gap: 14px; }
.kua-progress-item {}
.kua-prog-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 6px;
  font-size: 13px;
}
.kua-prog-name { font-weight: 600; color: var(--text); display: flex; align-items: center; gap: 6px; }
.kua-prog-count { font-size: 12px; color: var(--text-muted); font-weight: 600; }
.kua-icon--cpw { color: #c4719e; }
.kua-icon--cpp { color: #5a82c4; }
.prog-fill--cpw { background: linear-gradient(90deg, #c4719e 0%, #e8a0c8 100%); }
.prog-fill--cpp { background: linear-gradient(90deg, #5a82c4 0%, #8ab0e8 100%); }

/* === DASHBOARD BOTTOM === */
.dashboard-bottom {
  display: grid;
  grid-template-columns: 1fr;
  gap: var(--space-lg);
}
@media (min-width: 768px) {
  .dashboard-bottom { grid-template-columns: 1fr 1fr 1fr; }
}

/* === SECTION HEADER === */
.section-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: var(--space-lg) var(--space-xl);
  border-bottom: 1px solid var(--border);
}
.section-title { font-size: 15px; font-weight: 600; color: var(--text); letter-spacing: -0.01em; }

/* === BUDGET SUMMARY === */
.budget-summary { overflow: hidden; }
.budget-figures { display: flex; flex-wrap: wrap; gap: var(--space-md); padding: var(--space-lg) var(--space-xl) 0; }
.budget-figure {
  flex: 1;
  min-width: 120px;
  padding-right: var(--space-lg);
  border-right: 1px solid var(--border);
}
.budget-figure:last-child { border-right: none; margin-right: 0; padding-right: 0; }
.budget-figure__label {
  font-size: 12px; font-weight: 600; color: var(--text-muted);
  letter-spacing: 0.02em; margin-bottom: 4px;
}
.budget-figure__value { font-size: 15px; font-weight: 700; color: var(--text); letter-spacing: -0.01em; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.budget-figure__value--muted { color: var(--text-muted); }
.mx-4 { margin-left: var(--space-xl); margin-right: var(--space-xl); }
.prog-label {
  font-size: 12px; color: var(--text-muted);
  margin-top: var(--space-sm);
  padding: 0 var(--space-xl) var(--space-xl);
}

/* === CATEGORY CARD === */
.kat-card { overflow: hidden; }
.kat-list { padding: var(--space-md) var(--space-xl) var(--space-lg); }
.kat-row {
  display: flex; align-items: center; gap: 10px;
  padding: 9px 0; border-bottom: 1px solid #f5ece8;
}
.kat-row:last-child { border-bottom: none; }
.kat-name {
  font-size: 13px; color: var(--text); font-weight: 500;
  width: 110px; flex-shrink: 0;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.kat-bar-wrap { flex: 1; }
.kat-amount { font-size: 12px; color: var(--text-muted); width: 56px; text-align: right; flex-shrink: 0; }
.kat-pct { font-size: 12px; font-weight: 700; color: var(--text); width: 36px; text-align: right; flex-shrink: 0; }

/* === PENDING CHECKLIST === */
.pending-card { overflow: hidden; }
.pending-list { padding: 10px 20px 16px; display: flex; flex-direction: column; gap: 2px; }
.pending-item {
  display: flex; align-items: flex-start; gap: 12px;
  padding: 10px 0; border-bottom: 1px solid #f5ece8;
}
.pending-item:last-child { border-bottom: none; }
.pending-dot {
  width: 8px; height: 8px; border-radius: 50%;
  background: var(--rose); flex-shrink: 0; margin-top: 5px;
}
.pending-task { font-size: 13.5px; font-weight: 600; color: var(--text); }
.pending-range { font-size: 12px; color: var(--text-muted); margin-top: 2px; }
</style>