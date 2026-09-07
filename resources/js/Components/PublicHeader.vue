<script setup>
import { Link } from '@inertiajs/vue3';
import { onBeforeUnmount, onMounted, ref } from 'vue';

const activeSection = ref('');
const isScrolled = ref(false);

const updateActiveSection = () => {
    const sections = ['fitur', 'cara-kerja', 'faq'];
    const offset = 140;

    isScrolled.value = window.scrollY > 24;

    activeSection.value = sections.reduce((active, id) => {
        const section = document.getElementById(id);

        return section && section.getBoundingClientRect().top <= offset
            ? id
            : active;
    }, '');
};

onMounted(() => {
    updateActiveSection();
    window.addEventListener('scroll', updateActiveSection, { passive: true });
});

onBeforeUnmount(() => {
    window.removeEventListener('scroll', updateActiveSection);
});

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
        <div class="public-container public-header__inner" :class="{ 'is-scrolled': isScrolled }">
            <Link :href="route('home')" class="public-brand" aria-label="Ruang Pasangan, kembali ke beranda">
                <img src="/ruang-pasangan.svg" alt="" />
                <span><strong>Ruang Pasangan</strong><small>Wedding Planner</small></span>
            </Link>

            <nav v-if="!estimator" class="public-nav" aria-label="Navigasi utama">
                <a href="#fitur" :class="{ 'is-active': activeSection === 'fitur' }">Fitur</a>
                <a href="#cara-kerja" :class="{ 'is-active': activeSection === 'cara-kerja' }">Cara Kerja</a>
                <a href="#faq" :class="{ 'is-active': activeSection === 'faq' }">FAQ</a>
            </nav>

            <div class="public-header__actions">
                <Link v-if="$page.props.auth.user" :href="route('dashboard')" class="public-button public-button--ghost">Dashboard</Link>
                <template v-else>
                    <Link v-if="!estimator" :href="route('login')" class="public-button public-button--primary">Login</Link>
                    <Link v-else :href="estimatorResult ? '/budget-estimator' : route('home')" class="public-button public-button--primary">Kembali</Link>
                </template>
            </div>
        </div>
    </header>
</template>

<style scoped>
.public-header{position:fixed;z-index:50;top:16px;right:20px;left:20px;background:transparent;pointer-events:none}.public-container{width:min(1200px,calc(100% - 48px));margin:0 auto}.public-header__inner{display:flex;align-items:center;justify-content:space-between;height:64px;padding:8px 10px 8px 12px;border:1px solid rgba(36,0,41,.28);border-radius:16px;background:transparent;box-shadow:none;gap:28px;pointer-events:auto;backdrop-filter:none;transition:background .22s ease,box-shadow .22s ease,backdrop-filter .22s ease}.public-header__inner.is-scrolled{background:rgba(255,255,255,.96);box-shadow:0 8px 24px rgba(32,0,36,.1);backdrop-filter:blur(16px)}.public-brand{display:flex;align-items:center;gap:10px;color:#240029;text-decoration:none}.public-brand img{width:40px;height:40px;background:transparent}.public-brand span{display:flex;flex-direction:column;line-height:1.08}.public-brand strong{font-family:'Inter',system-ui,sans-serif;font-size:18px;font-weight:800}.public-brand small{margin-top:4px;color:#6d526d;font-family:ui-monospace,monospace;font-size:8px;font-weight:700;letter-spacing:.12em;text-transform:uppercase}.public-nav{display:flex;align-items:center;gap:28px}.public-nav a,.public-login{position:relative;padding:10px 0;color:#240029;font-family:'Inter',system-ui,sans-serif;font-size:14px;font-weight:500;text-decoration:none;transition:color .15s ease}.public-nav a::after,.public-login::after{position:absolute;right:0;bottom:5px;left:0;height:2px;border-radius:999px;background:#df37a7;content:"";transform:scaleX(0);transform-origin:center;transition:transform .15s ease}.public-nav a:hover,.public-login:hover,.public-nav a.is-active{color:#df37a7}.public-nav a:hover::after,.public-login:hover::after,.public-nav a.is-active::after{transform:scaleX(1)}.public-nav a.is-active{font-weight:700}.public-header__actions{display:flex;align-items:center;gap:18px}.public-button{display:inline-flex;align-items:center;justify-content:center;min-height:42px;padding:0 18px;border:1px solid #6d526d;border-radius:10px;font-family:'Bricolage Grotesque','Inter',system-ui,sans-serif;font-size:14px;font-weight:600;text-decoration:none;transition:background .18s ease,color .18s ease,box-shadow .18s ease}.public-button--primary{border-color:#df37a7;background:#df37a7;color:#fff;box-shadow:inset 0 0 0 1px rgba(255,255,255,.2),0 1px 2px rgba(32,0,36,.08)}.public-button--primary:hover{background:#c92d96}.public-button--ghost{background:transparent;color:#240029}.public-button--ghost:hover{background:#f8f5f8}.public-mobile-login{display:none}
@media(max-width:900px){.public-nav{display:none}}@media(max-width:600px){.public-header{top:8px;right:8px;left:8px}.public-container{width:min(100% - 28px,1200px)}.public-header__inner{height:58px;padding:8px 10px}.public-login,.public-start{display:none}.public-mobile-login{display:inline-flex}.public-header__actions{gap:10px}.public-button{min-height:38px;padding:0 13px;font-size:11px}.public-brand img{width:35px;height:35px}.public-brand strong{font-size:14px}}
</style>
