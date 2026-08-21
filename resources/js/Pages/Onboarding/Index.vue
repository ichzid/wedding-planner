<script setup>
import { computed, ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    ArrowLeft, ArrowRight, CalendarDays, Check, CheckCircle2, HeartHandshake,
    LoaderCircle, PenLine, Sparkles, UserRound, WalletCards
} from '@lucide/vue';
import InputError from '@/Components/InputError.vue';

const step = ref(1);
const direction = ref('forward');
const steps = [
    { number: 1, label: 'Pasangan' },
    { number: 2, label: 'Cara Memulai' },
    { number: 3, label: 'Selesaikan' },
];

const form = useForm({ nama_cpw: '', nama_cpp: '', tanggal_nikah: '', setup_type: '', budget: '' });
const stepOneValid = computed(() => Boolean(form.nama_cpw && form.nama_cpp && form.tanggal_nikah));
const canFinish = computed(() => !form.processing && (form.setup_type === 'manual' || Boolean(form.budget)));
const title = computed(() => [
    'Mari kenali hari bahagia Anda',
    'Pilih awal yang paling nyaman',
    form.setup_type === 'auto' ? 'Tentukan estimasi anggaran' : 'Semua siap untuk dimulai',
][step.value - 1]);
const description = computed(() => [
    'Informasi dasar ini membantu kami menyiapkan ruang perencanaan Anda.',
    'Mulai dengan kerangka rekomendasi atau susun semuanya sesuai cara Anda.',
    form.setup_type === 'auto' ? 'Kami akan menyiapkan rincian awal dan checklist sebagai titik mulai.' : 'Ruang pernikahan kosong akan dibuat untuk Anda susun secara bertahap.',
][step.value - 1]);

function moveTo(target) {
    if (form.processing || target === step.value) return;
    if (target > step.value && (target === 2 && !stepOneValid.value || target === 3 && !form.setup_type)) return;
    direction.value = target > step.value ? 'forward' : 'backward';
    step.value = target;
}
function next() { moveTo(step.value + 1); }
function previous() { moveTo(step.value - 1); }
function selectSetup(type) { form.setup_type = type; }
function formatBudget(event) {
    const value = event.target.value.replace(/\D/g, '');
    form.budget = value;
    event.target.value = value ? Number(value).toLocaleString('id-ID') : '';
}
function submit() {
    if (props.preview) {
        previewComplete.value = true;
        return;
    }

    form.post(route('onboarding.store'));
}
</script>

