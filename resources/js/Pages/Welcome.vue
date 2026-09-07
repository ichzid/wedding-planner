<script setup>
import { Head } from '@inertiajs/vue3';
import { onBeforeUnmount, onMounted } from 'vue';
import PublicFooter from '@/Components/PublicFooter.vue';
import PublicHeader from '@/Components/PublicHeader.vue';
import {
    ArrowRight,
    BadgeCheck,
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
                            <a :href="route('login')" class="button button-primary button-large">Atur Pernikahanmu Sekarang</a>
                            <a href="/budget-estimator" class="button button-ghost button-large">
                                Hitung Budget Nikah Gratis <ArrowRight aria-hidden="true" />
                            </a>
                        </div>
                        <div class="hero-note"><CheckCircle2 aria-hidden="true" /> Coba estimator tanpa perlu membuat akun</div>
                        <div class="feature-marquee" aria-label="Fitur Ruang Pasangan">
                            <div class="feature-marquee__track">
                                <template v-for="copy in 2" :key="copy">
                                    <span v-for="feature in features" :key="`${copy}-${feature.title}`">{{ feature.title }}</span>
                                </template>
                            </div>
                        </div>
                    </div>

                </div>
            </section>

            <section class="dashboard-showcase" aria-label="Pratinjau Ruang Pasangan">
                <div class="container showcase-grid">
                    <article class="showcase-card showcase-card--first">
                        <span class="showcase-label showcase-label--first">Semua rencana<br />dalam satu layar</span>
                        <svg class="showcase-arrow showcase-arrow--first" viewBox="0 0 120 80" aria-hidden="true"><path d="M8 12 C58 8 91 28 104 65"/><path d="M91 56 L104 67 L108 49"/></svg>
                        <div class="showcase-screen">
                            <img src="/screenshoot/dashboard.png" alt="Tampilan Dashboard Pernikahan Ruang Pasangan" />
                        </div>
                        <footer><strong>Dashboard Pernikahan</strong></footer>
                    </article>

                    <article class="showcase-card showcase-card--raised showcase-card--second">
                        <span class="showcase-label showcase-label--second">Anggaran lebih<br />mudah dipantau</span>
                        <svg class="showcase-arrow showcase-arrow--second" viewBox="0 0 120 80" aria-hidden="true"><path d="M112 68 C66 74 30 54 15 17"/><path d="M28 26 L14 15 L11 34"/></svg>
                        <div class="showcase-screen">
                            <img src="/screenshoot/budget.png" alt="Tampilan Anggaran Ruang Pasangan" />
                        </div>
                        <footer><strong>Anggaran Terarah</strong></footer>
                    </article>

                    <article class="showcase-card showcase-card--third">
                        <span class="showcase-label showcase-label--third">Tugas penting<br />tidak terlewat</span>
                        <svg class="showcase-arrow showcase-arrow--third" viewBox="0 0 120 80" aria-hidden="true"><path d="M112 10 C60 8 29 30 17 66"/><path d="M30 56 L16 68 L13 49"/></svg>
                        <div class="showcase-screen">
                            <img src="/screenshoot/checklist.png" alt="Tampilan Checklist Persiapan Ruang Pasangan" />
                        </div>
                        <footer><strong>Checklist Persiapan</strong></footer>
                    </article>
                </div>
            </section>

            <section class="testimonial-section reveal-section" data-reveal>
                <div class="container">
                    <div class="testimonial-heading">
                        <h2>“Persiapan terasa lebih tenang ketika semuanya tertata...”</h2>
                    </div>

                    <div class="testimonial-grid">
                        <article class="testimonial-card">
                            <p>“Ruang Pasangan membantu kami melihat seluruh persiapan dengan lebih jelas. Checklist dan anggarannya membuat kami tidak lagi bingung harus mulai dari mana.”</p>
                            <footer>
                                <span class="testimonial-avatar">AN</span>
                                <span><strong>Alya & Naufal</strong><small>Menikah di Jakarta</small></span>
                            </footer>
                        </article>

                        <article class="testimonial-card">
                            <p>“Yang paling membantu adalah semua kebutuhan ada di satu tempat. Kami bisa membagi tugas, memantau budget, dan tetap menikmati proses persiapan tanpa terlalu stres.”</p>
                            <footer>
                                <span class="testimonial-avatar">DR</span>
                                <span><strong>Dinda & Reza</strong><small>Menikah di Bandung</small></span>
                            </footer>
                        </article>

                        <article class="testimonial-card">
                            <p>“Awalnya kami hanya mencoba menghitung budget. Setelah itu, rencana pernikahan jadi jauh lebih nyata dan mudah diikuti sampai hari pernikahan kami tiba.”</p>
                            <footer>
                                <span class="testimonial-avatar">SK</span>
                                <span><strong>Salsa & Kevin</strong><small>Menikah di Surabaya</small></span>
                            </footer>
                        </article>
                    </div>
                </div>
            </section>

            <section id="fitur" class="features-section section-space reveal-section" data-reveal>
                <div class="container">
                    <div class="section-heading centered">
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
                                <span class="step-number">Langkah {{ item.number }}</span>
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
                        <a href="/budget-estimator" class="button button-primary button-large">Coba Budget Estimator <ArrowRight aria-hidden="true" /></a>
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

            <section class="final-cta section-space reveal-section" data-reveal>
                <div class="container">
                    <div class="final-cta-inner">
                    <HeartHandshake class="final-icon" aria-hidden="true" />
                    <p class="section-kicker">Satu langkah kecil untuk rencana yang lebih besar</p>
                    <h2>Mulai siapkan hari bahagiamu sekarang.</h2>
                    <p>Satukan setiap rencana bersama pasangan agar persiapan terasa lebih rapi, tenang, dan terarah.</p>
                        <a :href="route('login')" class="button button-primary button-large">Mulai Persiapkan Pernikahanmu <ArrowRight aria-hidden="true" /></a>
                    </div>
                </div>
            </section>
        </main>

        <PublicFooter />
    </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,800&family=Fraunces:opsz,wght@9..144,400&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400&family=Kalam:wght@400;700&display=swap');
.landing-page{overflow:hidden;background:var(--bg);color:var(--text)}
.container{width:min(1160px,calc(100% - 40px));margin:0 auto}.site-header{position:fixed;z-index:50;top:0;right:0;left:0;border-bottom:1px solid rgba(182,182,182,.72);background:rgba(252,250,245,.88);backdrop-filter:blur(18px)}
.header-inner{display:flex;align-items:center;justify-content:space-between;height:74px;gap:28px}.brand{display:flex;align-items:center;gap:10px;color:var(--text);text-decoration:none}.brand img{width:40px;height:40px}.brand span{display:flex;flex-direction:column;line-height:1.08}.brand strong{font-family:var(--font-display);font-size:16px}.brand small{margin-top:4px;color:var(--text-muted);font-size:8px;font-weight:700;letter-spacing:.12em;text-transform:uppercase}.desktop-nav{display:flex;align-items:center;gap:32px}.desktop-nav a,.login-link{color:var(--text-muted);font-size:13px;font-weight:600;text-decoration:none;transition:.2s}.desktop-nav a:hover,.login-link:hover{color:var(--accent-hover)}.header-actions{display:flex;align-items:center;gap:18px}.button{display:inline-flex;align-items:center;justify-content:center;gap:9px;min-height:42px;padding:0 18px;border:1px solid transparent;border-radius:10px;font-size:12px;font-weight:700;text-decoration:none;transition:transform .2s,box-shadow .2s,background .2s,color .2s}.button:hover{transform:translateY(-2px)}.button svg{width:16px}.button-primary{background:var(--accent);color:#fff}.button-primary:hover{background:var(--accent-hover)}.button-ghost{border-color:var(--border);background:rgba(255,255,255,.74);color:var(--text-muted)}.button-ghost:hover{border-color:var(--border-strong);color:var(--accent-hover)}.button-large{min-height:50px;padding:0 22px;font-size:13px}
.hero{position:relative;padding:158px 0 90px;background:linear-gradient(145deg,#f7f9f3 0%,#edf3e7 100%)}.hero-grid{position:relative;z-index:2;display:grid;grid-template-columns:minmax(0,.9fr) minmax(500px,1.1fr);align-items:center;gap:68px}.eyebrow-pill{display:inline-flex;align-items:center;gap:7px;margin-bottom:22px;padding:8px 12px;border:1px solid var(--border);border-radius:99px;background:rgba(255,255,255,.7);color:var(--accent-hover);font-size:10px;font-weight:700;letter-spacing:.07em;text-transform:uppercase}.eyebrow-pill svg{width:14px}.hero h1{max-width:630px;margin:0;font-family:var(--font-display);font-size:clamp(45px,5.5vw,72px);font-weight:600;line-height:1.04;letter-spacing:-.045em}.hero h1 em{display:block;color:var(--accent);font-weight:500}.hero-copy>p{max-width:590px;margin:24px 0 28px;color:var(--text-muted);font-size:16px;line-height:1.75}.hero-actions{display:flex;align-items:center;flex-wrap:wrap;gap:12px}.hero-note{display:flex;align-items:center;gap:7px;margin-top:18px;color:var(--text-muted);font-size:11px}.hero-note svg{width:15px;color:var(--accent)}.hero-orb{position:absolute;border-radius:50%;backface-visibility:hidden;transform-origin:center;will-change:transform,opacity}.hero-orb-one{top:100px;right:-160px;width:420px;height:420px;border:1px solid rgba(36,0,41,.16)}.hero-orb-two{bottom:-200px;left:-120px;width:370px;height:370px;background:rgba(255,189,211,.45)}
.product-preview{overflow:hidden;border:1px solid rgba(182,182,182,.9);border-radius:22px;background:#fff;box-shadow:0 35px 80px rgba(36,0,41,.16);transform:rotate(1.2deg);transition:.35s}.product-preview:hover{transform:rotate(0) translateY(-5px)}.preview-topbar{display:flex;align-items:center;justify-content:space-between;padding:14px 18px;border-bottom:1px solid var(--border);background:#ffffff}.preview-brand{display:flex;align-items:center;gap:7px;font-family:var(--font-display);font-size:11px;font-weight:600}.preview-brand img{width:25px}.preview-avatar{display:grid;place-items:center;width:25px;height:25px;border-radius:8px;background:var(--accent);color:#fff;font-size:9px;font-weight:800}.preview-body{padding:22px}.preview-heading{display:flex;align-items:center;justify-content:space-between;gap:15px}.preview-heading div{display:flex;flex-direction:column}.preview-heading small{color:var(--text-dim);font-size:8px}.preview-heading strong{font-family:var(--font-display);font-size:19px}.date-chip{display:flex;align-items:center;gap:5px;padding:6px 9px;border-radius:8px;background:var(--accent-soft);color:var(--accent-hover);font-size:8px;font-weight:700}.date-chip svg{width:12px}.preview-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:9px;margin-top:19px}.preview-stats article{display:grid;grid-template-columns:30px 1fr;align-items:center;padding:10px;border:1px solid var(--border);border-radius:10px}.preview-stats article>span{grid-row:span 2;display:grid;place-items:center;width:25px;height:25px;border-radius:7px;background:var(--accent-soft);color:var(--accent)}.preview-stats svg{width:13px}.preview-stats small{color:var(--text-dim);font-size:7px}.preview-stats strong{font-size:11px}.preview-panels{display:grid;grid-template-columns:1.6fr .8fr;gap:10px;margin-top:10px}.preview-panels article{border:1px solid var(--border);border-radius:10px;padding:13px}.panel-title{display:flex;justify-content:space-between;gap:8px;font-size:8px}.panel-title span{color:var(--text-dim)}.progress-track{height:5px;margin:10px 0 12px;border-radius:99px;background:var(--surface-muted)}.progress-track span{display:block;width:68%;height:100%;border-radius:inherit;background:var(--accent)}.progress-panel ul{display:grid;gap:7px;margin:0;padding:0;list-style:none;color:var(--text-muted);font-size:7px}.progress-panel li{display:flex;align-items:center;gap:6px}.progress-panel li svg{width:11px;color:var(--accent)}.progress-panel li.pending span{width:10px;height:10px;border:1px solid var(--border-strong);border-radius:50%}.budget-panel{position:relative;overflow:hidden}.budget-panel small{display:block;color:var(--text-dim);font-size:7px}.budget-panel>strong{display:block;margin-top:4px;font-size:12px}.budget-ring{display:grid;place-items:center;width:65px;height:65px;margin:13px auto 0;border:7px solid var(--accent-soft);border-top-color:var(--accent);border-right-color:var(--accent);border-radius:50%;transform:rotate(24deg)}.budget-ring span{font-size:9px;font-weight:800;transform:rotate(-24deg)}
.section-space{padding:105px 0}.section-kicker{margin:0 0 12px;color:var(--accent-hover);font-size:10px;font-weight:800;letter-spacing:.12em;text-transform:uppercase}.section-heading{max-width:650px}.section-heading.centered{margin:0 auto 48px;text-align:center}.section-heading h2,.problem-grid h2,.estimator-copy h2,.final-cta h2{margin:0;font-family:var(--font-display);font-size:clamp(32px,4vw,48px);font-weight:600;line-height:1.14;letter-spacing:-.035em}.section-heading>p:last-child{margin:17px 0 0;color:var(--text-muted);font-size:14px;line-height:1.75}
.problem-section{padding:70px 0;background:var(--accent-hover);color:#fff}.problem-grid{display:grid;grid-template-columns:1fr 1fr;align-items:center;gap:90px}.problem-grid .section-kicker{color:#ffbdd3}.problem-grid h2{font-size:clamp(30px,3.4vw,42px)}.problem-list{display:grid;grid-template-columns:1fr 1fr;gap:12px}.problem-list>div{display:flex;align-items:center;gap:10px;padding:14px;border:1px solid rgba(255,255,255,.14);border-radius:12px;background:rgba(255,255,255,.07);font-size:11px;font-weight:600}.problem-list span{display:grid;place-items:center}.problem-list svg{width:16px;color:#d4ccd4}
.features-section{background:#fff}.feature-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px}.feature-card{padding:27px;border:1px solid var(--border);border-radius:16px;background:var(--surface);transition:.25s}.feature-card:hover{border-color:var(--border-strong);box-shadow:var(--shadow);transform:translateY(-4px)}.feature-icon{display:grid;place-items:center;width:44px;height:44px;margin-bottom:19px;border-radius:13px;background:var(--accent-soft);color:var(--accent-hover)}.feature-icon svg{width:21px}.feature-card h3{margin:0 0 9px;font-family:var(--font-display);font-size:20px}.feature-card p{margin:0;color:var(--text-muted);font-size:12px;line-height:1.7}
.how-section{background:var(--bg)}.how-section .section-heading{margin-bottom:43px}.steps-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px}.step-card{padding:24px;border:1px solid var(--border);border-radius:15px;background:rgba(255,255,255,.76)}.step-top{display:flex;align-items:center;justify-content:space-between;color:var(--accent);font-size:10px;font-weight:800;letter-spacing:.1em}.step-top svg{width:18px;color:var(--border-strong)}.step-card h3{margin:25px 0 8px;font-family:var(--font-display);font-size:22px}.step-card p{margin:0;color:var(--text-muted);font-size:11px;line-height:1.7}
.estimator-section{padding-top:10px}.estimator-card{display:grid;grid-template-columns:1.2fr .8fr;align-items:center;gap:75px;padding:66px;border-radius:24px;color:#fff;box-shadow:var(--shadow-lg)}.estimator-icon{display:grid;place-items:center;width:48px;height:48px;margin-bottom:22px;border-radius:14px;background:rgba(255,255,255,.14)}.estimator-icon svg{width:23px}.estimator-copy .section-kicker{color:#d4ccd4}.estimator-copy h2{font-size:clamp(32px,3.6vw,46px)}.estimator-copy>p:not(.section-kicker){max-width:610px;margin:18px 0 27px;color:rgba(255,255,255,.76);font-size:13px;line-height:1.75}.button-light{background:#fff;color:var(--accent-hover);box-shadow:0 12px 30px rgba(25,45,22,.18)}.estimate-result{padding:27px;border:1px solid rgba(255,255,255,.25);border-radius:18px;background:rgba(255,255,255,.96);color:var(--text);box-shadow:0 18px 40px rgba(31,53,28,.18)}.result-label{display:flex;align-items:center;justify-content:space-between;margin-bottom:22px;color:var(--accent-hover);font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.06em}.result-label svg{width:18px}.estimate-result>small{display:block;color:var(--text-muted);font-size:10px}.estimate-result>strong{display:block;margin:3px 0 22px;font-family:var(--font-display);font-size:30px}.result-row{display:flex;justify-content:space-between;padding:11px 0;border-top:1px solid var(--border);font-size:11px}.estimate-result>p{margin:15px 0 0;color:var(--text-dim);font-size:9px;line-height:1.5}
.faq-section{background:#fff}.faq-grid{display:grid;grid-template-columns:.75fr 1.25fr;gap:100px}.faq-heading{position:sticky;top:115px;align-self:start}.faq-list{border-top:1px solid var(--border)}.faq-list details{border-bottom:1px solid var(--border)}.faq-list summary{display:flex;align-items:center;justify-content:space-between;gap:20px;padding:21px 0;cursor:pointer;font-family:var(--font-display);font-size:17px;font-weight:600;list-style:none}.faq-list summary::-webkit-details-marker{display:none}.faq-list summary svg{width:18px;color:var(--accent);transition:.2s}.faq-list details[open] summary svg{transform:rotate(180deg)}.faq-list details p{margin:-5px 40px 21px 0;color:var(--text-muted);font-size:12px;line-height:1.75}
.final-cta{padding:35px 0 105px;background:#fff}.final-cta-inner{display:flex;flex-direction:column;align-items:center;padding:72px 30px;border:1px solid var(--border);border-radius:24px;background:linear-gradient(145deg,var(--bg),var(--accent-soft));text-align:center}.final-icon{width:42px;height:42px;margin-bottom:20px;color:var(--accent)}.final-cta h2{max-width:700px}.final-cta-inner>p:not(.section-kicker){max-width:590px;margin:17px 0 27px;color:var(--text-muted);font-size:13px}.site-footer{padding:62px 0 25px;background:#240029;color:#fff}.footer-inner{display:flex;justify-content:space-between;gap:60px}.site-footer .brand{color:#fff}.footer-brand>p{max-width:310px;margin:17px 0 0;color:rgba(255,255,255,.58);font-size:11px}.footer-links{display:flex;gap:80px}.footer-links>div{display:flex;flex-direction:column;gap:10px;min-width:100px}.footer-links strong{margin-bottom:5px;font-size:10px;text-transform:uppercase;letter-spacing:.1em}.footer-links a{color:rgba(255,255,255,.58);font-size:11px;text-decoration:none}.footer-links a:hover{color:#fff}.footer-bottom{display:flex;justify-content:space-between;margin-top:55px;padding-top:22px;border-top:1px solid rgba(255,255,255,.1);color:rgba(255,255,255,.4);font-size:9px}
@media(max-width:900px){.desktop-nav{display:none}.hero{padding-top:130px}.hero-grid{grid-template-columns:1fr;gap:55px}.hero-copy{text-align:center}.hero h1,.hero-copy>p{margin-right:auto;margin-left:auto}.hero-actions,.hero-note{justify-content:center}.product-preview{max-width:650px;margin:auto}.problem-grid{grid-template-columns:1fr;gap:35px}.feature-grid{grid-template-columns:repeat(2,1fr)}.steps-grid{grid-template-columns:repeat(2,1fr)}.estimator-card{grid-template-columns:1fr;padding:50px;gap:40px}.faq-grid{grid-template-columns:1fr;gap:42px}.faq-heading{position:static}.footer-inner{flex-direction:column}}
@media(max-width:600px){.container{width:min(100% - 28px,1160px)}.header-inner{height:66px}.brand img{width:35px;height:35px}.brand strong{font-size:14px}.login-link{display:none}.header-cta{min-height:38px;padding:0 13px;font-size:10px}.hero{padding:115px 0 65px}.hero h1{font-size:42px}.hero-copy>p{font-size:14px}.hero-actions{flex-direction:column}.hero-actions .button{width:100%}.product-preview{border-radius:16px}.preview-body{padding:14px}.preview-stats{grid-template-columns:1fr}.preview-stats article{display:flex;gap:8px}.preview-stats article>span{grid-row:auto}.preview-stats small{flex:1}.preview-panels{grid-template-columns:1fr}.budget-panel{display:none}.problem-section{padding:55px 0}.problem-list{grid-template-columns:1fr}.section-space{padding:75px 0}.feature-grid,.steps-grid{grid-template-columns:1fr}.section-heading.centered{text-align:left}.feature-card{padding:22px}.estimator-section{padding-top:0}.estimator-card{width:100%;padding:52px 24px;border-radius:0}.estimate-result{padding:22px}.faq-list summary{font-size:15px}.final-cta{padding-bottom:75px}.final-cta-inner{width:100%;padding:60px 22px;border-radius:0}.footer-links{width:100%;gap:45px}.footer-bottom{align-items:flex-start;flex-direction:column;gap:8px}.preview-heading strong{font-size:16px}}
.reveal-section{opacity:0;transform:translateY(34px);transition:opacity .72s cubic-bezier(.22,1,.36,1),transform .72s cubic-bezier(.22,1,.36,1)}.reveal-section.is-visible{opacity:1;transform:none}
@media(min-width:601px){.hero+.dashboard-showcase .showcase-card{opacity:0;transform:translateY(34px);transition:opacity .72s cubic-bezier(.22,1,.36,1),transform .72s cubic-bezier(.22,1,.36,1),box-shadow .16s ease}.hero.is-visible+.dashboard-showcase .showcase-card{opacity:1;transform:none}.hero.is-visible+.dashboard-showcase .showcase-card:nth-child(2){transition-delay:.08s}.hero.is-visible+.dashboard-showcase .showcase-card:nth-child(3){transition-delay:.16s}.hero+.dashboard-showcase :is(.showcase-label,.showcase-arrow){opacity:0;transition:opacity .52s .2s ease}.hero.is-visible+.dashboard-showcase :is(.showcase-label,.showcase-arrow){opacity:1}}.reveal-section .section-heading,.reveal-section .problem-grid>div,.reveal-section .estimator-card>div,.reveal-section .faq-grid>div,.reveal-section .final-cta-inner{opacity:0;transform:translateY(22px);transition:opacity .58s .12s ease,transform .58s .12s cubic-bezier(.22,1,.36,1)}.reveal-section.is-visible .section-heading,.reveal-section.is-visible .problem-grid>div,.reveal-section.is-visible .estimator-card>div,.reveal-section.is-visible .faq-grid>div,.reveal-section.is-visible .final-cta-inner{opacity:1;transform:none}.reveal-section .feature-card,.reveal-section .step-card,.reveal-section .problem-list>div{opacity:0;transform:translateY(22px);transition:opacity .5s ease,transform .5s cubic-bezier(.22,1,.36,1),border-color .22s ease,box-shadow .22s ease}.reveal-section.is-visible .feature-card,.reveal-section.is-visible .step-card,.reveal-section.is-visible .problem-list>div{opacity:1;transform:none}.reveal-section.is-visible :is(.feature-card,.step-card,.problem-list>div):nth-child(1){transition-delay:.12s}.reveal-section.is-visible :is(.feature-card,.step-card,.problem-list>div):nth-child(2){transition-delay:.2s}.reveal-section.is-visible :is(.feature-card,.step-card,.problem-list>div):nth-child(3){transition-delay:.28s}.reveal-section.is-visible :is(.feature-card,.step-card,.problem-list>div):nth-child(4){transition-delay:.36s}.reveal-section.is-visible .feature-card:nth-child(5){transition-delay:.44s}.reveal-section.is-visible .feature-card:nth-child(6){transition-delay:.52s}

/* Hover must override the completed reveal animation without inheriting its delay. */
.reveal-section.is-visible :is(.feature-card,.step-card,.problem-list>div):hover{z-index:2;transition-delay:0s;transition-duration:.16s;border-color:transparent;box-shadow:0 0 0 1px rgba(41,0,41,.16),0 10px 24px rgba(32,0,36,.09)}
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
.button,.button-large{font-size:16px}.hero-note,.section-kicker,.step-number,.result-label{font-size:14px}.problem-list>div,.feature-card p,.step-card p,.faq-list details p{font-size:16px}.section-heading>p:last-child,.estimator-copy>p:not(.section-kicker),.final-cta-inner>p:not(.section-kicker){font-size:18px}.feature-card h3{font-size:24px}.step-card h3{font-size:28px}.estimate-result>small,.result-row{font-size:14px}.estimate-result>p{font-size:11px}
@media(max-width:600px){.hero-copy>p,.section-heading>p:last-child{font-size:16px}.feature-card h3{font-size:16px}.step-card h3{font-size:14px}.step-number{font-size:11px}.problem-list>div{font-size:14px}}
/* Problem cards: warmer typography and immediate hover feedback. */
.problem-list>div{font-family:'Inter',system-ui,sans-serif;font-size:17px;font-weight:500;line-height:1.45;letter-spacing:-.01em;transition:transform .13s cubic-bezier(.2,.8,.2,1),border-color .13s ease,box-shadow .13s ease}
.reveal-section.is-visible .problem-list>div:nth-child(n),
.reveal-section.is-visible :is(.feature-card,.step-card):nth-child(n){transition-delay:0s!important;transition-duration:.06s}
.reveal-section.is-visible .problem-list>div:nth-child(n):hover,
.reveal-section.is-visible :is(.feature-card,.step-card):nth-child(n):hover{transition-delay:0s!important;transition-duration:.11s}
@media(max-width:600px){.problem-list>div{font-size:14px;font-weight:500;line-height:1.4}}

/* Outseta sunset system — DESIGN.md */
.landing-page{--aubergine:#240029;--fuchsia:#df37a7;--heather:#6d526d;--plum:#d4ccd4;--canvas:#fff;--warm:#fff1bd;--purple-shadow:0 0 0 1px rgba(41,0,41,.11),0 2px 2px rgba(32,0,36,.05);background:var(--canvas);color:var(--aubergine);font-family:'Inter Var','Inter',ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;font-feature-settings:'calt' on,'cv11' on,'ss02' on,'ss03' on}
.container{width:min(1200px,calc(100% - 48px))}
.button{min-height:48px;padding:0 24px;border-radius:10px;font-family:'Bricolage Grotesque','Inter',ui-sans-serif,sans-serif;font-size:16px;font-weight:600;line-height:1.2;letter-spacing:-.015em}
.button-primary{border-color:transparent;background:var(--fuchsia);color:var(--canvas);box-shadow:inset 0 0 0 1px rgba(255,255,255,.2),0 1px 2px rgba(32,0,36,.08)}
.button-primary:hover{background:#c92b96;box-shadow:inset 0 0 0 1px rgba(255,255,255,.2),0 4px 12px rgba(32,0,36,.16);transform:translateY(-2px)}
.button-ghost{border-color:var(--heather);background:transparent;color:var(--aubergine)}
.button-ghost:hover{border-color:var(--aubergine);background:rgba(255,255,255,.34);color:var(--aubergine)}
.hero{min-height:auto;padding:164px 0 240px;background:linear-gradient(145deg,#ffbdd3,#fff1bd 40%,#fff1bd 60%,#ffcb0f)}
.hero-grid{display:block}
.hero-copy{max-width:1040px;margin:0 auto;text-align:center}
.hero h1,.section-heading h2,.problem-grid h2,.estimator-copy h2,.final-cta h2{color:var(--aubergine);font-family:'Bricolage Grotesque','Inter',ui-sans-serif,sans-serif;font-weight:800;letter-spacing:-.04em}
.hero h1{max-width:1100px;margin:0 auto;font-family:'Bricolage Grotesque','Inter',ui-sans-serif,sans-serif;font-size:clamp(58px,6.8vw,86px);font-weight:800;line-height:1.04;letter-spacing:-.04em}
.hero h1 em{display:inline;padding:0;background:none;color:inherit;font-style:normal;font-weight:800}
.hero-copy>p{max-width:1000px;margin:32px auto;color:var(--heather);font-family:'Fraunces',Georgia,serif;font-size:clamp(20px,2.15vw,28px);font-weight:400;line-height:1.45;letter-spacing:-.02em}
.hero-actions,.hero-note{justify-content:center}.hero-actions .button{min-height:58px;padding:0 30px}.hero-note{color:var(--heather);font-size:14px}.hero-note svg{color:var(--aubergine)}.feature-marquee{width:min(100%,800px);margin:28px auto -28px;overflow:hidden;mask-image:linear-gradient(90deg,transparent,#000 8%,#000 92%,transparent)}.feature-marquee__track{display:flex;width:max-content;align-items:center;gap:12px;animation:feature-marquee 24s linear infinite;will-change:transform}.feature-marquee span{display:inline-flex;min-height:34px;padding:0 18px;align-items:center;border:1px solid rgba(36,0,41,.18);border-radius:999px;background:rgba(255,255,255,.12);color:var(--aubergine);font-family:'JetBrains Mono',ui-monospace,monospace;font-size:12px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;white-space:nowrap}@keyframes feature-marquee{to{transform:translateX(calc(-50% - 6px))}}
.product-preview{border:0;border-radius:14px;background:var(--canvas);box-shadow:0 0 0 1px rgba(32,0,36,.11),0 1px 1.5px rgba(32,0,36,.01),0 4px 6px rgba(32,0,36,.03),0 16px 24px rgba(32,0,36,.05);transform:rotate(1deg)}
.preview-topbar{border-color:var(--plum);background:var(--canvas)}.preview-body{background:var(--canvas)}
.preview-stats article,.preview-panels article{border-color:var(--plum);border-radius:6px}.preview-stats article>span,.date-chip{background:#f7eff7;color:var(--aubergine)}
.section-space{padding:96px 0}.section-kicker{padding:0;background:transparent!important;color:var(--aubergine)!important;font-family:'JetBrains Mono','IBM Plex Mono',ui-monospace,monospace;font-size:12px;line-height:1.5;letter-spacing:.1em;text-transform:uppercase}
.section-heading h2,.problem-grid h2,.estimator-copy h2,.final-cta h2{font-size:clamp(40px,4.7vw,56px);line-height:1.1}.section-heading>p:last-child{color:var(--heather);font-size:16px;line-height:1.5}
.problem-section,.how-section,.estimator-section,.faq-section{background:var(--canvas)}.features-section{background:#f7eff7}.dashboard-showcase{position:relative;z-index:3;margin-top:-168px;padding:0 0 96px;background:transparent}.dashboard-showcase::after{position:absolute;z-index:-1;right:0;bottom:0;left:0;height:calc(50% + 96px);background:#fff;content:""}.showcase-grid{display:grid;grid-template-columns:repeat(3,1fr);align-items:center;gap:32px}.showcase-card{position:relative;overflow:visible;border:1px solid rgba(36,0,41,.16);border-radius:18px;background:#fff;box-shadow:0 14px 36px rgba(32,0,36,.13),inset 0 0 0 3px rgba(255,255,255,.58);transition:transform .16s ease,box-shadow .16s ease}.showcase-label{position:absolute;z-index:4;color:#df37a7;font-family:'Kalam',cursive;font-size:24px;font-weight:700;font-style:normal;line-height:1.02;letter-spacing:.01em;text-transform:none;pointer-events:none}.showcase-label--first{top:-112px;left:-4px;text-align:left;transform:rotate(-3deg)}.showcase-label--second{right:-62px;bottom:-116px;text-align:right;transform:rotate(3deg)}.showcase-label--third{top:-113px;right:-2px;text-align:right;transform:rotate(3deg)}.showcase-arrow{position:absolute;z-index:4;width:112px;height:76px;overflow:visible;fill:none;stroke:#df37a7;stroke-width:4;stroke-linecap:round;stroke-linejoin:round;filter:drop-shadow(0 2px 0 rgba(255,255,255,.5));pointer-events:none}.showcase-arrow--first{top:-70px;left:42px;transform:rotate(-5deg)}.showcase-arrow--second{right:-54px;bottom:-62px;transform:rotate(5deg)}.showcase-arrow--third{top:-70px;right:38px;transform:rotate(4deg)}.showcase-card::after{position:absolute;z-index:2;inset:2px;border:1px solid rgba(255,255,255,.78);border-radius:15px;box-shadow:inset 1px 1px 0 rgba(255,255,255,.9),inset -1px -1px 0 rgba(109,82,109,.09);content:"";pointer-events:none}.showcase-card--raised{transform:none}.showcase-card:hover{box-shadow:0 20px 46px rgba(32,0,36,.18);transform:translateY(-6px)}.showcase-card--raised:hover{transform:translateY(-6px)}.showcase-screen{display:flex;height:240px;padding:28px 26px;overflow:hidden;align-items:center;justify-content:center;border-bottom:1px solid #eee4ee;background:linear-gradient(180deg,#fff2b8 0%,#fff8dc 46%,#fff 100%)}.showcase-screen img{display:block;width:100%;height:100%;object-fit:cover;object-position:top center;border:1px solid rgba(36,0,41,.16);border-radius:8px;background:#fff;box-shadow:0 8px 20px rgba(32,0,36,.14)}.showcase-screen--dashboard{background:linear-gradient(145deg,#fff1bd,#ffdeec)}.showcase-screen--budget{background:linear-gradient(145deg,#f6eafa,#fff)}.showcase-screen--checklist{background:linear-gradient(145deg,#fff,#fff1bd)}.mock-top{display:flex;align-items:center;justify-content:space-between;height:24px;margin-bottom:15px;padding:0 8px;border-radius:6px;background:#fff}.mock-top span{width:62px;height:7px;border-radius:5px;background:#240029}.mock-top i{width:18px;height:18px;border-radius:50%;background:#df37a7}.mock-hero,.mock-budget,.mock-progress{display:flex;padding:18px;border-radius:10px;background:#fff;flex-direction:column}.mock-hero small,.mock-budget small,.mock-progress small{color:#6d526d;font-size:8px}.mock-hero strong,.mock-budget strong,.mock-progress strong{margin-top:5px;color:#240029;font-family:'Bricolage Grotesque',sans-serif;font-size:17px}.mock-hero b{align-self:flex-start;margin-top:16px;padding:6px 9px;border-radius:6px;background:#df37a7;color:#fff;font-size:8px}.mock-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-top:9px}.mock-stats i{height:55px;border-radius:8px;background:#fff}.mock-budget i{height:8px;margin-top:9px;border-radius:5px;background:#eaddea}.mock-budget i:nth-of-type(1){width:90%;background:#df37a7}.mock-budget i:nth-of-type(2){width:68%}.mock-budget i:nth-of-type(3){width:48%}.mock-chart{display:flex;height:50px;align-items:end;gap:8px;margin:10px 16px 0}.mock-chart b{width:18%;height:45%;border-radius:4px 4px 0 0;background:#d4ccd4}.mock-chart b:nth-child(2){height:78%;background:#df37a7}.mock-chart b:nth-child(3){height:62%}.mock-chart b:nth-child(4){height:90%;background:#240029}.mock-progress>i{height:8px;margin-top:14px;border-radius:99px;background:#eaddea}.mock-progress>i b{display:block;width:68%;height:100%;border-radius:inherit;background:#df37a7}.showcase-screen ul{display:grid;gap:8px;margin:10px 0 0;padding:0;list-style:none}.showcase-screen li{height:25px;border-radius:6px;background:#fff}.showcase-card footer{display:flex;min-height:94px;padding:20px 22px;align-items:center;flex-direction:column;justify-content:center;border-top:1px solid #eee4ee;text-align:center}.showcase-card footer strong{color:#240029;font-family:'Bricolage Grotesque',sans-serif;font-size:18px}.showcase-card footer span{margin-top:5px;color:#6d526d;font-size:13px}.testimonial-section{padding:96px 0;background:var(--canvas)}.testimonial-heading{max-width:760px;margin:0 auto 48px;text-align:center}.testimonial-heading h2{margin:0;color:var(--aubergine);font-family:'Bricolage Grotesque','Inter',ui-sans-serif,sans-serif;font-size:clamp(40px,4.7vw,56px);font-weight:800;line-height:1.1;letter-spacing:-.04em}.testimonial-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:22px}.testimonial-card{display:flex;min-height:280px;padding:26px 22px 22px;flex-direction:column;justify-content:space-between;border:1px solid var(--plum);border-radius:14px;background:var(--canvas);box-shadow:0 1px 2px rgba(32,0,36,.04);transition:transform .14s ease,box-shadow .14s ease,border-color .14s ease}.testimonial-card:hover{border-color:#bfaebf;box-shadow:0 10px 24px rgba(32,0,36,.09);transform:translateY(-3px)}.testimonial-card>p{margin:0;color:var(--aubergine);font-family:'Fraunces',Georgia,serif;font-size:18px;line-height:1.55}.testimonial-card footer{display:flex;align-items:center;gap:12px;margin-top:28px}.testimonial-avatar{display:grid;width:46px;height:46px;flex:none;place-items:center;border-radius:50%;background:#f7e8f3;color:var(--aubergine);font-family:'Bricolage Grotesque','Inter',sans-serif;font-size:13px;font-weight:800}.testimonial-card footer>span:last-child{display:flex;min-width:0;flex-direction:column}.testimonial-card strong{font-size:14px}.testimonial-card small{margin-top:3px;color:var(--heather);font-size:12px}.problem-section{padding:96px 0}.problem-grid{gap:72px}.problem-list{gap:16px}.problem-list>div,.problem-list>div:nth-child(n){min-height:76px;border:0;border-radius:14px;background:var(--canvas);color:var(--aubergine);box-shadow:var(--purple-shadow);font-size:16px}.problem-list>div span{border-color:var(--plum);border-radius:6px;background:#f7eff7}.problem-cta{align-self:flex-start}
.features-section{padding-top:32px;padding-bottom:44px}.feature-grid{gap:0;border-top:1px solid rgba(36,0,41,.16);border-bottom:1px solid rgba(36,0,41,.16)}.feature-card,.feature-card:nth-child(n){display:block;min-height:210px;padding:28px 26px;border:0;border-right:1px solid rgba(36,0,41,.14);border-radius:0;background:transparent;box-shadow:none}.feature-card:nth-child(3n){border-right:0}.feature-card:nth-child(n+4){border-top:1px solid rgba(36,0,41,.14)}.feature-card,.feature-card:nth-child(n){transition:background .18s ease,transform .18s ease,box-shadow .18s ease}.reveal-section.is-visible .feature-card:nth-child(n):hover{z-index:1;background:rgba(255,255,255,.72);box-shadow:0 10px 24px rgba(36,0,41,.07);transform:translateY(-3px);transition-delay:0s}.feature-icon{display:grid;width:34px;height:34px;margin:0 0 18px;place-items:center;border:1px solid rgba(36,0,41,.15);border-radius:7px;background:rgba(255,255,255,.7);box-shadow:0 1px 2px rgba(36,0,41,.05);color:var(--aubergine);transition:border-color .18s ease,background .18s ease,color .18s ease,transform .18s ease}.feature-card:hover .feature-icon{border-color:rgba(223,55,167,.32);background:#fff;color:var(--fuchsia);transform:translateY(-1px)}.feature-icon svg{width:17px;height:17px}.feature-card h3,.step-card h3{color:var(--aubergine);font-family:'Inter',ui-sans-serif,sans-serif}.feature-card h3{margin:0 0 8px;font-size:18px;font-weight:600;line-height:1.3;letter-spacing:-.02em}.feature-card p,.step-card p{color:var(--heather)}.feature-card p{max-width:34ch;margin:0;font-size:14px;line-height:1.55}.feature-cta-wrap{display:flex;margin:44px 0 0;align-items:center;justify-content:center}.feature-cta{min-height:58px;padding:0 32px;text-align:center}
.how-section{position:relative;border-top:1px solid var(--plum);background:#fff}.how-section .section-heading{max-width:820px;margin:0 auto 48px;text-align:center}.steps-grid{position:relative;gap:18px}.step-card,.step-card:nth-child(n){position:relative;display:flex;min-height:300px;padding:24px;flex-direction:column;border:1px solid rgba(36,0,41,.13);border-radius:16px;background:rgba(255,255,255,.88);box-shadow:0 8px 24px rgba(36,0,41,.05);transition:transform .18s ease,border-color .18s ease,box-shadow .18s ease}.reveal-section.is-visible .step-card:nth-child(n):hover{border-color:rgba(223,55,167,.28);box-shadow:0 14px 30px rgba(36,0,41,.09);transform:translateY(-4px) rotate(0);transition-delay:0s!important}.step-top{position:relative;z-index:1;min-height:28px}.step-number{display:inline-flex;min-height:28px;padding:0 10px;align-items:center;border:1px solid rgba(36,0,41,.18);border-radius:6px;background:rgba(255,255,255,.82);color:var(--heather);font-family:'JetBrains Mono',ui-monospace,monospace;font-size:10px;font-weight:600;line-height:1;letter-spacing:.08em;text-transform:uppercase}.step-top>svg{width:18px;border:0;background:transparent;color:rgba(36,0,41,.34)}.step-icon{display:grid;width:52px;height:52px;margin:34px 0 24px;place-items:center;border:1px solid rgba(223,55,167,.18);border-radius:14px;background:#f7eff7;box-shadow:none;color:var(--fuchsia)}.step-icon svg{width:24px;height:24px}.step-card h3{margin:0 0 10px;font-family:'Bricolage Grotesque','Inter',sans-serif;font-size:27px;font-weight:800;letter-spacing:-.03em}.step-card p{margin:0;color:var(--heather);font-size:15px;line-height:1.6}
.estimator-section{padding:96px 0}.estimator-card{border:0;border-radius:14px;background:var(--aubergine);color:var(--canvas);box-shadow:0 0 0 1px rgba(41,0,41,.11),0 16px 30px rgba(32,0,36,.12)}.estimator-copy h2{color:var(--canvas)}.estimator-copy>p:not(.section-kicker){color:var(--plum)}.estimator-copy .section-kicker{color:var(--plum)!important}.button-light{border:0;border-radius:6px;background:var(--canvas);color:var(--aubergine);box-shadow:none}.estimate-result{border:0;border-radius:14px;background:var(--canvas);color:var(--aubergine);box-shadow:var(--purple-shadow)}.result-label{color:var(--aubergine)}
.faq-list,.faq-list details{border-color:var(--plum)}.faq-list summary{color:var(--aubergine);font-family:'Inter',ui-sans-serif,sans-serif}.faq-list summary svg{color:var(--aubergine)}.faq-list details p{color:var(--heather)}
.testimonial-section,.how-section,.estimator-section,.faq-section,.final-cta{padding-top:48px;padding-bottom:48px}.final-cta{padding-bottom:96px;background:#fff}.final-cta-inner{display:flex;width:100%;padding:72px 32px;align-items:center;flex-direction:column;border:0;border-radius:14px;background:var(--aubergine);color:var(--canvas);box-shadow:0 0 0 1px rgba(41,0,41,.11),0 16px 30px rgba(32,0,36,.12);text-align:center}.final-icon{color:var(--plum)}.final-cta h2{color:var(--canvas)}.final-cta-inner>.button{min-height:58px;margin-top:27px;padding:0 30px}.final-cta .section-kicker{color:var(--plum)!important}.final-cta-inner>p:not(.section-kicker){color:var(--plum)}
.reveal-section.is-visible :is(.feature-card,.step-card,.problem-list>div):hover{border-color:transparent;box-shadow:0 0 0 1px rgba(41,0,41,.16),0 10px 24px rgba(32,0,36,.09)}
@media(max-width:1199px){.hero-grid{grid-template-columns:1fr}.hero-copy{max-width:800px;text-align:center}.hero-actions,.hero-note{justify-content:center}.product-preview{width:min(100%,800px);margin:auto}}
@media(max-width:900px){.container{width:min(100% - 40px,1200px)}.steps-grid::before{display:none}.showcase-grid{grid-template-columns:1fr;max-width:620px}.showcase-card--raised{transform:none}.showcase-card--raised:hover{transform:translateY(-6px)}.testimonial-grid{grid-template-columns:1fr}.testimonial-card{min-height:230px}.feature-grid{grid-template-columns:repeat(2,1fr)}.feature-card:nth-child(n){border-top:0;border-right:1px solid rgba(36,0,41,.14)}.feature-card:nth-child(2n){border-right:0}.feature-card:nth-child(n+3){border-top:1px solid rgba(36,0,41,.14)}.steps-grid{grid-template-columns:repeat(2,1fr)}.problem-cta{align-self:center}}
@media(max-width:600px){.showcase-label,.showcase-arrow{display:none}.container{width:min(100% - 28px,1200px)}.testimonial-section,.how-section,.estimator-section,.faq-section,.final-cta{padding-top:36px;padding-bottom:36px}.hero{padding:120px 0 220px}.dashboard-showcase{margin-top:-140px}.hero h1{font-size:clamp(40px,12vw,52px)}.hero-copy>p{font-size:17px}.hero-actions{align-items:stretch}.hero-actions .button{width:100%}.product-preview{display:none}.section-space,.problem-section{padding:72px 0}.problem-list,.feature-grid{grid-template-columns:1fr}.feature-card,.feature-card:nth-child(n){min-height:auto;padding:24px 4px;border-right:0;border-top:1px solid rgba(36,0,41,.14);text-align:left}.feature-card:first-child{border-top:0}.feature-card p{display:block}.feature-card h3{text-align:left}.steps-grid{grid-template-columns:repeat(2,1fr);gap:12px}.step-card{min-height:210px;padding:18px 14px}.step-icon{width:42px;height:42px}.step-card h3{font-size:20px}.step-card p{display:block;font-size:13px}.estimator-card{width:calc(100% - 28px);padding:40px 22px;border-radius:14px}.final-cta-inner{width:calc(100% - 28px);padding:72px 8px}.section-heading h2,.problem-grid h2,.estimator-copy h2,.final-cta h2{font-size:36px}.testimonial-section,.how-section,.estimator-section,.faq-section,.final-cta{padding-top:36px;padding-bottom:36px}.final-cta{padding-bottom:72px}}
</style>
