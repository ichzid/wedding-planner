<script setup>
import { computed, onMounted, ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import {
    ArrowLeft, ArrowRight, Check, CheckCircle2, HeartHandshake,
    LoaderCircle, PenLine, Sparkles, WalletCards
} from '@lucide/vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({ preview: { type: Boolean, default: false } });
const step = ref(1);
const direction = ref('forward');
const previewComplete = ref(false);
const steps = [
    { number: 1, label: 'Cara Memulai' },
    { number: 2, label: 'Selesaikan' },
];

const form = useForm({ setup_type: 'auto', budget: '' });

onMounted(() => {
    try {
        const savedEstimate = JSON.parse(sessionStorage.getItem('wedding_estimator_result'));
        const isRecent = savedEstimate?.savedAt > Date.now() - 24 * 60 * 60 * 1000;

        if (isRecent && Number(savedEstimate.budget) > 0) {
            form.budget = String(Math.round(savedEstimate.budget));
        }
    } catch {
        sessionStorage.removeItem('wedding_estimator_result');
    }
});

const canFinish = computed(() => !form.processing && (form.setup_type === 'manual' || Boolean(form.budget)));
const title = computed(() => [
    'Gimana cara Anda memulai?',
    form.setup_type === 'auto' ? 'Berapa perkiraan budget Anda?' : 'Ruang rencana Anda sudah siap.',
][step.value - 1]);
const description = computed(() => [
    'Pilih rencana otomatis agar kami menyiapkan titik awal, atau mulai dari ruang kosong untuk mengaturnya sendiri.',
    form.setup_type === 'auto' ? 'Masukkan perkiraan awal. Kami akan membaginya ke 7 pos anggaran dan menyiapkan 9 checklist penting.' : 'Mulai dari ruang kosong dan susun semua kebutuhan sesuai prioritas Anda.',
][step.value - 1]);

function moveTo(target) {
    if (form.processing || target === step.value) return;
    if (target > step.value && target === 2 && !form.setup_type) return;
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

    form.post(route('onboarding.store'), {
        onSuccess: () => sessionStorage.removeItem('wedding_estimator_result'),
    });
}
</script>

<template>
    <Head title="Siapkan Pernikahan" />
    <main class="onboarding-shell">
        <div class="ambient ambient--one" aria-hidden="true"></div>
        <div class="ambient ambient--two" aria-hidden="true"></div>

        <div v-if="preview" class="preview-banner"><Sparkles aria-hidden="true" /> Mode Preview — tidak ada data yang akan disimpan</div>

        <section v-if="!previewComplete" class="wizard-layout">
            <aside class="wizard-aside">
                <p class="aside-eyebrow">Mulai perencanaan</p>
                <h1>Siapkan fondasi untuk hari yang istimewa.</h1>
                <p class="aside-copy">Selesaikan tiga langkah singkat agar Ruang Pasangan dapat menyiapkan perencanaan yang relevan untuk Anda dan pasangan.</p>

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
                    <p><strong>Rencana tetap fleksibel.</strong><span>Seluruh informasi dapat diperbarui kembali kapan saja melalui menu Pengaturan.</span></p>
                </div>
            </aside>

            <section class="wizard-main">
                <div class="mobile-progress" aria-hidden="true">
                    <span>{{ step }} dari 2</span><div><i :style="{ width: `${step / 2 * 100}%` }"></i></div>
                </div>

                <header class="wizard-heading" :key="`heading-${step}`">
                    <p>Langkah {{ step }} dari 2</p>
                    <h2>{{ title }}</h2>
                    <span>{{ description }}</span>
                </header>

                <div v-if="form.errors.error" class="global-error" role="alert">{{ form.errors.error }}</div>

                <Transition :name="direction === 'forward' ? 'step-forward' : 'step-backward'" mode="out-in">
                    <div :key="step" class="step-content" :class="{ 'step-content--budget': step === 2 && form.setup_type === 'auto' }">
                        <template v-if="step === 1">
                            <div class="setup-options">
                                <button type="button" class="setup-option" :class="{ selected: form.setup_type === 'auto' }" :aria-pressed="form.setup_type === 'auto'" @click="selectSetup('auto')">
                                    <span class="option-icon"><Sparkles aria-hidden="true" /></span>
                                    <span class="option-copy">
                                        <small>Direkomendasikan</small>
                                        <strong>Siapkan Rencana Otomatis</strong>
                                        <p>Kami membuatkan titik awal berdasarkan total budget kalian.</p>
                                        <span class="option-features">
                                            <span><CheckCircle2 aria-hidden="true" /> 7 pos anggaran utama</span>
                                            <span><CheckCircle2 aria-hidden="true" /> 9 checklist persiapan</span>
                                        </span>
                                    </span>
                                    <span class="option-check"><Check aria-hidden="true" /></span>
                                </button>
                                <button type="button" class="setup-option" :class="{ selected: form.setup_type === 'manual' }" :aria-pressed="form.setup_type === 'manual'" @click="selectSetup('manual')">
                                    <span class="option-icon option-icon--plain"><PenLine aria-hidden="true" /></span>
                                    <span class="option-copy">
                                        <small>Lebih Fleksibel</small>
                                        <strong>Mulai dari Ruang Kosong</strong>
                                        <p>Susun sendiri budget dan checklist sesuai rencana kalian.</p>
                                        <span class="option-features option-features--manual">
                                            <span><CheckCircle2 aria-hidden="true" /> Atur semuanya dari awal</span>
                                            <span><CheckCircle2 aria-hidden="true" /> Bebas diubah kapan saja</span>
                                        </span>
                                    </span>
                                    <span class="option-check"><Check aria-hidden="true" /></span>
                                </button>
                            </div>
                        </template>

                        <template v-else>
                            <div v-if="form.setup_type === 'auto'" class="budget-panel">
                                <label class="field-group budget-field">
                                    <span>Perkiraan Total Budget <b>*</b></span>
                                    <div class="field-shell budget-input"><WalletCards aria-hidden="true" /><em>Rp</em><input :value="form.budget ? Number(form.budget).toLocaleString('id-ID') : ''" type="text" inputmode="numeric" required placeholder="0" @input="formatBudget" /></div>
                                    <small class="field-helper">Masukkan perkiraan awal. Nominal ini bisa diubah kapan saja.</small>
                                    <InputError :message="form.errors.budget" />
                                </label>
                                <div class="included-list">
                                    <p>Rencana awal yang akan dibuat</p>
                                    <span><CheckCircle2 aria-hidden="true" /> 7 pos anggaran utama</span>
                                    <span><CheckCircle2 aria-hidden="true" /> 9 checklist persiapan</span>
                                    <span><CheckCircle2 aria-hidden="true" /> Tetap fleksibel untuk disesuaikan</span>
                                </div>
                            </div>
                            <div v-else class="ready-state">
                                <span class="ready-icon"><HeartHandshake aria-hidden="true" /></span>
                                <p class="ready-eyebrow">Ruang Anda Siap</p>
                                <h3>Mulai dari rencana Anda</h3>
                                <p>Susun budget dan checklist sesuai prioritas. Nama pasangan dan tanggal pernikahan bisa dilengkapi nanti di Pengaturan.</p>
                            </div>
                        </template>
                    </div>
                </Transition>

                <footer class="wizard-actions">
                    <button v-if="step > 1" type="button" class="button-secondary" :disabled="form.processing" @click="previous"><ArrowLeft aria-hidden="true" /> Kembali</button>
                    <span v-else></span>
                    <button v-if="step < 2" type="button" class="button-primary" :disabled="!form.setup_type" @click="next">Lanjut <ArrowRight aria-hidden="true" /></button>
                    <button v-else type="button" class="button-primary" :disabled="!canFinish" @click="submit">
                        <LoaderCircle v-if="form.processing" class="spinner" aria-hidden="true" />
                        {{ form.processing ? 'Menyiapkan...' : 'Mulai Rencanain' }}
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
@import url('https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,800&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400&display=swap');

.onboarding-shell{position:relative;min-height:100dvh;overflow:hidden;background:#fcfaf5;color:var(--text);font-family:var(--font)}
.ambient{position:fixed;border-radius:50%;filter:blur(2px);pointer-events:none}.ambient--one{top:-280px;right:-190px;width:620px;height:620px;background:radial-gradient(circle,rgba(168,229,229,.22),transparent 68%)}.ambient--two{bottom:-260px;left:-180px;width:540px;height:540px;background:radial-gradient(circle,rgba(246,208,255,.24),transparent 68%)}
.onboarding-topbar{position:relative;z-index:2;display:flex;align-items:center;justify-content:space-between;max-width:1280px;margin:auto;padding:25px 36px}.preview-banner{position:relative;z-index:2;display:flex;align-items:center;justify-content:center;gap:7px;width:max-content;max-width:calc(100% - 32px);margin:0 auto -20px;padding:7px 12px;border:1px solid #b6b6b6;border-radius:9999px;background:#ffe95c;color:#1a3300;font-size:12px;font-weight:700;letter-spacing:.04em}.preview-banner svg{width:13px}.onboarding-brand{display:flex;align-items:center;gap:11px;color:var(--text);text-decoration:none}.brand-mark{display:grid;place-items:center;width:42px;height:42px;border:1px solid #b6b6b6;border-radius:12px;background:#d5f5c2;color:#1a3300}.brand-mark svg{width:22px}.onboarding-brand strong,.onboarding-brand small{display:block}.onboarding-brand strong{font:600 17px var(--font-display)}.onboarding-brand small{margin-top:2px;color:var(--text-dim);font-size:9px;font-weight:600;letter-spacing:.09em;text-transform:uppercase}.exit-button{border:0;background:transparent;color:var(--text-muted);font:600 12px var(--font);cursor:pointer}.exit-button:hover{color:var(--danger-text)}
.wizard-layout{position:relative;z-index:1;display:grid;grid-template-columns:minmax(280px,360px) minmax(0,720px);min-height:calc(100dvh - 92px);max-width:1120px;margin:auto;padding:38px 34px 64px;gap:clamp(50px,8vw,110px)}
.wizard-aside{display:flex;flex-direction:column;padding-top:8px}.aside-eyebrow,.wizard-heading>p,.ready-eyebrow{margin:0 0 12px;color:var(--accent);font-size:10px;font-weight:700;letter-spacing:.14em;text-transform:uppercase}.wizard-aside h1{margin:0;max-width:330px;font:600 clamp(34px,4vw,48px)/1.08 var(--font-display);letter-spacing:-.035em}.aside-copy{margin:18px 0 0;color:var(--text-muted);font-size:13px;line-height:1.7}.step-list{display:flex;flex-direction:column;gap:4px;margin-top:42px}.step-item{position:relative;display:flex;align-items:center;gap:13px;padding:9px 0;border:0;background:transparent;color:var(--text-dim);text-align:left;cursor:pointer}.step-item:not(:last-child)::after{content:'';position:absolute;top:41px;left:15px;width:1px;height:20px;background:var(--border)}.step-number{display:grid;place-items:center;width:30px;height:30px;border:1px solid var(--border-strong);border-radius:50%;background:var(--surface);font-size:10px;font-weight:700;transition:.25s}.step-number svg{width:14px}.step-item small,.step-item strong{display:block}.step-item small{font-size:8px;letter-spacing:.09em;text-transform:uppercase}.step-item strong{margin-top:2px;font-size:12px}.step-item.active,.step-item.complete{color:var(--text)}.step-item.active .step-number{border-color:var(--accent);background:var(--accent);color:#fff;box-shadow:0 0 0 5px var(--accent-soft)}.step-item.complete .step-number{border-color:var(--accent-light);background:var(--accent-soft);color:var(--accent)}.aside-note{display:flex;align-items:flex-start;gap:10px;margin-top:auto;padding:15px;border:1px solid rgba(164,188,148,.42);border-radius:13px;background:rgba(255,255,255,.52)}.aside-note>svg{width:16px;color:var(--accent)}.aside-note p{margin:0}.aside-note strong,.aside-note span{display:block}.aside-note strong{font-size:11px}.aside-note span{margin-top:4px;color:var(--text-muted);font-size:10px;line-height:1.5}
.wizard-main{align-self:center;min-width:0}.mobile-progress{display:none}.wizard-heading{animation:headingIn .4s ease both}.wizard-heading h2{margin:0;font:600 clamp(29px,3vw,40px)/1.15 var(--font-display);letter-spacing:-.035em}.wizard-heading>span{display:block;max-width:590px;margin-top:11px;color:var(--text-muted);font-size:13px;line-height:1.65}.global-error{margin-top:18px;padding:12px 14px;border:1px solid var(--danger-border);border-radius:10px;background:var(--danger-bg);color:var(--danger-text);font-size:11px}.step-content{min-height:282px;padding-top:31px}.couple-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}.field-group{display:flex;flex-direction:column;gap:7px}.field-group>span{font-size:12px;font-weight:600}.field-group b{color:var(--accent)}.field-group--date{max-width:360px;margin-top:15px}.field-shell{position:relative;display:flex;align-items:center;height:48px;border:1px solid var(--border);border-radius:11px;background:rgba(255,255,255,.78);transition:.18s}.field-shell:focus-within{border-color:var(--accent);box-shadow:0 0 0 3px var(--accent-soft);background:#fff}.field-shell>svg{position:absolute;left:14px;width:16px;color:#82937e}.field-shell input{width:100%;height:100%;box-sizing:border-box;padding:0 14px 0 41px;border:0!important;outline:0!important;background:transparent!important;color:var(--text);font:500 13px var(--font);box-shadow:none!important}.field-shell input::placeholder{color:#b6b6b6}.budget-input em{position:absolute;left:42px;color:var(--text-muted);font-size:11px;font-style:normal;font-weight:700}.budget-input input{padding-left:66px;font-size:17px;font-weight:700}
.setup-options{display:grid;grid-template-columns:1fr 1fr;gap:14px}.setup-option{position:relative;display:flex;flex-direction:column;min-height:215px;padding:21px;border:1px solid var(--border);border-radius:15px;background:rgba(255,255,255,.72);color:var(--text);text-align:left;cursor:pointer;transition:.25s}.setup-option:hover{border-color:var(--accent-light);transform:translateY(-3px);box-shadow:0 14px 34px rgba(69,94,63,.08)}.setup-option.selected{border-color:var(--accent);background:#fcfaf5;box-shadow:0 0 0 3px var(--accent-soft)}.option-icon{display:grid;place-items:center;width:42px;height:42px;border-radius:12px;background:var(--accent);color:#fff}.option-icon--plain{background:var(--surface-muted);color:var(--text-muted)}.option-icon svg{width:19px}.option-copy{margin-top:auto}.option-copy small{color:var(--accent);font-size:9px;font-weight:700;letter-spacing:.08em;text-transform:uppercase}.option-copy strong{display:block;margin-top:7px;font:600 17px var(--font-display)}.option-copy p{margin:7px 0 0;color:var(--text-muted);font-size:11px;line-height:1.6}.option-check{position:absolute;top:20px;right:20px;display:grid;place-items:center;width:22px;height:22px;border:1px solid var(--border);border-radius:50%;color:transparent}.option-check svg{width:12px}.selected .option-check{border-color:var(--accent);background:var(--accent);color:#fff}
.budget-panel{display:grid;grid-template-columns:minmax(0,1fr) minmax(220px,.75fr);gap:18px}.included-list{display:flex;flex-direction:column;gap:10px;padding:17px;border:1px solid var(--border);border-radius:13px;background:rgba(255,255,255,.58)}.included-list p{margin:0 0 2px;font-size:10px;font-weight:700}.included-list span{display:flex;align-items:flex-start;gap:7px;color:var(--text-muted);font-size:10px;line-height:1.45}.included-list svg{width:14px;flex:none;color:var(--accent)}.ready-state{padding:6px 0;text-align:center}.ready-icon{display:grid;place-items:center;width:68px;height:68px;margin:0 auto 18px;border-radius:20px;background:var(--accent-soft);color:var(--accent);transform:rotate(-4deg);animation:readyFloat 3s ease-in-out infinite}.ready-icon svg{width:32px}.ready-state h3{margin:0;font:600 27px var(--font-display)}.ready-state>p:last-child{max-width:410px;margin:10px auto 0;color:var(--text-muted);font-size:12px;line-height:1.65}
.preview-result{position:relative;z-index:1;display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:calc(100dvh - 110px);padding:30px;text-align:center;animation:headingIn .45s ease both}.preview-result__icon{display:grid;place-items:center;width:76px;height:76px;border-radius:24px;background:var(--accent-soft);color:var(--accent)}.preview-result__icon svg{width:36px}.preview-result>p{margin:22px 0 9px;color:var(--accent);font-size:10px;font-weight:700;letter-spacing:.14em;text-transform:uppercase}.preview-result h1{margin:0;font:600 clamp(32px,5vw,48px)/1.12 var(--font-display)}.preview-result>span:not(.preview-result__icon){max-width:470px;margin:13px auto 22px;color:var(--text-muted);font-size:12px;line-height:1.65}.wizard-actions{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-top:24px;padding-top:20px;border-top:1px solid var(--border)}.button-primary,.button-secondary{display:inline-flex;align-items:center;justify-content:center;gap:8px;min-height:43px;padding:0 18px;border-radius:10px;font:600 12px var(--font);cursor:pointer;transition:.18s}.button-primary{border:0;background:var(--accent);color:#fff}.button-primary:hover:not(:disabled){background:var(--accent-hover);transform:translateY(-1px)}.button-secondary{border:1px solid var(--border);background:rgba(255,255,255,.6);color:var(--text-muted)}.button-secondary:hover{background:#fff;color:var(--text)}.button-primary:disabled,.button-secondary:disabled{opacity:.45;cursor:not-allowed}.button-primary svg,.button-secondary svg{width:15px}.spinner{animation:spin .7s linear infinite}
.step-forward-enter-active,.step-forward-leave-active,.step-backward-enter-active,.step-backward-leave-active{transition:.3s cubic-bezier(.22,1,.36,1)}.step-forward-enter-from{opacity:0;transform:translateX(24px)}.step-forward-leave-to{opacity:0;transform:translateX(-18px)}.step-backward-enter-from{opacity:0;transform:translateX(-24px)}.step-backward-leave-to{opacity:0;transform:translateX(18px)}
@keyframes headingIn{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:none}}@keyframes readyFloat{50%{transform:translateY(-6px) rotate(3deg)}}@keyframes spin{to{transform:rotate(360deg)}}
@media(max-width:850px){.onboarding-shell{overflow:auto}.onboarding-topbar{padding:18px 20px}.wizard-layout{display:block;min-height:auto;padding:24px 20px 50px}.wizard-aside{display:none}.wizard-main{max-width:620px;margin:auto}.mobile-progress{display:flex;align-items:center;gap:12px;margin-bottom:25px;color:var(--text-dim);font-size:9px;font-weight:700;text-transform:uppercase}.mobile-progress>div{flex:1;height:3px;overflow:hidden;border-radius:99px;background:var(--border)}.mobile-progress i{display:block;height:100%;border-radius:inherit;background:var(--accent);transition:width .35s}.step-content{min-height:0;padding-top:26px}.wizard-heading h2{font-size:30px}}
@media(max-width:560px){.onboarding-brand small{display:none}.wizard-layout{padding-inline:16px}.couple-grid,.setup-options,.budget-panel{grid-template-columns:1fr}.field-group--date{max-width:none}.setup-option{min-height:168px}.wizard-actions{position:sticky;bottom:0;margin-inline:-16px;padding:14px 16px calc(14px + env(safe-area-inset-bottom));background:linear-gradient(transparent 0,#fcfaf5 25%);border:0}.button-primary{margin-left:auto}.ready-state{padding:8px 0 18px}}
@media(prefers-reduced-motion:reduce){*,*::before,*::after{animation:none!important;transition:none!important}}

/* Design system: cream paper, forest ink, and restrained sticky-note accents. */
.onboarding-shell{
    --onboarding-ink:#1a3300;
    --onboarding-paper:#fcfaf5;
    --onboarding-line:#b6b6b6;
    --onboarding-yellow:#ffe95c;
    --onboarding-mint:#d5f5c2;
    --onboarding-teal:#a8e5e5;
    --onboarding-blush:#f6d0ff;
    background:var(--onboarding-paper);
    color:var(--onboarding-ink);
    font-family:'Inter',system-ui,sans-serif;
}
.ambient{display:none}
.onboarding-topbar,.wizard-layout{width:min(1200px,calc(100% - 40px));max-width:1200px;box-sizing:border-box}
.onboarding-topbar{margin:16px auto 0;padding:8px 10px 8px 12px;border:1px solid var(--onboarding-line);border-radius:16px;background:rgba(252,250,245,.96)}
.wizard-layout{grid-template-columns:minmax(280px,360px) minmax(0,720px);justify-content:space-between;padding:64px 24px;gap:64px}
.onboarding-brand,.wizard-aside h1,.wizard-heading h2,.field-group>span,.option-copy strong,.ready-state h3,.preview-result h1{color:var(--onboarding-ink)}
.wizard-aside h1,.wizard-heading h2,.preview-result h1{font-family:var(--font-bricolage-grotesque);font-weight:800;line-height:1.1;letter-spacing:.04em}.wizard-aside h1{font-size:40px}.wizard-heading h2{font-size:40px}.aside-copy,.wizard-heading>span{font-size:16px;line-height:1.5}.aside-eyebrow,.wizard-heading>p,.ready-eyebrow,.option-copy small{font-family:var(--font-roboto-mono);font-size:12px;font-weight:400}.field-group>span{font-size:14px}.field-shell{height:48px;border-radius:6px}.field-shell input{font:400 16px/1.5 var(--font-inter)}.setup-option,.aside-note,.included-list{border-radius:12px}.option-copy strong{font-family:var(--font-inter);font-size:20px;font-weight:600}.option-copy p{font-size:14px;line-height:1.5}.button-primary,.button-secondary{min-height:40px;padding:0 20px;font:500 14px/1.5 var(--font-inter)}
.onboarding-brand strong{font:700 20px/1.2 var(--font-inter)}
.onboarding-brand small,.preview-banner,.exit-button,.aside-eyebrow,.wizard-heading>p,.ready-eyebrow,.step-item small,.option-copy small,.preview-result>p,.mobile-progress{font-size:12px}
.aside-copy,.wizard-heading>span,.field-shell input{font-size:16px}
.step-number{font-size:14px}.step-item strong{font-size:14px}.aside-note strong{font-size:14px}.aside-note span{font-size:14px;line-height:1.5}
.global-error{font-size:14px}.field-group>span{font-size:14px}.budget-input em{font-size:14px}.budget-input input{font-size:18px}
.option-copy p,.included-list p,.included-list span,.ready-state>p:last-child,.preview-result>span:not(.preview-result__icon){font-size:14px;line-height:1.5}
.ready-state h3{font-size:28px}
.brand-mark{border-color:var(--onboarding-ink);border-radius:6px;background:var(--onboarding-yellow);color:var(--onboarding-ink)}
.exit-button{padding:8px 16px;border:1px solid var(--onboarding-ink);border-radius:6px;color:var(--onboarding-ink)}
.exit-button:hover{background:var(--onboarding-mint);color:var(--onboarding-ink)}
.preview-banner{border-color:var(--onboarding-ink);border-radius:6px;background:var(--onboarding-yellow);color:var(--onboarding-ink)}
.aside-eyebrow,.wizard-heading>p,.ready-eyebrow,.option-copy small{color:var(--onboarding-ink)}
.step-item.active .step-number,.selected .option-check,.button-primary{border-color:var(--onboarding-ink);background:var(--onboarding-ink);color:var(--onboarding-paper);box-shadow:0 1px 2px rgba(0,0,0,.05)}
.step-item.complete .step-number{border-color:var(--onboarding-ink);background:var(--onboarding-mint);color:var(--onboarding-ink)}
.aside-note,.included-list,.setup-option,.field-shell{border-color:var(--onboarding-line);background:var(--onboarding-paper);box-shadow:none}
.aside-note>svg,.included-list svg,.field-group b{color:var(--onboarding-ink)}
.field-shell:focus-within,.setup-option.selected{border-color:var(--onboarding-ink);background:var(--onboarding-paper);box-shadow:0 0 0 2px var(--onboarding-teal)}
.setup-option:hover{border-color:var(--onboarding-ink);box-shadow:none}
.option-icon{border-radius:12px;background:var(--onboarding-teal);color:var(--onboarding-ink)}
.option-icon--plain{background:var(--onboarding-blush);color:var(--onboarding-ink)}
.ready-icon,.preview-result__icon{background:var(--onboarding-mint);color:var(--onboarding-ink)}
.button-primary,.button-secondary{border-radius:6px}
.button-primary:hover:not(:disabled){background:var(--onboarding-ink)}
.button-secondary{border-color:var(--onboarding-ink);background:transparent;color:var(--onboarding-ink)}
.wizard-actions{border-color:var(--onboarding-line)}
.mobile-progress i{background:var(--onboarding-ink)}

@media(max-width:850px){
    .onboarding-topbar,.wizard-layout{width:calc(100% - 32px)}
    .onboarding-topbar{padding:8px 10px 8px 12px}
    .wizard-layout{padding:32px 0 56px}
}
@media(max-width:560px){
    .wizard-layout{padding-inline:0}
    .wizard-actions{margin-inline:-16px;padding-inline:16px;background:linear-gradient(transparent 0,var(--onboarding-paper) 25%)}
}

/* DESIGN.md — Sunset Marketplace */
.onboarding-shell{--onboarding-ink:#240029;--onboarding-paper:#fff;--onboarding-line:#d4ccd4;--onboarding-yellow:#ffcc11;--onboarding-mint:#dcfce7;--onboarding-teal:#fff1bd;--onboarding-blush:#ffbdd3;background:#fff;color:#240029;font-family:Inter,system-ui,sans-serif}.onboarding-topbar{border-color:#d4ccd4;border-radius:14px;background:#fff;box-shadow:0 2px 2px rgba(32,0,36,.05)}.brand-mark{border-color:#6d526d;background:#fff;color:#240029}.onboarding-brand strong{color:#240029}.onboarding-brand small{color:#6d526d}.exit-button{border-color:#6d526d;color:#240029}.exit-button:hover{background:#f8f4f8;color:#240029}.preview-banner{border-color:#bbf7d0;border-radius:999px;background:#dcfce7;color:#15803d}.wizard-layout{border:1px solid #d4ccd4;border-radius:14px;background:linear-gradient(145deg,rgba(255,189,211,.5),rgba(255,241,189,.55) 40%,rgba(255,241,189,.35) 60%,rgba(255,203,15,.42));box-shadow:0 2px 2px rgba(32,0,36,.05)}.wizard-aside h1,.wizard-heading h2,.preview-result h1{color:#240029;font-family:'Bricolage Grotesque',Inter,system-ui,sans-serif;font-weight:800;line-height:1.1;letter-spacing:.04em}.aside-eyebrow,.wizard-heading>p,.ready-eyebrow,.option-copy small{color:#240029;font-family:'JetBrains Mono',ui-monospace,monospace;letter-spacing:.1em}.aside-copy,.wizard-heading>span,.option-copy p,.ready-state>p:last-child{color:#6d526d}.step-item{color:#6d526d}.step-item.active,.step-item.complete{color:#240029}.step-number{border-color:#6d526d;background:#fff}.step-item.active .step-number{border-color:#240029;background:#240029;color:#fff;box-shadow:none}.step-item.complete .step-number{border-color:#bbf7d0;background:#dcfce7;color:#15803d}.aside-note,.included-list,.setup-option{padding:18px;border-color:#d4ccd4;border-radius:14px;background:#fff;box-shadow:0 0 0 1px rgba(41,0,41,.11),0 2px 2px rgba(32,0,36,.05)}.field-shell{border-color:#767676;border-radius:3px;background:#fff}.field-shell:focus-within,.setup-option.selected{border-color:#6d526d;background:#fff;box-shadow:0 0 0 2px rgba(109,82,109,.18)}.setup-option:hover{border-color:#6d526d;box-shadow:0 4px 8px rgba(32,0,36,.06)}.option-icon{border-radius:6px;background:#fff1bd;color:#240029}.option-icon--plain{background:#ffbdd3;color:#240029}.selected .option-check{border-color:#240029;background:#240029}.ready-icon,.preview-result__icon{background:#dcfce7;color:#15803d}.button-primary{border-color:#df37a7;background:#df37a7;color:#fff;box-shadow:inset 0 0 0 1px rgba(255,255,255,.2),0 1px 2px rgba(32,0,36,.05)}.button-primary:hover:not(:disabled){background:#c82f96}.button-secondary{border-color:#6d526d;background:transparent;color:#240029}.wizard-actions{border-color:#d4ccd4}.mobile-progress i{background:#240029}@media(max-width:560px){.wizard-actions{background:linear-gradient(transparent 0,#fff 25%)}}

/* Refined onboarding composition */
.onboarding-shell{
    min-height:100dvh;
    padding-bottom:32px;
    background:#fffafd;
    font-family:'Inter Var',Inter,system-ui,sans-serif;
}
.onboarding-topbar{
    width:min(1160px,calc(100% - 48px));
    margin-top:20px;
    padding:10px 12px;
    border-radius:14px;
    box-shadow:0 0 0 1px rgba(41,0,41,.06),0 2px 2px rgba(32,0,36,.04);
}
.onboarding-brand{gap:12px}.brand-mark{width:40px;height:40px;border-radius:10px;background:#240029;color:#fff}.onboarding-brand strong{font-family:'Bricolage Grotesque',Inter,sans-serif;font-size:18px;font-weight:800;letter-spacing:.01em}.onboarding-brand small{margin-top:3px;font-family:'JetBrains Mono',ui-monospace,monospace;font-size:9px;font-weight:400;letter-spacing:.1em}.exit-button{min-height:38px;padding:0 16px;border-radius:6px;font-size:13px;font-weight:600;text-decoration:none}
.wizard-layout{
    width:min(1160px,calc(100% - 48px));
    min-height:calc(100dvh - 48px);
    margin:24px auto 0;
    padding:0;
    overflow:hidden;
    grid-template-columns:minmax(300px,390px) minmax(0,1fr);
    gap:0;
    border:1px solid #d4ccd4;
    border-radius:14px;
    background:#fff;
    box-shadow:0 0 0 1px rgba(41,0,41,.08),0 12px 32px rgba(32,0,36,.06);
}
.wizard-aside{
    position:relative;
    padding:52px 42px 38px;
    overflow:hidden;
    background:linear-gradient(145deg,#ffbdd3,#fff1bd 48%,#ffcc11);
}
.wizard-aside::after{position:absolute;right:-80px;bottom:-110px;width:250px;height:250px;border:1px solid rgba(36,0,41,.18);border-radius:50%;content:''}.wizard-aside>*{position:relative;z-index:1}.aside-eyebrow,.wizard-heading>p,.ready-eyebrow{margin-bottom:14px;font-family:'JetBrains Mono',ui-monospace,monospace;font-size:11px;font-weight:400;line-height:1.4;letter-spacing:.1em}.wizard-aside h1{max-width:310px;font-family:'Bricolage Grotesque',Inter,sans-serif;font-size:clamp(36px,3.5vw,48px);font-weight:800;line-height:1.04;letter-spacing:-.025em}.aside-copy{max-width:315px;margin-top:20px;color:#240029;font-size:14px;line-height:1.65}.step-list{margin-top:40px;gap:7px}.step-item{padding:8px 0;color:rgba(36,0,41,.68)}.step-item:not(:last-child)::after{top:42px;left:16px;height:22px;background:rgba(36,0,41,.2)}.step-number{width:32px;height:32px;border-color:rgba(36,0,41,.42);font-size:12px}.step-item small{font-family:'JetBrains Mono',ui-monospace,monospace;font-size:8px;font-weight:400}.step-item strong{margin-top:3px;font-size:13px;font-weight:700}.aside-note{margin-top:auto;padding:16px;border-color:rgba(36,0,41,.22);background:rgba(255,255,255,.58);box-shadow:none;backdrop-filter:blur(8px)}.aside-note strong{font-size:12px}.aside-note span{font-size:11px}
.wizard-main{display:flex;min-width:0;padding:clamp(44px,6vw,72px);align-self:stretch;justify-content:center;flex-direction:column;background:#fff}.wizard-heading{max-width:620px}.wizard-heading h2{font-family:'Bricolage Grotesque',Inter,sans-serif;font-size:clamp(32px,3.2vw,44px);font-weight:800;line-height:1.08;letter-spacing:-.025em}.wizard-heading>span{max-width:560px;margin-top:13px;font-size:15px;line-height:1.6}.step-content{min-height:270px;padding-top:34px}.couple-grid{gap:18px}.field-group{gap:8px}.field-group>span{font-size:13px;font-weight:600}.field-group--date{max-width:none;margin-top:18px}.field-shell{height:50px;border-color:rgba(109,82,109,.48);border-radius:12px;background:#fffafd;box-shadow:0 1px 2px rgba(32,0,36,.035)}.field-shell:focus-within{border-color:#df37a7;box-shadow:0 0 0 3px rgba(223,55,167,.12)}.field-shell>svg{color:#6d526d}.field-shell input{font-size:14px}.field-shell input::placeholder{color:#9b899b}.setup-options{gap:18px}.setup-option{min-height:228px;padding:22px;border-radius:14px}.setup-option:hover{transform:translateY(-2px)}.setup-option.selected{border-color:#6d526d;box-shadow:0 0 0 3px rgba(109,82,109,.13)}.option-copy strong{font-family:Inter,sans-serif;font-size:18px;font-weight:700}.option-copy p{font-size:13px;line-height:1.55}.option-features{display:grid;margin-top:14px;gap:9px}.option-features>span{display:flex;align-items:flex-start;gap:9px;color:#240029;font-size:12px;line-height:1.4}.option-features svg{width:16px;height:16px;margin-top:1px;flex:none;color:#15803d}.option-features>span>span{display:grid;gap:2px}.option-features b{font-size:12px;font-style:normal;font-weight:700}.option-features em{color:#6d526d;font-size:10px;font-style:normal;font-weight:400;line-height:1.45}.option-features--manual svg{color:#6d526d}.option-note{display:block;margin-top:13px;padding-top:11px;border-top:1px solid #d4ccd4;color:#6d526d;font-size:10px;font-weight:500;line-height:1.45}.setup-option:first-child{background:#fffafd}.setup-option:first-child.selected{border-color:#df37a7;box-shadow:0 0 0 3px rgba(223,55,167,.1)}.setup-option:first-child .option-copy>small{display:inline-flex;width:max-content;padding:4px 8px;border-radius:999px;background:#240029;color:#fff}.budget-panel{gap:20px}.included-list{padding:18px;border-radius:14px}.included-list p{font-size:12px}.included-list span{font-size:12px}.wizard-actions{margin-top:28px;padding-top:22px}.button-primary,.button-secondary{min-height:46px;padding:0 22px;border-radius:6px;font-size:14px;font-weight:600}.button-primary{background:#df37a7}.button-primary:hover:not(:disabled){background:#c82f96;box-shadow:0 5px 14px rgba(223,55,167,.2)}
@media(max-width:850px){
    .onboarding-shell{padding-bottom:0}.onboarding-topbar,.wizard-layout{width:calc(100% - 32px)}.wizard-layout{min-height:auto;margin-top:16px;padding:0;border-radius:14px}.wizard-main{padding:42px 34px}.mobile-progress{margin-bottom:30px}.wizard-heading h2{font-size:34px}.step-content{padding-top:30px}
}
@media(max-width:560px){
    .onboarding-topbar{width:calc(100% - 24px);margin-top:12px}.brand-mark{width:36px;height:36px}.onboarding-brand strong{font-size:16px}.exit-button{min-height:36px;padding-inline:12px;font-size:12px}.wizard-layout{width:100%;margin-top:12px;border-right:0;border-bottom:0;border-left:0;border-radius:0;box-shadow:none}.wizard-main{padding:30px 20px 0}.wizard-heading h2{font-size:30px}.wizard-heading>span{font-size:14px}.couple-grid,.setup-options,.budget-panel{gap:16px}.setup-option{min-height:174px;padding:18px}.step-content{padding-top:26px}.wizard-actions{margin-inline:-20px;padding:16px 20px calc(16px + env(safe-area-inset-bottom));background:linear-gradient(transparent 0,#fff 24%)}.button-primary,.button-secondary{min-height:46px;padding-inline:17px}
}

/* Full-page onboarding */
.onboarding-shell{padding:0;background:#fff;overflow:auto}.wizard-layout{width:100%;min-height:100dvh;margin:0;border:0;border-radius:0;box-shadow:none;grid-template-columns:minmax(340px,40%) minmax(0,60%)}.wizard-aside{padding:clamp(48px,7vw,92px) clamp(36px,6vw,88px);justify-content:center}.wizard-aside h1{max-width:480px;font-family:'Bricolage Grotesque',Inter,system-ui,sans-serif;font-size:clamp(46px,5.2vw,72px);font-weight:800;line-height:1.02;letter-spacing:-.045em}.aside-copy{max-width:470px;font-family:Inter,system-ui,sans-serif;font-size:16px;line-height:1.6}.step-list{width:min(100%,380px);margin-top:48px}.aside-note{width:min(100%,430px);margin-top:48px}.wizard-main{padding:clamp(48px,7vw,96px) clamp(44px,8vw,120px)}.wizard-heading{width:100%;max-width:none}.wizard-heading h2{font-family:'Bricolage Grotesque',Inter,system-ui,sans-serif;font-size:clamp(44px,4.5vw,64px);font-weight:800;line-height:1.03;letter-spacing:-.045em}.wizard-heading>span{max-width:680px;margin-top:18px;color:#6d526d;font-family:Inter,system-ui,sans-serif;font-size:17px;line-height:1.65}.wizard-heading>p{font-size:12px}.step-content{width:100%;max-width:none;min-height:300px;padding-top:42px}.wizard-actions{width:100%;max-width:none}.setup-options{grid-template-columns:1fr;gap:12px}.setup-option{min-height:auto;padding:20px 22px 20px 80px;justify-content:center}.setup-option .option-icon{position:absolute;top:22px;left:22px}.option-copy{margin-top:0}.option-features{grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}.option-note{margin-top:14px}.preview-banner{position:fixed;z-index:10;top:16px;left:50%;margin:0;transform:translateX(-50%)}
@media(max-width:850px){.wizard-layout{display:block;min-height:100dvh;width:100%;margin:0;border:0}.wizard-main{min-height:100dvh;padding:48px clamp(24px,7vw,56px)}.wizard-heading h2{font-size:clamp(38px,8vw,52px)}.wizard-heading>span{font-size:16px}.setup-options{grid-template-columns:1fr}.option-features{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:560px){.wizard-layout{margin:0}.wizard-main{padding:32px 20px 0}.wizard-heading h2{font-size:36px;line-height:1.05}.wizard-heading>span{font-size:15px}.setup-option{padding:18px 18px 18px 68px}.setup-option .option-icon{top:18px;left:18px;width:36px;height:36px}.option-features{grid-template-columns:1fr}.wizard-actions{margin-inline:-20px}}

/* Professional onboarding layout */
.onboarding-shell{min-height:100dvh;background:#fff;color:#240029}
.wizard-layout{display:grid;width:100%;min-height:100dvh;margin:0;grid-template-columns:340px minmax(0,1fr);border:0;border-radius:0;background:#fff;box-shadow:none}
.wizard-aside{display:flex;min-height:100dvh;padding:56px 42px 40px;justify-content:flex-start;background:#240029;color:#fff}
.wizard-aside::after{right:-95px;bottom:-110px;width:280px;height:280px;border-color:rgba(255,255,255,.09)}
.aside-eyebrow{margin-bottom:20px;color:#ffbdd3;font-family:'JetBrains Mono',ui-monospace,monospace;font-size:11px;letter-spacing:.12em}
.wizard-aside h1{max-width:260px;color:#fff;font-family:'Bricolage Grotesque',Inter,system-ui,sans-serif;font-size:38px;font-weight:800;line-height:1.06;letter-spacing:-.04em}
.aside-copy{max-width:260px;margin-top:18px;color:#d4ccd4;font-family:Inter,system-ui,sans-serif;font-size:13px;line-height:1.65}
.step-list{width:100%;margin-top:52px;gap:12px}.step-item{padding:5px 0;color:#bbadbb;cursor:default}.step-item:not(:last-child)::after{top:40px;left:15px;height:27px;background:rgba(255,255,255,.16)}.step-number{width:30px;height:30px;border-color:rgba(255,255,255,.28);background:transparent;color:#d4ccd4}.step-item small{color:#bbadbb;font-family:'JetBrains Mono',ui-monospace,monospace;font-size:8px}.step-item strong{color:inherit;font-size:13px}.step-item.active,.step-item.complete{color:#fff}.step-item.active .step-number{border-color:#ffbdd3;background:#ffbdd3;color:#240029}.step-item.complete .step-number{border-color:#bbf7d0;background:#dcfce7;color:#15803d}.wizard-aside .aside-note{display:flex;width:100%;margin-top:52px;padding:0;border:0!important;border-radius:0!important;background:transparent!important;color:#fff!important;box-shadow:none!important;backdrop-filter:none}.wizard-aside .aside-note>svg{width:16px;flex:none;color:#ffbdd3!important}.wizard-aside .aside-note p{margin:0}.wizard-aside .aside-note strong{display:block;color:#fff!important;font-size:12px;line-height:1.4}.wizard-aside .aside-note span{display:block;margin-top:5px;color:#d4ccd4!important;font-size:11px;line-height:1.55}
.wizard-main{display:flex;min-width:0;align-self:auto;justify-content:center;flex-direction:column}
.mobile-progress{display:none}.wizard-heading,.step-content,.wizard-actions{width:100%;max-width:none;margin-right:0;margin-left:0}.wizard-heading{text-align:left}.wizard-heading>p{margin-bottom:14px;color:#6d526d;font-family:'JetBrains Mono',ui-monospace,monospace;font-size:11px;letter-spacing:.1em}.wizard-heading h2{max-width:700px;color:#240029;font-family:'Bricolage Grotesque',Inter,system-ui,sans-serif;font-size:clamp(42px,4.4vw,60px);font-weight:800;line-height:1.04;letter-spacing:-.045em}.wizard-heading>span{max-width:650px;margin:18px 0 0;color:#6d526d;font-family:Inter,system-ui,sans-serif;font-size:16px;line-height:1.65}.step-content{min-height:310px;padding-top:38px}.setup-options{grid-template-columns:1fr;gap:12px}.setup-option{min-height:auto;padding:20px 54px 20px 76px;border:1px solid #d4ccd4;border-radius:14px;background:#fff;text-align:left;box-shadow:0 1px 2px rgba(32,0,36,.04)}.setup-option .option-icon{position:absolute;top:20px;left:20px;width:40px;height:40px}.setup-option:hover{border-color:#6d526d;transform:none;box-shadow:0 4px 12px rgba(32,0,36,.06)}.setup-option.selected{border-color:#240029;background:#fffafd;box-shadow:0 0 0 2px rgba(36,0,41,.08)}.setup-option:first-child.selected{border-color:#240029;box-shadow:0 0 0 2px rgba(36,0,41,.08)}.option-copy{margin-top:0}.option-copy strong{font-size:17px}.option-copy p{margin-top:5px}.option-features{grid-template-columns:repeat(2,minmax(0,1fr));margin-top:12px;gap:12px}.option-note{margin-top:12px}.wizard-actions{margin-top:30px;padding-top:22px}.button-primary,.button-secondary{min-height:46px}
@media(max-width:900px){.wizard-layout{display:block}.wizard-aside{display:none}.wizard-main{min-height:100dvh;padding:48px clamp(24px,7vw,56px)}.mobile-progress{display:flex;max-width:760px;margin:0 auto 34px;align-items:center;gap:14px;color:#6d526d;font-family:'JetBrains Mono',ui-monospace,monospace;font-size:10px;letter-spacing:.08em;text-transform:uppercase}.mobile-progress>div{height:4px;flex:1;overflow:hidden;border-radius:999px;background:#eadfea}.mobile-progress i{display:block;height:100%;border-radius:inherit;background:#240029}.wizard-heading h2{font-size:clamp(38px,8vw,52px)}}
@media(max-width:560px){.wizard-main{padding:30px 20px 0}.mobile-progress{margin-bottom:28px}.wizard-heading h2{font-size:36px}.wizard-heading>span{font-size:14px}.step-content{min-height:0;padding-top:28px}.setup-option{padding:18px 44px 18px 66px}.setup-option .option-icon{top:18px;left:16px;width:36px;height:36px}.option-features{grid-template-columns:1fr}.wizard-actions{width:auto;margin-right:-20px;margin-left:-20px;padding-right:20px;padding-left:20px}}

.onboarding-shell .wizard-aside h1,
.onboarding-shell .wizard-main .wizard-heading h2{
    font-family:'Bricolage Grotesque','Inter',ui-sans-serif,sans-serif!important;
    font-style:normal;
    font-weight:800!important;
    line-height:1.04;
    letter-spacing:-.04em;
    font-optical-sizing:auto;
}

.onboarding-shell .wizard-aside .aside-copy + .step-list{
    margin-top:48px!important;
}
.onboarding-shell .wizard-aside .step-list + .aside-note{
    margin-top:48px!important;
}
.onboarding-shell .wizard-aside .aside-note strong,
.onboarding-shell .wizard-aside .aside-note span{
    font-family:'Inter',ui-sans-serif,system-ui,sans-serif!important;
}

.onboarding-shell .wizard-aside .step-number,
.onboarding-shell .wizard-aside .step-item.active .step-number{
    border-color:#df37a7!important;
    background:#df37a7!important;
    color:#fff!important;
    box-shadow:none!important;
}
.onboarding-shell .wizard-aside .step-item.complete .step-number{
    border-color:#b61f7d!important;
    background:#b61f7d!important;
    color:#fff!important;
    box-shadow:none!important;
}

.onboarding-shell .wizard-main{
    align-items:stretch!important;
    padding-right:40px!important;
    padding-left:40px!important;
}
.onboarding-shell .wizard-main .wizard-heading,
.onboarding-shell .wizard-main .step-content,
.onboarding-shell .wizard-main .wizard-actions,
.onboarding-shell .wizard-main .couple-grid,
.onboarding-shell .wizard-main .field-group--date,
.onboarding-shell .wizard-main .setup-options,
.onboarding-shell .wizard-main .budget-panel{
    width:100%!important;
    max-width:none!important;
    margin-right:0!important;
    margin-left:0!important;
}
/* Final responsive polish */
.onboarding-shell .wizard-main{
    padding:clamp(36px,6vh,72px) clamp(32px,4vw,64px)!important;
    background: radial-gradient(circle at top right, rgba(255,204,17,0.05), transparent 400px), #fff !important;
}
.onboarding-shell .wizard-heading h2{max-width:780px}
.onboarding-shell .step-content{min-height:330px;padding-top:42px}

/* Langkah 1 */
.onboarding-shell .couple-grid,
.onboarding-shell .field-group--date{
    box-sizing:border-box;
}
.onboarding-shell .couple-grid{
    padding:32px 32px 0;
    gap:22px;
    border:1px solid #eadfea;
    border-bottom:0;
    border-radius:24px 24px 0 0;
    background:linear-gradient(155deg,#fffafd,#fff 60%);
    box-shadow: 0 -4px 24px rgba(32, 0, 36, 0.03);
}
.onboarding-shell .field-group--date{
    margin-top:0!important;
    padding:22px 32px 32px;
    border:1px solid #eadfea;
    border-top:0;
    border-radius:0 0 24px 24px;
    background:linear-gradient(155deg,#fffafd,#fff 60%);
    box-shadow: 0 4px 24px rgba(32, 0, 36, 0.03);
}
.onboarding-shell .field-shell{height:56px;border-radius:14px;background:#fff;transition: all 0.2s ease;}
.onboarding-shell .field-shell > svg {left: 18px; width: 18px;}
.onboarding-shell .field-shell input {padding-left: 48px; font-size: 14.5px;}
.onboarding-shell .field-group>span{color:#3d173f;font-size:13.5px;font-weight: 700;letter-spacing: -0.01em; margin-bottom: 2px;}
.onboarding-shell .field-group b{color: #df37a7;}

/* Langkah 2 */
.onboarding-shell .setup-options{
    display:grid!important;
    grid-template-columns:repeat(2,minmax(0,1fr))!important;
    align-items:stretch;
    gap:20px!important;
}
.onboarding-shell .setup-option{
    min-height:330px;
    padding:32px!important;
    border-radius:24px;
    border: 1px solid #eadfea;
    background: linear-gradient(155deg, rgba(255,255,255,0.95), rgba(255,255,255,0.7));
    backdrop-filter: blur(8px);
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}
.onboarding-shell .setup-option .option-icon{
    position:static;
    width:52px;
    height:52px;
    margin-bottom:28px;
    border-radius:16px;
    box-shadow: 0 4px 12px rgba(223, 55, 167, 0.15);
}
.onboarding-shell .setup-option .option-icon svg {width: 22px;}
.onboarding-shell .setup-option .option-copy{display:flex;flex:1;flex-direction:column}
.onboarding-shell .setup-option .option-copy strong{font-size:20px;line-height:1.25;letter-spacing:-0.02em;margin-top: 6px;}
.onboarding-shell .setup-option .option-copy p{min-height:42px;margin-top:10px;font-size: 14px;color: #5c455c;}
.onboarding-shell .setup-option .option-features{grid-template-columns:1fr;gap:12px;margin-top: 18px;}
.onboarding-shell .setup-option .option-features > span {font-size: 13px;}
.onboarding-shell .setup-option .option-features b {font-size: 12.5px;}
.onboarding-shell .setup-option .option-note{margin-top:auto;padding-top: 16px;font-size: 11px;color: #8b718b;}
.onboarding-shell .setup-option .option-check{top:28px;right:28px;width:24px;height:24px;background: #fff;border-color: #eadfea;}

/* Langkah 3 */
.onboarding-shell .budget-panel{
    grid-template-columns:minmax(0,1.15fr) minmax(300px,.85fr);
    align-items:stretch;
    gap:22px;
}
.onboarding-shell .budget-panel>.field-group,
.onboarding-shell .included-list{
    box-sizing:border-box;
    min-height:220px;
    padding:32px;
    border:1px solid #eadfea;
    border-radius:24px;
    background:linear-gradient(155deg,#fffafd,#fff 60%);
    box-shadow: 0 4px 24px rgba(32, 0, 36, 0.03);
}
.onboarding-shell .budget-panel>.field-group{justify-content:center}
.onboarding-shell .budget-input{height:68px;margin-top:6px;border-radius: 18px; border: 1px solid rgba(109,82,109,.25);}
.onboarding-shell .budget-input em {font-size: 16px; left: 52px; color: #8b718b;}
.onboarding-shell .budget-input input {font-size: 24px; padding-left: 82px; letter-spacing: -0.02em; color: #240029; font-weight: 800;}
.onboarding-shell .budget-input:focus-within{border-color:#df37a7;background:#fff;box-shadow:0 0 0 3px rgba(223,55,167,.12)}
.onboarding-shell .budget-input > svg {width: 22px; left: 20px; color: #df37a7;}

.onboarding-shell .included-list{justify-content:center;gap:14px;background:#fff;border-color: rgba(223, 55, 167, 0.15);}
.onboarding-shell .included-list p{margin-bottom:6px;color:#240029;font-size:14px;font-weight:700;letter-spacing: -0.01em;}
.onboarding-shell .included-list span{font-size:13.5px;color: #5c455c; line-height: 1.5;}
.onboarding-shell .included-list svg {width: 16px; margin-top: 2px;}

.onboarding-shell .ready-state{
    padding:44px 28px;
    border:1px solid #eadfea;
    border-radius:24px;
    background:linear-gradient(145deg,#fffafd,#fff1f7);
    box-shadow: 0 12px 40px rgba(223, 55, 167, 0.08);
}
.onboarding-shell .ready-state h3 {font-size: 32px; letter-spacing: -0.03em; margin: 8px 0;}
.onboarding-shell .ready-state p:last-child {font-size: 14px; max-width: 440px; color: #5c455c; line-height: 1.7;}
.onboarding-shell .ready-icon {width: 80px; height: 80px; border-radius: 24px; background: #fff; box-shadow: 0 8px 24px rgba(223, 55, 167, 0.15); color: #df37a7;}
.onboarding-shell .ready-icon svg {width: 36px;}
.onboarding-shell .wizard-actions{margin-top:32px; padding-top: 28px;}

@media(max-width:1100px) and (min-width:901px){
    .wizard-layout{grid-template-columns:300px minmax(0,1fr)}
    .wizard-aside{padding:42px 30px}
    .wizard-aside h1{font-size:34px}
    .onboarding-shell .wizard-main{padding:40px 32px!important}
    .onboarding-shell .wizard-heading h2{font-size:42px}
    .onboarding-shell .setup-option{min-height:350px;padding:20px!important}
}
@media(max-width:760px){
    .onboarding-shell .setup-options{grid-template-columns:1fr!important}
    .onboarding-shell .setup-option{min-height:auto}
    .onboarding-shell .setup-option .option-copy p{min-height:0}
    .onboarding-shell .budget-panel{grid-template-columns:1fr}
    .onboarding-shell .budget-panel>.field-group,
    .onboarding-shell .included-list{min-height:0}
}
@media(max-width:560px){
    .onboarding-shell .wizard-main{padding:26px 16px 0!important}
    .onboarding-shell .mobile-progress{margin-bottom:24px}
    .onboarding-shell .wizard-heading h2{font-size:clamp(31px,10vw,38px)}
    .onboarding-shell .wizard-heading>span{margin-top:12px;line-height:1.55}
    .onboarding-shell .step-content{min-height:0;padding-top:24px}
    .onboarding-shell .couple-grid{grid-template-columns:1fr;padding:18px 18px 0;gap:15px}
    .onboarding-shell .field-group--date{padding:15px 18px 18px}
    .onboarding-shell .field-shell{height:50px}
    .onboarding-shell .setup-option{padding:20px!important}
    .onboarding-shell .setup-option .option-icon{width:40px;height:40px;margin-bottom:18px}
    .onboarding-shell .setup-option .option-copy strong{font-size:18px}
    .onboarding-shell .setup-option .option-check{top:20px;right:20px}
    .onboarding-shell .budget-panel>.field-group,
    .onboarding-shell .included-list{padding:20px}
    .onboarding-shell .ready-state{padding:30px 18px}
    .onboarding-shell .wizard-actions{margin-right:-16px;margin-left:-16px;padding-right:16px;padding-left:16px}
}
@media(max-height:760px) and (min-width:901px){
    .onboarding-shell .wizard-main{padding-top:28px!important;padding-bottom:28px!important}
    .onboarding-shell .wizard-heading h2{font-size:40px}
    .onboarding-shell .step-content{min-height:280px;padding-top:24px}
    .onboarding-shell .setup-option{min-height:280px}
}

/* Centered landing-page composition */
.onboarding-shell{
    background:
        radial-gradient(circle at 8% 12%,rgba(255,189,211,.32),transparent 28%),
        radial-gradient(circle at 92% 88%,rgba(255,204,17,.2),transparent 30%),
        #fffafd;
}
.onboarding-shell .wizard-layout{
    display:block;
    min-height:100dvh;
    background:transparent;
}
.onboarding-shell .wizard-aside{display:none}
.onboarding-shell .wizard-main{
    width:min(100%,1120px)!important;
    min-height:100dvh;
    box-sizing:border-box;
    margin:0 auto!important;
    padding:clamp(34px,6vh,68px) clamp(24px,5vw,64px)!important;
    align-items:center!important;
    background:transparent;
}
.onboarding-shell .mobile-progress{
    display:flex;
    width:min(100%,760px);
    margin:0 auto clamp(34px,6vh,60px);
    align-items:center;
    gap:16px;
    color:#6d526d;
    font-family:'JetBrains Mono',ui-monospace,monospace;
    font-size:11px;
    letter-spacing:.1em;
    text-transform:uppercase;
}
.onboarding-shell .mobile-progress>div{
    height:5px;
    flex:1;
    overflow:hidden;
    border-radius:999px;
    background:#eadfea;
}
.onboarding-shell .mobile-progress i{display:block;height:100%;border-radius:inherit;background:#df37a7}
.onboarding-shell .wizard-heading{text-align:center}
.onboarding-shell .wizard-heading>p{color:#b61f7d}
.onboarding-shell .wizard-heading h2{max-width:820px;margin-inline:auto;font-size:clamp(42px,5vw,64px)}
.onboarding-shell .wizard-heading>span{max-width:680px;margin-right:auto;margin-left:auto}
.onboarding-shell .step-content,
.onboarding-shell .wizard-actions{width:min(100%,960px)!important;margin-right:auto!important;margin-left:auto!important}
.onboarding-shell .step-content{min-height:330px}
.onboarding-shell .wizard-actions{background:rgba(255,250,253,.72);backdrop-filter:blur(10px)}

/* Consistent form surfaces */
.onboarding-shell .step-content{box-sizing:border-box}
.onboarding-shell .couple-grid,
.onboarding-shell .field-group--date{
    background:#fff;
}
.onboarding-shell .couple-grid,
.onboarding-shell .field-group--date,
.onboarding-shell .setup-option,
.onboarding-shell .budget-panel>.field-group,
.onboarding-shell .included-list,
.onboarding-shell .ready-state{
    border-color:rgba(109,82,109,.15);
    box-shadow:0 12px 30px rgba(32,0,36,.04);
}
.onboarding-shell .field-shell{
    border-color:rgba(109,82,109,.25);
    background:#fffafd;
    box-shadow:none;
}
.onboarding-shell .field-shell:hover{border-color:#8b718b}
.onboarding-shell .field-shell:focus-within{border-color:#df37a7;background:#fff;box-shadow:0 0 0 3px rgba(223,55,167,.12)}
.onboarding-shell .setup-option{background:rgba(255,255,255,.95)}
.onboarding-shell .setup-option:hover{border-color:rgba(223,55,167,.4);transform:translateY(-3px);box-shadow:0 16px 34px rgba(32,0,36,.06)}
.onboarding-shell .setup-option.selected,
.onboarding-shell .setup-option:first-child.selected{border-color:#df37a7;background:#fffafd;box-shadow:0 0 0 3px rgba(223,55,167,.11),0 14px 30px rgba(32,0,36,.06)}
.onboarding-shell .option-icon{background:#fff1bd}
.onboarding-shell .option-icon--plain{background:#ffbdd3}
.onboarding-shell .included-list{background:#fff}
.onboarding-shell .wizard-actions{padding:24px 0 0;border-top-color:rgba(109,82,109,.15);background:transparent;backdrop-filter:none}
.onboarding-shell .button-primary,
.onboarding-shell .button-secondary{min-height:50px;border-radius:14px;padding-inline:28px;font-size: 14.5px;letter-spacing: -0.01em; transition: all 0.2s;}
.onboarding-shell .button-primary:hover:not(:disabled) {transform: translateY(-2px); box-shadow: 0 8px 20px rgba(223, 55, 167, 0.25);}

/* Force layout overrides to resolve collisions */
.onboarding-shell .wizard-layout {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    min-height: 100dvh !important;
    width: 100% !important;
    max-width: none !important;
    padding: 0 !important;
    margin: 0 !important;
}

.onboarding-shell .wizard-main {
    display: flex !important;
    flex-direction: column !important;
    justify-content: center !important;
    align-items: center !important;
    width: 100% !important;
    max-width: 820px !important;
    min-height: auto !important;
    margin: 40px auto !important;
    padding: 40px !important;
    border: 1px solid #d4ccd4 !important;
    border-radius: 20px !important;
    background: #fff !important;
    box-shadow: 0 20px 50px rgba(32,0,36,.06) !important;
}

.onboarding-shell .mobile-progress {
    width: 100% !important;
    max-width: none !important;
    margin-bottom: 24px !important;
}

.onboarding-shell .wizard-heading {
    text-align: center !important;
}

.onboarding-shell .wizard-heading h2 {
    font-size: 38px !important;
    margin-inline: auto !important;
}

.onboarding-shell .wizard-heading span {
    margin-inline: auto !important;
}

.onboarding-shell .step-content,
.onboarding-shell .wizard-actions {
    width: 100% !important;
    max-width: none !important;
}

@media(max-width:900px) {
    .onboarding-shell .wizard-main {
        max-width: 680px !important;
        margin: 24px auto !important;
        padding: 30px !important;
    }
}

@media(max-width:760px) {
    .onboarding-shell .wizard-layout {
        display: block !important;
    }
    .onboarding-shell .wizard-main {
        max-width: none !important;
        margin: 0 !important;
        padding: 24px 16px !important;
        border: 0 !important;
        border-radius: 0 !important;
        box-shadow: none !important;
        background: transparent !important;
    }
    .onboarding-shell .wizard-heading {
        text-align: left !important;
    }
    .onboarding-shell .wizard-heading h2 {
        font-size: 32px !important;
        margin-left: 0 !important;
    }
    .onboarding-shell .wizard-heading span {
        margin-left: 0 !important;
    }
    .onboarding-shell .setup-options{grid-template-columns:1fr!important}
    .onboarding-shell .setup-option{min-height:auto}
    .onboarding-shell .setup-option .option-copy p{min-height:0}
    .onboarding-shell .budget-panel{grid-template-columns:1fr}
    .onboarding-shell .budget-panel>.field-group,
    .onboarding-shell .included-list{min-height:0}
}
@media(max-width:560px){
    .onboarding-shell .wizard-main{padding-right:16px!important;padding-left:16px!important}
    .onboarding-shell .mobile-progress{margin-bottom:26px}
    .onboarding-shell .wizard-heading h2{font-size:clamp(28px,10vw,34px)!important;}
    .onboarding-shell .wizard-heading>span{margin-top:12px;line-height:1.55;font-size: 14px;}
    .onboarding-shell .step-content{min-height:0;padding-top:24px}
    .onboarding-shell .couple-grid{grid-template-columns:1fr;padding:24px 24px 0;gap:18px}
    .onboarding-shell .field-group--date{padding:18px 24px 24px}
    .onboarding-shell .field-shell{height:52px}
    .onboarding-shell .setup-option{padding:24px!important}
    .onboarding-shell .setup-option .option-icon{width:44px;height:44px;margin-bottom:20px}
    .onboarding-shell .setup-option .option-copy strong{font-size:18px}
    .onboarding-shell .setup-option .option-check{top:24px;right:24px}
    .onboarding-shell .budget-panel>.field-group,
    .onboarding-shell .included-list{padding:24px}
    .onboarding-shell .budget-input {height: 60px;}
    .onboarding-shell .budget-input input {font-size: 20px;}
    .onboarding-shell .ready-state{padding:34px 24px}
    .onboarding-shell .ready-state h3 {font-size: 26px;}
    .onboarding-shell .ready-icon {width: 64px; height: 64px; margin-bottom: 20px;}
    .onboarding-shell .ready-icon svg {width: 28px;}
    .onboarding-shell .wizard-actions{width:auto!important;margin-right:-16px;margin-left:-16px;padding-right:16px;padding-left:16px}
}

/* Final centered composition — aligned with the landing-page hero. */
.onboarding-shell .wizard-layout{
    padding:clamp(24px,5vw,64px)!important;
    background:linear-gradient(145deg,#ffbdd3,#fff1bd 42%,#fff1bd 62%,#ffcb0f)!important;
}
.onboarding-shell .wizard-main{
    width:min(100%,900px)!important;
    max-width:900px!important;
    height:760px!important;
    min-height:760px!important;
    max-height:calc(100dvh - 48px)!important;
    box-sizing:border-box;
    margin:auto!important;
    padding:clamp(30px,5vw,56px)!important;
    border:1px solid rgba(41,0,41,.11)!important;
    border-radius:14px!important;
    background:rgba(255,255,255,.94)!important;
    box-shadow:0 0 0 1px rgba(41,0,41,.08),0 16px 40px rgba(32,0,36,.09)!important;
    backdrop-filter:blur(12px);
}
.onboarding-shell .mobile-progress{width:min(100%,680px)!important;margin-bottom:28px!important;flex:none}
.onboarding-shell .wizard-heading{width:100%;height:190px;flex:none;text-align:center!important}
.onboarding-shell .wizard-heading>p{color:#240029}
.onboarding-shell .wizard-heading h2{max-width:720px!important;font-size:clamp(38px,5vw,56px)!important;letter-spacing:-.045em}
.onboarding-shell .wizard-heading>span{max-width:640px!important;font-size:16px;line-height:1.6}
.onboarding-shell .step-content,.onboarding-shell .wizard-actions{width:min(100%,760px)!important}
.onboarding-shell .step-content{display:flex;min-height:0;height:290px;padding-top:24px;align-items:stretch;justify-content:center;flex-direction:column}
.onboarding-shell .step-content--budget{height:320px;padding:20px 0 8px;box-sizing:border-box;justify-content:center}
.onboarding-shell .step-content--budget .budget-panel{margin:0;transform:translateY(9px)}
.onboarding-shell .step-content>*, .onboarding-shell .couple-grid,.onboarding-shell .setup-options,.onboarding-shell .budget-panel,.onboarding-shell .ready-state{width:100%;box-sizing:border-box}
.onboarding-shell .couple-grid,.onboarding-shell .field-group--date{flex:none}
.onboarding-shell .couple-grid .field-group{min-width:0}
.onboarding-shell .field-shell input{min-width:0}
.onboarding-shell .wizard-actions{margin-top:auto!important;flex:none}
.onboarding-shell .couple-grid{padding:26px 26px 0;border-radius:14px 14px 0 0}
.onboarding-shell .field-group--date{padding:20px 26px 26px;border-radius:0 0 14px 14px}
.onboarding-shell .field-shell{height:52px;border-radius:10px}
.onboarding-shell .field-helper{margin-top:2px;color:#6d526d;font-size:12px;line-height:1.5}
.onboarding-shell .setup-options{grid-template-columns:repeat(2,minmax(0,1fr))!important;gap:16px!important}
.onboarding-shell .setup-option{min-height:250px;padding:26px!important;border-radius:14px}
.onboarding-shell .setup-option .option-icon{width:44px;height:44px;margin-bottom:24px;border-radius:6px;box-shadow:none}
.onboarding-shell .setup-option .option-copy{display:block}
.onboarding-shell .setup-option .option-copy strong{margin-top:8px;font-size:19px}
.onboarding-shell .setup-option .option-copy p{min-height:44px;margin-top:10px;font-size:13px;line-height:1.55}
.onboarding-shell .setup-option .option-features{display:grid;grid-template-columns:1fr;gap:9px;margin-top:18px;padding-top:16px;border-top:1px solid #d4ccd4}
.onboarding-shell .setup-option .option-features>span{align-items:center;color:#6d526d;font-size:12px}
.onboarding-shell .setup-option .option-features svg{color:#240029}
.onboarding-shell .setup-option .option-check{display:grid;place-items:center;border:1px solid #d4ccd4;background:#fff;color:transparent;transition:background .18s,border-color .18s,color .18s}
.onboarding-shell .setup-option.selected .option-check{border-color:#240029!important;background:#240029!important;color:#fff!important}
.onboarding-shell .setup-option.selected .option-check svg{display:block;width:13px;height:13px;stroke-width:3}
.onboarding-shell .budget-panel{display:grid!important;grid-template-columns:minmax(0,1.15fr) minmax(260px,.85fr)!important;gap:0!important;overflow:hidden;border:1px solid rgba(109,82,109,.18);border-radius:14px;background:#fff;box-shadow:0 12px 30px rgba(32,0,36,.04)}
.onboarding-shell .budget-panel>.budget-field,.onboarding-shell .budget-panel>.included-list{min-height:230px;padding:30px!important;border:0!important;border-radius:0!important;box-shadow:none!important}
.onboarding-shell .budget-panel>.budget-field{justify-content:center;background:#fffafd}
.onboarding-shell .budget-panel>.included-list{justify-content:center;border-left:1px solid #d4ccd4!important;background:#fff}
.onboarding-shell .budget-input{height:62px!important;margin-top:4px;border:1px solid rgba(109,82,109,.35)!important;border-radius:10px!important;background:#fff!important}
.onboarding-shell .budget-input>svg{left:18px!important;width:20px!important;color:#6d526d!important}
.onboarding-shell .budget-input em{left:50px!important;color:#6d526d!important;font-size:14px!important}
.onboarding-shell .budget-input input{padding-left:76px!important;font-size:22px!important;font-weight:700!important}
.onboarding-shell .included-list p{margin:0 0 4px;font-size:13px;font-weight:700}
.onboarding-shell .included-list span{font-size:12px;line-height:1.5}
.onboarding-shell .included-list svg{color:#15803d}
.onboarding-shell .ready-state{max-height:270px;padding:28px;border-radius:14px}
.onboarding-shell .ready-state .ready-icon{width:64px;height:64px;margin-bottom:14px;border-radius:20px}
.onboarding-shell .ready-state h3{font-size:28px}
.onboarding-shell .ready-state>p:last-child{margin-top:8px}
.onboarding-shell .button-primary,.onboarding-shell .button-secondary{min-height:48px;border-radius:6px}
.onboarding-shell .button-primary{background:#df37a7}
.onboarding-shell .button-secondary{background:#fff}
@media(max-width:760px){
    .onboarding-shell .wizard-layout{padding:0!important;background:linear-gradient(145deg,rgba(255,189,211,.5),#fff1bd 34%,#fff 68%)!important}
    .onboarding-shell .wizard-main{height:auto!important;min-height:100dvh!important;max-height:none!important;padding:28px 20px 0!important;border:0!important;border-radius:0!important;background:rgba(255,255,255,.88)!important;box-shadow:none!important}
    .onboarding-shell .wizard-heading{height:auto;text-align:center!important}
    .onboarding-shell .step-content{height:auto;min-height:340px}
    .onboarding-shell .step-content--budget{height:auto;padding:44px 0 28px}
    .onboarding-shell .step-content--budget .budget-panel{transform:none}
    .onboarding-shell .wizard-heading{text-align:center!important}
    .onboarding-shell .wizard-heading h2{margin-inline:auto!important;font-size:clamp(34px,9vw,46px)!important}
    .onboarding-shell .wizard-heading span{margin-inline:auto!important}
    .onboarding-shell .setup-options{grid-template-columns:1fr!important}
    .onboarding-shell .budget-panel{grid-template-columns:1fr!important}
    .onboarding-shell .budget-panel>.budget-field,.onboarding-shell .budget-panel>.included-list{min-height:auto;padding:22px!important}
    .onboarding-shell .budget-panel>.included-list{border-top:1px solid #d4ccd4!important;border-left:0!important}
}
@media(max-width:560px){
    .onboarding-shell .mobile-progress{margin-bottom:28px!important}
    .onboarding-shell .wizard-heading h2{font-size:clamp(31px,10vw,38px)!important}
    .onboarding-shell .step-content{padding-top:26px}
    .onboarding-shell .couple-grid{padding:20px 20px 0}
    .onboarding-shell .field-group--date{padding:16px 20px 20px}
    .onboarding-shell .setup-option{padding:20px!important}
}
</style>
