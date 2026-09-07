<template>
  <AppLayout>
    <div class="dashboard">
      <header class="hero-card">
        <div class="hero-card__content">
          <h1>{{ greetingText }}</h1>
          <p>Pantau seluruh persiapan hari bahagia dalam satu tempat.</p>
          <div v-if="countdownText" class="countdown-badge">
            <CalendarHeart aria-hidden="true" />
            <span>{{ countdownText }}</span>
          </div>
        </div>
        <div class="quick-actions" aria-label="Akses cepat">
          <Link :href="route('checklist.index')" class="quick-action quick-action--primary">
            <ListChecks aria-hidden="true" />
            <span>Daftar Persiapan</span>
          </Link>
          <Link :href="route('budget.index')" class="quick-action">
            <WalletCards aria-hidden="true" />
            <span>Anggaran</span>
          </Link>
          <Link :href="route('tamu.index')" class="quick-action">
            <UsersRound aria-hidden="true" />
            <span>Daftar Tamu</span>
          </Link>
        </div>
      </header>

      <section aria-labelledby="progress-title">
        <div class="section-heading">
          <div>
            <span class="section-heading__eyebrow">Progres utama</span>
            <h2 id="progress-title">Progres Persiapan</h2>
          </div>
          <p>Lihat perkembangan setiap kebutuhan utama.</p>
        </div>

        <div class="stats-grid">
          <Link v-for="stat in summaryStats" :key="stat.label" :href="route(stat.route)" class="stat-card">
            <div class="stat-card__top">
              <span class="icon-box" :class="`icon-box--${stat.tone}`">
                <component :is="stat.icon" aria-hidden="true" />
              </span>
              <ArrowUpRight class="stat-card__arrow" aria-hidden="true" />
            </div>
            <div class="stat-card__body">
              <p class="stat-card__value">{{ stat.value }}<span>{{ stat.suffix }}</span></p>
              <p class="stat-card__label">{{ stat.label }}</p>
            </div>
            <div class="progress-block">
              <div class="progress-meta"><span>Progres</span><strong>{{ stat.progress }}%</strong></div>
              <div class="progress-track"><span :style="{ width: stat.progress + '%' }"></span></div>
            </div>
          </Link>
        </div>
      </section>

      <div class="overview-grid">
        <section class="panel wedding-panel" aria-labelledby="wedding-title">
          <div class="panel__header">
            <div class="panel__title">
              <span class="icon-box icon-box--mint"><HeartHandshake aria-hidden="true" /></span>
              <div><span>Hari bahagia</span><h2 id="wedding-title">Informasi Pernikahan</h2></div>
            </div>
            <Link :href="route('profile.edit')" class="text-link">Pengaturan <ArrowRight aria-hidden="true" /></Link>
          </div>
          <dl class="detail-list">
            <div><dt><Heart aria-hidden="true" />Calon Pengantin</dt><dd>{{ coupleNames }}</dd></div>
            <div><dt><CalendarDays aria-hidden="true" />Tanggal</dt><dd>{{ formattedDate }}</dd></div>
            <div><dt><MapPin aria-hidden="true" />Lokasi Akad</dt><dd>{{ props.lokasiAkad || 'Belum ditentukan' }}</dd></div>
            <div><dt><PartyPopper aria-hidden="true" />Lokasi Resepsi</dt><dd>{{ props.lokasiResepsi || 'Belum ditentukan' }}</dd></div>
          </dl>
        </section>

        <section class="panel guest-panel" aria-labelledby="guest-title">
          <div class="panel__header">
            <div class="panel__title">
              <span class="icon-box icon-box--teal"><UsersRound aria-hidden="true" /></span>
              <div><span>Undangan</span><h2 id="guest-title">Daftar Tamu</h2></div>
            </div>
            <Link :href="route('tamu.index')" class="text-link">Lihat semua <ArrowRight aria-hidden="true" /></Link>
          </div>
          <div class="guest-stats">
            <div><strong>{{ props.totalTamu }}</strong><span>Total tamu</span></div>
            <div><strong>{{ props.tamuCpw }}</strong><span>Pihak wanita</span></div>
            <div><strong>{{ props.tamuCpp }}</strong><span>Pihak pria</span></div>
            <div><strong>{{ props.hadir }}</strong><span>Konfirmasi hadir</span></div>
          </div>
          <div class="progress-block guest-progress">
            <div class="progress-meta"><span>Konfirmasi kehadiran</span><strong>{{ attendanceProgress }}%</strong></div>
            <div class="progress-track"><span :style="{ width: attendanceProgress + '%' }"></span></div>
          </div>
        </section>
      </div>

      <div class="details-grid">
        <section class="panel budget-panel" aria-labelledby="budget-title">
          <div class="panel__header">
            <div class="panel__title">
              <span class="icon-box icon-box--sand"><WalletCards aria-hidden="true" /></span>
              <div><span>Keuangan</span><h2 id="budget-title">Ringkasan Anggaran</h2></div>
            </div>
            <Link :href="route('budget.index')" class="text-link">Kelola <ArrowRight aria-hidden="true" /></Link>
          </div>
          <div class="budget-figures">
            <div><span>Total estimasi</span><strong>{{ formatRp(props.totalEstimasi) }}</strong></div>
            <div><span>Sudah dibayar</span><strong>{{ formatRp(props.totalAktual) }}</strong></div>
            <div><span>Sisa anggaran</span><strong>{{ formatRp(props.totalEstimasi - props.totalAktual) }}</strong></div>
          </div>
          <div class="progress-block budget-progress">
            <div class="progress-meta"><span>Pembayaran anggaran</span><strong>{{ safeProgress(props.progressBudget) }}%</strong></div>
            <div class="progress-track"><span :style="{ width: safeProgress(props.progressBudget) + '%' }"></span></div>
          </div>
        </section>

        <section class="panel kua-panel" aria-labelledby="kua-title">
          <div class="panel__header">
            <div class="panel__title">
              <span class="icon-box icon-box--blush"><FileCheck2 aria-hidden="true" /></span>
              <div><span>Administrasi</span><h2 id="kua-title">Dokumen KUA</h2></div>
            </div>
            <Link :href="route('dokumen-kua.index')" class="text-link">Kelola <ArrowRight aria-hidden="true" /></Link>
          </div>
          <div class="kua-list">
            <div v-for="item in kuaProgress" :key="item.label">
              <div class="progress-meta"><span>{{ item.label }}</span><strong>{{ item.done }}/{{ props.totalKua }}</strong></div>
              <div class="progress-track"><span :style="{ width: item.progress + '%' }"></span></div>
            </div>
          </div>
        </section>
      </div>

      <div class="details-grid details-grid--bottom">
        <section class="panel category-panel" aria-labelledby="category-title">
          <div class="panel__header">
            <div class="panel__title">
              <span class="icon-box icon-box--yellow"><ChartPie aria-hidden="true" /></span>
              <div><span>Rincian biaya</span><h2 id="category-title">Anggaran per Kategori</h2></div>
            </div>
            <Link :href="route('budget.index')" class="text-link">Lihat Anggaran <ArrowRight aria-hidden="true" /></Link>
          </div>
          <div v-if="budgetCategories.length" class="category-list">
            <div v-for="item in budgetCategories" :key="item.name" class="category-item">
              <div class="category-item__meta"><strong>{{ item.name }}</strong><span>{{ formatRpShort(item.estimasi) }} · {{ item.progress }}%</span></div>
              <div class="progress-track"><span :style="{ width: item.progress + '%' }"></span></div>
            </div>
          </div>
          <div v-else class="empty-state"><WalletCards aria-hidden="true" /><strong>Belum ada anggaran</strong><span>Tambahkan rincian melalui menu Anggaran.</span></div>
        </section>

        <section class="panel checklist-panel" aria-labelledby="checklist-title">
          <div class="panel__header">
            <div class="panel__title">
              <span class="icon-box icon-box--mint"><Clock3 aria-hidden="true" /></span>
              <div><span>Prioritas berikutnya</span><h2 id="checklist-title">Persiapan Belum Selesai</h2></div>
            </div>
            <Link :href="route('checklist.index')" class="text-link">Lihat semua <ArrowRight aria-hidden="true" /></Link>
          </div>
          <div v-if="props.pendingChecklists.length" class="checklist-list">
            <div v-for="item in props.pendingChecklists" :key="item.id" class="checklist-item">
              <span class="checklist-item__marker"><Circle aria-hidden="true" /></span>
              <div><strong>{{ item.persiapan }}</strong><span>{{ item.bulan_range }}</span></div>
            </div>
          </div>
          <div v-else class="empty-state"><CircleCheckBig aria-hidden="true" /><strong>Semua persiapan selesai</strong><span>Kerja bagus, tidak ada tugas tertunda.</span></div>
        </section>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ArrowRight, ArrowUpRight, CalendarDays, CalendarHeart, ChartPie, Circle, CircleCheckBig, Clock3, FileCheck2, Gift, Heart, HeartHandshake, ListChecks, MapPin, PartyPopper, UsersRound, WalletCards } from '@lucide/vue';

