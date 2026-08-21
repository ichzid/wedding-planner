<script setup>
import { Link } from '@inertiajs/vue3';
import { ArrowRight } from '@lucide/vue';

defineProps({
    estimator: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <footer class="public-footer">
        <div class="public-container public-footer__main">
            <div class="public-footer__brand">
                <Link :href="route('home')" class="public-brand">
                    <img src="/ruang-pasangan.svg" alt="" />
                    <span><strong>Ruang Pasangan</strong><small>Wedding Planner</small></span>
                </Link>
                <p>Tempat yang tenang untuk menyusun setiap bagian hari bahagia bersama pasangan.</p>
                <a href="/budget-estimator" class="public-footer__cta">{{ estimator ? 'Hitung budget lagi' : 'Hitung budget gratis' }} <ArrowRight aria-hidden="true" /></a>
            </div>

            <nav class="public-footer__links" aria-label="Navigasi footer">
                <div><strong>Jelajahi</strong><a href="/#fitur">Semua Fitur</a><a href="/#cara-kerja">Cara Kerja</a><a href="/#faq">Pertanyaan Umum</a></div>
                <div><strong>Mulai</strong><a href="/budget-estimator">Budget Estimator</a><Link :href="route('register')">Buat Akun</Link><Link :href="route('login')">Masuk</Link></div>
            </nav>
        </div>
        <div class="public-container public-footer__bottom"><span>© 2026 Ruang Pasangan</span><span>Dibuat untuk perjalanan menuju hari bahagia.</span><a :href="estimator ? '/' : '#beranda'">{{ estimator ? 'Kembali ke beranda' : 'Kembali ke atas' }} ↑</a></div>
    </footer>
</template>

<style scoped>
.public-container{width:min(1160px,calc(100% - 40px));max-width:1200px;margin:0 auto}.public-footer{padding:74px 0 24px;border-top:1px solid #1a3300;background:linear-gradient(135deg,#d5f5c2 0%,#ccebea 52%,#dcebc7 100%);color:#1a3300;font-family:'Inter',system-ui,sans-serif}.public-footer__main{display:grid;align-items:start;grid-template-columns:minmax(0,1.3fr) minmax(360px,.7fr);gap:100px}.public-brand{display:flex;align-items:center;gap:10px;color:#1a3300;text-decoration:none}.public-brand img{width:40px;height:40px;background:transparent}.public-brand span{display:flex;flex-direction:column;line-height:1.08}.public-brand strong{font-size:20px;font-weight:800}.public-brand small{margin-top:4px;color:rgba(26,51,0,.6);font-family:ui-monospace,monospace;font-size:8px;font-weight:700;letter-spacing:.12em;text-transform:uppercase}.public-footer__brand>p{max-width:420px;margin:22px 0 25px;color:rgba(26,51,0,.72);font-size:14px;line-height:1.7}.public-footer__cta{display:inline-flex;align-items:center;gap:8px;padding:10px 14px;border:1px solid #1a3300;border-radius:6px;background:#ffe95c;color:#1a3300;box-shadow:3px 3px 0 #1a3300;font-size:13px;font-weight:700;text-decoration:none;transition:.18s}.public-footer__cta:hover{gap:13px;background:#fff18a;box-shadow:1px 1px 0 #1a3300;transform:translate(2px,2px)}.public-footer__cta svg{width:15px}.public-footer__links{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:55px}.public-footer__links>div{display:flex;align-items:flex-start;flex-direction:column;gap:13px;min-width:0}.public-footer__links strong{width:max-content;margin-bottom:6px;padding:4px 7px;border-radius:4px;background:#ffe95c;font-family:ui-monospace,monospace;font-size:11px;letter-spacing:.08em;text-transform:uppercase}.public-footer__links a{width:max-content;color:rgba(26,51,0,.7);font-size:13px;line-height:1.2;text-decoration:none;transition:.2s}.public-footer__links a:hover{color:#1a3300;transform:translateX(3px)}.public-footer__bottom{display:grid;grid-template-columns:1fr auto 1fr;align-items:center;gap:28px;margin-top:62px;padding-top:22px;border-top:1px solid rgba(26,51,0,.25);color:rgba(26,51,0,.58);font-size:10px}.public-footer__bottom span:nth-child(2){text-align:center}.public-footer__bottom a{justify-self:end;color:rgba(26,51,0,.72);font-weight:600;text-decoration:none}.public-footer__bottom a:hover{background:linear-gradient(transparent 55%,#ffe95c 55%)}
@media(max-width:900px){.public-footer__main{grid-template-columns:1fr;gap:52px}.public-footer__links{max-width:460px}.public-footer__bottom{grid-template-columns:1fr auto}.public-footer__bottom span:nth-child(2){display:none}}@media(max-width:600px){.public-container{width:min(100% - 28px,1160px)}.public-footer{padding-top:58px}.public-footer__main{gap:44px}.public-footer__links{width:100%;gap:28px}.public-footer__bottom{grid-template-columns:1fr;gap:10px;margin-top:46px}.public-footer__bottom a{justify-self:start}}
</style>
