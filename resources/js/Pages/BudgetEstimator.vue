<script setup>
import { computed, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import PublicFooter from '@/Components/PublicFooter.vue';
import PublicHeader from '@/Components/PublicHeader.vue';
import {
    ArrowRight,
    BadgeCheck,
    ChevronDown,
    Info,
    MapPin,
    RotateCcw,
    UsersRound,
    WalletCards,
} from '@lucide/vue';

const concepts = [
    { value: 'akad', label: 'Akad saja', factor: 0.65 },
    { value: 'intimate', label: 'Intimate Wedding', factor: 0.85 },
    { value: 'resepsi', label: 'Akad + Resepsi', factor: 1 },
    { value: 'besar', label: 'Resepsi besar', factor: 1.35 },
    { value: 'belum', label: 'Belum menentukan', factor: 1 },
];

const vendorClasses = [
    { value: 'hemat', label: 'Hemat', factor: 0.85 },
    { value: 'standar', label: 'Standar', factor: 1 },
    { value: 'premium', label: 'Premium', factor: 1.3 },
    { value: 'belum', label: 'Belum menentukan', factor: 1 },
];

const venues = [
    { value: 'rumah', label: 'Rumah', factor: 0.85 },
    { value: 'gedung', label: 'Gedung', factor: 1 },
    { value: 'restoran', label: 'Restoran', factor: 0.95 },
    { value: 'hotel', label: 'Hotel', factor: 1.25 },
    { value: 'outdoor', label: 'Outdoor', factor: 1.15 },
    { value: 'belum', label: 'Belum menentukan', factor: 1 },
];

const locations = [
    { value: 'bandung', label: 'Bandung', factor: 1.08 },
    { value: 'jakarta', label: 'Jakarta', factor: 1.25 },
    { value: 'makassar', label: 'Makassar', factor: 1.08 },
    { value: 'medan', label: 'Medan', factor: 1 },
    { value: 'semarang', label: 'Semarang', factor: 1 },
    { value: 'surabaya', label: 'Surabaya', factor: 1.12 },
    { value: 'yogyakarta', label: 'Yogyakarta', factor: 0.95 },
    { value: 'lainnya', label: 'Kota lainnya', factor: 1 },
];

const form = ref({ concept: '', vendorClass: '', venue: '', location: '', budget: '', guests: '' });
const result = ref(null);
const calculating = ref(false);

const selectedConcept = computed(() => concepts.find(item => item.value === form.value.concept));
const selectedVendorClass = computed(() => vendorClasses.find(item => item.value === form.value.vendorClass));
const selectedVenue = computed(() => venues.find(item => item.value === form.value.venue));
const selectedLocation = computed(() => locations.find(item => item.value === form.value.location));
const completedFields = computed(() => [form.value.concept, form.value.vendorClass, form.value.venue, form.value.location, form.value.budget].filter(Boolean).length);
const formProgress = computed(() => Math.round(completedFields.value / 5 * 100));
const formReady = computed(() => completedFields.value === 5);
function formatInput(event, field) {
    const value = event.target.value.replace(/\D/g, '');
    form.value[field] = value;
    event.target.value = value ? Number(value).toLocaleString('id-ID') : '';
}

function rupiah(value) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(Math.round(value / 100000) * 100000);
}

function shortRupiah(value) {
    const millions = value / 1000000;
    return `Rp${Number.isInteger(millions) ? millions : millions.toFixed(1).replace('.', ',')} juta`;
}

async function calculate() {
    if (!formReady.value || calculating.value) return;

    calculating.value = true;
    await new Promise(resolve => setTimeout(resolve, 650));

    const guestCount = Number(form.value.guests) || 200;
    const availableBudget = Number(form.value.budget);
    const conceptFactor = selectedConcept.value.factor;
    const vendorFactor = selectedVendorClass.value.factor;
    const venueFactor = selectedVenue.value.factor;
    const locationFactor = selectedLocation.value.factor;
    const baseEventCost = guestCount * 115000 + 14000000;
    const baseTotal = Math.max(18000000, baseEventCost * conceptFactor * locationFactor * (0.65 + vendorFactor * 0.35) * (0.7 + venueFactor * 0.3));

    const allocations = [
        { name: 'Catering', percent: 38 },
        { name: 'Venue', percent: 13 },
        { name: 'Dekorasi', percent: 12 },
        { name: 'MUA & Busana', percent: 12 },
        { name: 'Dokumentasi', percent: 9 },
        { name: 'Undangan', percent: 4 },
        { name: 'Seserahan', percent: 5 },
        { name: 'Lain-lain', percent: 7 },
    ];

    const low = baseTotal * 0.9;
    const high = baseTotal * 1.12;
    const midpoint = (low + high) / 2;

    result.value = {
        low,
        high,
        midpoint,
        availableBudget,
        guestCount,
        breakdown: allocations.map(item => ({
            ...item,
            amount: midpoint * item.percent / 100,
        })),
    };

    calculating.value = false;
    requestAnimationFrame(() => document.getElementById('hasil-estimasi')?.scrollIntoView({ behavior: 'smooth' }));
}