const props = defineProps({
  totalChecklist: Number, doneChecklist: Number, progressChecklist: Number, pendingChecklists: Array,
  totalEstimasi: Number, totalAktual: Number, progressBudget: Number,
  totalSeserahan: Number, sudahBeli: Number, belumBeli: Number,
  totalKua: Number, doneKua: Number, doneCpw: Number, doneCpp: Number,
  budgetByKategori: Object, totalTamu: Number, hadir: Number, tamuCpw: Number, tamuCpp: Number,
  weddingDate: { type: String, default: '' }, namaCpw: { type: String, default: '' }, namaCpp: { type: String, default: '' },
  lokasiAkad: { type: String, default: '' }, lokasiResepsi: { type: String, default: '' },
});

const safeProgress = (value) => Math.min(100, Math.max(0, Number(value) || 0));
const ratio = (done, total) => total > 0 ? safeProgress(Math.round(done / total * 100)) : 0;

const greetingText = computed(() => {
  const hour = new Date().getHours();
  if (hour >= 4 && hour < 10) return 'Selamat pagi, Calon Pengantin';
  if (hour < 14) return 'Selamat siang, Calon Pengantin';
  if (hour < 18) return 'Selamat sore, Calon Pengantin';
  return 'Selamat malam, Calon Pengantin';
});

