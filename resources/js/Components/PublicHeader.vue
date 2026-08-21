<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    estimator: {
        type: Boolean,
        default: false,
    },
    estimatorResult: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <header class="public-header">
        <div class="public-container public-header__inner">
            <Link :href="route('home')" class="public-brand" aria-label="Ruang Pasangan, kembali ke beranda">
                <img src="/ruang-pasangan.svg" alt="" />
                <span><strong>Ruang Pasangan</strong><small>Wedding Planner</small></span>
            </Link>

            <nav v-if="!estimator" class="public-nav" aria-label="Navigasi utama">
                <a href="#fitur">Fitur</a>
                <a href="#cara-kerja">Cara Kerja</a>
                <a href="#faq">FAQ</a>
            </nav>

            <div class="public-header__actions">
                <Link v-if="$page.props.auth.user" :href="route('dashboard')" class="public-button public-button--ghost">Dashboard</Link>
                <template v-else>
                    <Link :href="route('login')" class="public-login">Masuk</Link>
                    <a v-if="!estimator" href="/budget-estimator" class="public-button public-button--primary">Mulai</a>
                    <Link v-else :href="estimatorResult ? '/budget-estimator' : route('home')" class="public-button public-button--primary">Kembali</Link>
                </template>
            </div>
        </div>
    </header>
</template>

<style scoped>
.public-header{position:fixed;z-index:50;top:16px;right:20px;left:20px;background:transparent}.public-container{width:min(1160px,calc(100% - 40px));max-width:1200px;margin:0 auto}.public-header__inner{display:flex;align-items:center;justify-content:space-between;height:64px;padding:8px 10px 8px 12px;border:1px solid #b6b6b6;border-radius:16px;background:rgba(252,250,245,.96);box-shadow:0 18px 50px rgba(255,233,92,.22);gap:28px}.public-brand{display:flex;align-items:center;gap:10px;color:#1a3300;text-decoration:none}.public-brand img{width:40px;height:40px;background:transparent}.public-brand span{display:flex;flex-direction:column;line-height:1.08}.public-brand strong{font-family:'Inter',system-ui,sans-serif;font-size:18px;font-weight:800}.public-brand small{margin-top:4px;color:#1a3300;font-family:ui-monospace,monospace;font-size:8px;font-weight:700;letter-spacing:.12em;text-transform:uppercase}.public-nav{display:flex;align-items:center;gap:32px}.public-nav a,.public-login{color:#1a3300;font-family:'Inter',system-ui,sans-serif;font-size:14px;font-weight:500;text-decoration:none}.public-nav a:hover,.public-login:hover{background:linear-gradient(transparent 56%,#ffe95c 56%)}.public-header__actions{display:flex;align-items:center;gap:18px}.public-button{display:inline-flex;align-items:center;justify-content:center;min-height:42px;padding:0 18px;border:1px solid #1a3300;border-radius:6px;font-family:'Inter',system-ui,sans-serif;font-size:14px;font-weight:600;text-decoration:none}.public-button--primary{background:#ffe95c;color:#1a3300;box-shadow:3px 3px 0 #1a3300;transition:background .18s ease,box-shadow .18s ease,transform .18s ease}.public-button--primary:hover{background:#fff18a;box-shadow:1px 1px 0 #1a3300;transform:translate(2px,2px)}.public-button--ghost{background:transparent;color:#1a3300}
@media(max-width:900px){.public-nav{display:none}}@media(max-width:600px){.public-header{top:8px;right:8px;left:8px}.public-container{width:min(100% - 28px,1160px)}.public-header__inner{height:58px}.public-login{display:none}.public-header__actions{gap:10px}.public-button{min-height:38px;padding:0 13px;font-size:11px}.public-brand img{width:35px;height:35px}.public-brand strong{font-size:14px}}
</style>
