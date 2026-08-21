<script setup>
import { Link } from '@inertiajs/vue3';
import { CalendarCheck2, FileCheck2, HeartHandshake, ListChecks, WalletCards } from '@lucide/vue';
</script>

<template>
    <main class="auth-shell">
        <section class="auth-intro" aria-label="Ruang Pasangan">
            <Link href="/" class="auth-brand">
                <span class="auth-brand__mark"><HeartHandshake aria-hidden="true" /></span>
                <span>
                    <strong>Ruang Pasangan</strong>
                    <small>Wedding Planner</small>
                </span>
            </Link>

            <div class="auth-intro__copy">
                <p class="auth-eyebrow">Rencanakan dengan tenang</p>
                <h1>Semua persiapan hari istimewa dalam <span>satu ruang.</span></h1>
                <p class="auth-lead">Susun setiap detail pernikahan dengan lebih rapi, terarah, dan menyenangkan bersama pasangan.</p>

                <div class="auth-features" aria-label="Fitur utama">
                    <span><ListChecks aria-hidden="true" /> Persiapan</span>
                    <span><WalletCards aria-hidden="true" /> Anggaran</span>
                    <span><FileCheck2 aria-hidden="true" /> Dokumen</span>
                    <span><CalendarCheck2 aria-hidden="true" /> Hari Pernikahan</span>
                </div>
            </div>

        </section>

        <section class="auth-panel">
            <div class="auth-card"><slot /></div>
        </section>
    </main>
</template>

<style>
body { margin:0; font-family:var(--font); color:#1a3300; background:#fcfaf5; }
.auth-shell { min-height:100dvh; display:grid; grid-template-columns:minmax(360px,.92fr) minmax(440px,1.08fr); background:#fcfaf5; }
.auth-intro { position:sticky; top:0; align-self:start; display:flex; flex-direction:column; justify-content:space-between; box-sizing:border-box; height:100dvh; padding:clamp(32px,5vw,68px); overflow:hidden; border-right:1px solid #1a3300; background:#d5f5c2; color:#1a3300; animation:authIntroIn .5s cubic-bezier(.22,1,.36,1) both; }
.auth-intro::before,.auth-intro::after { content:''; position:absolute; pointer-events:none; }
.auth-intro::before { width:230px; height:230px; top:-70px; right:-65px; border:2px solid rgba(26,51,0,.18); border-radius:46% 54% 57% 43%; transform:rotate(18deg); }
.auth-intro::after { width:170px; height:170px; left:-70px; bottom:-55px; border-radius:43% 57% 46% 54%; background:#a8e5e5; transform:rotate(-12deg); }
.auth-brand { position:relative; z-index:1; display:inline-flex; align-items:center; gap:12px; width:fit-content; color:#1a3300; text-decoration:none; }
.auth-brand__mark { display:grid; place-items:center; width:44px; height:44px; border:1px solid #1a3300; border-radius:12px; background:#fcfaf5; }
.auth-brand__mark svg { width:24px; height:24px; color:#1a3300; stroke-width:1.8; }
.auth-brand strong,.auth-brand small { display:block; }
.auth-brand strong { font:700 19px/1.2 var(--font-display); letter-spacing:.02em; }
.auth-brand small { margin-top:3px; color:rgba(26,51,0,.62); font-size:10px; font-weight:600; letter-spacing:.12em; text-transform:uppercase; }
.auth-intro__copy { position:relative; z-index:1; max-width:570px; padding:8vh 0; }
.auth-eyebrow { display:inline-flex; margin:0 0 18px; padding:6px 9px; border:1px solid #1a3300; border-radius:6px; background:#a8e5e5; color:#1a3300; font-size:11px; font-weight:700; letter-spacing:.13em; text-transform:uppercase; }
.auth-intro__copy h1 { max-width:550px; margin:0; font:800 clamp(38px,4vw,58px)/1.04 var(--font-display); letter-spacing:.025em; }
.auth-intro__copy h1 span { background:linear-gradient(transparent 58%,#ffe95c 58%); }
.auth-lead { max-width:500px; margin:22px 0 0; color:rgba(26,51,0,.76); font-size:16px; line-height:1.65; }
.auth-features { display:flex; flex-wrap:wrap; gap:9px; margin-top:30px; }
.auth-features span { display:inline-flex; align-items:center; gap:7px; padding:9px 11px; border:1px solid #1a3300; border-radius:8px; background:#fcfaf5; color:#1a3300; font-size:12px; font-weight:600; }
.auth-features span:nth-child(2) { background:#f6d0ff; }
.auth-features span:nth-child(3) { background:#a8e5e5; }
.auth-features svg { width:14px; height:14px; }
.auth-intro__footer { position:relative; z-index:1; margin:0; color:rgba(26,51,0,.58); font-size:12px; }
.auth-panel { display:flex; align-items:center; justify-content:center; padding:clamp(28px,6vw,76px); background:#fcfaf5; }
.auth-card { width:min(100%,470px); box-sizing:border-box; padding:clamp(28px,3vw,40px); border:1px solid #1a3300; border-radius:12px; background:#fcfaf5; box-shadow:7px 7px 0 #a8e5e5; animation:authCardIn .45s .06s cubic-bezier(.22,1,.36,1) both; }
.auth-card h2 { margin:0; font:800 30px/1.15 var(--font-display); letter-spacing:.025em; }
.auth-card .form-input { width:100%; min-height:48px; box-sizing:border-box; border:1px solid #1a3300; border-radius:8px; padding:0 14px; background:#fff; color:#1a3300; font:500 13.5px var(--font); transition:border-color .15s,box-shadow .15s,background .15s; }
.auth-card .form-input::placeholder { color:#788570; }
.auth-card .form-input:focus { outline:none; border-color:#1a3300; background:#fff; box-shadow:3px 3px 0 #a8e5e5; }
.auth-card .btn-primary { display:inline-flex; align-items:center; justify-content:center; gap:8px; width:100%; min-height:48px; border:1px solid #1a3300; border-radius:6px; background:#1a3300; color:#fcfaf5; font:650 13.5px var(--font); cursor:pointer; box-shadow:3px 3px 0 #a8e5e5; transition:transform .15s,box-shadow .15s; }
.auth-card .btn-primary:hover:not(:disabled) { transform:translate(2px,2px); box-shadow:1px 1px 0 #a8e5e5; }
.auth-card .btn-primary:disabled { opacity:.65; cursor:wait; }
@keyframes authIntroIn { from { opacity:0; transform:translateX(-18px); } to { opacity:1; transform:translateX(0); } }
@keyframes authCardIn { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:translateY(0); } }
@media(max-width:820px) {
  .auth-shell { display:block; min-height:100dvh; padding:18px 14px 32px; background:#d5f5c2; }
  .auth-intro { position:relative; display:block; width:auto; height:auto; padding:4px 4px 26px; overflow:visible; border:0; background:transparent; }
  .auth-intro::before,.auth-intro::after,.auth-intro__copy,.auth-intro__footer { display:none; }
  .auth-brand__mark { width:42px; height:42px; }
  .auth-panel { display:block; padding:0; background:transparent; }
  .auth-card { margin:0 auto; padding:26px 20px; box-shadow:5px 5px 0 #1a3300; }
}
@media(max-width:380px) { .auth-card { padding:23px 17px; } }
@media(prefers-reduced-motion:reduce) { .auth-intro,.auth-card { animation:none; } .auth-card * { transition:none!important; } }
</style>