const countdownText = computed(() => {
  if (!props.weddingDate) return '';
  const target = new Date(props.weddingDate);
  if (Number.isNaN(target.getTime())) return '';
  const days = Math.ceil((target.getTime() - Date.now()) / 86400000);
  if (days < 0) return 'Hari bahagia telah terlewati';
  if (days === 0) return 'Hari bahagia tiba hari ini';
  return `${days} hari menuju hari bahagia`;
});

const coupleNames = computed(() => [props.namaCpw, props.namaCpp].filter(Boolean).join(' & ') || 'Belum dilengkapi');
const formattedDate = computed(() => {
  if (!props.weddingDate) return 'Belum ditentukan';
  const date = new Date(props.weddingDate);
  if (Number.isNaN(date.getTime())) return 'Belum ditentukan';
  return date.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
});
const attendanceProgress = computed(() => ratio(props.hadir, props.totalTamu));
const summaryStats = computed(() => [
  { label: 'Daftar Persiapan', value: props.doneChecklist, suffix: `/${props.totalChecklist}`, progress: safeProgress(props.progressChecklist), icon: ListChecks, route: 'checklist.index', tone: 'mint' },
  { label: 'Anggaran Terbayar', value: safeProgress(props.progressBudget), suffix: '%', progress: safeProgress(props.progressBudget), icon: WalletCards, route: 'budget.index', tone: 'yellow' },
  { label: 'Seserahan Dibeli', value: props.sudahBeli, suffix: `/${props.totalSeserahan}`, progress: ratio(props.sudahBeli, props.totalSeserahan), icon: Gift, route: 'seserahan.index', tone: 'teal' },
  { label: 'Dokumen KUA', value: props.doneKua, suffix: `/${props.totalKua}`, progress: ratio(props.doneKua, props.totalKua), icon: FileCheck2, route: 'dokumen-kua.index', tone: 'blush' },
]);
const kuaProgress = computed(() => [
  { label: props.namaCpw || 'Calon pengantin wanita', done: props.doneCpw, progress: ratio(props.doneCpw, props.totalKua) },
  { label: props.namaCpp || 'Calon pengantin pria', done: props.doneCpp, progress: ratio(props.doneCpp, props.totalKua) },
  { label: 'Selesai untuk keduanya', done: props.doneKua, progress: ratio(props.doneKua, props.totalKua) },
]);
const budgetCategories = computed(() => Object.entries(props.budgetByKategori || {}).map(([name, data]) => ({ name, ...data, progress: ratio(data.aktual, data.estimasi) })));

