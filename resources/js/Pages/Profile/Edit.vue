<script setup>
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import UpdateWeddingInformationForm from './Partials/UpdateWeddingInformationForm.vue';
import { CalendarHeart, ChevronRight, HeartHandshake, ShieldCheck, UserRound } from '@lucide/vue';

defineProps({
    mustVerifyEmail: Boolean,
    status: String,
    wedding: { type: Object, default: () => ({}) },
});

const activeTab = ref('account');
const tabs = [
    { id: 'account', label: 'Profil Akun', description: 'Nama dan alamat email', icon: UserRound },
    { id: 'wedding', label: 'Pernikahan', description: 'Pasangan, tanggal, dan lokasi', icon: HeartHandshake },
    { id: 'security', label: 'Keamanan', description: 'Perbarui password akun', icon: ShieldCheck },
];
</script>

<template>
    <AppLayout>
        <main class="profile-page">
            <section class="profile-hero">
                <div class="profile-hero__copy">
                    <span class="profile-eyebrow"><HeartHandshake aria-hidden="true" /> Ruang Personal</span>
                    <h1>Pengaturan Profil</h1>
                    <p>Kelola identitas akun, detail hari pernikahan, dan keamanan dalam satu tempat.</p>
                </div>
                <div class="profile-hero__visual" aria-hidden="true"><span><CalendarHeart /></span></div>
            </section>

            <section class="settings-layout">
                <nav class="settings-tabs" aria-label="Kategori pengaturan">
                    <p class="settings-tabs__label">Menu Pengaturan</p>
                    <div class="settings-tabs__list" role="tablist" aria-orientation="vertical">
                        <button
                            v-for="tab in tabs"
                            :key="tab.id"
                            type="button"
                            class="settings-tab"
                            :class="{ active: activeTab === tab.id }"
                            role="tab"
                            :aria-selected="activeTab === tab.id"
                            :aria-controls="`settings-panel-${tab.id}`"
                            @click="activeTab = tab.id"
                        >
                            <span class="settings-tab__icon"><component :is="tab.icon" aria-hidden="true" /></span>
                            <span class="settings-tab__copy"><strong>{{ tab.label }}</strong><small>{{ tab.description }}</small></span>
                            <ChevronRight class="settings-tab__arrow" aria-hidden="true" />
                        </button>
                    </div>
                </nav>

                <article class="settings-panel" role="tabpanel" :id="`settings-panel-${activeTab}`">
                    <Transition name="settings-fade" mode="out-in">
                        <UpdateProfileInformationForm
                            v-if="activeTab === 'account'"
                            key="account"
                            :must-verify-email="mustVerifyEmail"
                            :status="status"
                        />
                        <UpdateWeddingInformationForm v-else-if="activeTab === 'wedding'" key="wedding" :wedding="wedding" />
                        <UpdatePasswordForm v-else key="security" />
                    </Transition>
                </article>
            </section>
        </main>
    </AppLayout>
</template>