<template>
    <Head title="Siapkan Pernikahan" />
    <main class="onboarding-shell">
        <div class="ambient ambient--one" aria-hidden="true"></div>
        <div class="ambient ambient--two" aria-hidden="true"></div>

        <header class="onboarding-topbar">
            <Link href="/" class="onboarding-brand">
                <span class="brand-mark"><HeartHandshake aria-hidden="true" /></span>
                <span><strong>Ruang Pasangan</strong><small>Rencanakan Hari Bahagia</small></span>
            </Link>
            <Link v-if="preview" :href="route('login')" class="exit-button">Kembali ke Login</Link>
            <Link v-else :href="route('logout')" method="post" as="button" class="exit-button">Keluar</Link>
        </header>

        <div v-if="preview" class="preview-banner"><Sparkles aria-hidden="true" /> Mode Preview — tidak ada data yang akan disimpan</div>

        <section v-if="!previewComplete" class="wizard-layout">
            <aside class="wizard-aside">
                <p class="aside-eyebrow">Persiapan awal</p>
                <h1>Ruang kecil untuk rencana yang besar.</h1>
                <p class="aside-copy">Tiga langkah singkat sebelum Anda mulai menyusun hari bahagia bersama pasangan.</p>

                <nav class="step-list" aria-label="Tahapan onboarding">
                    <button
                        v-for="item in steps"
                        :key="item.number"
                        type="button"
                        class="step-item"
                        :class="{ active: step === item.number, complete: step > item.number }"
                        :aria-current="step === item.number ? 'step' : undefined"
                        @click="moveTo(item.number)"
                    >
                        <span class="step-number"><Check v-if="step > item.number" aria-hidden="true" /><template v-else>{{ item.number }}</template></span>
                        <span><small>Langkah {{ item.number }}</small><strong>{{ item.label }}</strong></span>
                    </button>
                </nav>

                <div class="aside-note">
                    <Sparkles aria-hidden="true" />
                    <p><strong>Tenang, semuanya fleksibel.</strong><span>Data dan rencana dapat diperbarui kembali dari menu Pengaturan.</span></p>
                </div>
            </aside>

            <section class="wizard-main">
                <div class="mobile-progress" aria-hidden="true">
                    <span>{{ step }} dari 3</span><div><i :style="{ width: `${step / 3 * 100}%` }"></i></div>
                </div>

                <header class="wizard-heading" :key="`heading-${step}`">
                    <p>Langkah {{ step }} dari 3</p>
                    <h2>{{ title }}</h2>
                    <span>{{ description }}</span>
                </header>

                <div v-if="form.errors.error" class="global-error" role="alert">{{ form.errors.error }}</div>

                <Transition :name="direction === 'forward' ? 'step-forward' : 'step-backward'" mode="out-in">
                    <div :key="step" class="step-content">
                        <template v-if="step === 1">
                            <div class="couple-grid">
                                <label class="field-group">
                                    <span>Calon Pengantin Wanita <b>*</b></span>
                                    <div class="field-shell"><UserRound aria-hidden="true" /><input v-model="form.nama_cpw" type="text" required autocomplete="name" placeholder="Nama panggilan" /></div>
                                    <InputError :message="form.errors.nama_cpw" />
                                </label>
                                <label class="field-group">
                                    <span>Calon Pengantin Pria <b>*</b></span>
                                    <div class="field-shell"><UserRound aria-hidden="true" /><input v-model="form.nama_cpp" type="text" required autocomplete="name" placeholder="Nama panggilan" /></div>
                                    <InputError :message="form.errors.nama_cpp" />
                                </label>
                            </div>
                            <label class="field-group field-group--date">
                                <span>Rencana Tanggal Pernikahan <b>*</b></span>
                                <div class="field-shell"><CalendarDays aria-hidden="true" /><input v-model="form.tanggal_nikah" type="date" required /></div>
                                <InputError :message="form.errors.tanggal_nikah" />
                            </label>
                        </template>

                        <template v-else-if="step === 2">
                            <div class="setup-options">
                                <button type="button" class="setup-option" :class="{ selected: form.setup_type === 'auto' }" :aria-pressed="form.setup_type === 'auto'" @click="selectSetup('auto')">
                                    <span class="option-icon"><Sparkles aria-hidden="true" /></span>
                                    <span class="option-copy"><small>Direkomendasikan</small><strong>Buatkan kerangka awal</strong><p>Dapatkan pembagian anggaran dan checklist dasar yang siap disesuaikan.</p></span>
                                    <span class="option-check"><Check aria-hidden="true" /></span>
                                </button>
                                <button type="button" class="setup-option" :class="{ selected: form.setup_type === 'manual' }" :aria-pressed="form.setup_type === 'manual'" @click="selectSetup('manual')">
                                    <span class="option-icon option-icon--plain"><PenLine aria-hidden="true" /></span>
                                    <span class="option-copy"><small>Lebih fleksibel</small><strong>Mulai dari ruang kosong</strong><p>Susun anggaran dan persiapan sendiri sesuai kebutuhan Anda.</p></span>
                                    <span class="option-check"><Check aria-hidden="true" /></span>
                                </button>
                            </div>
                        </template>

                        <template v-else>
                            <div v-if="form.setup_type === 'auto'" class="budget-panel">
                                <label class="field-group">
                                    <span>Total Estimasi Anggaran <b>*</b></span>
                                    <div class="field-shell budget-input"><WalletCards aria-hidden="true" /><em>Rp</em><input :value="form.budget ? Number(form.budget).toLocaleString('id-ID') : ''" type="text" inputmode="numeric" required placeholder="0" @input="formatBudget" /></div>
                                    <InputError :message="form.errors.budget" />
                                </label>
                                <div class="included-list">
                                    <p>Kerangka awal yang akan disiapkan</p>
                                    <span><CheckCircle2 aria-hidden="true" /> Pembagian estimasi anggaran per kategori</span>
                                    <span><CheckCircle2 aria-hidden="true" /> Checklist dasar persiapan pernikahan</span>
                                    <span><CheckCircle2 aria-hidden="true" /> Semua data dapat diedit setelah selesai</span>
                                </div>
                            </div>
                            <div v-else class="ready-state">
                                <span class="ready-icon"><HeartHandshake aria-hidden="true" /></span>
                                <p class="ready-eyebrow">Ruang Anda siap</p>
                                <h3>{{ form.nama_cpw || 'Anda' }} & {{ form.nama_cpp || 'Pasangan' }}</h3>
                                <p>Mulai dari ruang kosong dan susun setiap rencana dengan ritme Anda sendiri.</p>
                            </div>
                        </template>
                    </div>
                </Transition>

                <footer class="wizard-actions">
                    <button v-if="step > 1" type="button" class="button-secondary" :disabled="form.processing" @click="previous"><ArrowLeft aria-hidden="true" /> Kembali</button>
                    <span v-else></span>
                    <button v-if="step < 3" type="button" class="button-primary" :disabled="step === 1 ? !stepOneValid : !form.setup_type" @click="next">Lanjutkan <ArrowRight aria-hidden="true" /></button>
                    <button v-else type="button" class="button-primary" :disabled="!canFinish" @click="submit">
                        <LoaderCircle v-if="form.processing" class="spinner" aria-hidden="true" />
                        {{ form.processing ? 'Menyiapkan...' : 'Mulai Merencanakan' }}
                        <ArrowRight v-if="!form.processing" aria-hidden="true" />
                    </button>
                </footer>
            </section>
        </section>

        <section v-else class="preview-result">
            <span class="preview-result__icon"><CheckCircle2 aria-hidden="true" /></span>
            <p>Simulasi selesai</p>
            <h1>Onboarding berjalan dengan baik.</h1>
            <span>Tidak ada data yang disimpan ke database. Anda dapat mengulang wizard untuk mencoba skenario lain.</span>
            <button type="button" class="button-primary" @click="previewComplete = false; step = 1">Ulangi Preview <ArrowRight aria-hidden="true" /></button>
        </section>
    </main>