function formatRp(value) { return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(Number(value) || 0); }
function formatRpShort(value) { return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', notation: 'compact', maximumFractionDigits: 1 }).format(Number(value) || 0); }
</script>

<style scoped>
.dashboard { display: grid; gap: 28px; padding-bottom: 24px; }
.hero-card { display: flex; align-items: center; justify-content: space-between; gap: 28px; min-height: 220px; padding: 36px 40px; border: 1px solid var(--border); border-radius: 20px; background: #fcfaf5; overflow: hidden; }
.hero-card__content { max-width: 620px; }
.hero-card__eyebrow, .section-heading__eyebrow, .panel__title div > span { display: block; margin-bottom: 7px; color: var(--rose); font-size: 11px; font-weight: 700; letter-spacing: .09em; text-transform: uppercase; }
.hero-card h1 { color: var(--text); font-family: var(--font-display); font-size: clamp(28px, 3vw, 40px); line-height: 1.15; letter-spacing: -.025em; }
.hero-card__content > p { margin-top: 10px; color: var(--text-muted); font-size: 16px; }
.countdown-badge { display: inline-flex; align-items: center; gap: 8px; margin-top: 20px; padding: 8px 12px; border: 1px solid var(--rose-light); border-radius: 999px; background: rgba(255,255,255,.76); color: var(--text); font-size: 14px; font-weight: 600; }
.countdown-badge svg { width: 16px; color: var(--rose); }
.quick-actions { display: grid; width: min(100%, 220px); gap: 9px; flex-shrink: 0; }
.quick-action { display: flex; align-items: center; gap: 10px; min-height: 43px; padding: 10px 14px; border: 1px solid var(--border); border-radius: 10px; background: rgba(255,255,255,.82); color: var(--text); font-size: 14px; font-weight: 600; text-decoration: none; transition: .2s ease; }
.quick-action svg { width: 17px; color: var(--rose); }
.quick-action:hover { border-color: var(--rose-light); transform: translateX(3px); }
.quick-action--primary { border-color: var(--accent); background: var(--accent); color: #fff; }
.quick-action--primary:hover { border-color: var(--accent-hover); background: var(--accent-hover); }
.quick-action--primary svg { color: #fff; }
.section-heading { display: flex; align-items: end; justify-content: space-between; gap: 20px; margin-bottom: 14px; }
.section-heading h2 { font-size: 20px; letter-spacing: -.02em; }
.section-heading > p { color: var(--text-muted); font-size: 14px; }
.stats-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; }
.stat-card, .panel { border: 1px solid var(--border); border-radius: 14px; background: var(--surface); box-shadow: var(--shadow-sm); }
.stat-card, .panel { transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease; }
.stat-card { display: flex; min-height: 206px; padding: 20px; flex-direction: column; color: inherit; text-decoration: none; }
@media (hover: hover) and (pointer: fine) {
  .stat-card:hover, .panel:hover { border-color: var(--rose-light); box-shadow: var(--shadow-hover); transform: translateY(-2px); }
}
.stat-card__top { display: flex; align-items: center; justify-content: space-between; }
.stat-card__arrow { width: 17px; color: var(--text-dim); }
.icon-box { display: inline-flex; width: 38px; height: 38px; align-items: center; justify-content: center; border-radius: 10px; flex-shrink: 0; }
.icon-box svg { width: 19px; height: 19px; stroke-width: 1.8; }
.icon-box--mint { background: #d5f5c2; color: #1a3300; }.icon-box--yellow { background: #ffe95c; color: #1a3300; }.icon-box--sand { background: #fff1bd; color: #240029; }.icon-box--teal { background: #a8e5e5; color: #1a3300; }.icon-box--blush { background: #f6d0ff; color: #1a3300; }
.stat-card__body { margin-top: 20px; }
.stat-card__value { color: var(--text); font-size: 28px; font-weight: 800; line-height: 1; letter-spacing: -.035em; }
.stat-card__value span { margin-left: 2px; color: var(--text-dim); font-size: 16px; font-weight: 600; }
.stat-card__label { margin-top: 7px; color: var(--text-muted); font-size: 14px; font-weight: 600; }
.progress-block { margin-top: auto; padding-top: 17px; }
.progress-meta { display: flex; justify-content: space-between; gap: 12px; margin-bottom: 7px; color: var(--text-muted); font-size: 12px; }
.progress-meta strong { color: var(--text); font-weight: 700; }
.progress-track { height: 6px; border-radius: 999px; background: #e9eee7; overflow: hidden; }
.progress-track > span { display: block; height: 100%; border-radius: inherit; background: linear-gradient(90deg, #cb5521, #ffe95c); transition: width .35s ease; }
.overview-grid, .details-grid { display: grid; grid-template-columns: minmax(0, .9fr) minmax(0, 1.4fr); gap: 16px; align-items: stretch; }
.details-grid { grid-template-columns: minmax(0, 1.25fr) minmax(300px, .75fr); }
.details-grid--bottom { grid-template-columns: 1fr 1fr; }
.panel { min-width: 0; overflow: hidden; }
.panel__header { display: flex; min-height: 80px; padding: 18px 20px; align-items: center; justify-content: space-between; gap: 12px; border-bottom: 1px solid var(--border); }
.panel__title { display: flex; align-items: center; gap: 12px; min-width: 0; }
.panel__title div > span { margin-bottom: 3px; font-size: 11px; }
.panel__title h2 { font-size: 16px; line-height: 1.25; letter-spacing: -.01em; }
.text-link { display:inline-flex; min-height:34px; padding:7px; align-items:center; gap:6px; color:var(--text-muted); font-size:12px; font-weight:600; text-decoration:none; white-space:nowrap; transition:color .15s ease; }
.text-link svg { width:14px; transition:transform .15s ease; }
@media(hover:hover) and (pointer:fine) {
  .text-link:hover { color:#df37a7; }
  .text-link:hover svg { transform:translateX(3px); }
}
.text-link:hover { color: var(--rose); }.text-link svg { width: 14px; }
.detail-list { display: grid; padding: 7px 20px 14px; }
.detail-list > div { display: grid; grid-template-columns: minmax(105px, .8fr) 1.2fr; gap: 12px; padding: 13px 0; border-bottom: 1px solid #edf1eb; }
.detail-list > div:last-child { border-bottom: 0; }
.detail-list dt { display: flex; align-items: center; gap: 8px; color: #6b6b6b; font-size: 12px; }.detail-list dt svg { width: 15px; color: #287f7f; }
.detail-list dd { color: var(--text); font-size: 12px; font-weight: 600; text-align: right; overflow-wrap: anywhere; }
.guest-stats { display: grid; grid-template-columns: repeat(4, 1fr); padding: 22px 20px 12px; }
.guest-stats > div { padding: 5px 12px; border-right: 1px solid var(--border); text-align: center; }.guest-stats > div:last-child { border: 0; }
.guest-stats strong, .guest-stats span { display: block; }.guest-stats strong { font-size: 24px; letter-spacing: -.03em; }.guest-stats span { margin-top: 5px; color: var(--text-muted); font-size: 11px; }
.guest-progress, .budget-progress { margin: 0 20px 20px; padding-top: 15px; }
.budget-figures { display: grid; grid-template-columns: repeat(3, 1fr); padding: 22px 20px 8px; }
.budget-figures > div { min-width: 0; padding: 3px 16px; border-right: 1px solid var(--border); }.budget-figures > div:first-child { padding-left: 0; }.budget-figures > div:last-child { padding-right: 0; border: 0; }
.budget-figures span, .budget-figures strong { display: block; }.budget-figures span { margin-bottom: 7px; color: var(--text-muted); font-size: 11px; }.budget-figures strong { font-size: 14px; overflow-wrap: anywhere; }
.kua-list, .category-list, .checklist-list { display: grid; gap: 16px; padding: 20px; }
.kua-list { gap: 18px; }
.category-item__meta { display: flex; justify-content: space-between; gap: 12px; margin-bottom: 8px; font-size: 12px; }.category-item__meta strong { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }.category-item__meta span { color: var(--text-muted); white-space: nowrap; }
.checklist-list { gap: 0; padding-top: 8px; }
.checklist-item { display: flex; gap: 11px; padding: 13px 0; border-bottom: 1px solid #edf1eb; }.checklist-item:last-child { border: 0; }
.checklist-item__marker { color: var(--rose); }.checklist-item__marker svg { width: 15px; }
.checklist-item strong, .checklist-item span { display: block; }.checklist-item strong { font-size: 12px; }.checklist-item span { margin-top: 4px; color: var(--text-muted); font-size: 11px; }
.empty-state { display: flex; min-height: 180px; padding: 24px; flex-direction: column; align-items: center; justify-content: center; color: var(--text-muted); text-align: center; }.empty-state svg { width: 25px; margin-bottom: 10px; color: var(--rose); }.empty-state strong { color: var(--text); font-size: 14px; }.empty-state span { margin-top: 5px; font-size: 12px; }
@media (max-width: 1100px) { .stats-grid { grid-template-columns: repeat(2, 1fr); }.overview-grid, .details-grid, .details-grid--bottom { grid-template-columns: 1fr; } }
@media (max-width: 767px) { .dashboard { gap: 22px; }.hero-card { min-height: auto; padding: 26px 20px; flex-direction: column; align-items: stretch; }.quick-actions { width: 100%; grid-template-columns: repeat(3, 1fr); }.quick-action { justify-content: center; padding-inline: 8px; }.quick-action span { font-size: 11px; }.section-heading { align-items: start; flex-direction: column; gap: 4px; }.stats-grid { gap: 10px; }.stat-card { min-height: 190px; padding: 16px; }.stat-card__value { font-size: 24px; }.guest-stats { grid-template-columns: repeat(2, 1fr); gap: 12px; }.guest-stats > div { border: 0; }.budget-figures { grid-template-columns: 1fr; gap: 14px; }.budget-figures > div, .budget-figures > div:first-child, .budget-figures > div:last-child { padding: 0 0 14px; border: 0; border-bottom: 1px solid var(--border); }.budget-figures > div:last-child { border: 0; padding-bottom: 0; } }
@media (max-width: 430px) { .quick-actions { grid-template-columns: 1fr; }.stats-grid { grid-template-columns: 1fr; }.stat-card { min-height: 182px; }.panel__header { align-items: flex-start; }.detail-list > div { grid-template-columns: 1fr; gap: 7px; }.detail-list dd { text-align: left; padding-left: 23px; } }

/* DESIGN.md — creative sketchbook */
.dashboard { width: min(1200px, calc(100% - 48px)); margin: 0 auto; padding: 12px 0 64px; gap: 24px; color: #1a3300; }
.hero-card { min-height: 300px; padding: 48px; border-color: #1a3300; border-radius: 16px; background: #fcfaf5; box-shadow: none; }
.hero-card__eyebrow, .section-heading__eyebrow, .panel__title div > span { color: #1a3300; font-family: var(--font-mono); }
.hero-card__eyebrow { display: inline-flex; width: fit-content; padding: 5px 9px; border-radius: 6px; background: #ffe95c; }
.hero-card h1 { max-width: 720px; margin-top: 18px; color: #1a3300; font-family: var(--font-display); font-size: clamp(40px, 5vw, 55px); font-weight: 800; line-height: 1; letter-spacing: .04em; }
.hero-card__content > p { max-width: 600px; margin-top: 18px; color: #1a3300; font-size: 18px; line-height: 1.5; }
.countdown-badge { border: 0; border-radius: 9999px; background: #ffe95c; color: #1a3300; }
.countdown-badge svg { color: #1a3300; }
.quick-actions { width: min(100%, 240px); gap: 12px; }
.quick-action { min-height: 46px; border-color: #1a3300; border-radius: 6px; background: transparent; color: #1a3300; font-size: 14px; }
.quick-action svg { color: #1a3300; }
.quick-action:hover { border-color: #1a3300; background: #d5f5c2; }
.quick-action--primary, .quick-action--primary:hover { border-color: #1a3300; background: #1a3300; color: #fcfaf5; }
.quick-action--primary svg { color: #fcfaf5; }
.section-heading { margin-bottom: 24px; }
.section-heading h2 { color: #1a3300; font-size: 28px; }
.section-heading > p { color: #526159; font-size: 14px; }
.stats-grid { gap: 16px; }
.stat-card, .panel { border-color: #b6b6b6; border-radius: 12px; background: #fcfaf5; box-shadow: none; }
.stat-card { min-height: 220px; padding: 24px; }
.stat-card__value, .panel__title h2, .progress-meta strong, .detail-list dd, .guest-stats strong, .budget-figures strong, .category-item__meta strong, .checklist-item strong, .empty-state strong { color: #1a3300; }
.stat-card__label, .progress-meta, .guest-stats span, .budget-figures span, .category-item__meta span, .checklist-item span, .empty-state { color: #526159; }
.detail-list dt { color: #6b6b6b; }.detail-list dt svg { color: #287f7f; }
.icon-box { border-radius: 6px; color: #1a3300; }
.icon-box--mint { background: #d5f5c2; color: #1a3300; }.icon-box--yellow { background: #ffe95c; color: #1a3300; }.icon-box--teal { background: #a8e5e5; color: #1a3300; }.icon-box--blush { background: #f6d0ff; color: #1a3300; }
.progress-track { background: #e6e2d8; }.progress-track > span { background: #1a3300; }
.overview-grid, .details-grid { gap: 24px; }
.panel__header { padding: 20px 24px; border-color: #b6b6b6; }
.text-link { color: #1a3300; }.text-link:hover { color: #cb5521; }
.detail-list, .guest-stats, .budget-figures, .kua-list, .category-list, .checklist-list { padding-left: 24px; padding-right: 24px; }
@media (hover: hover) and (pointer: fine) { .stat-card:hover, .panel:hover { border-color: #1a3300; box-shadow: none; transform: translateY(-2px); } }
@media (max-width: 767px) { .dashboard { width: min(100% - 32px, 1200px); padding: 24px 0 40px; gap: 16px; }.hero-card { padding: 28px 20px; }.hero-card h1 { font-size: clamp(40px, 10vw, 55px); }.hero-card__content > p { font-size: 16px; } }

/* DESIGN.md — Sunset Marketplace */
.dashboard { color: #240029; font-family: 'Inter Var', Inter, ui-sans-serif, system-ui, sans-serif; }
.hero-card { border: 0; border-radius: 14px; background: linear-gradient(145deg, #ffbdd3, #fff1bd 40%, #fff1bd 60%, #ffcb0f); box-shadow: 0 0 0 1px rgba(41,0,41,.11), 0 2px 2px rgba(32,0,36,.05); }
.hero-card__eyebrow, .section-heading__eyebrow, .panel__title div > span { color: #240029; font-family: 'JetBrains Mono', ui-monospace, monospace; font-weight: 400; letter-spacing: .1em; }
.hero-card__eyebrow { padding: 0; background: transparent; }
.hero-card h1 { max-width:none; color:#240029; font-family:'Kaio','Inter Tight',Inter,sans-serif; font-size:clamp(38px,4.2vw,48px); letter-spacing:-.02em; white-space:nowrap; }
@media(max-width:767px) { .hero-card h1 { white-space:normal; } }
.hero-card__content > p, .section-heading > p { color: #6d526d; }
.countdown-badge { border: 1px solid #6d526d; background: rgba(255,255,255,.8); color: #240029; }
.countdown-badge svg, .quick-action svg { color: #240029; }
.quick-action { border-color: #6d526d; border-radius: 6px; background: transparent; color: #240029; }
.quick-action:hover { border-color: #6d526d; background: rgba(255,255,255,.6); }
.quick-action--primary, .quick-action--primary:hover { border-color: #df37a7; background: #df37a7; color: #fff; box-shadow: inset 0 0 0 1px rgba(255,255,255,.2), 0 1px 2px rgba(32,0,36,.05); }
.quick-action--primary svg { color: #fff; }
.section-heading h2, .stat-card__value, .panel__title h2, .progress-meta strong, .detail-list dd, .guest-stats strong, .budget-figures strong, .category-item__meta strong, .checklist-item strong, .empty-state strong { color: #240029; }
.stat-card, .panel { border-color: #d4ccd4; border-radius: 14px; background: #fff; box-shadow: 0 0 0 1px rgba(41,0,41,.11), 0 2px 2px rgba(32,0,36,.05); }
.stat-card__label, .progress-meta, .guest-stats span, .budget-figures span, .category-item__meta span, .checklist-item span, .empty-state { color: #6d526d; }
.icon-box { border-radius: 6px; color: #240029; }
.progress-track { background: #eee8ee; }
.progress-track > span { background: #240029; }
.panel__header { border-color: #d4ccd4; }
.text-link { color:#240029; }
@media(hover:hover) and (pointer:fine) { .text-link:hover { color:#df37a7; } }
@media (hover: hover) and (pointer: fine) { .stat-card:hover, .panel:hover { border-color: #6d526d; box-shadow: 0 0 0 1px rgba(41,0,41,.2), 0 4px 8px rgba(32,0,36,.06); } }

/* Selaraskan card non-domain dengan sistem card halaman Seserahan */
.dashboard :is(.stat-card, .panel) {
  border-color: #d4ccd4;
  border-radius: 14px;
  background: #fff;
  box-shadow: 0 0 0 1px rgba(41, 0, 41, .11), 0 2px 2px rgba(32, 0, 36, .05);
  font-family: Inter, system-ui, sans-serif;
}
.dashboard .stat-card {
  padding: 24px;
}
.dashboard :is(.panel__header, .detail-list, .guest-stats, .budget-figures, .kua-list, .category-list, .checklist-list) {
  padding-left: 24px;
  padding-right: 24px;
}
.dashboard :is(.stat-card__value, .guest-stats strong, .budget-figures strong) {
  font-weight: 800;
}
.dashboard :is(.stat-card__label, .progress-meta, .detail-list dt, .guest-stats span, .budget-figures span, .category-item__meta span, .checklist-item span, .empty-state span) {
  letter-spacing: 0;
}
@media (hover: hover) and (pointer: fine) {
  .dashboard :is(.stat-card, .panel):hover {
    border-color: #6d526d;
    box-shadow: 0 0 0 1px rgba(41, 0, 41, .2), 0 4px 8px rgba(32, 0, 36, .06);
  }
}
@media(max-width:767px) {
  .dashboard { width:calc(100% - 24px); padding:12px 0 32px; gap:16px; }
  .hero-card { min-height:0; padding:24px 18px; gap:22px; border-radius:14px; }
  .hero-card h1 { font-size:clamp(32px,9vw,40px); line-height:1.08; }
  .hero-card__content>p { margin-top:14px; font-size:15px; line-height:1.5; }
  .countdown-badge { margin-top:16px; }
  .quick-actions { gap:8px; }
  .quick-action { min-height:44px; padding:10px 12px; }
  .section-heading { margin-bottom:14px; align-items:flex-end; }
  .section-heading h2 { font-size:22px; }
  .stats-grid { grid-template-columns:repeat(2,minmax(0,1fr)); gap:10px; }
  .dashboard .stat-card { min-width:0; min-height:160px; padding:16px; }
  .stat-card__top .icon-box { width:34px; height:34px; }
  .stat-card__arrow { width:15px; }
  .stat-card__body { margin-top:16px; }
  .stat-card__value { font-size:24px; }
  .stat-card__label { margin-top:6px; font-size:12px; line-height:1.3; }
  .stat-card .progress-track { margin-top:14px; }
  .overview-grid,.details-grid,.details-grid--bottom { gap:12px; }
  .panel__header { min-height:68px; padding:14px 16px; align-items:center; }
  .dashboard :is(.panel__header,.detail-list,.guest-stats,.budget-figures,.kua-list,.category-list,.checklist-list) { padding-left:16px; padding-right:16px; }
  .panel__title { gap:10px; }
  .panel__title .icon-box { width:34px; height:34px; }
  .panel__title h2 { font-size:15px; }
  .text-link { padding:5px 0; font-size:11px; }
  .detail-list { padding-top:5px; padding-bottom:10px; }
  .detail-list>div { grid-template-columns:1fr; gap:5px; padding:11px 0; }
  .detail-list dd { padding-left:23px; text-align:left; }
  .guest-stats { grid-template-columns:repeat(2,1fr); gap:0; padding-top:16px; }
  .guest-stats>div { padding:10px; border-bottom:1px solid #d4ccd4; }
  .guest-stats>div:nth-child(2) { border-right:0; }
  .guest-stats>div:nth-last-child(-n+2) { border-bottom:0; }
  .budget-figures { grid-template-columns:1fr; padding-top:14px; }
  .budget-figures>div,.budget-figures>div:first-child,.budget-figures>div:last-child { padding:10px 0; border-right:0; border-bottom:1px solid #d4ccd4; }
  .budget-figures>div:last-child { border-bottom:0; }
  .progress-block { margin:14px 16px 18px; }
}
@media(max-width:360px) {
  .dashboard { width:calc(100% - 20px); }
  .dashboard .stat-card { min-height:150px; padding:14px; }
  .stat-card__value { font-size:21px; }
}

/* Hero Dashboard — selaras dengan PlannerHero. */
.dashboard .hero-card {
  min-height:190px;
  padding:34px 38px;
  gap:28px;
  align-items:center;
}
.dashboard .hero-card h1 {
  max-width:none;
  margin:0;
  color:#240029;
  font-family:'Bricolage Grotesque',Inter,system-ui,sans-serif;
  font-size:50px;
  font-weight:800;
  line-height:1.1;
  letter-spacing:.04em;
  white-space:nowrap;
}
.dashboard .hero-card__content>p {
  margin-top:9px;
  color:#240029;
  font-size:18px;
  line-height:1.5;
}
.dashboard .countdown-badge {
  margin-top:14px;
  border-color:rgba(109,82,109,.48);
  background:rgba(255,255,255,.72);
  color:#240029;
}

@media(max-width:767px) {
  .dashboard .hero-card {
    min-height:auto;
    padding:18px 16px;
    align-items:stretch;
    gap:14px;
  }
  .dashboard .hero-card h1 {
    font-size:40px;
    white-space:normal;
  }
  .dashboard .hero-card__content>p {
    margin-top:9px;
    font-size:16px;
  }
  .dashboard .countdown-badge { margin-top:14px; }
}
</style>