<style scoped>
.profile-page{display:grid;gap:18px}.profile-hero{position:relative;display:flex;align-items:center;justify-content:space-between;min-height:190px;padding:34px 38px;overflow:hidden;border:1px solid var(--border);border-radius:20px;background:linear-gradient(135deg,#fff 0%,var(--surface-soft) 100%);box-shadow:0 8px 26px rgba(61,83,57,.06)}.profile-hero::after{position:absolute;right:-60px;bottom:-90px;width:240px;height:240px;border-radius:50%;background:rgba(118,145,109,.08);content:""}.profile-hero__copy{position:relative;z-index:1;max-width:650px}.profile-eyebrow{display:inline-flex;align-items:center;gap:7px;margin-bottom:10px;color:var(--accent);font-size:11px;font-weight:700;letter-spacing:.12em;text-transform:uppercase}.profile-eyebrow svg{width:15px;height:15px}.profile-hero h1{margin:0;color:var(--text);font-family:var(--font-display);font-size:clamp(28px,3vw,38px);font-weight:700;line-height:1.15}.profile-hero p{max-width:570px;margin:10px 0 0;color:var(--text-muted);font-size:14px;line-height:1.65}.profile-hero__visual{position:relative;z-index:1;display:grid;width:92px;height:92px;flex:none;place-items:center;border:1px solid rgba(118,145,109,.2);border-radius:28px;background:rgba(255,255,255,.72);transform:rotate(5deg)}.profile-hero__visual span{display:grid;width:56px;height:56px;place-items:center;border-radius:18px;background:var(--accent-soft);color:var(--accent);transform:rotate(-5deg)}.profile-hero__visual svg{width:29px;height:29px}.settings-layout{display:grid;grid-template-columns:280px minmax(0,1fr);gap:18px;align-items:start}.settings-tabs,.settings-panel{border:1px solid var(--border);border-radius:14px;background:var(--surface);box-shadow:0 5px 18px rgba(61,83,57,.045)}.settings-tabs{padding:18px}.settings-tabs__label{margin:0 0 10px 5px;color:var(--text-dim);font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase}.settings-tabs__list{display:grid;gap:6px}.settings-tab{display:flex;width:100%;align-items:center;gap:11px;padding:11px;border:0;border-radius:10px;background:transparent;color:var(--text-muted);text-align:left;cursor:pointer;transition:background .18s ease,color .18s ease}.settings-tab:hover{background:var(--surface-soft);color:var(--text)}.settings-tab.active{background:var(--accent-soft);color:var(--accent)}.settings-tab__icon{display:grid;width:36px;height:36px;flex:none;place-items:center;border:1px solid var(--border);border-radius:9px;background:var(--surface)}.settings-tab.active .settings-tab__icon{border-color:rgba(118,145,109,.25)}.settings-tab__icon svg{width:17px;height:17px}.settings-tab__copy{display:grid;min-width:0;flex:1;gap:2px}.settings-tab__copy strong{font-size:12.5px;font-weight:700}.settings-tab__copy small{overflow:hidden;color:var(--text-dim);font-size:10.5px;font-weight:500;text-overflow:ellipsis;white-space:nowrap}.settings-tab__arrow{width:15px;height:15px;flex:none;opacity:.45}.settings-tab.active .settings-tab__arrow{opacity:1}.settings-panel{min-height:480px;padding:28px 30px}.settings-panel :deep(.section-title){margin:0!important;color:var(--text);font-family:var(--font-display);font-size:22px!important;line-height:1.3}.settings-panel :deep(.section-description){max-width:560px;margin:5px 0 0;color:var(--text-muted);font-size:12.5px;line-height:1.55}.settings-panel :deep(form){display:grid;width:100%;gap:16px;margin-top:24px!important}.settings-panel :deep(.form-field){display:grid;gap:7px}.settings-panel :deep(.form-label){margin:0;color:var(--text);font-size:12px;font-weight:650}.settings-panel :deep(.input-shell){position:relative}.settings-panel :deep(.input-shell>svg){position:absolute;top:50%;left:13px;width:17px;height:17px;color:var(--text-dim);pointer-events:none;transform:translateY(-50%)}.settings-panel :deep(.form-input){box-sizing:border-box;width:100%;height:44px;padding:0 13px 0 42px!important;border:1px solid var(--border)!important;border-radius:10px!important;background:var(--surface)!important;color:var(--text)!important;font-size:13px!important}.settings-panel :deep(.form-input:focus){border-color:var(--accent)!important;box-shadow:0 0 0 3px var(--accent-soft)!important;outline:0}.settings-panel :deep(.form-grid){display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}.settings-panel :deep(.form-actions){display:flex;justify-content:flex-end;padding-top:4px}.settings-panel :deep(.btn){min-height:42px;padding-inline:18px}.settings-panel :deep(.verification-note){padding:12px 14px;border:1px solid var(--border);border-radius:10px;background:var(--surface-soft);color:var(--text-muted);font-size:12px;line-height:1.55}.settings-panel :deep(.verification-link){color:var(--accent);font-weight:650;text-decoration:underline;text-underline-offset:2px}.settings-panel :deep(.verification-success){margin-top:7px;color:var(--ok-text);font-weight:650}.settings-fade-enter-active,.settings-fade-leave-active{transition:opacity .15s ease,transform .15s ease}.settings-fade-enter-from{opacity:0;transform:translateY(5px)}.settings-fade-leave-to{opacity:0;transform:translateY(-3px)}@media(max-width:900px){.settings-layout{grid-template-columns:1fr}.settings-tabs{padding:12px}.settings-tabs__label{display:none}.settings-tabs__list{display:flex;gap:8px;overflow-x:auto;scrollbar-width:none}.settings-tabs__list::-webkit-scrollbar{display:none}.settings-tab{width:auto;min-width:max-content;padding:9px 12px}.settings-tab__copy small,.settings-tab__arrow{display:none}.settings-panel{min-height:440px}}@media(max-width:767px){.profile-page{min-width:0;gap:14px}.profile-hero{min-height:auto;padding:22px 20px;border-radius:14px}.profile-eyebrow{margin-bottom:7px;font-size:9.5px}.profile-hero h1{font-size:28px}.profile-hero p{margin-top:7px;font-size:12.5px;line-height:1.55}.profile-hero__visual{display:none}.settings-layout{min-width:0;gap:14px}.settings-tabs,.settings-panel{min-width:0;border-radius:13px}.settings-tabs{padding:8px}.settings-tabs__list{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:5px;overflow:visible}.settings-tab{display:grid;width:100%;min-width:0;justify-items:center;gap:5px;padding:8px 4px;text-align:center}.settings-tab__icon{width:30px;height:30px}.settings-tab__icon svg{width:15px;height:15px}.settings-tab__copy{display:block;width:100%;min-width:0}.settings-tab__copy strong{display:block;overflow:hidden;font-size:10.5px;text-overflow:ellipsis;white-space:nowrap}.settings-tab__copy small,.settings-tab__arrow{display:none}.settings-panel{min-height:0;padding:22px 18px}.settings-panel :deep(.section-title){font-size:20px!important}.settings-panel :deep(.section-description){font-size:12px}.settings-panel :deep(form){gap:15px;margin-top:20px!important}.settings-panel :deep(.form-grid){grid-template-columns:1fr;gap:15px}.settings-panel :deep(.form-input){min-width:0;height:44px}.settings-panel :deep(.form-actions){justify-content:stretch}.settings-panel :deep(.form-actions .btn){width:100%;min-height:44px}}@media(prefers-reduced-motion:reduce){.settings-tab,.settings-fade-enter-active,.settings-fade-leave-active{transition:none!important}}
</style>
