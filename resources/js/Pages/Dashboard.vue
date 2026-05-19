<template>
  <AppLayout>
    <Head title="Dashboard" />

    <!-- Page Header -->
    <div class="page-header">
      <h1 class="page-title">Dashboard</h1>
      <p class="page-sub">Ringkasan persiapan pernikahan Anda.</p>
    </div>

    <!-- Stat Cards -->
    <div class="stats-grid">
      <Link :href="route('checklist.index')" class="stat-card stat-card--link">
        <div class="stat-card__header">
          <div class="stat-card__icon">
            <i class="fa-solid fa-list-check"></i>
          </div>
          <i class="fa-solid fa-arrow-right stat-card__arrow"></i>
        </div>
        <p class="stat-card__value">{{ props.doneChecklist }}<span class="stat-card__total">/{{ props.totalChecklist }}</span></p>
        <p class="stat-card__label">Checklist Selesai</p>
        <div class="prog-track mt-3">
          <div class="prog-fill" :style="{ width: props.progressChecklist + '%' }"></div>
        </div>
      </Link>

      <Link :href="route('budget.index')" class="stat-card stat-card--link">
        <div class="stat-card__header">
          <div class="stat-card__icon">
            <i class="fa-solid fa-wallet"></i>
          </div>
          <i class="fa-solid fa-arrow-right stat-card__arrow"></i>
        </div>
        <p class="stat-card__value">{{ props.progressBudget }}<span class="stat-card__total">%</span></p>
        <p class="stat-card__label">Budget Terbayar</p>
        <div class="prog-track mt-3">
          <div class="prog-fill" :style="{ width: props.progressBudget + '%' }"></div>
        </div>
      </Link>

      <Link :href="route('seserahan.index')" class="stat-card stat-card--link">
        <div class="stat-card__header">
          <div class="stat-card__icon">
            <i class="fa-solid fa-gift"></i>
          </div>
          <i class="fa-solid fa-arrow-right stat-card__arrow"></i>
        </div>
        <p class="stat-card__value">{{ props.sudahBeli }}<span class="stat-card__total">/{{ props.totalSeserahan }}</span></p>
        <p class="stat-card__label">Seserahan Dibeli</p>
      </Link>

      <Link :href="route('dokumen-kua.index')" class="stat-card stat-card--link">
        <div class="stat-card__header">
          <div class="stat-card__icon">
            <i class="fa-solid fa-file-contract"></i>
          </div>
          <i class="fa-solid fa-arrow-right stat-card__arrow"></i>
        </div>
        <p class="stat-card__value">{{ props.doneKua }}<span class="stat-card__total">/{{ props.totalKua }}</span></p>
        <p class="stat-card__label">Dokumen KUA</p>
      </Link>
    </div>

    <!-- Budget Summary + Budget by Category -->
    <div class="dashboard-bottom">
      <!-- Budget Summary -->
      <div class="card budget-summary">
        <div class="section-header">
          <h2 class="section-title">Ringkasan Budget</h2>
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
        <div class="prog-track mt-4">
          <div class="prog-fill" :style="{ width: props.progressBudget + '%' }"></div>
        </div>
        <p class="prog-label">{{ props.progressBudget }}% terbayar</p>
      </div>

      <!-- Budget by category -->
      <div class="card kat-card">
        <div class="section-header">
          <h2 class="section-title">Budget per Kategori</h2>
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
    </div>
  </AppLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  totalChecklist: Number,
  doneChecklist: Number,
  progressChecklist: Number,
  totalEstimasi: Number,
  totalAktual: Number,
  progressBudget: Number,
  totalSeserahan: Number,
  sudahBeli: Number,
  belumBeli: Number,
  totalKua: Number,
  doneKua: Number,
  doneCpw: Number,
  doneCpp: Number,
  budgetByKategori: Object,
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
/* Stats grid */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 14px;
  margin-bottom: 20px;
}
@media (min-width: 768px) {
  .stats-grid { grid-template-columns: repeat(4, 1fr); }
}

/* Stat card */
.stat-card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 18px;
  transition: border-color 0.15s, box-shadow 0.15s;
}
.stat-card--link {
  text-decoration: none;
  display: block;
  cursor: pointer;
}
.stat-card--link:hover {
  border-color: var(--ink-300);
  box-shadow: var(--shadow-sm);
}
.stat-card__header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 14px;
}
.stat-card__icon {
  width: 32px;
  height: 32px;
  background: var(--ink-100);
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--ink-500);
  font-size: 13px;
  transition: background 0.15s, color 0.15s;
}
.stat-card--link:hover .stat-card__icon {
  background: var(--ink-900);
  color: #fff;
}
.stat-card__arrow {
  color: var(--ink-200);
  font-size: 11px;
  transition: color 0.15s;
}
.stat-card--link:hover .stat-card__arrow { color: var(--ink-500); }
.stat-card__value {
  font-size: 26px;
  font-weight: 800;
  color: var(--ink-900);
  letter-spacing: -0.03em;
  line-height: 1;
}
.stat-card__total {
  font-size: 16px;
  font-weight: 500;
  color: var(--ink-300);
  margin-left: 1px;
}
.stat-card__label {
  font-size: 11.5px;
  font-weight: 600;
  color: var(--ink-400);
  margin-top: 5px;
  text-transform: uppercase;
  letter-spacing: 0.04em;
}

/* Dashboard bottom row */
.dashboard-bottom {
  display: grid;
  grid-template-columns: 1fr;
  gap: 14px;
}
@media (min-width: 768px) {
  .dashboard-bottom { grid-template-columns: 1fr 1fr; }
}

/* Section header */
.section-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 18px 14px;
  border-bottom: 1px solid var(--border);
}
.section-title {
  font-size: 13.5px;
  font-weight: 700;
  color: var(--ink-800);
  letter-spacing: -0.01em;
}

/* Budget summary */
.budget-summary {
  overflow: hidden;
}
.budget-figures {
  display: flex;
  gap: 0;
  padding: 16px 18px 0;
}
.budget-figure {
  flex: 1;
  padding-right: 16px;
  border-right: 1px solid var(--ink-100);
  margin-right: 16px;
}
.budget-figure:last-child {
  border-right: none;
  margin-right: 0;
}
.budget-figure__label {
  font-size: 11px;
  font-weight: 600;
  color: var(--ink-400);
  text-transform: uppercase;
  letter-spacing: 0.04em;
  margin-bottom: 4px;
}
.budget-figure__value {
  font-size: 15px;
  font-weight: 700;
  color: var(--ink-900);
  letter-spacing: -0.01em;
}
.budget-figure__value--muted { color: var(--ink-500); }

.prog-label {
  font-size: 11.5px;
  color: var(--ink-400);
  margin-top: 6px;
  padding: 0 18px 18px;
}

/* Category card */
.kat-card { overflow: hidden; }
.kat-list { padding: 12px 18px 16px; }
.kat-row {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 8px 0;
  border-bottom: 1px solid var(--ink-100);
}
.kat-row:last-child { border-bottom: none; }
.kat-name {
  font-size: 12.5px;
  color: var(--ink-700);
  font-weight: 500;
  width: 120px;
  flex-shrink: 0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.kat-bar-wrap { flex: 1; }
.kat-amount {
  font-size: 11.5px;
  color: var(--ink-500);
  width: 56px;
  text-align: right;
  flex-shrink: 0;
}
.kat-pct {
  font-size: 11.5px;
  font-weight: 700;
  color: var(--ink-700);
  width: 36px;
  text-align: right;
  flex-shrink: 0;
}
</style>