</template>

<style scoped>
.onboarding-shell{position:relative;min-height:100dvh;overflow:hidden;background:#f4f7ef;color:var(--text);font-family:var(--font)}
.ambient{position:fixed;border-radius:50%;filter:blur(2px);pointer-events:none}.ambient--one{top:-280px;right:-190px;width:620px;height:620px;background:radial-gradient(circle,rgba(141,169,129,.2),transparent 68%)}.ambient--two{bottom:-260px;left:-180px;width:540px;height:540px;background:radial-gradient(circle,rgba(196,215,184,.24),transparent 68%)}
.onboarding-topbar{position:relative;z-index:2;display:flex;align-items:center;justify-content:space-between;max-width:1280px;margin:auto;padding:25px 36px}.preview-banner{position:relative;z-index:2;display:flex;align-items:center;justify-content:center;gap:7px;width:max-content;max-width:calc(100% - 32px);margin:0 auto -20px;padding:7px 12px;border:1px solid #c7d9ba;border-radius:99px;background:rgba(255,255,255,.72);color:var(--accent);font-size:9px;font-weight:700;letter-spacing:.04em}.preview-banner svg{width:13px}.onboarding-brand{display:flex;align-items:center;gap:11px;color:var(--text);text-decoration:none}.brand-mark{display:grid;place-items:center;width:42px;height:42px;border:1px solid #c4d7b8;border-radius:12px;background:#e2edda;color:#587b4b}.brand-mark svg{width:22px}.onboarding-brand strong,.onboarding-brand small{display:block}.onboarding-brand strong{font:600 17px var(--font-display)}.onboarding-brand small{margin-top:2px;color:var(--text-dim);font-size:8.5px;font-weight:650;letter-spacing:.09em;text-transform:uppercase}.exit-button{border:0;background:transparent;color:var(--text-muted);font:600 11.5px var(--font);cursor:pointer}.exit-button:hover{color:var(--danger-text)}
.wizard-layout{position:relative;z-index:1;display:grid;grid-template-columns:minmax(280px,360px) minmax(0,720px);min-height:calc(100dvh - 92px);max-width:1120px;margin:auto;padding:38px 34px 64px;gap:clamp(50px,8vw,110px)}
.wizard-aside{display:flex;flex-direction:column;padding-top:8px}.aside-eyebrow,.wizard-heading>p,.ready-eyebrow{margin:0 0 12px;color:var(--accent);font-size:10px;font-weight:750;letter-spacing:.14em;text-transform:uppercase}.wizard-aside h1{margin:0;max-width:330px;font:600 clamp(34px,4vw,48px)/1.08 var(--font-display);letter-spacing:-.035em}.aside-copy{margin:18px 0 0;color:var(--text-muted);font-size:12.5px;line-height:1.7}.step-list{display:flex;flex-direction:column;gap:4px;margin-top:42px}.step-item{position:relative;display:flex;align-items:center;gap:13px;padding:9px 0;border:0;background:transparent;color:var(--text-dim);text-align:left;cursor:pointer}.step-item:not(:last-child)::after{content:'';position:absolute;top:41px;left:15px;width:1px;height:20px;background:var(--border)}.step-number{display:grid;place-items:center;width:30px;height:30px;border:1px solid var(--border-strong);border-radius:50%;background:var(--surface);font-size:10px;font-weight:700;transition:.25s}.step-number svg{width:14px}.step-item small,.step-item strong{display:block}.step-item small{font-size:8px;letter-spacing:.09em;text-transform:uppercase}.step-item strong{margin-top:2px;font-size:11.5px}.step-item.active,.step-item.complete{color:var(--text)}.step-item.active .step-number{border-color:var(--accent);background:var(--accent);color:#fff;box-shadow:0 0 0 5px var(--accent-soft)}.step-item.complete .step-number{border-color:var(--accent-light);background:var(--accent-soft);color:var(--accent)}.aside-note{display:flex;align-items:flex-start;gap:10px;margin-top:auto;padding:15px;border:1px solid rgba(164,188,148,.42);border-radius:13px;background:rgba(255,255,255,.52)}.aside-note>svg{width:16px;color:var(--accent)}.aside-note p{margin:0}.aside-note strong,.aside-note span{display:block}.aside-note strong{font-size:10.5px}.aside-note span{margin-top:4px;color:var(--text-muted);font-size:9.5px;line-height:1.5}
.wizard-main{align-self:center;min-width:0}.mobile-progress{display:none}.wizard-heading{animation:headingIn .4s ease both}.wizard-heading h2{margin:0;font:600 clamp(29px,3vw,40px)/1.15 var(--font-display);letter-spacing:-.035em}.wizard-heading>span{display:block;max-width:590px;margin-top:11px;color:var(--text-muted);font-size:12.5px;line-height:1.65}.global-error{margin-top:18px;padding:12px 14px;border:1px solid var(--danger-border);border-radius:10px;background:var(--danger-bg);color:var(--danger-text);font-size:11px}.step-content{min-height:282px;padding-top:31px}.couple-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}.field-group{display:flex;flex-direction:column;gap:7px}.field-group>span{font-size:11.5px;font-weight:650}.field-group b{color:var(--accent)}.field-group--date{max-width:360px;margin-top:15px}.field-shell{position:relative;display:flex;align-items:center;height:48px;border:1px solid var(--border);border-radius:11px;background:rgba(255,255,255,.78);transition:.18s}.field-shell:focus-within{border-color:var(--accent);box-shadow:0 0 0 3px var(--accent-soft);background:#fff}.field-shell>svg{position:absolute;left:14px;width:16px;color:#82937e}.field-shell input{width:100%;height:100%;box-sizing:border-box;padding:0 14px 0 41px;border:0!important;outline:0!important;background:transparent!important;color:var(--text);font:500 12.5px var(--font);box-shadow:none!important}.field-shell input::placeholder{color:#9aa797}.budget-input em{position:absolute;left:42px;color:var(--text-muted);font-size:11px;font-style:normal;font-weight:700}.budget-input input{padding-left:66px;font-size:17px;font-weight:700}
.setup-options{display:grid;grid-template-columns:1fr 1fr;gap:14px}.setup-option{position:relative;display:flex;flex-direction:column;min-height:215px;padding:21px;border:1px solid var(--border);border-radius:15px;background:rgba(255,255,255,.72);color:var(--text);text-align:left;cursor:pointer;transition:.25s}.setup-option:hover{border-color:var(--accent-light);transform:translateY(-3px);box-shadow:0 14px 34px rgba(69,94,63,.08)}.setup-option.selected{border-color:var(--accent);background:#fbfdf9;box-shadow:0 0 0 3px var(--accent-soft)}.option-icon{display:grid;place-items:center;width:42px;height:42px;border-radius:12px;background:var(--accent);color:#fff}.option-icon--plain{background:var(--surface-muted);color:var(--text-muted)}.option-icon svg{width:19px}.option-copy{margin-top:auto}.option-copy small{color:var(--accent);font-size:8.5px;font-weight:750;letter-spacing:.08em;text-transform:uppercase}.option-copy strong{display:block;margin-top:7px;font:600 17px var(--font-display)}.option-copy p{margin:7px 0 0;color:var(--text-muted);font-size:10.5px;line-height:1.6}.option-check{position:absolute;top:20px;right:20px;display:grid;place-items:center;width:22px;height:22px;border:1px solid var(--border);border-radius:50%;color:transparent}.option-check svg{width:12px}.selected .option-check{border-color:var(--accent);background:var(--accent);color:#fff}
.budget-panel{display:grid;grid-template-columns:minmax(0,1fr) minmax(220px,.75fr);gap:18px}.included-list{display:flex;flex-direction:column;gap:10px;padding:17px;border:1px solid var(--border);border-radius:13px;background:rgba(255,255,255,.58)}.included-list p{margin:0 0 2px;font-size:10px;font-weight:700}.included-list span{display:flex;align-items:flex-start;gap:7px;color:var(--text-muted);font-size:9.5px;line-height:1.45}.included-list svg{width:14px;flex:none;color:var(--accent)}.ready-state{padding:6px 0;text-align:center}.ready-icon{display:grid;place-items:center;width:68px;height:68px;margin:0 auto 18px;border-radius:20px;background:var(--accent-soft);color:var(--accent);transform:rotate(-4deg);animation:readyFloat 3s ease-in-out infinite}.ready-icon svg{width:32px}.ready-state h3{margin:0;font:600 27px var(--font-display)}.ready-state>p:last-child{max-width:410px;margin:10px auto 0;color:var(--text-muted);font-size:11.5px;line-height:1.65}
.preview-result{position:relative;z-index:1;display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:calc(100dvh - 110px);padding:30px;text-align:center;animation:headingIn .45s ease both}.preview-result__icon{display:grid;place-items:center;width:76px;height:76px;border-radius:24px;background:var(--accent-soft);color:var(--accent)}.preview-result__icon svg{width:36px}.preview-result>p{margin:22px 0 9px;color:var(--accent);font-size:10px;font-weight:750;letter-spacing:.14em;text-transform:uppercase}.preview-result h1{margin:0;font:600 clamp(32px,5vw,48px)/1.12 var(--font-display)}.preview-result>span:not(.preview-result__icon){max-width:470px;margin:13px auto 22px;color:var(--text-muted);font-size:12px;line-height:1.65}.wizard-actions{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-top:24px;padding-top:20px;border-top:1px solid var(--border)}.button-primary,.button-secondary{display:inline-flex;align-items:center;justify-content:center;gap:8px;min-height:43px;padding:0 18px;border-radius:10px;font:650 11.5px var(--font);cursor:pointer;transition:.18s}.button-primary{border:0;background:var(--accent);color:#fff}.button-primary:hover:not(:disabled){background:var(--accent-hover);transform:translateY(-1px)}.button-secondary{border:1px solid var(--border);background:rgba(255,255,255,.6);color:var(--text-muted)}.button-secondary:hover{background:#fff;color:var(--text)}.button-primary:disabled,.button-secondary:disabled{opacity:.45;cursor:not-allowed}.button-primary svg,.button-secondary svg{width:15px}.spinner{animation:spin .7s linear infinite}
.step-forward-enter-active,.step-forward-leave-active,.step-backward-enter-active,.step-backward-leave-active{transition:.3s cubic-bezier(.22,1,.36,1)}.step-forward-enter-from{opacity:0;transform:translateX(24px)}.step-forward-leave-to{opacity:0;transform:translateX(-18px)}.step-backward-enter-from{opacity:0;transform:translateX(-24px)}.step-backward-leave-to{opacity:0;transform:translateX(18px)}
@keyframes headingIn{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:none}}@keyframes readyFloat{50%{transform:translateY(-6px) rotate(3deg)}}@keyframes spin{to{transform:rotate(360deg)}}
@media(max-width:850px){.onboarding-shell{overflow:auto}.onboarding-topbar{padding:18px 20px}.wizard-layout{display:block;min-height:auto;padding:24px 20px 50px}.wizard-aside{display:none}.wizard-main{max-width:620px;margin:auto}.mobile-progress{display:flex;align-items:center;gap:12px;margin-bottom:25px;color:var(--text-dim);font-size:9px;font-weight:700;text-transform:uppercase}.mobile-progress>div{flex:1;height:3px;overflow:hidden;border-radius:99px;background:var(--border)}.mobile-progress i{display:block;height:100%;border-radius:inherit;background:var(--accent);transition:width .35s}.step-content{min-height:0;padding-top:26px}.wizard-heading h2{font-size:30px}}
@media(max-width:560px){.onboarding-brand small{display:none}.wizard-layout{padding-inline:16px}.couple-grid,.setup-options,.budget-panel{grid-template-columns:1fr}.field-group--date{max-width:none}.setup-option{min-height:168px}.wizard-actions{position:sticky;bottom:0;margin-inline:-16px;padding:14px 16px calc(14px + env(safe-area-inset-bottom));background:linear-gradient(transparent 0,#f4f7ef 25%);border:0}.button-primary{margin-left:auto}.ready-state{padding:8px 0 18px}}
@media(prefers-reduced-motion:reduce){*,*::before,*::after{animation:none!important;transition:none!important}}
</style>
