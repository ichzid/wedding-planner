<script setup>
import { Head } from '@inertiajs/vue3';
import { onBeforeUnmount, onMounted } from 'vue';
import PublicFooter from '@/Components/PublicFooter.vue';
import PublicHeader from '@/Components/PublicHeader.vue';
import {
    ArrowRight,
    BadgeCheck,
    CalendarCheck2,
    CheckCircle2,
    ChevronDown,
    ClipboardCheck,
    FileCheck2,
    Gift,
    HeartHandshake,
    LayoutDashboard,
    UsersRound,
    WalletCards,
} from '@lucide/vue';

const features = [
    {
        icon: LayoutDashboard,
        title: 'Dashboard',
        description: 'Lihat progres persiapan, kondisi anggaran, dan hari menuju pernikahan dalam satu layar.',
    },
    {
        icon: ClipboardCheck,
        title: 'Daftar Persiapan',
        description: 'Susun tugas, tentukan tenggat, dan tandai satu per satu persiapan yang sudah selesai.',
    },
    {
        icon: WalletCards,
        title: 'Anggaran Pernikahan',
        description: 'Rencanakan biaya, catat pengeluaran aktual, dan pantau sumber dana dengan lebih tenang.',
    },
    {
        icon: Gift,
        title: 'Seserahan',
        description: 'Catat barang, harga, jumlah, dan status seserahan agar tidak ada yang terlupa.',
    },
    {
        icon: FileCheck2,
        title: 'Dokumen KUA',
        description: 'Pantau kelengkapan dokumen calon pengantin pria dan wanita secara terpisah.',
    },
    {
        icon: UsersRound,
        title: 'Daftar Undangan',
        description: 'Kelola data undangan, jumlah tamu, dan konfirmasi kehadiran dalam satu daftar.',
    },
];

const problems = [
    { icon: WalletCards, text: 'Budget terasa sulit dikontrol' },
    { icon: ClipboardCheck, text: 'Persiapan mudah tercecer dan terlupa' },
    { icon: FileCheck2, text: 'Dokumen serta seserahan belum terorganisir' },
    { icon: UsersRound, text: 'Daftar undangan tersebar di banyak tempat' },
];

const steps = [
    { number: '01', icon: WalletCards, title: 'Hitung', description: 'Dapatkan gambaran awal kebutuhan budget pernikahanmu secara gratis.' },
    { number: '02', icon: HeartHandshake, title: 'Rencanakan', description: 'Simpan hasil dan jadikan sebagai titik awal Wedding Plan milikmu.' },
    { number: '03', icon: ClipboardCheck, title: 'Atur', description: 'Kelola anggaran, tugas, dokumen, seserahan, dan daftar undangan.' },
    { number: '04', icon: LayoutDashboard, title: 'Pantau', description: 'Lihat progres seluruh persiapan sampai hari bahagia tiba.' },
];

let revealObserver;

onMounted(() => {
    const sections = document.querySelectorAll('[data-reveal]');

    revealObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            entry.target.classList.add('is-visible');
            revealObserver.unobserve(entry.target);
        });
    }, { threshold: 0.14, rootMargin: '0px 0px -60px' });

    sections.forEach((section) => revealObserver.observe(section));
});

onBeforeUnmount(() => revealObserver?.disconnect());

const faqs = [
    {
        question: 'Apakah Ruang Pasangan gratis?',
        answer: 'Ya. Kamu bisa mencoba Budget Estimator secara gratis dan mulai mengatur persiapan pernikahan di Ruang Pasangan.',
    },
    {
        question: 'Apakah harus membuat akun untuk menghitung budget?',
        answer: 'Tidak. Budget Estimator dapat digunakan tanpa login. Akun baru diperlukan saat kamu ingin menyimpan hasil dan membuat Wedding Plan.',
    },
    {
        question: 'Apakah hasil estimator merupakan harga pasti?',
        answer: 'Bukan. Hasilnya adalah gambaran awal berbentuk estimasi atau rentang. Biaya sebenarnya dapat berbeda sesuai lokasi, vendor, konsep, dan kebutuhanmu.',
    },
    {
        question: 'Apakah hasil estimasi bisa diubah?',
        answer: 'Bisa. Hasil estimator hanya menjadi titik awal dan tetap dapat kamu sesuaikan ketika membuat Wedding Plan.',
    },
];
</script>