function reset() {
    form.value = { concept: '', vendorClass: '', venue: '', location: '', budget: '', guests: '' };
    result.value = null;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

const gapStatus = computed(() => {
    if (!result.value) return null;
    const { availableBudget, low, high } = result.value;
    if (availableBudget >= high) return { type: 'enough', label: 'Budgetmu berada di atas estimasi kebutuhan', value: availableBudget - high };
    if (availableBudget >= low) return { type: 'close', label: 'Budgetmu berada dalam rentang estimasi', value: 0 };
    return { type: 'gap', label: 'Perkiraan budget yang masih perlu disiapkan', valueLow: low - availableBudget, valueHigh: high - availableBudget };
});
</script>

<template>
    <Head>
        <title>Budget Estimator Nikah Gratis</title>
        <meta name="description" content="Hitung gambaran awal kebutuhan budget pernikahan berdasarkan konsep, lokasi, dan jumlah tamu secara gratis." />
    </Head>

    <div class="estimator-page">
        <PublicHeader estimator :estimator-result="Boolean(result)" />

        <main>
            <section v-if="!result" class="estimator-hero">
                <div class="decor decor-one"></div><div class="decor decor-two"></div>
                <div class="container intro">
                    <h1>Kira-kira, berapa budget nikah yang kamu butuhkan?</h1>
                    <p>Isi beberapa informasi di bawah. Kami akan memberikan gambaran awal kebutuhan biaya pernikahanmu.</p>
                </div>
            </section>

            <section v-if="!result" class="form-section">
                <form class="container estimator-form" @submit.prevent="calculate">
                    <div class="form-heading">
                        <span>01</span>
                        <div><h2>Ceritakan rencana pernikahanmu</h2><p>Tidak perlu data pasangan atau tanggal pernikahan.</p></div>
                        <div class="form-progress" :class="{ complete: formReady }">
                            <div><span>Kelengkapan</span><strong>{{ completedFields }}/5</strong></div>
                            <div class="progress-track"><span :style="{ width: `${formProgress}%` }"></span></div>
                        </div>
                    </div>

                    <div class="estimator-fields-layout">
                        <fieldset>
                            <legend>Konsep pernikahan <b>*</b></legend>
                            <div class="concept-grid">
                                <label v-for="item in concepts" :key="item.value" :class="{ selected: form.concept === item.value }">
                                    <input v-model="form.concept" type="radio" name="concept" :value="item.value" required />
                                    <span>{{ item.label }}</span>
                                    <BadgeCheck v-if="form.concept === item.value" class="concept-check" aria-hidden="true" />
                                </label>
                            </div>
                        </fieldset>

                        <fieldset>
                            <legend>Venue pernikahan <b>*</b></legend>
                            <div class="compact-choices">
                                <label v-for="item in venues" :key="item.value" :class="{ selected: form.venue === item.value }"><input v-model="form.venue" type="radio" name="venue" :value="item.value" required /><span>{{ item.label }}</span><BadgeCheck v-if="form.venue === item.value" /></label>
                            </div>
                        </fieldset>

                        <fieldset>
                            <legend>Preferensi vendor <b>*</b></legend>
                            <div class="compact-choices">
                                <label v-for="item in vendorClasses" :key="item.value" :class="{ selected: form.vendorClass === item.value }"><input v-model="form.vendorClass" type="radio" name="vendorClass" :value="item.value" required /><span>{{ item.label }}</span><BadgeCheck v-if="form.vendorClass === item.value" /></label>
                            </div>
                        </fieldset>

                        <label class="field" :class="{ filled: form.location }"><span>Lokasi pernikahan <b>*</b><BadgeCheck v-if="form.location" /></span><div class="input-wrap"><MapPin /><select v-model="form.location" required><option value="" disabled>Pilih kota atau wilayah</option><option v-for="item in locations" :key="item.value" :value="item.value">{{ item.label }}</option></select><ChevronDown class="select-chevron" /></div><small class="field-note">Lokasi memengaruhi kisaran biaya vendor.</small></label>

                        <label class="field" :class="{ filled: form.budget }"><span>Budget yang tersedia <b>*</b><BadgeCheck v-if="form.budget" /></span><div class="input-wrap"><WalletCards /><span class="currency">Rp</span><input :value="form.budget ? Number(form.budget).toLocaleString('id-ID') : ''" type="text" inputmode="numeric" placeholder="30.000.000" required @input="formatInput($event, 'budget')" /></div><small class="field-note">Masukkan dana yang sudah disiapkan untuk melihat kekurangan atau kelebihan budget.</small></label>

                        <label class="field" :class="{ filled: form.guests }"><span>Jumlah tamu <BadgeCheck v-if="form.guests" /></span><div class="input-wrap"><UsersRound /><input :value="form.guests ? Number(form.guests).toLocaleString('id-ID') : ''" type="text" inputmode="numeric" placeholder="Contoh: 300" @input="formatInput($event, 'guests')" /><span class="suffix">orang</span></div><small class="field-note">Boleh dikosongkan jika jumlah tamu belum diketahui.</small></label>
                    </div>

                    <button type="submit" class="primary-button submit-button" :class="{ ready: formReady }" :disabled="calculating">
                        <LoaderCircle v-if="calculating" class="loading-icon" />
                        <span>{{ calculating ? 'Menghitung estimasi...' : (formReady ? 'Semua siap, lihat estimasi' : `Lengkapi data (${completedFields}/5)`) }}</span>
                        <ArrowRight v-if="!calculating" />
                    </button>
                </form>
            </section>

            <section v-else id="hasil-estimasi" class="result-section">
                <div class="container result-container">
                    <div class="result-intro">
                        <h1><span>Estimasi Awal</span><span>Budget Pernikahanmu</span></h1>
                        <p>Estimasi ini disusun berdasarkan konsep <strong>{{ selectedConcept.label }}</strong>, venue <strong>{{ selectedVenue.label }}</strong>, vendor kelas <strong>{{ selectedVendorClass.label }}</strong>, dan lokasi acara di <strong>{{ selectedLocation.label }}</strong>{{ form.guests ? ` untuk sekitar ${Number(form.guests).toLocaleString('id-ID')} tamu` : ' dengan asumsi awal 200 tamu' }}. Nominalnya dapat menjadi acuan awal untuk membagi prioritas biaya sebelum membandingkan penawaran vendor yang sebenarnya.</p>
                    </div>

                    <div class="result-grid">
                        <article class="main-result-card">
                            <small>Estimasi kebutuhan</small>
                            <strong>{{ shortRupiah(result.low) }}–{{ shortRupiah(result.high).replace('Rp', '') }}</strong>
                            <p>Kisaran ini lebih aman digunakan daripada satu angka pasti.</p>
                            <div class="available-row"><span>Budget yang tersedia</span><b>{{ rupiah(result.availableBudget) }}</b></div>
                        </article>
                        <article class="gap-card" :class="gapStatus.type">
                            <span class="gap-icon"><WalletCards /></span>
                            <small>Perbandingan budget</small>
                            <h2 v-if="gapStatus.type === 'gap'">{{ shortRupiah(gapStatus.valueLow) }}–{{ shortRupiah(gapStatus.valueHigh).replace('Rp', '') }}</h2>
                            <h2 v-else-if="gapStatus.type === 'enough'">+{{ shortRupiah(gapStatus.value) }}</h2>
                            <h2 v-else>Sesuai rentang</h2>
                            <p>{{ gapStatus.label }}</p>
                        </article>
                    </div>

                    <article class="breakdown-card">
                        <div class="breakdown-heading"><div><span>Perkiraan pembagian</span><h2>Breakdown kebutuhan</h2></div><span class="total-chip">Total tengah ±{{ shortRupiah(result.midpoint) }}</span></div>
                        <div class="breakdown-list">
                            <div v-for="item in result.breakdown" :key="item.name" class="breakdown-item">
                                <div class="breakdown-copy"><strong>{{ item.name }}</strong><span>{{ item.percent }}%</span></div>
                                <div class="bar"><span :style="{ width: `${item.percent * 2.2}%` }"></span></div>
                                <b>{{ rupiah(item.amount) }}</b>
                            </div>
                        </div>
                    </article>

                    <div class="disclaimer"><Info /><p><strong>Gunakan hasil ini sebagai titik awal perencanaan.</strong> Biaya sebenarnya dapat berubah mengikuti vendor, lokasi, jumlah tamu, konsep, waktu pelaksanaan, dan kebutuhan pernikahanmu.</p></div>

                    <div class="result-actions">
                        <div class="result-action-copy">
                            <span>Langkah berikutnya</span>
                            <h2>Budget sudah tergambar, sekarang saatnya mulai merencanakan.</h2>
                            <p>Masuk atau buat akun untuk menyimpan hasil, menyusun anggaran, mengatur checklist, dan memantau seluruh persiapan pernikahan dalam satu ruang.</p>
                        </div>

                        <div class="save-area">
                            <Link :href="route('register')" class="primary-button result-save-button">Mulai Rencanakan Pernikahanmu <ArrowRight /></Link>
                            <Link :href="route('login')" class="result-login-link">Sudah punya akun? Masuk</Link>
                        </div>
                    </div>

                    <button type="button" class="secondary-button recalculate-button" @click="reset"><RotateCcw /> Hitung Ulang</button>
                </div>
            </section>
        </main>

        <PublicFooter estimator />
    </div>
</template>

<style scoped>
.estimator-page{min-height:100vh;background:var(--bg);color:var(--text)}.container{width:min(920px,calc(100% - 40px));margin:0 auto}.header{position:relative;z-index:10;border-bottom:1px solid var(--border);background:rgba(255,255,255,.78);backdrop-filter:blur(16px)}.header-inner{display:flex;align-items:center;justify-content:space-between;height:72px}.brand{display:flex;align-items:center;gap:10px;color:var(--text);text-decoration:none}.brand img{width:39px}.brand span{display:flex;flex-direction:column;line-height:1.1}.brand strong{font-family:var(--font-display);font-size:16px}.brand small{margin-top:4px;color:var(--text-muted);font-size:8px;font-weight:800;letter-spacing:.1em;text-transform:uppercase}.back-link{display:flex;align-items:center;gap:7px;color:var(--text-muted);font-size:11px;font-weight:700;text-decoration:none}.back-link:hover{color:var(--accent-hover)}.back-link svg{width:15px}.estimator-hero{position:relative;overflow:hidden;padding:78px 0 135px;background:linear-gradient(145deg,#f8faf5,var(--accent-soft))}.intro{position:relative;z-index:2;max-width:760px;text-align:center}.eyebrow{display:inline-flex;align-items:center;gap:7px;margin-bottom:17px;color:var(--accent-hover);font-size:10px;font-weight:800;letter-spacing:.1em;text-transform:uppercase}.eyebrow svg{width:15px}.intro h1,.result-intro h1{margin:0;font-family:var(--font-display);font-size:clamp(39px,5vw,57px);font-weight:600;line-height:1.08;letter-spacing:-.04em}.intro>p{max-width:650px;margin:19px auto 22px;color:var(--text-muted);font-size:14px;line-height:1.75}.trust-row{display:flex;justify-content:center;flex-wrap:wrap;gap:17px;color:var(--text-muted);font-size:10px;font-weight:600}.trust-row span{display:flex;align-items:center;gap:5px}.trust-row svg{width:13px;color:var(--accent)}.decor{position:absolute;border-radius:50%}.decor-one{top:-150px;right:-80px;width:390px;height:390px;border:1px solid rgba(111,146,95,.16)}.decor-two{bottom:-240px;left:-120px;width:390px;height:390px;background:rgba(255,255,255,.35)}.form-section{position:relative;z-index:3;margin-top:-75px;padding-bottom:90px}.estimator-form{padding:38px;border:1px solid var(--border);border-radius:20px;background:#fff;box-shadow:0 25px 60px rgba(48,71,43,.11)}.form-heading{display:flex;align-items:flex-start;gap:15px;padding-bottom:25px;border-bottom:1px solid var(--border)}.form-heading>span{display:grid;place-items:center;width:34px;height:34px;border-radius:10px;background:var(--accent-soft);color:var(--accent-hover);font-size:10px;font-weight:800}.form-heading h2{margin:0;font-family:var(--font-display);font-size:24px}.form-heading p{margin:4px 0 0;color:var(--text-muted);font-size:10px}fieldset{margin:28px 0 0;padding:0;border:0}legend,.field>span{display:block;margin-bottom:10px;font-size:11px;font-weight:700}legend b,.field b{color:var(--danger-text)}.concept-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:9px}.concept-grid label{display:flex;align-items:center;gap:9px;min-height:49px;padding:0 13px;border:1px solid var(--border);border-radius:10px;cursor:pointer;color:var(--text-muted);font-size:11px;font-weight:600;transition:.2s}.concept-grid label:hover,.concept-grid label.selected{border-color:var(--accent);background:var(--accent-soft);color:var(--accent-hover)}.concept-grid input{position:absolute;opacity:0;pointer-events:none}.radio-dot{width:14px;height:14px;border:1px solid var(--border-strong);border-radius:50%;background:#fff}.selected .radio-dot{border:4px solid var(--accent)}.field em{float:right;padding:2px 7px;border-radius:99px;background:var(--surface-soft);color:var(--text-muted);font-size:8px;font-style:normal}.input-wrap{position:relative;display:flex;align-items:center}.input-wrap>svg:first-child{position:absolute;z-index:2;left:13px;width:17px;color:var(--accent)}.input-wrap input,.input-wrap select{width:100%;height:48px;padding:0 38px 0 41px;border:1px solid var(--border);border-radius:10px;outline:0;appearance:none;font-size:12px}.input-wrap select{padding-right:38px}.input-wrap input:focus,.input-wrap select:focus{border-color:var(--accent)}.currency{position:absolute;left:41px;color:var(--text-muted);font-size:11px}.currency+input{padding-left:64px}.select-chevron{position:absolute!important;z-index:3;right:13px!important;left:auto!important;width:15px!important;height:15px;color:var(--ink)!important;pointer-events:none}.suffix{position:absolute;right:14px;color:var(--text-muted);font-size:10px}.field small{display:block;margin-top:7px;color:var(--text-dim);font-size:9px}.privacy-note{display:flex;align-items:flex-start;gap:9px;margin:27px 0 20px;padding:13px;border-radius:10px;background:var(--surface-soft);color:var(--text-muted);font-size:9px;line-height:1.55}.privacy-note svg{flex:none;width:15px;color:var(--accent)}.primary-button,.secondary-button{display:inline-flex;align-items:center;justify-content:center;gap:8px;min-height:49px;padding:0 20px;border:0;border-radius:10px;cursor:pointer;font-size:11px;font-weight:800;text-decoration:none}.primary-button{width:100%;background:var(--accent);color:#fff;box-shadow:0 10px 24px rgba(88,123,75,.2)}.primary-button:hover{background:var(--accent-hover)}.primary-button svg,.secondary-button svg{width:16px}.result-section{padding:70px 0 90px;background:linear-gradient(180deg,var(--accent-soft),var(--bg) 380px)}.result-intro{max-width:700px;margin:0 auto 38px;text-align:center}.result-intro>p{margin:16px 0 0;color:var(--text-muted);font-size:12px}.result-grid{display:grid;grid-template-columns:1.25fr .75fr;gap:15px}.main-result-card,.gap-card,.breakdown-card{border:1px solid var(--border);border-radius:17px;background:#fff;box-shadow:var(--shadow-sm)}.main-result-card{padding:31px}.main-result-card>small,.gap-card>small{color:var(--text-muted);font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.08em}.main-result-card>strong{display:block;margin:7px 0 5px;font-family:var(--font-display);font-size:34px}.main-result-card>p{margin:0;color:var(--text-dim);font-size:9px}.available-row{display:flex;justify-content:space-between;margin-top:24px;padding-top:17px;border-top:1px solid var(--border);font-size:11px}.gap-card{padding:25px;background:var(--accent-hover);color:#fff}.gap-card.enough{background:#53735a}.gap-card.close{background:#788f68}.gap-card>small{display:block;margin-top:16px;color:rgba(255,255,255,.65)}.gap-icon{display:grid;place-items:center;width:37px;height:37px;border-radius:11px;background:rgba(255,255,255,.14)}.gap-icon svg{width:18px}.gap-card h2{margin:6px 0 4px;font-family:var(--font-display);font-size:23px}.gap-card p{margin:0;color:rgba(255,255,255,.72);font-size:9px;line-height:1.55}.breakdown-card{margin-top:15px;padding:28px}.breakdown-heading{display:flex;align-items:center;justify-content:space-between;gap:20px;margin-bottom:24px}.breakdown-heading span{color:var(--accent-hover);font-size:8px;font-weight:800;text-transform:uppercase;letter-spacing:.08em}.breakdown-heading h2{margin:3px 0 0;font-family:var(--font-display);font-size:23px}.total-chip{padding:7px 10px;border-radius:99px;background:var(--accent-soft)}.breakdown-list{display:grid;grid-template-columns:1fr 1fr;gap:0 35px}.breakdown-item{display:grid;grid-template-columns:1fr auto;align-items:center;padding:13px 0;border-top:1px solid var(--border)}.breakdown-copy{display:flex;gap:8px;font-size:10px}.breakdown-copy span{color:var(--text-dim)}.breakdown-item>b{font-size:10px}.bar{grid-column:1/-1;height:4px;margin-top:8px;border-radius:99px;background:var(--surface-muted)}.bar span{display:block;height:100%;border-radius:inherit;background:var(--accent)}.disclaimer{display:flex;align-items:flex-start;gap:10px;margin-top:15px;padding:16px;border:1px solid var(--border);border-radius:12px;background:rgba(255,255,255,.65)}.disclaimer svg{flex:none;width:17px;color:var(--accent)}.disclaimer p{margin:0;color:var(--text-muted);font-size:9px;line-height:1.6}.result-actions{display:flex;align-items:flex-end;justify-content:space-between;gap:25px;margin-top:29px}.secondary-button{width:auto;border:1px solid var(--border);background:#fff;color:var(--text-muted)}.save-area{text-align:right}.save-area p{margin:0 0 8px;color:var(--text-muted);font-size:9px}.save-area .primary-button{width:auto}footer{padding:30px 0;background:#263324;color:#fff}footer .container{display:flex;align-items:center;gap:8px}footer svg{width:19px;color:var(--accent-light)}footer span{font-family:var(--font-display);font-size:14px}footer p{margin:0 0 0 auto;color:rgba(255,255,255,.5);font-size:9px}
@media(max-width:700px){.container{width:min(100% - 28px,920px)}.header-inner{height:64px}.back-link{font-size:0}.back-link svg{width:19px}.estimator-hero{padding:60px 0 115px}.intro h1,.result-intro h1{font-size:38px}.estimator-form{padding:25px 18px}.concept-grid{grid-template-columns:1fr 1fr}.result-section{padding-top:55px}.result-grid{grid-template-columns:1fr}.breakdown-list{grid-template-columns:1fr}.breakdown-heading{align-items:flex-start;flex-direction:column}.result-actions{align-items:stretch;flex-direction:column}.secondary-button,.save-area .primary-button{width:100%}.save-area{text-align:center}.main-result-card>strong{font-size:29px}footer .container{justify-content:center}footer p{display:none}}
@media(max-width:420px){.trust-row{gap:9px}.intro h1,.result-intro h1{font-size:34px}.main-result-card{padding:23px}.breakdown-card{padding:20px}}

/* Creative sketchbook theme from design.md */
.estimator-page{--ink:#1a3300;--yellow:#ffe95c;--paper:#fcfaf5;--line:#b6b6b6;--mint:#d5f5c2;--teal:#a8e5e5;--blush:#f6d0ff;--terracotta:#cb5521;background:var(--paper);color:var(--ink);font-family:'Inter',system-ui,sans-serif}.container{width:min(1160px,calc(100% - 40px));max-width:1200px;margin:0 auto}.header{position:fixed;z-index:50;top:16px;right:20px;left:20px;padding:0;border:0;background:transparent;backdrop-filter:none}.header-inner{height:64px;padding:8px 10px 8px 12px;border:1px solid var(--line);border-radius:16px;background:rgba(252,250,245,.96);box-shadow:0 18px 50px rgba(255,233,92,.22)}.brand{color:var(--ink)}.brand img{width:40px;background:transparent}.brand strong{font-family:'Inter',system-ui,sans-serif;font-size:17px;font-weight:800}.brand small{color:var(--ink);font-family:ui-monospace,monospace}.back-link{padding:9px 13px;border:1px solid var(--ink);border-radius:6px;color:var(--ink);font-size:12px;transition:.2s}.back-link:hover{background:var(--mint);color:var(--ink);transform:translateX(-2px)}
.estimator-hero{padding:150px 0 138px;background:var(--paper)}.eyebrow{padding:6px 9px;border-radius:6px;background:var(--yellow);color:var(--ink);font-family:ui-monospace,monospace;font-size:12px;letter-spacing:0;text-transform:none}.eyebrow svg{color:var(--ink)}.intro h1,.result-intro h1{max-width:860px;margin:auto;color:var(--ink);font-family:'Arial Black','Inter',system-ui,sans-serif;font-size:clamp(48px,6vw,72px);font-weight:900;line-height:1.03;letter-spacing:.025em}.intro>p{max-width:640px;color:var(--ink);font-size:18px;line-height:1.5}.trust-row{gap:12px;color:var(--ink);font-size:12px}.trust-row span{padding:7px 10px;border:1px solid var(--ink);border-radius:6px;background:var(--paper)}.trust-row svg{color:var(--ink)}.decor-one{border:2px dashed rgba(26,51,0,.18)}.decor-two{border:2px solid rgba(26,51,0,.12);background:transparent}
.form-section{margin-top:-104px;padding-bottom:96px}.estimator-form{padding:40px;border:1px solid var(--ink);border-radius:12px;background:var(--paper);box-shadow:none}.form-heading{align-items:center;border-color:var(--line)}.form-heading>span{flex:none;border:1px solid var(--ink);border-radius:6px;background:var(--yellow);color:var(--ink);font-family:ui-monospace,monospace}.form-heading h2{color:var(--ink);font-family:'Inter',system-ui,sans-serif;font-size:28px;font-weight:700}.form-heading p{color:rgba(26,51,0,.65);font-size:12px}.form-progress{width:180px;margin-left:auto}.form-progress>div:first-child{display:flex;justify-content:space-between;margin-bottom:7px;color:rgba(26,51,0,.6);font-size:10px}.form-progress strong{color:var(--ink)}.progress-track{height:7px;overflow:hidden;border:1px solid var(--ink);border-radius:99px;background:#e9e6de}.progress-track span{display:block;height:100%;border-radius:inherit;background:var(--teal);transition:width .35s cubic-bezier(.22,1,.36,1)}.form-progress.complete .progress-track span{background:var(--mint)}legend,.field>span{color:var(--ink);font-size:13px}legend b,.field b{color:var(--terracotta)}.field>span b{margin-left:4px}.concept-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:8px}.concept-grid label{min-height:42px;padding:0 10px;border-color:var(--line);border-radius:12px;background:#fff;color:var(--ink);font-size:11px}.concept-grid label:hover{border-color:var(--ink);background:#fff;box-shadow:3px 3px 0 rgba(26,51,0,.12);color:var(--ink);transform:translate(-2px,-2px)}.concept-grid label.selected{border-color:var(--ink);background:var(--mint);box-shadow:3px 3px 0 rgba(26,51,0,.12);color:var(--ink);transform:translate(-2px,-2px)}.radio-dot{border-color:var(--ink);background:var(--paper)}.selected .radio-dot{border-color:var(--ink);background:var(--yellow)}.field em{border-radius:6px;background:var(--blush);color:var(--ink)}.input-wrap>svg:first-child{color:var(--ink)}.input-wrap input,.input-wrap select{border-color:var(--line);border-radius:6px;background:var(--paper);color:var(--ink);font-size:13px}.input-wrap input:focus,.input-wrap select:focus{border-color:var(--ink);box-shadow:0 0 0 3px rgba(168,229,229,.55)}.currency,.suffix,.field small{color:rgba(26,51,0,.58)}.privacy-note{border:1px solid var(--ink);border-radius:6px;background:var(--mint);color:var(--ink);font-size:11px}.privacy-note svg{color:var(--ink)}.primary-button,.secondary-button{border-radius:6px;font-size:13px;transition:.2s}.primary-button{border:1px solid var(--ink);background:var(--yellow);color:var(--ink);box-shadow:4px 4px 0 var(--ink)}.primary-button:hover{background:#fff18a;box-shadow:2px 2px 0 var(--ink);transform:translate(2px,2px)}
.result-section{padding:130px 0 96px;background:var(--paper)}.result-intro{display:flex;align-items:center;flex-direction:column;width:100%;max-width:none}.result-intro h1{display:flex;align-items:center;flex-direction:column;width:100%;max-width:none;margin-right:auto;margin-left:auto;text-align:center}.result-intro h1 span{display:block;width:100%;white-space:nowrap;text-align:center}.result-intro>p{color:var(--ink);font-size:14px}.result-grid{gap:20px}.main-result-card,.gap-card,.breakdown-card{border-color:var(--ink);border-radius:12px;box-shadow:none}.main-result-card,.gap-card{position:relative;overflow:hidden;transition:transform .18s ease,box-shadow .18s ease}.main-result-card:hover,.gap-card:hover{box-shadow:5px 5px 0 var(--ink);transform:translate(-3px,-3px)}.main-result-card::after,.gap-card::after{position:absolute;right:-38px;bottom:-48px;width:145px;height:145px;border:2px dashed rgba(26,51,0,.18);border-radius:50%;content:'';pointer-events:none}.main-result-card{background:var(--mint)}.main-result-card>small,.gap-card>small{position:relative;z-index:1;display:inline-flex;width:max-content;padding:6px 9px;border:1px solid var(--ink);border-radius:6px;background:var(--paper);color:var(--ink);font-family:ui-monospace,monospace;font-size:9px;font-weight:800;letter-spacing:.06em;box-shadow:2px 2px 0 var(--ink)}.main-result-card>strong{position:relative;z-index:1;margin:18px 0 8px;color:var(--ink);font-family:'Arial Black','Inter',system-ui,sans-serif;font-size:clamp(32px,4vw,46px);line-height:1;letter-spacing:-.04em}.main-result-card>p{position:relative;z-index:1;color:rgba(26,51,0,.7);font-size:11px;font-weight:600;line-height:1.5}.available-row{position:relative;z-index:1;align-items:center;margin-top:26px;padding:15px 16px;border:1px solid var(--ink);border-radius:8px;background:rgba(252,250,245,.72);font-size:11px}.available-row b{font-size:14px;font-weight:900}.gap-card,.gap-card.enough,.gap-card.close{background:var(--teal);color:var(--ink)}.gap-card>small{margin-top:18px;color:var(--ink)}.gap-card p{position:relative;z-index:1;color:rgba(26,51,0,.7);font-size:11px;font-weight:600}.gap-icon{position:relative;z-index:1;width:44px;height:44px;border:1px solid var(--ink);border-radius:10px;background:var(--yellow);box-shadow:3px 3px 0 var(--ink)}.gap-icon svg{width:20px}.gap-card h2{position:relative;z-index:1;margin:14px 0 7px;font-family:'Arial Black','Inter',system-ui,sans-serif;font-size:30px;font-weight:900;line-height:1;letter-spacing:-.025em}.breakdown-card{background:var(--paper)}.breakdown-heading{position:relative;align-items:flex-end;margin-bottom:28px;padding:4px 4px 22px;border-bottom:2px solid var(--ink)}.breakdown-heading>div>span{display:inline-flex;width:max-content;margin-bottom:9px;padding:6px 9px;border:1px solid var(--ink);border-radius:6px;background:var(--teal);color:var(--ink);font-family:ui-monospace,monospace;font-size:10px;font-weight:800;letter-spacing:.06em;line-height:1;text-transform:uppercase;box-shadow:2px 2px 0 var(--ink)}.breakdown-heading h2{position:relative;width:max-content;margin:0;color:var(--ink);font-family:'Arial Black','Inter',system-ui,sans-serif;font-size:30px;font-weight:900;line-height:1.1;letter-spacing:-.025em}.breakdown-heading h2::after{position:absolute;z-index:-1;right:-5px;bottom:1px;left:-3px;height:9px;background:var(--mint);content:'';transform:rotate(-1deg)}.total-chip{display:inline-flex;align-items:center;min-height:38px;padding:0 13px;border:1px solid var(--ink);border-radius:8px;background:var(--yellow);color:var(--ink)!important;font-family:'Inter',system-ui,sans-serif;font-size:11px!important;font-weight:800!important;letter-spacing:0!important;text-transform:none!important;box-shadow:3px 3px 0 var(--ink)}.breakdown-list{gap:12px 16px}.breakdown-item{position:relative;grid-template-columns:minmax(0,1fr) auto;padding:15px 16px;border:1px solid var(--ink);border-radius:10px;background:#fff;overflow:hidden;transition:transform .16s ease,box-shadow .16s ease}.breakdown-item:hover{box-shadow:3px 3px 0 rgba(26,51,0,.16);transform:translate(-2px,-2px)}.breakdown-item:nth-child(4n+1){background:rgba(213,245,194,.42)}.breakdown-item:nth-child(4n+2){background:rgba(168,229,229,.38)}.breakdown-item:nth-child(4n+3){background:rgba(219,232,255,.5)}.breakdown-item:nth-child(4n+4){background:rgba(246,208,255,.36)}.breakdown-copy{align-items:center;justify-content:space-between;gap:10px}.breakdown-copy strong{font-size:12px;font-weight:800}.breakdown-copy span{display:grid;place-items:center;flex:none;min-width:36px;height:24px;padding:0 7px;border:1px solid var(--ink);border-radius:99px;background:var(--paper);color:var(--ink);font-size:10px;font-weight:800}.breakdown-item>b{align-self:start;margin-left:12px;font-size:11px;font-weight:800}.bar{position:relative;grid-column:1/-1;height:10px;margin-top:12px;border:1px solid rgba(26,51,0,.45);border-radius:99px;background:rgba(252,250,245,.8);overflow:hidden}.bar span{display:block;height:100%;border-radius:inherit;background:var(--mint);box-shadow:inset -1px 0 0 rgba(26,51,0,.25);transition:width .7s cubic-bezier(.22,1,.36,1)}.breakdown-item:nth-child(4n+2) .bar span{background:var(--teal)}.breakdown-item:nth-child(4n+3) .bar span{background:#9dbcea}.breakdown-item:nth-child(4n+4) .bar span{background:#df9ded}.disclaimer{align-items:center;margin-top:20px;padding:17px 19px;border-color:var(--ink);border-radius:10px;background:var(--blush);box-shadow:3px 3px 0 rgba(26,51,0,.16)}.disclaimer svg{width:22px;height:22px}.disclaimer svg,.disclaimer p{color:var(--ink)}.disclaimer p{font-size:11px;line-height:1.65}.result-actions{position:relative;display:grid;align-items:center;grid-template-columns:minmax(0,1fr) auto;gap:42px;margin-top:24px;padding:32px 34px;border:1px solid var(--ink);border-radius:12px;background:var(--mint);box-shadow:6px 6px 0 var(--ink);overflow:hidden}.result-actions::after{position:absolute;right:-55px;bottom:-70px;width:180px;height:180px;border:2px dashed rgba(26,51,0,.18);border-radius:50%;content:'';pointer-events:none}.result-action-copy{position:relative;z-index:1;max-width:650px}.result-action-copy>span{display:inline-flex;padding:6px 9px;border:1px solid var(--ink);border-radius:6px;background:var(--teal);font-family:ui-monospace,monospace;font-size:9px;font-weight:800;letter-spacing:.07em;text-transform:uppercase;box-shadow:2px 2px 0 var(--ink)}.result-action-copy h2{max-width:590px;margin:14px 0 10px;color:var(--ink);font-family:'Arial Black','Inter',system-ui,sans-serif;font-size:clamp(24px,3vw,35px);font-weight:900;line-height:1.12;letter-spacing:-.025em}.result-action-copy p{max-width:620px;margin:0;color:rgba(26,51,0,.72);font-size:12px;font-weight:550;line-height:1.65}.save-area{position:relative;z-index:1;display:flex;align-items:center;flex-direction:column;min-width:310px;text-align:center}.save-area .result-save-button{width:100%;min-height:58px;padding:0 24px;background:var(--yellow);font-size:14px;box-shadow:5px 5px 0 var(--ink)}.save-area .result-save-button:hover{background:#fff18a;box-shadow:2px 2px 0 var(--ink)}.result-save-button svg{transition:transform .18s ease}.result-save-button:hover svg{transform:translateX(5px)}.result-login-link{margin-top:15px;color:var(--ink);font-size:11px;font-weight:800;text-underline-offset:4px}.result-login-link:hover{text-decoration-thickness:2px}.secondary-button{border-color:var(--ink);background:var(--paper);color:var(--ink)}.secondary-button:hover{background:var(--mint);transform:translateY(-2px)}.recalculate-button{margin-top:28px}.save-area p{color:rgba(26,51,0,.65)}footer{border-top:1px solid var(--ink);background:var(--mint);color:var(--ink)}footer svg{color:var(--ink)}footer span{font-family:'Inter',system-ui,sans-serif;font-weight:800}footer p{color:rgba(26,51,0,.62)}
.desktop-nav{display:flex;align-items:center;gap:32px}.desktop-nav a,.login-link{color:var(--ink);font-size:14px;font-weight:500;text-decoration:none}.desktop-nav a:hover,.login-link:hover{background:linear-gradient(transparent 56%,var(--yellow) 56%)}.header-actions{display:flex;align-items:center;gap:18px}.header-button{display:inline-flex;align-items:center;justify-content:center;gap:7px;min-height:40px;padding:0 16px;border:1px solid var(--ink);border-radius:6px;font-size:13px;font-weight:600;text-decoration:none}.header-button svg{width:15px}.header-button-primary{background:var(--ink);color:var(--paper)}.header-button-ghost{background:transparent;color:var(--ink)}.site-footer{padding:74px 0 24px;border-top:1px solid var(--ink);background:linear-gradient(135deg,var(--mint) 0%,#ccebea 52%,#dcebc7 100%);color:var(--ink)}.footer-main{display:grid;grid-template-columns:minmax(0,1.3fr) minmax(360px,.7fr);gap:100px}.site-footer .brand{color:var(--ink)}.site-footer .brand img{width:40px;background:transparent}.site-footer .brand strong{font-size:20px}.site-footer .brand small{color:rgba(26,51,0,.6)}.footer-brand>p{max-width:420px;margin:22px 0 25px;color:rgba(26,51,0,.72);font-size:14px;line-height:1.7}.footer-estimator{display:inline-flex;align-items:center;gap:8px;padding:8px 11px;border:1px solid var(--ink);border-radius:6px;background:var(--yellow);color:var(--ink);cursor:pointer;font-size:13px;font-weight:700;transition:.2s}.footer-estimator:hover{gap:13px;transform:translateY(-2px)}.footer-estimator svg{width:15px}.footer-links{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:55px}.footer-links>div{display:flex;flex-direction:column;gap:13px}.footer-links strong{width:max-content;margin-bottom:6px;padding:4px 7px;border-radius:4px;background:var(--yellow);font-family:ui-monospace,monospace;font-size:11px;letter-spacing:.08em;text-transform:uppercase}.footer-links a,.footer-links button{width:max-content;padding:0;border:0;background:transparent;color:rgba(26,51,0,.7);cursor:pointer;font:inherit;font-size:13px;text-decoration:none;transition:.2s}.footer-links a:hover,.footer-links button:hover{color:var(--ink);transform:translateX(3px)}.footer-bottom{display:grid!important;grid-template-columns:1fr auto 1fr;align-items:center!important;gap:28px!important;margin-top:62px!important;padding-top:22px;border-top:1px solid rgba(26,51,0,.25);color:rgba(26,51,0,.58);font-size:10px}.footer-bottom span:nth-child(2){text-align:center}.footer-bottom a{justify-self:end;color:rgba(26,51,0,.72);font-weight:600;text-decoration:none}.footer-bottom a:hover{background:linear-gradient(transparent 55%,var(--yellow) 55%)}
.estimator-fields-layout{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));align-items:start;gap:28px 24px;margin-top:28px}.estimator-fields-layout fieldset{min-width:0;margin:0}.estimator-fields-layout .field{min-width:0}.compact-choices{display:grid;grid-template-columns:repeat(2,1fr);gap:8px}.compact-choices label{display:flex;align-items:center;gap:7px;min-height:42px;padding:0 10px;border:1px solid var(--line);border-radius:6px;background:#fff;color:var(--ink);cursor:pointer;font-size:11px;font-weight:600;transition:.2s}.compact-choices label:hover{border-color:var(--ink);box-shadow:3px 3px 0 rgba(26,51,0,.12);transform:translate(-2px,-2px)}.compact-choices label.selected{border-color:var(--ink);background:var(--mint);box-shadow:3px 3px 0 rgba(26,51,0,.12);transform:translate(-2px,-2px)}.compact-choices label.selected:nth-child(2){background:var(--teal)}.compact-choices label.selected:nth-child(3){background:#dbe8ff}.compact-choices label.selected:nth-child(4){background:var(--yellow)}.compact-choices label.selected:nth-child(5){background:var(--blush)}.compact-choices label.selected:nth-child(6){background:#f3dfcf}.compact-choices input{position:absolute;opacity:0;pointer-events:none}.compact-choices svg{width:14px;margin-left:auto}.location-field{position:relative}.location-dropdown{position:absolute;z-index:20;top:76px;right:0;left:0;max-height:250px;overflow:auto;padding:7px;border:1px solid var(--ink);border-radius:8px;background:#fff;box-shadow:5px 5px 0 rgba(26,51,0,.12)}.location-dropdown button{display:flex;align-items:center;gap:9px;width:100%;padding:10px;border:0;border-radius:5px;background:transparent;color:var(--ink);cursor:pointer;font:inherit;font-size:12px;text-align:left}.location-dropdown button:hover,.location-dropdown button.active{background:var(--mint)}.location-dropdown button svg{flex:none;width:14px}.location-dropdown button svg:last-child{margin-left:auto}.location-dropdown p{margin:0;padding:15px;color:rgba(26,51,0,.58);font-size:12px;text-align:center}.concept-number{display:grid;place-items:center;flex:none;width:28px;height:28px;border:1px solid rgba(26,51,0,.35);border-radius:6px;background:rgba(252,250,245,.7);font-family:ui-monospace,monospace;font-size:9px;font-weight:800}.concept-check{width:17px;margin-left:auto;color:var(--ink)}.concept-grid label.selected:nth-child(2){background:var(--teal)}.concept-grid label.selected:nth-child(3){background:#dbe8ff}.concept-grid label.selected:nth-child(4){background:var(--yellow)}.concept-grid label.selected:nth-child(5){background:var(--blush)}.field>span{display:flex;align-items:center}.field>span svg{width:15px;margin-left:auto;color:#4d7a42}.field.filled .input-wrap input,.field.filled .input-wrap select{border-color:#4d7a42;background:rgba(213,245,194,.28)}.input-wrap .select-chevron{position:absolute;z-index:4;right:13px;left:auto;width:16px;height:16px;color:var(--ink);pointer-events:none}.field .field-help{display:flex;align-items:flex-start;gap:7px;min-height:32px;margin-top:9px;padding:7px 9px;border-left:3px solid #7ba66f;border-radius:0 5px 5px 0;background:rgba(213,245,194,.34);color:#52654d;font-size:10px;font-weight:550;line-height:1.55}.field .field-help svg{flex:none;width:13px;height:13px;margin-top:1px;color:#4d7a42}.field .field-note{display:block;margin-top:9px;color:#52654d;font-size:12px;font-weight:500;line-height:1.55}.compact-choices label,.input-wrap input,.input-wrap select,.primary-button{border-radius:12px}.submit-button{margin-top:30px}.submit-button:disabled{cursor:wait;opacity:.82}.loading-icon{animation:spin .8s linear infinite}.primary-button:not(.ready){background:#60705b}.save-area .primary-button.result-save-button,.save-area .primary-button.result-save-button:not(.ready){background:var(--yellow);color:var(--ink)}.save-area .primary-button.result-save-button:hover{background:#fff18a}.primary-button.ready{box-shadow:4px 4px 0 var(--yellow);animation:buttonReady .4s ease both}.primary-button.ready svg{animation:arrowNudge 1.3s ease-in-out infinite}@keyframes spin{to{transform:rotate(360deg)}}@keyframes buttonReady{from{transform:scale(.985)}to{transform:scale(1)}}@keyframes arrowNudge{50%{transform:translateX(4px)}}
@media(max-width:900px){.result-actions{grid-template-columns:1fr;gap:26px}.save-area{align-items:flex-start;min-width:0;width:100%;text-align:left}.save-area .result-save-button{width:auto}.desktop-nav{display:none}.footer-main{grid-template-columns:1fr;gap:52px}.footer-links{max-width:460px}.footer-bottom{grid-template-columns:1fr auto}.footer-bottom span:nth-child(2){display:none}}@media(max-width:700px){.container{width:min(100% - 28px,1160px)}.estimator-hero{padding:125px 0 120px}.intro h1,.result-intro h1{font-size:40px}.intro>p{font-size:16px}.estimator-form{padding:28px 20px}.concept-grid{grid-template-columns:1fr 1fr}.form-heading{align-items:flex-start;flex-wrap:wrap}.form-heading h2{font-size:23px}.form-progress{width:100%;margin:8px 0 0}.estimator-fields-layout{grid-template-columns:1fr}.result-grid{grid-template-columns:1fr}}@media(max-width:420px){.intro h1,.result-intro h1{font-size:35px}.trust-row span{width:100%;justify-content:center}.breakdown-heading{align-items:flex-start}.breakdown-heading h2{width:auto;font-size:24px}.total-chip{min-height:34px}.result-actions{padding:25px 20px}.result-action-copy h2{font-size:24px}.save-area{align-items:stretch;text-align:center}.save-area .result-save-button{width:100%;padding:0 16px;font-size:12px}}

/* Exact shared header and footer alignment with landing page */
.header .brand strong{font-family:'Inter',system-ui,sans-serif;font-size:18px;font-weight:800}.header .brand small{margin-top:4px;color:var(--ink);font-family:ui-monospace,monospace;font-size:8px;font-weight:700;letter-spacing:.12em;text-transform:uppercase}.header-inner{gap:28px}.header-actions{gap:18px}.header-button{min-height:42px;padding:0 18px;font-family:'Inter',system-ui,sans-serif;font-size:14px;font-weight:600}.login-link{font-family:'Inter',system-ui,sans-serif;font-size:14px;font-weight:500}
.site-footer .footer-main{display:grid;align-items:start;grid-template-columns:minmax(0,1.3fr) minmax(360px,.7fr);gap:100px}.site-footer .footer-brand{display:block}.site-footer .footer-brand .brand{display:flex}.site-footer .footer-brand>p{display:block;max-width:420px;margin:22px 0 25px;color:rgba(26,51,0,.72);font-family:'Inter',system-ui,sans-serif;font-size:14px;line-height:1.7}.site-footer .footer-links{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:55px}.site-footer .footer-links>div{display:flex;align-items:flex-start;flex-direction:column;gap:13px;min-width:0}.site-footer .footer-links strong{margin-bottom:6px}.site-footer .footer-links a,.site-footer .footer-links button{margin:0;font-family:'Inter',system-ui,sans-serif;font-size:13px;line-height:1.2}.site-footer .footer-bottom{display:grid!important;grid-template-columns:1fr auto 1fr;align-items:center!important;justify-content:initial;gap:28px!important;margin-top:62px!important;padding-top:22px}.site-footer .footer-bottom span{font-family:'Inter',system-ui,sans-serif;font-size:10px;font-weight:400}.site-footer .footer-bottom span:nth-child(2){text-align:center}.site-footer .footer-bottom a{justify-self:end;font-family:'Inter',system-ui,sans-serif;font-size:10px}
@media(max-width:900px){.site-footer .footer-main{grid-template-columns:1fr;gap:52px}.site-footer .footer-links{max-width:460px}.site-footer .footer-bottom{grid-template-columns:1fr auto}.site-footer .footer-bottom span:nth-child(2){display:none}}@media(max-width:600px){.header{top:8px;right:8px;left:8px;padding:0}.header-inner{height:58px}.header-actions{gap:10px}.login-link{display:none}.header-button{min-height:38px;padding:0 13px;font-size:11px}.site-footer{padding-top:58px}.site-footer .footer-main{gap:44px}.site-footer .footer-links{width:100%;gap:28px}.site-footer .footer-bottom{grid-template-columns:1fr;gap:10px!important;margin-top:46px!important}.site-footer .footer-bottom a{justify-self:start}}

/* Page entrance animations */
.header-inner{animation:estimatorSlideDown .55s cubic-bezier(.22,1,.36,1) both}.intro h1{animation:estimatorRise .7s .08s cubic-bezier(.22,1,.36,1) both}.intro>p{animation:estimatorRise .7s .18s cubic-bezier(.22,1,.36,1) both}.decor-one{animation:estimatorDecor 1s .12s ease-out both}.decor-two{animation:estimatorDecor 1s .22s ease-out both}.estimator-form{animation:estimatorRise .75s .28s cubic-bezier(.22,1,.36,1) both}.form-heading{animation:estimatorRise .55s .42s cubic-bezier(.22,1,.36,1) both}.estimator-fields-layout>*{animation:estimatorRise .55s cubic-bezier(.22,1,.36,1) both}.estimator-fields-layout>*:nth-child(1){animation-delay:.5s}.estimator-fields-layout>*:nth-child(2){animation-delay:.57s}.estimator-fields-layout>*:nth-child(3){animation-delay:.64s}.estimator-fields-layout>*:nth-child(4){animation-delay:.71s}.estimator-fields-layout>*:nth-child(5){animation-delay:.78s}.estimator-fields-layout>*:nth-child(6){animation-delay:.85s}.submit-button{animation:estimatorRise .55s .92s cubic-bezier(.22,1,.36,1) both}.result-intro{animation:estimatorRise .65s cubic-bezier(.22,1,.36,1) both}.result-grid{animation:estimatorRise .65s .1s cubic-bezier(.22,1,.36,1) both}.breakdown-card{animation:estimatorRise .65s .2s cubic-bezier(.22,1,.36,1) both}.disclaimer{animation:estimatorRise .65s .3s cubic-bezier(.22,1,.36,1) both}.result-actions{animation:estimatorRise .65s .4s cubic-bezier(.22,1,.36,1) both}
@keyframes estimatorRise{from{opacity:0;transform:translateY(22px)}to{opacity:1;transform:translateY(0)}}@keyframes estimatorSlideDown{from{opacity:0;transform:translateY(-16px)}to{opacity:1;transform:translateY(0)}}@keyframes estimatorDecor{from{opacity:0;transform:scale(.82) rotate(-8deg)}to{opacity:1;transform:scale(1) rotate(0)}}
@media(prefers-reduced-motion:reduce){.header-inner,.intro h1,.intro>p,.decor-one,.decor-two,.estimator-form,.form-heading,.estimator-fields-layout>*,.submit-button,.result-intro,.result-grid,.breakdown-card,.disclaimer,.result-actions{animation:none!important}.concept-grid label,.compact-choices label,.primary-button,.secondary-button{transition:none!important}}
</style>
