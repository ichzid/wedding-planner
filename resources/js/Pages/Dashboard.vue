<template>
  <AppLayout>
    <div class="dashboard">
      <header class="hero-card">
        <div class="hero-card__content">
          <span class="hero-card__eyebrow">Ringkasan pernikahan</span>
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
              <span class="icon-box icon-box--sage"><HeartHandshake aria-hidden="true" /></span>
              <div><span>Hari bahagia</span><h2 id="wedding-title">Informasi Pernikahan</h2></div>
            </div>
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
              <span class="icon-box icon-box--mist"><UsersRound aria-hidden="true" /></span>
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
              <span class="icon-box icon-box--fern"><FileCheck2 aria-hidden="true" /></span>
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
              <span class="icon-box icon-box--stone"><ChartPie aria-hidden="true" /></span>
              <div><span>Rincian biaya</span><h2 id="category-title">Anggaran per Kategori</h2></div>
            </div>
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
              <span class="icon-box icon-box--sage"><Clock3 aria-hidden="true" /></span>
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
  { label: 'Daftar Persiapan', value: props.doneChecklist, suffix: `/${props.totalChecklist}`, progress: safeProgress(props.progressChecklist), icon: ListChecks, route: 'checklist.index', tone: 'sage' },
  { label: 'Anggaran Terbayar', value: safeProgress(props.progressBudget), suffix: '%', progress: safeProgress(props.progressBudget), icon: WalletCards, route: 'budget.index', tone: 'sand' },
  { label: 'Seserahan Dibeli', value: props.sudahBeli, suffix: `/${props.totalSeserahan}`, progress: ratio(props.sudahBeli, props.totalSeserahan), icon: Gift, route: 'seserahan.index', tone: 'stone' },
  { label: 'Dokumen KUA', value: props.doneKua, suffix: `/${props.totalKua}`, progress: ratio(props.doneKua, props.totalKua), icon: FileCheck2, route: 'dokumen-kua.index', tone: 'fern' },
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
.hero-card { display: flex; align-items: center; justify-content: space-between; gap: 28px; min-height: 220px; padding: 36px 40px; border: 1px solid var(--border); border-radius: 20px; background: radial-gradient(circle at 90% 20%, rgba(111,146,95,.24), transparent 34%), linear-gradient(135deg, #fff 0%, #eef4e8 100%); overflow: hidden; }
.hero-card__content { max-width: 620px; }
.hero-card__eyebrow, .section-heading__eyebrow, .panel__title div > span { display: block; margin-bottom: 7px; color: var(--rose); font-size: 11px; font-weight: 700; letter-spacing: .09em; text-transform: uppercase; }
.hero-card h1 { color: var(--text); font-family: var(--font-display); font-size: clamp(27px, 3vw, 38px); line-height: 1.15; letter-spacing: -.025em; }
.hero-card__content > p { margin-top: 10px; color: var(--text-muted); font-size: 15px; }
.countdown-badge { display: inline-flex; align-items: center; gap: 8px; margin-top: 20px; padding: 8px 12px; border: 1px solid var(--rose-light); border-radius: 999px; background: rgba(255,255,255,.76); color: var(--text); font-size: 12px; font-weight: 650; }
.countdown-badge svg { width: 16px; color: var(--rose); }
.quick-actions { display: grid; width: min(100%, 220px); gap: 9px; flex-shrink: 0; }
.quick-action { display: flex; align-items: center; gap: 10px; min-height: 43px; padding: 10px 14px; border: 1px solid var(--border); border-radius: 10px; background: rgba(255,255,255,.82); color: var(--text); font-size: 13px; font-weight: 600; text-decoration: none; transition: .2s ease; }
.quick-action svg { width: 17px; color: var(--rose); }
.quick-action:hover { border-color: var(--rose-light); transform: translateX(3px); }
.quick-action--primary { border-color: var(--accent); background: var(--accent); color: #fff; }
.quick-action--primary:hover { border-color: var(--accent-hover); background: var(--accent-hover); }
.quick-action--primary svg { color: #fff; }
.section-heading { display: flex; align-items: end; justify-content: space-between; gap: 20px; margin-bottom: 14px; }
.section-heading h2 { font-size: 20px; letter-spacing: -.02em; }
.section-heading > p { color: var(--text-muted); font-size: 13px; }
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
.icon-box--sage { background: var(--accent-soft); color: var(--accent-hover); }.icon-box--sand { background: #eef1dc; color: #70794b; }.icon-box--stone { background: #edf1e8; color: #64705f; }.icon-box--fern { background: #deebd8; color: #4f7748; }.icon-box--mist { background: #e7efe2; color: #5d7955; }
.stat-card__body { margin-top: 20px; }
.stat-card__value { color: var(--text); font-size: 28px; font-weight: 800; line-height: 1; letter-spacing: -.035em; }
.stat-card__value span { margin-left: 2px; color: var(--text-dim); font-size: 15px; font-weight: 600; }
.stat-card__label { margin-top: 7px; color: var(--text-muted); font-size: 12px; font-weight: 600; }
.progress-block { margin-top: auto; padding-top: 17px; }
.progress-meta { display: flex; justify-content: space-between; gap: 12px; margin-bottom: 7px; color: var(--text-muted); font-size: 11.5px; }
.progress-meta strong { color: var(--text); font-weight: 700; }
.progress-track { height: 6px; border-radius: 999px; background: #e9eee7; overflow: hidden; }
.progress-track > span { display: block; height: 100%; border-radius: inherit; background: linear-gradient(90deg, var(--rose), #a8b9a5); transition: width .35s ease; }
.overview-grid, .details-grid { display: grid; grid-template-columns: minmax(0, .9fr) minmax(0, 1.4fr); gap: 16px; align-items: stretch; }
.details-grid { grid-template-columns: minmax(0, 1.25fr) minmax(300px, .75fr); }
.details-grid--bottom { grid-template-columns: 1fr 1fr; }
.panel { min-width: 0; overflow: hidden; }
.panel__header { display: flex; min-height: 80px; padding: 18px 20px; align-items: center; justify-content: space-between; gap: 12px; border-bottom: 1px solid var(--border); }
.panel__title { display: flex; align-items: center; gap: 12px; min-width: 0; }
.panel__title div > span { margin-bottom: 3px; font-size: 9px; }
.panel__title h2 { font-size: 15px; line-height: 1.25; letter-spacing: -.01em; }
.text-link { display: inline-flex; align-items: center; gap: 5px; padding: 7px; color: var(--text-muted); font-size: 11.5px; font-weight: 650; text-decoration: none; white-space: nowrap; }
.text-link:hover { color: var(--rose); }.text-link svg { width: 14px; }
.detail-list { display: grid; padding: 7px 20px 14px; }
.detail-list > div { display: grid; grid-template-columns: minmax(105px, .8fr) 1.2fr; gap: 12px; padding: 13px 0; border-bottom: 1px solid #edf1eb; }
.detail-list > div:last-child { border-bottom: 0; }
.detail-list dt { display: flex; align-items: center; gap: 8px; color: var(--text-muted); font-size: 12px; }.detail-list dt svg { width: 15px; color: var(--rose); }
.detail-list dd { color: var(--text); font-size: 12.5px; font-weight: 650; text-align: right; overflow-wrap: anywhere; }
.guest-stats { display: grid; grid-template-columns: repeat(4, 1fr); padding: 22px 20px 12px; }
.guest-stats > div { padding: 5px 12px; border-right: 1px solid var(--border); text-align: center; }.guest-stats > div:last-child { border: 0; }
.guest-stats strong, .guest-stats span { display: block; }.guest-stats strong { font-size: 24px; letter-spacing: -.03em; }.guest-stats span { margin-top: 5px; color: var(--text-muted); font-size: 10.5px; }
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
.checklist-item strong, .checklist-item span { display: block; }.checklist-item strong { font-size: 12.5px; }.checklist-item span { margin-top: 4px; color: var(--text-muted); font-size: 11px; }
.empty-state { display: flex; min-height: 180px; padding: 24px; flex-direction: column; align-items: center; justify-content: center; color: var(--text-muted); text-align: center; }.empty-state svg { width: 25px; margin-bottom: 10px; color: var(--rose); }.empty-state strong { color: var(--text); font-size: 13px; }.empty-state span { margin-top: 5px; font-size: 11.5px; }
@media (max-width: 1100px) { .stats-grid { grid-template-columns: repeat(2, 1fr); }.overview-grid, .details-grid, .details-grid--bottom { grid-template-columns: 1fr; } }
@media (max-width: 767px) { .dashboard { gap: 22px; }.hero-card { min-height: auto; padding: 26px 20px; flex-direction: column; align-items: stretch; }.quick-actions { width: 100%; grid-template-columns: repeat(3, 1fr); }.quick-action { justify-content: center; padding-inline: 8px; }.quick-action span { font-size: 11px; }.section-heading { align-items: start; flex-direction: column; gap: 4px; }.stats-grid { gap: 10px; }.stat-card { min-height: 190px; padding: 16px; }.stat-card__value { font-size: 24px; }.guest-stats { grid-template-columns: repeat(2, 1fr); gap: 12px; }.guest-stats > div { border: 0; }.budget-figures { grid-template-columns: 1fr; gap: 14px; }.budget-figures > div, .budget-figures > div:first-child, .budget-figures > div:last-child { padding: 0 0 14px; border: 0; border-bottom: 1px solid var(--border); }.budget-figures > div:last-child { border: 0; padding-bottom: 0; } }
@media (max-width: 430px) { .quick-actions { grid-template-columns: 1fr; }.stats-grid { grid-template-columns: 1fr; }.stat-card { min-height: 182px; }.panel__header { align-items: flex-start; }.detail-list > div { grid-template-columns: 1fr; gap: 7px; }.detail-list dd { text-align: left; padding-left: 23px; } }
</style>