<template>
    <Head>
        <title>Rencanakan Pernikahan Tanpa Bikin Pusing</title>
        <meta name="description" content="Atur budget, persiapan, seserahan, dokumen KUA, dan daftar undangan dalam satu tempat bersama Ruang Pasangan." />
    </Head>

    <div class="landing-page">
        <PublicHeader />

        <main>
            <section id="beranda" class="hero reveal-section" data-reveal>
                <div class="container hero-grid">
                    <div class="hero-copy">
                        <h1>Siapkan Pernikahanmu <em>Tanpa Bikin Pusing</em></h1>
                        <p>Atur budget, persiapan, seserahan, dokumen, dan daftar undangan dalam satu tempat. Lebih rapi, lebih terarah, dan bisa dipantau bersama.</p>
                        <div class="hero-actions">
                            <a href="/budget-estimator" class="button button-primary button-large">
                                Hitung Budget Nikah Gratis <ArrowRight aria-hidden="true" />
                            </a>
                            <a href="#fitur" class="button button-ghost button-large">Lihat Semua Fitur</a>
                        </div>
                        <div class="hero-note"><CheckCircle2 aria-hidden="true" /> Coba estimator tanpa perlu membuat akun</div>
                    </div>

                    <div class="product-preview" aria-label="Pratinjau dashboard Ruang Pasangan">
                        <div class="preview-topbar">
                            <div class="preview-brand"><img src="/ruang-pasangan.svg" alt="" /><span>Ruang Pasangan</span></div>
                            <span class="preview-avatar">R</span>
                        </div>
                        <div class="preview-body">
                            <div class="preview-heading">
                                <div><small>Selamat datang kembali</small><strong>Hari Bahagia Kita</strong></div>
                                <span class="date-chip"><CalendarCheck2 aria-hidden="true" /> 128 hari lagi</span>
                            </div>
                            <div class="preview-stats">
                                <article><span><ClipboardCheck /></span><small>Persiapan</small><strong>68%</strong></article>
                                <article><span><WalletCards /></span><small>Anggaran</small><strong>Rp40 jt</strong></article>
                                <article><span><UsersRound /></span><small>Undangan</small><strong>186</strong></article>
                            </div>
                            <div class="preview-panels">
                                <article class="progress-panel">
                                    <div class="panel-title"><strong>Progres Persiapan</strong><span>17/25 selesai</span></div>
                                    <div class="progress-track"><span></span></div>
                                    <ul>
                                        <li><CheckCircle2 /> Tentukan konsep pernikahan</li>
                                        <li><CheckCircle2 /> Pesan venue acara</li>
                                        <li class="pending"><span></span> Pilih vendor dokumentasi</li>
                                    </ul>
                                </article>
                                <article class="budget-panel">
                                    <small>Budget digunakan</small>
                                    <strong>Rp24,5 jt</strong>
                                    <div class="budget-ring"><span>61%</span></div>
                                </article>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="problem-section reveal-section" data-reveal>
                <div class="container problem-grid">
                    <div>
                        <p class="section-kicker">Semua terasa lebih ringan saat tertata</p>
                        <h2>Banyak hal kecil bisa terasa besar kalau diurus sendirian.</h2>
                    </div>
                    <div class="problem-content">
                        <div class="problem-list">
                            <div v-for="problem in problems" :key="problem.text"><span><component :is="problem.icon" aria-hidden="true" /></span>{{ problem.text }}</div>
                        </div>
                        <a :href="route('register')" class="button button-primary problem-cta">
                            Coba Wedding Planner <ArrowRight aria-hidden="true" />
                        </a>
                    </div>
                </div>
            </section>

            <section id="fitur" class="features-section section-space reveal-section" data-reveal>
                <div class="container">
                    <div class="section-heading centered">
                        <p class="section-kicker">Semua dalam satu ruang</p>
                        <h2>Satu tempat untuk seluruh rencana hari bahagiamu.</h2>
                        <p>Tidak perlu lagi berpindah-pindah catatan. Kelola kebutuhan utama persiapan pernikahan dengan alur yang sederhana dan mudah dipahami.</p>
                    </div>
                    <div class="feature-grid">
                        <article v-for="feature in features" :key="feature.title" class="feature-card">
                            <span class="feature-icon"><component :is="feature.icon" aria-hidden="true" /></span>
                            <h3>{{ feature.title }}</h3>
                            <p>{{ feature.description }}</p>
                        </article>
                    </div>
                    <div class="feature-cta-wrap">
                        <a :href="route('register')" class="button button-primary feature-cta">
                            Rencanakan Pernikahanmu Sekarang <ArrowRight aria-hidden="true" />
                        </a>
                    </div>
                </div>
            </section>

            <section id="cara-kerja" class="how-section section-space reveal-section" data-reveal>
                <div class="container">
                    <div class="section-heading">
                        <p class="section-kicker">Cara kerja Ruang Pasangan</p>
                        <h2>Mulai dari hitung budget, lanjutkan menjadi rencana nyata.</h2>
                    </div>
                    <div class="steps-grid">
                        <article v-for="(item, index) in steps" :key="item.number" class="step-card">
                            <div class="step-top">
                                <span class="step-number">Tahap {{ item.number }}</span>
                                <ArrowRight v-if="index < steps.length - 1" aria-hidden="true" />
                            </div>
                            <span class="step-icon"><component :is="item.icon" aria-hidden="true" /></span>
                            <h3>{{ item.title }}</h3>
                            <p>{{ item.description }}</p>
                        </article>
                    </div>
                </div>
            </section>

            <section class="estimator-section section-space reveal-section" data-reveal>
                <div class="container estimator-card">
                    <div class="estimator-copy">
                        <h2>Belum tahu budget nikah yang perlu disiapkan?</h2>
                        <p>Jawab beberapa pertanyaan sederhana tentang konsep, lokasi, budget yang tersedia, dan jumlah tamu. Kami akan memberikan gambaran awal kebutuhanmu.</p>
                        <a href="/budget-estimator" class="button button-light button-large">Coba Budget Estimator <ArrowRight aria-hidden="true" /></a>
                    </div>
                    <div class="estimate-result">
                        <div class="result-label"><span>Contoh hasil estimasi</span><BadgeCheck aria-hidden="true" /></div>
                        <small>Estimasi kebutuhan</small>
                        <strong>Rp35–45 juta</strong>
                        <div class="result-row"><span>Budget tersedia</span><b>Rp30 juta</b></div>
                        <div class="result-row"><span>Perkiraan gap</span><b>Rp5–15 juta</b></div>
                        <p>Hasil berupa gambaran awal dan dapat disesuaikan kembali.</p>
                    </div>
                </div>
            </section>

            <section id="faq" class="faq-section section-space reveal-section" data-reveal>
                <div class="container faq-grid">
                    <div class="section-heading faq-heading">
                        <p class="section-kicker">Pertanyaan yang sering ditanyakan</p>
                        <h2>Masih ada yang ingin kamu ketahui?</h2>
                        <p>Kami merangkum beberapa hal penting sebelum kamu mulai menggunakan Ruang Pasangan.</p>
                    </div>
                    <div class="faq-list">
                        <details v-for="faq in faqs" :key="faq.question">
                            <summary>{{ faq.question }}<ChevronDown aria-hidden="true" /></summary>
                            <p>{{ faq.answer }}</p>
                        </details>
                    </div>
                </div>
            </section>

            <section class="final-cta reveal-section" data-reveal>
                <div class="container final-cta-inner">
                    <HeartHandshake class="final-icon" aria-hidden="true" />
                    <p class="section-kicker">Satu langkah kecil untuk rencana yang lebih besar</p>
                    <h2>Mulai siapkan hari bahagiamu sekarang.</h2>
                    <p>Hitung perkiraan budget secara gratis. Tidak perlu login, tidak perlu mengisi data pasangan.</p>
                    <a href="/budget-estimator" class="button button-primary button-large">Hitung Budget Nikah Gratis <ArrowRight aria-hidden="true" /></a>
                </div>
            </section>
        </main>

        <PublicFooter />
    </div>
</template>

<style scoped>
.landing-page{overflow:hidden;background:var(--bg);color:var(--text)}
.container{width:min(1160px,calc(100% - 40px));margin:0 auto}.site-header{position:fixed;z-index:50;top:0;right:0;left:0;border-bottom:1px solid rgba(211,222,201,.72);background:rgba(246,248,241,.88);backdrop-filter:blur(18px)}
.header-inner{display:flex;align-items:center;justify-content:space-between;height:74px;gap:28px}.brand{display:flex;align-items:center;gap:10px;color:var(--text);text-decoration:none}.brand img{width:40px;height:40px}.brand span{display:flex;flex-direction:column;line-height:1.08}.brand strong{font-family:var(--font-display);font-size:16px}.brand small{margin-top:4px;color:var(--text-muted);font-size:8px;font-weight:700;letter-spacing:.12em;text-transform:uppercase}.desktop-nav{display:flex;align-items:center;gap:32px}.desktop-nav a,.login-link{color:var(--text-muted);font-size:13px;font-weight:600;text-decoration:none;transition:.2s}.desktop-nav a:hover,.login-link:hover{color:var(--accent-hover)}.header-actions{display:flex;align-items:center;gap:18px}.button{display:inline-flex;align-items:center;justify-content:center;gap:9px;min-height:42px;padding:0 18px;border:1px solid transparent;border-radius:10px;font-size:12px;font-weight:700;text-decoration:none;transition:transform .2s,box-shadow .2s,background .2s,color .2s}.button:hover{transform:translateY(-2px)}.button svg{width:16px}.button-primary{background:var(--accent);color:#fff;box-shadow:0 9px 20px rgba(88,123,75,.2)}.button-primary:hover{background:var(--accent-hover);box-shadow:0 12px 26px rgba(88,123,75,.26)}.button-ghost{border-color:var(--border);background:rgba(255,255,255,.74);color:var(--text-muted)}.button-ghost:hover{border-color:var(--border-strong);color:var(--accent-hover)}.button-large{min-height:50px;padding:0 22px;font-size:13px}
.hero{position:relative;padding:158px 0 90px;background:linear-gradient(145deg,#f7f9f3 0%,#edf3e7 100%)}.hero-grid{position:relative;z-index:2;display:grid;grid-template-columns:minmax(0,.9fr) minmax(500px,1.1fr);align-items:center;gap:68px}.eyebrow-pill{display:inline-flex;align-items:center;gap:7px;margin-bottom:22px;padding:8px 12px;border:1px solid var(--border);border-radius:99px;background:rgba(255,255,255,.7);color:var(--accent-hover);font-size:10px;font-weight:700;letter-spacing:.07em;text-transform:uppercase}.eyebrow-pill svg{width:14px}.hero h1{max-width:630px;margin:0;font-family:var(--font-display);font-size:clamp(45px,5.5vw,72px);font-weight:600;line-height:1.04;letter-spacing:-.045em}.hero h1 em{display:block;color:var(--accent);font-weight:500}.hero-copy>p{max-width:590px;margin:24px 0 28px;color:var(--text-muted);font-size:16px;line-height:1.75}.hero-actions{display:flex;align-items:center;flex-wrap:wrap;gap:12px}.hero-note{display:flex;align-items:center;gap:7px;margin-top:18px;color:var(--text-muted);font-size:11px}.hero-note svg{width:15px;color:var(--accent)}.hero-orb{position:absolute;border-radius:50%;backface-visibility:hidden;transform-origin:center;will-change:transform,opacity}.hero-orb-one{top:100px;right:-160px;width:420px;height:420px;border:1px solid rgba(111,146,95,.16)}.hero-orb-two{bottom:-200px;left:-120px;width:370px;height:370px;background:rgba(226,237,218,.65)}
.product-preview{overflow:hidden;border:1px solid rgba(183,200,170,.9);border-radius:22px;background:#fff;box-shadow:0 35px 80px rgba(50,72,46,.16);transform:rotate(1.2deg);transition:.35s}.product-preview:hover{transform:rotate(0) translateY(-5px)}.preview-topbar{display:flex;align-items:center;justify-content:space-between;padding:14px 18px;border-bottom:1px solid var(--border);background:#fafcf8}.preview-brand{display:flex;align-items:center;gap:7px;font-family:var(--font-display);font-size:11px;font-weight:600}.preview-brand img{width:25px}.preview-avatar{display:grid;place-items:center;width:25px;height:25px;border-radius:8px;background:var(--accent);color:#fff;font-size:9px;font-weight:800}.preview-body{padding:22px}.preview-heading{display:flex;align-items:center;justify-content:space-between;gap:15px}.preview-heading div{display:flex;flex-direction:column}.preview-heading small{color:var(--text-dim);font-size:8px}.preview-heading strong{font-family:var(--font-display);font-size:19px}.date-chip{display:flex;align-items:center;gap:5px;padding:6px 9px;border-radius:8px;background:var(--accent-soft);color:var(--accent-hover);font-size:8px;font-weight:700}.date-chip svg{width:12px}.preview-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:9px;margin-top:19px}.preview-stats article{display:grid;grid-template-columns:30px 1fr;align-items:center;padding:10px;border:1px solid var(--border);border-radius:10px}.preview-stats article>span{grid-row:span 2;display:grid;place-items:center;width:25px;height:25px;border-radius:7px;background:var(--accent-soft);color:var(--accent)}.preview-stats svg{width:13px}.preview-stats small{color:var(--text-dim);font-size:7px}.preview-stats strong{font-size:11px}.preview-panels{display:grid;grid-template-columns:1.6fr .8fr;gap:10px;margin-top:10px}.preview-panels article{border:1px solid var(--border);border-radius:10px;padding:13px}.panel-title{display:flex;justify-content:space-between;gap:8px;font-size:8px}.panel-title span{color:var(--text-dim)}.progress-track{height:5px;margin:10px 0 12px;border-radius:99px;background:var(--surface-muted)}.progress-track span{display:block;width:68%;height:100%;border-radius:inherit;background:var(--accent)}.progress-panel ul{display:grid;gap:7px;margin:0;padding:0;list-style:none;color:var(--text-muted);font-size:7px}.progress-panel li{display:flex;align-items:center;gap:6px}.progress-panel li svg{width:11px;color:var(--accent)}.progress-panel li.pending span{width:10px;height:10px;border:1px solid var(--border-strong);border-radius:50%}.budget-panel{position:relative;overflow:hidden}.budget-panel small{display:block;color:var(--text-dim);font-size:7px}.budget-panel>strong{display:block;margin-top:4px;font-size:12px}.budget-ring{display:grid;place-items:center;width:65px;height:65px;margin:13px auto 0;border:7px solid var(--accent-soft);border-top-color:var(--accent);border-right-color:var(--accent);border-radius:50%;transform:rotate(24deg)}.budget-ring span{font-size:9px;font-weight:800;transform:rotate(-24deg)}
.section-space{padding:105px 0}.section-kicker{margin:0 0 12px;color:var(--accent-hover);font-size:10px;font-weight:800;letter-spacing:.12em;text-transform:uppercase}.section-heading{max-width:650px}.section-heading.centered{margin:0 auto 46px;text-align:center}.section-heading h2,.problem-grid h2,.estimator-copy h2,.final-cta h2{margin:0;font-family:var(--font-display);font-size:clamp(32px,4vw,48px);font-weight:600;line-height:1.14;letter-spacing:-.035em}.section-heading>p:last-child{margin:17px 0 0;color:var(--text-muted);font-size:14px;line-height:1.75}
.problem-section{padding:70px 0;background:var(--accent-hover);color:#fff}.problem-grid{display:grid;grid-template-columns:1fr 1fr;align-items:center;gap:90px}.problem-grid .section-kicker{color:#dcebd3}.problem-grid h2{font-size:clamp(30px,3.4vw,42px)}.problem-list{display:grid;grid-template-columns:1fr 1fr;gap:12px}.problem-list>div{display:flex;align-items:center;gap:10px;padding:14px;border:1px solid rgba(255,255,255,.14);border-radius:12px;background:rgba(255,255,255,.07);font-size:11px;font-weight:600}.problem-list span{display:grid;place-items:center}.problem-list svg{width:16px;color:#dcebd3}
.features-section{background:#fff}.feature-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px}.feature-card{padding:27px;border:1px solid var(--border);border-radius:16px;background:var(--surface);transition:.25s}.feature-card:hover{border-color:var(--border-strong);box-shadow:var(--shadow);transform:translateY(-4px)}.feature-icon{display:grid;place-items:center;width:44px;height:44px;margin-bottom:19px;border-radius:13px;background:var(--accent-soft);color:var(--accent-hover)}.feature-icon svg{width:21px}.feature-card h3{margin:0 0 9px;font-family:var(--font-display);font-size:20px}.feature-card p{margin:0;color:var(--text-muted);font-size:12px;line-height:1.7}
.how-section{background:var(--bg)}.how-section .section-heading{margin-bottom:43px}.steps-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px}.step-card{padding:24px;border:1px solid var(--border);border-radius:15px;background:rgba(255,255,255,.76)}.step-top{display:flex;align-items:center;justify-content:space-between;color:var(--accent);font-size:10px;font-weight:800;letter-spacing:.1em}.step-top svg{width:18px;color:var(--border-strong)}.step-card h3{margin:25px 0 8px;font-family:var(--font-display);font-size:22px}.step-card p{margin:0;color:var(--text-muted);font-size:11px;line-height:1.7}
.estimator-section{padding-top:10px}.estimator-card{display:grid;grid-template-columns:1.2fr .8fr;align-items:center;gap:75px;padding:66px;border-radius:24px;background:linear-gradient(135deg,#587b4b,#759665);color:#fff;box-shadow:var(--shadow-lg)}.estimator-icon{display:grid;place-items:center;width:48px;height:48px;margin-bottom:22px;border-radius:14px;background:rgba(255,255,255,.14)}.estimator-icon svg{width:23px}.estimator-copy .section-kicker{color:#dcebd3}.estimator-copy h2{font-size:clamp(32px,3.6vw,46px)}.estimator-copy>p:not(.section-kicker){max-width:610px;margin:18px 0 27px;color:rgba(255,255,255,.76);font-size:13px;line-height:1.75}.button-light{background:#fff;color:var(--accent-hover);box-shadow:0 12px 30px rgba(25,45,22,.18)}.estimate-result{padding:27px;border:1px solid rgba(255,255,255,.25);border-radius:18px;background:rgba(255,255,255,.96);color:var(--text);box-shadow:0 18px 40px rgba(31,53,28,.18)}.result-label{display:flex;align-items:center;justify-content:space-between;margin-bottom:22px;color:var(--accent-hover);font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.06em}.result-label svg{width:18px}.estimate-result>small{display:block;color:var(--text-muted);font-size:10px}.estimate-result>strong{display:block;margin:3px 0 22px;font-family:var(--font-display);font-size:30px}.result-row{display:flex;justify-content:space-between;padding:11px 0;border-top:1px solid var(--border);font-size:11px}.estimate-result>p{margin:15px 0 0;color:var(--text-dim);font-size:9px;line-height:1.5}
.faq-section{background:#fff}.faq-grid{display:grid;grid-template-columns:.75fr 1.25fr;gap:100px}.faq-heading{position:sticky;top:115px;align-self:start}.faq-list{border-top:1px solid var(--border)}.faq-list details{border-bottom:1px solid var(--border)}.faq-list summary{display:flex;align-items:center;justify-content:space-between;gap:20px;padding:21px 0;cursor:pointer;font-family:var(--font-display);font-size:17px;font-weight:600;list-style:none}.faq-list summary::-webkit-details-marker{display:none}.faq-list summary svg{width:18px;color:var(--accent);transition:.2s}.faq-list details[open] summary svg{transform:rotate(180deg)}.faq-list details p{margin:-5px 40px 21px 0;color:var(--text-muted);font-size:12px;line-height:1.75}
.final-cta{padding:35px 0 105px;background:#fff}.final-cta-inner{display:flex;flex-direction:column;align-items:center;padding:72px 30px;border:1px solid var(--border);border-radius:24px;background:linear-gradient(145deg,var(--bg),var(--accent-soft));text-align:center}.final-icon{width:42px;height:42px;margin-bottom:20px;color:var(--accent)}.final-cta h2{max-width:700px}.final-cta-inner>p:not(.section-kicker){max-width:590px;margin:17px 0 27px;color:var(--text-muted);font-size:13px}.site-footer{padding:62px 0 25px;background:#263324;color:#fff}.footer-inner{display:flex;justify-content:space-between;gap:60px}.site-footer .brand{color:#fff}.footer-brand>p{max-width:310px;margin:17px 0 0;color:rgba(255,255,255,.58);font-size:11px}.footer-links{display:flex;gap:80px}.footer-links>div{display:flex;flex-direction:column;gap:10px;min-width:100px}.footer-links strong{margin-bottom:5px;font-size:10px;text-transform:uppercase;letter-spacing:.1em}.footer-links a{color:rgba(255,255,255,.58);font-size:11px;text-decoration:none}.footer-links a:hover{color:#fff}.footer-bottom{display:flex;justify-content:space-between;margin-top:55px;padding-top:22px;border-top:1px solid rgba(255,255,255,.1);color:rgba(255,255,255,.4);font-size:9px}
@media(max-width:900px){.desktop-nav{display:none}.hero{padding-top:130px}.hero-grid{grid-template-columns:1fr;gap:55px}.hero-copy{text-align:center}.hero h1,.hero-copy>p{margin-right:auto;margin-left:auto}.hero-actions,.hero-note{justify-content:center}.product-preview{max-width:650px;margin:auto}.problem-grid{grid-template-columns:1fr;gap:35px}.feature-grid{grid-template-columns:repeat(2,1fr)}.steps-grid{grid-template-columns:repeat(2,1fr)}.estimator-card{grid-template-columns:1fr;padding:50px;gap:40px}.faq-grid{grid-template-columns:1fr;gap:42px}.faq-heading{position:static}.footer-inner{flex-direction:column}}
@media(max-width:600px){.container{width:min(100% - 28px,1160px)}.header-inner{height:66px}.brand img{width:35px;height:35px}.brand strong{font-size:14px}.login-link{display:none}.header-cta{min-height:38px;padding:0 13px;font-size:10px}.hero{padding:115px 0 65px}.hero h1{font-size:42px}.hero-copy>p{font-size:14px}.hero-actions{flex-direction:column}.hero-actions .button{width:100%}.product-preview{border-radius:16px}.preview-body{padding:14px}.preview-stats{grid-template-columns:1fr}.preview-stats article{display:flex;gap:8px}.preview-stats article>span{grid-row:auto}.preview-stats small{flex:1}.preview-panels{grid-template-columns:1fr}.budget-panel{display:none}.problem-section{padding:55px 0}.problem-list{grid-template-columns:1fr}.section-space{padding:75px 0}.feature-grid,.steps-grid{grid-template-columns:1fr}.section-heading.centered{text-align:left}.feature-card{padding:22px}.estimator-section{padding-top:0}.estimator-card{width:100%;padding:52px 24px;border-radius:0}.estimate-result{padding:22px}.faq-list summary{font-size:15px}.final-cta{padding-bottom:75px}.final-cta-inner{width:100%;padding:60px 22px;border-radius:0}.footer-links{width:100%;gap:45px}.footer-bottom{align-items:flex-start;flex-direction:column;gap:8px}.preview-heading strong{font-size:16px}}
/* Creative sketchbook theme from design.md */
.landing-page{--sketch-ink:#1a3300;--sketch-yellow:#ffe95c;--sketch-paper:#fcfaf5;--sketch-line:#b6b6b6;--sketch-mint:#d5f5c2;--sketch-teal:#a8e5e5;--sketch-blush:#f6d0ff;--sketch-terracotta:#cb5521;background:var(--sketch-paper);color:var(--sketch-ink);font-family:'Inter',system-ui,sans-serif}
.container{max-width:1200px}.site-header{top:16px;right:20px;left:20px;border:0;background:transparent;backdrop-filter:none}.header-inner{height:64px;padding:8px 10px 8px 12px;border:1px solid var(--sketch-line);border-radius:16px;background:rgba(252,250,245,.96);box-shadow:0 18px 50px rgba(255,233,92,.22)}
.brand{color:var(--sketch-ink)}.brand img{border-radius:6px;background:transparent}.brand strong{font-family:'Inter',system-ui,sans-serif;font-size:18px;font-weight:800}.brand small{color:var(--sketch-ink);font-family:ui-monospace,monospace}.desktop-nav a,.login-link{color:var(--sketch-ink);font-size:14px;font-weight:500}.desktop-nav a:hover,.login-link:hover{background:linear-gradient(transparent 56%,var(--sketch-yellow) 56%);color:var(--sketch-ink)}
.button{border-radius:6px;box-shadow:0 1px 2px rgba(0,0,0,.05);font-size:14px;font-weight:600}.button-primary{border-color:var(--sketch-ink);background:var(--sketch-yellow);color:var(--sketch-ink);box-shadow:4px 4px 0 var(--sketch-ink)}.button-primary:hover{background:#fff18a;box-shadow:2px 2px 0 var(--sketch-ink);transform:translate(2px,2px)}.button-ghost{border-color:var(--sketch-ink);background:transparent;color:var(--sketch-ink)}.button-ghost:hover{border-color:var(--sketch-ink);background:var(--sketch-mint);color:var(--sketch-ink)}
.hero{min-height:820px;padding:166px 0 104px;overflow:hidden;background:var(--sketch-paper)}.hero-grid{grid-template-columns:1fr;gap:64px}.hero-copy{max-width:970px;margin:0 auto;text-align:center}.eyebrow-pill{margin-bottom:28px;padding:7px 12px;border:0;border-radius:6px;background:var(--sketch-yellow);color:var(--sketch-ink);font-size:12px;font-weight:600;letter-spacing:0;text-transform:none}.hero h1{max-width:1050px;margin:auto;color:var(--sketch-ink);font-family:'Arial Black','Inter',system-ui,sans-serif;font-size:clamp(58px,7.1vw,90px);font-weight:900;line-height:1;letter-spacing:.035em;text-transform:uppercase}.hero h1 em{display:inline;padding:0 .08em;background:linear-gradient(transparent 10%,var(--sketch-yellow) 10%,var(--sketch-yellow) 90%,transparent 90%);color:var(--sketch-ink);font-style:normal;font-weight:900}.hero-copy>p{max-width:650px;margin:32px auto;color:var(--sketch-ink);font-size:19px;line-height:1.5}.hero-actions,.hero-note{justify-content:center}.hero-note{color:#747474;font-size:13px}.hero-note svg{color:var(--sketch-ink)}.hero-orb-one{top:105px;right:-125px;width:430px;height:430px;border:3px dashed rgba(26,51,0,.28);animation:heroOrbIn 1s .12s ease-out both}.hero-gem{position:absolute;z-index:1;bottom:28px;left:24px;width:270px;height:270px;color:var(--sketch-ink);stroke-width:.8;opacity:.14;transform:rotate(-13deg);transform-origin:center;animation:heroGemIn 1s .22s cubic-bezier(.22,1,.36,1) both}@keyframes heroGemIn{from{opacity:0;transform:translate(-24px,24px) rotate(-20deg) scale(.9)}to{opacity:.14;transform:translate(0,0) rotate(-13deg) scale(1)}}.hero-orb-two{bottom:-180px;left:-180px;width:360px;height:360px;border:3px solid rgba(26,51,0,.22);background:transparent;animation:heroOrbIn 1s .22s ease-out both}@keyframes heroOrbIn{from{opacity:0;transform:scale(.82) rotate(-8deg)}to{opacity:1;transform:scale(1) rotate(0)}}
.product-preview{max-width:820px;margin:0 auto;border:1px solid var(--sketch-ink);border-radius:12px;box-shadow:none;transform:rotate(-1deg)}.product-preview:hover{transform:rotate(0)}.preview-topbar{border-color:var(--sketch-ink);background:var(--sketch-mint)}.preview-body{background:var(--sketch-paper)}.preview-stats article,.preview-panels article{border-color:var(--sketch-ink);border-radius:6px}.preview-stats article:nth-child(1)>span{background:var(--sketch-yellow)}.preview-stats article:nth-child(2)>span{background:var(--sketch-teal)}.preview-stats article:nth-child(3)>span{background:var(--sketch-blush)}
.section-space{padding:96px 0}.section-kicker{display:inline-block;padding:5px 8px;border-radius:6px;background:var(--sketch-yellow);color:var(--sketch-ink);font-family:ui-monospace,monospace;font-size:12px;letter-spacing:0}.section-heading h2,.problem-grid h2,.estimator-copy h2,.final-cta h2{color:var(--sketch-ink);font-family:'Arial Black','Inter',system-ui,sans-serif;font-size:clamp(40px,5vw,66px);font-weight:900;line-height:1.05;letter-spacing:.025em;text-transform:uppercase}.section-heading>p:last-child{color:var(--sketch-ink);font-size:18px;line-height:1.5}
.problem-section{padding:96px 0;background:var(--sketch-paper);color:var(--sketch-ink)}.problem-grid{gap:72px;padding-top:64px;border-top:1px solid var(--sketch-ink)}.problem-grid .section-kicker{color:var(--sketch-ink)}.problem-grid h2{font-size:clamp(38px,4.4vw,55px)}.problem-list>div{min-height:68px;border:1px solid var(--sketch-ink);border-radius:12px;background:var(--sketch-paper);color:var(--sketch-ink);font-size:15px}.problem-list>div:nth-child(1){background:var(--sketch-mint);transform:rotate(-1deg)}.problem-list>div:nth-child(3){background:var(--sketch-teal);transform:rotate(1deg)}.problem-list svg{color:var(--sketch-ink)}
.features-section,.how-section,.faq-section,.final-cta{background:var(--sketch-paper)}.feature-grid{gap:24px}.feature-card{min-height:245px;padding:28px;border:1px solid var(--sketch-ink);border-radius:12px;background:var(--sketch-paper);box-shadow:none}.feature-card:hover{border-color:var(--sketch-ink);box-shadow:none;transform:rotate(-1deg) translateY(-3px)}.feature-card:nth-child(2){background:var(--sketch-mint)}.feature-card:nth-child(4){background:var(--sketch-blush)}.feature-card:nth-child(6){background:var(--sketch-teal)}.feature-icon{border-radius:6px;background:var(--sketch-yellow);color:var(--sketch-ink)}.feature-card h3{color:var(--sketch-ink);font-family:'Inter',system-ui,sans-serif;font-size:24px;font-weight:700}.feature-card p{color:var(--sketch-ink);font-size:16px;line-height:1.5}
.steps-grid{gap:24px}.step-card{padding:28px;border:1px solid var(--sketch-ink);border-radius:12px;background:var(--sketch-paper)}.step-card:nth-child(2){background:var(--sketch-yellow)}.step-top{color:var(--sketch-ink);font-family:ui-monospace,monospace}.step-top svg{color:var(--sketch-ink)}.step-card h3{color:var(--sketch-ink);font-family:'Inter',system-ui,sans-serif;font-size:28px;font-weight:700}.step-card p{color:var(--sketch-ink);font-size:16px;line-height:1.5}
.estimator-section{background:var(--sketch-paper)}.estimator-card{border:1px solid var(--sketch-ink);border-radius:12px;background:var(--sketch-mint);color:var(--sketch-ink);box-shadow:none}.estimator-icon{border-radius:6px;background:var(--sketch-yellow)}.estimator-copy .section-kicker{color:var(--sketch-ink)}.estimator-copy>p:not(.section-kicker){color:var(--sketch-ink);font-size:17px}.button-light{border-color:var(--sketch-ink);background:var(--sketch-yellow);color:var(--sketch-ink);box-shadow:4px 4px 0 rgba(26,51,0,.9)}.button-light:hover{background:#fff18a;box-shadow:2px 2px 0 rgba(26,51,0,.9);transform:translate(2px,2px)}.estimate-result{border:1px solid var(--sketch-ink);border-radius:12px;background:var(--sketch-paper);box-shadow:none}.result-label{color:var(--sketch-ink)}
.faq-list{border-color:var(--sketch-ink)}.faq-list details{border-color:var(--sketch-ink)}.faq-list summary{color:var(--sketch-ink);font-family:'Inter',system-ui,sans-serif;font-size:20px;font-weight:700}.faq-list summary svg{color:var(--sketch-ink)}.faq-list details p{color:var(--sketch-ink);font-size:16px}.final-cta-inner{border:1px solid var(--sketch-ink);border-radius:12px;background:var(--sketch-yellow)}.final-icon{color:var(--sketch-ink)}.final-cta-inner>p:not(.section-kicker){color:var(--sketch-ink);font-size:17px}.final-cta .section-kicker{background:var(--sketch-paper)}
.site-footer{padding:74px 0 24px;border-top:1px solid var(--sketch-ink);background:linear-gradient(135deg,var(--sketch-mint) 0%,#ccebea 52%,#dcebc7 100%);color:var(--sketch-ink)}.footer-main{display:grid;grid-template-columns:minmax(0,1.3fr) minmax(360px,.7fr);gap:100px}.site-footer .brand{color:var(--sketch-ink)}.site-footer .brand img{background:transparent}.site-footer .brand strong{font-size:20px}.site-footer .brand small{color:rgba(26,51,0,.6)}.footer-brand>p{max-width:420px;margin:22px 0 25px;color:rgba(26,51,0,.72);font-size:14px;line-height:1.7}.footer-estimator{display:inline-flex;align-items:center;gap:8px;padding:8px 11px;border:1px solid var(--sketch-ink);border-radius:6px;background:var(--sketch-yellow);color:var(--sketch-ink);font-size:13px;font-weight:700;text-decoration:none;transition:gap .2s ease,background .2s ease,transform .2s ease}.footer-estimator:hover{gap:13px;background:#fff1a0;transform:translateY(-2px)}.footer-estimator svg{width:15px}.footer-links{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:55px}.footer-links>div{display:flex;flex-direction:column;gap:13px;min-width:0}.footer-links strong{width:max-content;margin-bottom:6px;padding:4px 7px;border-radius:4px;background:var(--sketch-yellow);color:var(--sketch-ink);font-family:ui-monospace,monospace;font-size:11px;letter-spacing:.08em;text-transform:uppercase}.footer-links a{width:max-content;color:rgba(26,51,0,.7);font-size:13px;text-decoration:none;transition:color .2s ease,transform .2s ease}.footer-links a:hover{color:var(--sketch-ink);transform:translateX(3px)}.footer-bottom{display:grid;grid-template-columns:1fr auto 1fr;align-items:center;gap:28px;margin-top:62px;padding-top:22px;border-top:1px solid rgba(26,51,0,.25);color:rgba(26,51,0,.58);font-size:10px}.footer-bottom span:nth-child(2){text-align:center}.footer-bottom a{justify-self:end;color:rgba(26,51,0,.72);font-weight:600;text-decoration:none}.footer-bottom a:hover{color:var(--sketch-ink);background:linear-gradient(transparent 55%,var(--sketch-yellow) 55%)}
@media(max-width:900px){.hero h1{font-size:clamp(52px,10vw,76px)}.hero-grid{gap:48px}.section-heading h2,.problem-grid h2,.estimator-copy h2,.final-cta h2{font-size:clamp(38px,7vw,56px)}.footer-main{grid-template-columns:1fr;gap:52px}.footer-links{max-width:460px}.footer-bottom{grid-template-columns:1fr auto}.footer-bottom span:nth-child(2){display:none}}
@media(max-width:600px){.site-header{top:8px;right:8px;left:8px}.header-inner{height:58px}.header-cta{font-size:11px}.hero{min-height:auto;padding:125px 0 72px}.hero h1{font-size:42px;letter-spacing:.025em}.hero-copy>p{font-size:17px}.section-space,.problem-section{padding:72px 0}.problem-grid{padding-top:48px}.section-heading h2,.problem-grid h2,.estimator-copy h2,.final-cta h2{font-size:36px}.feature-card{min-height:auto}.estimator-card,.final-cta-inner{width:calc(100% - 28px);border-radius:12px}.estimator-card{padding:40px 22px}.site-footer{padding-top:58px}.footer-main{gap:44px}.footer-links{width:100%;gap:28px}.footer-bottom{grid-template-columns:1fr;gap:10px;margin-top:46px}.footer-bottom a{justify-self:start}}
/* Layout refinement: quieter hierarchy and better reading rhythm */
.hero{min-height:auto;padding:150px 0 96px}.hero-grid{grid-template-columns:minmax(0,1fr) minmax(0,1fr);align-items:center;gap:56px}.hero-copy{width:100%;min-width:0;max-width:540px;margin:0;text-align:left}.hero h1{width:100%;max-width:100%;margin:0;font-size:clamp(46px,4.15vw,56px);line-height:1.05;letter-spacing:0;overflow-wrap:anywhere;text-transform:none}.hero h1 em{display:inline;padding:0 .08em;margin:0}.hero-copy>p{max-width:500px;margin:27px 0 30px;font-size:17px}.hero-actions,.hero-note{justify-content:flex-start}.product-preview{width:100%;min-width:0;max-width:100%;margin:0}
.problem-section,.features-section,.how-section,.estimator-section,.faq-section,.final-cta{position:relative}.problem-section{padding-top:96px;padding-bottom:96px;background:#f3f0e8}.problem-section .problem-grid{padding-top:0}.how-section,.faq-section{background:var(--sketch-paper)}.features-section{background:linear-gradient(180deg,#f1f7ea 0%,#f7faf2 100%)}.estimator-section{background:#f3f0e8}.final-cta{background:#eef6e7}.problem-grid{display:grid;grid-template-columns:minmax(0,.9fr) minmax(0,1.1fr);align-items:center;gap:80px;border-top:0}.problem-grid h2{max-width:560px;margin-bottom:0;font-size:clamp(38px,4vw,52px);line-height:1.1;letter-spacing:.005em;text-transform:none}.problem-list{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));align-content:center;gap:14px;margin:0}.problem-list>div{display:flex;align-items:center;min-height:82px;padding:18px 20px;border-color:rgba(26,51,0,.55);font-size:14px;line-height:1.45;transform:none;transition:transform .22s ease,border-color .22s ease,box-shadow .22s ease}.problem-list>div span{display:grid;flex:0 0 auto;place-items:center;width:29px;height:29px;border:1px solid rgba(26,51,0,.35);border-radius:8px;background:rgba(252,250,245,.65)}.problem-list>div svg{width:15px}.problem-list>div:nth-child(1){background:var(--sketch-mint);transform:none}.problem-list>div:nth-child(2){background:var(--sketch-yellow);transform:none}.problem-list>div:nth-child(3){background:var(--sketch-teal);transform:none}.problem-list>div:nth-child(4){background:var(--sketch-blush);transform:none}.problem-list>div:hover{z-index:1;border-color:var(--sketch-ink);box-shadow:4px 4px 0 rgba(26,51,0,.14)}.problem-list>div:nth-child(1):hover{transform:translate(-3px,-3px) rotate(-.3deg)}.problem-list>div:nth-child(2):hover{transform:translate(2px,-4px) rotate(.4deg)}.problem-list>div:nth-child(3):hover{transform:translate(-2px,-4px) rotate(.35deg)}.problem-list>div:nth-child(4):hover{transform:translate(3px,-3px) rotate(-.4deg)}.problem-content{display:flex;align-items:stretch;flex-direction:column;gap:24px}.problem-cta{align-self:center;width:max-content;min-height:48px;padding:0 22px}.problem-cta:hover svg{transform:translateX(4px)}.problem-cta svg{transition:transform .18s ease}
.section-heading{max-width:760px}.section-heading.centered{margin-bottom:54px}.section-heading h2,.estimator-copy h2,.final-cta h2{font-size:clamp(38px,4.4vw,58px);line-height:1.08;letter-spacing:.005em;text-transform:none}.section-heading>p:last-child{max-width:680px;font-size:17px}.section-heading.centered>p:last-child{margin-right:auto;margin-left:auto}.feature-grid{grid-template-columns:repeat(3,minmax(0,1fr));max-width:1100px;margin:auto}.feature-card{display:flex;align-items:flex-start;flex-direction:column;min-height:250px;padding:28px;transition:transform .22s ease,border-color .22s ease,box-shadow .22s ease}.feature-card:hover{z-index:1;border-color:var(--sketch-ink);box-shadow:5px 5px 0 rgba(26,51,0,.14);transform:translate(-3px,-3px)}.feature-card:nth-child(1){background:#e7ddd0}.feature-card:nth-child(2){background:var(--sketch-mint)}.feature-card:nth-child(3){background:#dce8ff}.feature-card:nth-child(4){background:var(--sketch-blush)}.feature-card:nth-child(5){background:#f4c7a9}.feature-card:nth-child(6){background:var(--sketch-teal)}.feature-card:nth-child(1):hover{transform:translate(-3px,-3px) rotate(-.35deg)}.feature-card:nth-child(2):hover{transform:translate(2px,-4px) rotate(.4deg)}.feature-card:nth-child(3):hover{transform:translate(-2px,-4px) rotate(.25deg)}.feature-card:nth-child(4):hover{transform:translate(3px,-3px) rotate(-.3deg)}.feature-card:nth-child(5):hover{transform:translate(-3px,-4px) rotate(.35deg)}.feature-card:nth-child(6):hover{transform:translate(2px,-3px) rotate(-.4deg)}.feature-icon{display:grid;place-items:center;width:52px;height:52px;margin:0 0 24px;border:1px solid var(--sketch-ink);border-radius:12px;box-shadow:3px 3px 0 rgba(26,51,0,.13)}.feature-icon svg{width:24px}.feature-card h3{margin:0 0 10px}.feature-card p{margin:0;font-size:15px}.feature-cta-wrap{display:flex;align-items:center;flex-direction:column;gap:12px;margin-top:42px;text-align:center}.feature-cta-wrap p{margin:0;color:var(--sketch-ink);font-size:15px}.feature-cta{min-height:56px;padding:0 28px}.feature-cta svg{transition:transform .18s ease}.feature-cta:hover svg{transform:translateX(4px)}
.how-section .section-heading{max-width:760px;margin-bottom:48px}.steps-grid{grid-template-columns:repeat(4,minmax(0,1fr));max-width:none;gap:18px}.step-card{position:relative;display:flex;align-items:flex-start;flex-direction:column;min-height:300px;padding:24px;overflow:visible;transition:transform .22s ease,border-color .22s ease,box-shadow .22s ease}.step-card:hover{z-index:1;border-color:var(--sketch-ink);box-shadow:5px 5px 0 rgba(26,51,0,.14);transform:translate(-3px,-3px)}.step-card:nth-child(1){background:var(--sketch-mint)}.step-card:nth-child(2){background:var(--sketch-yellow)}.step-card:nth-child(3){background:#dce8ff}.step-card:nth-child(4){background:var(--sketch-blush)}.step-top{width:100%}.step-number{padding:5px 8px;border:1px solid rgba(26,51,0,.5);border-radius:6px;background:rgba(252,250,245,.68);font-size:10px;letter-spacing:.04em}.step-top>svg{position:absolute;z-index:2;top:50%;right:-21px;width:23px;height:23px;padding:5px;border:1px solid var(--sketch-ink);border-radius:50%;background:var(--sketch-paper);transform:translateY(-50%)}.step-icon{display:grid;place-items:center;width:52px;height:52px;margin-top:36px;border:1px solid var(--sketch-ink);border-radius:12px;background:var(--sketch-paper);box-shadow:3px 3px 0 rgba(26,51,0,.13)}.step-icon svg{width:24px}.step-card h3{margin:21px 0 9px}.step-card p{font-size:14px}.estimator-card{grid-template-columns:1.1fr .9fr;gap:64px}.estimator-copy h2{max-width:600px}.faq-grid{grid-template-columns:minmax(0,.85fr) minmax(0,1.15fr);gap:90px}.faq-heading h2{font-size:clamp(38px,4vw,52px)}.final-cta h2{max-width:760px}.footer-inner{align-items:flex-start}
@media(max-width:1199px){.hero-grid{grid-template-columns:minmax(0,1fr);gap:52px}.hero-copy{width:100%;max-width:760px;margin:0 auto;text-align:center}.hero h1{max-width:760px;margin:0 auto;font-size:clamp(48px,7vw,68px);overflow-wrap:anywhere}.hero-copy>p{max-width:620px;margin-right:auto;margin-left:auto}.hero-actions,.hero-note{justify-content:center}.product-preview{width:min(100%,760px);min-width:0;margin:0 auto}}
@media(max-width:900px){.problem-grid{grid-template-columns:1fr;gap:40px}.problem-grid h2{max-width:700px}.feature-grid{grid-template-columns:1fr}.estimator-card{grid-template-columns:1fr}.faq-grid{grid-template-columns:1fr}}
@media(max-width:600px){.hero{padding-top:118px}.product-preview{display:none}.hero-grid{gap:0}.hero h1{font-size:38px;line-height:1.08}.hero h1 em{margin-top:7px}.hero-copy>p{font-size:16px}.section-heading h2,.problem-grid h2,.estimator-copy h2,.final-cta h2{font-size:34px;line-height:1.12}.section-heading>p:last-child{font-size:16px}.section-heading.centered{text-align:left}.feature-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:10px}.feature-card{display:flex;align-items:center;justify-content:center;flex-direction:column;min-height:128px;padding:15px;text-align:center}.feature-icon{width:36px;height:36px;margin:0 auto 13px}.feature-icon svg{width:18px}.feature-card h3{margin:0;font-size:15px;line-height:1.25;text-align:center}.feature-card p{display:none}.steps-grid{grid-template-columns:repeat(4,minmax(0,1fr));gap:7px}.step-card{align-items:center;min-width:0;min-height:112px;padding:8px 5px;text-align:center}.step-top{justify-content:center}.step-number{padding:3px 4px;font-size:7px}.step-top>svg{display:none}.step-icon{width:30px;height:30px;margin-top:10px;border-radius:7px;box-shadow:2px 2px 0 rgba(26,51,0,.13)}.step-icon svg{width:14px}.step-card h3{margin:9px 0 0;font-size:10px;line-height:1.2}.step-card p{display:none}.problem-list{gap:10px}.problem-list>div{min-height:66px;padding:11px 12px;gap:9px;font-size:12px;line-height:1.3}.problem-list>div span{width:25px;height:25px;border-radius:7px}.problem-list>div svg{width:13px}.problem-list>div:nth-child(1),.problem-list>div:nth-child(3){transform:none}.problem-content{gap:20px}.faq-list summary{font-size:17px}.faq-list summary svg{flex:0 0 18px;width:18px;height:18px}}
/* Keep yellow exclusive to primary CTAs. */
.problem-list>div:nth-child(2){background:#dce8ff}
.feature-card .feature-icon{background:var(--sketch-paper)}
.final-cta-inner{background:var(--sketch-mint)}
.final-cta .section-kicker{background:var(--sketch-teal)}

.reveal-section{opacity:0;transform:translateY(34px);transition:opacity .72s cubic-bezier(.22,1,.36,1),transform .72s cubic-bezier(.22,1,.36,1)}.reveal-section.is-visible{opacity:1;transform:none}.reveal-section .section-heading,.reveal-section .problem-grid>div,.reveal-section .estimator-card>div,.reveal-section .faq-grid>div,.reveal-section .final-cta-inner{opacity:0;transform:translateY(22px);transition:opacity .58s .12s ease,transform .58s .12s cubic-bezier(.22,1,.36,1)}.reveal-section.is-visible .section-heading,.reveal-section.is-visible .problem-grid>div,.reveal-section.is-visible .estimator-card>div,.reveal-section.is-visible .faq-grid>div,.reveal-section.is-visible .final-cta-inner{opacity:1;transform:none}.reveal-section .feature-card,.reveal-section .step-card,.reveal-section .problem-list>div{opacity:0;transform:translateY(22px);transition:opacity .5s ease,transform .5s cubic-bezier(.22,1,.36,1),border-color .22s ease,box-shadow .22s ease}.reveal-section.is-visible .feature-card,.reveal-section.is-visible .step-card,.reveal-section.is-visible .problem-list>div{opacity:1;transform:none}.reveal-section.is-visible :is(.feature-card,.step-card,.problem-list>div):nth-child(1){transition-delay:.12s}.reveal-section.is-visible :is(.feature-card,.step-card,.problem-list>div):nth-child(2){transition-delay:.2s}.reveal-section.is-visible :is(.feature-card,.step-card,.problem-list>div):nth-child(3){transition-delay:.28s}.reveal-section.is-visible :is(.feature-card,.step-card,.problem-list>div):nth-child(4){transition-delay:.36s}.reveal-section.is-visible .feature-card:nth-child(5){transition-delay:.44s}.reveal-section.is-visible .feature-card:nth-child(6){transition-delay:.52s}

/* Hover must override the completed reveal animation without inheriting its delay. */
.reveal-section.is-visible :is(.feature-card,.step-card,.problem-list>div):hover{z-index:2;transition-delay:0s;transition-duration:.16s;border-color:var(--sketch-ink);box-shadow:5px 5px 0 rgba(26,51,0,.14)}
.reveal-section.is-visible .problem-list>div:nth-child(1):hover{transform:translate(-3px,-3px) rotate(-.3deg)}
.reveal-section.is-visible .problem-list>div:nth-child(2):hover{transform:translate(2px,-4px) rotate(.4deg)}
.reveal-section.is-visible .problem-list>div:nth-child(3):hover{transform:translate(-2px,-4px) rotate(.35deg)}
.reveal-section.is-visible .problem-list>div:nth-child(4):hover{transform:translate(3px,-3px) rotate(-.4deg)}
.reveal-section.is-visible .feature-card:nth-child(1):hover{transform:translate(-3px,-3px) rotate(-.35deg)}
.reveal-section.is-visible .feature-card:nth-child(2):hover{transform:translate(2px,-4px) rotate(.4deg)}
.reveal-section.is-visible .feature-card:nth-child(3):hover{transform:translate(-2px,-4px) rotate(.25deg)}
.reveal-section.is-visible .feature-card:nth-child(4):hover{transform:translate(3px,-3px) rotate(-.3deg)}
.reveal-section.is-visible .feature-card:nth-child(5):hover{transform:translate(-3px,-4px) rotate(.35deg)}
.reveal-section.is-visible .feature-card:nth-child(6):hover{transform:translate(2px,-3px) rotate(-.4deg)}
.reveal-section.is-visible .step-card:nth-child(1):hover{transform:translate(-3px,-3px) rotate(-.3deg)}
.reveal-section.is-visible .step-card:nth-child(2):hover{transform:translate(2px,-4px) rotate(.35deg)}
.reveal-section.is-visible .step-card:nth-child(3):hover{transform:translate(-2px,-4px) rotate(.25deg)}
.reveal-section.is-visible .step-card:nth-child(4):hover{transform:translate(3px,-3px) rotate(-.35deg)}
@media(prefers-reduced-motion:reduce){.hero-orb,.hero-gem{animation:none!important}.product-preview,.feature-card,.button,.reveal-section,.reveal-section *{transition:none!important}.product-preview:hover,.feature-card:hover,.button:hover{transform:none}.reveal-section,.reveal-section *{opacity:1!important;transform:none!important}}
</style>
