<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowRight, LoaderCircle, LockKeyhole, Mail } from '@lucide/vue';

defineProps({ canResetPassword: Boolean, status: String });

const form = useForm({ email: '', password: '', remember: false });
const submit = () => form.post(route('login'), { onFinish: () => form.reset('password') });
</script>

<template>
    <GuestLayout>
        <Head title="Masuk" />

        <header class="login-header">
            <h2>Masuk ke akun Anda</h2>
            <p>Lanjutkan merencanakan hari bahagia bersama pasangan.</p>
        </header>

        <div v-if="status" class="login-status" role="status">{{ status }}</div>

        <form class="login-form" @submit.prevent="submit">
            <div class="login-field">
                <label for="email">Alamat Email <span aria-hidden="true">*</span></label>
                <div class="login-input">
                    <Mail aria-hidden="true" />
                    <TextInput id="email" v-model="form.email" type="email" required autofocus autocomplete="username" placeholder="nama@email.com" :aria-invalid="Boolean(form.errors.email)" />
                </div>
                <InputError :message="form.errors.email" />
            </div>

            <div class="login-field login-field--password">
                <label for="password">Password <span aria-hidden="true">*</span></label>
                <div class="login-input">
                    <LockKeyhole aria-hidden="true" />
                    <TextInput id="password" v-model="form.password" type="password" required autocomplete="current-password" placeholder="Masukkan password" :aria-invalid="Boolean(form.errors.password)" />
                </div>
                <Link v-if="canResetPassword" :href="route('password.request')" class="login-link login-forgot">Lupa password?</Link>
                <InputError :message="form.errors.password" />
            </div>

            <button type="submit" class="btn-primary" :disabled="form.processing">
                <LoaderCircle v-if="form.processing" class="login-spinner" aria-hidden="true" />
                <span>{{ form.processing ? 'Memproses...' : 'Masuk' }}</span>
                <ArrowRight v-if="!form.processing" aria-hidden="true" />
            </button>
        </form>

        <div class="social-divider"><span>Atau lanjutkan dengan</span></div>
        <div class="social-login" aria-label="Pilihan login sosial segera hadir">
            <button type="button" class="social-button social-button--google" disabled>
                <svg aria-hidden="true" viewBox="0 0 48 48">
                    <path fill="#FFC107" d="M43.611 20.083H42V20H24v8h11.303C33.654 32.657 29.223 36 24 36c-6.627 0-12-5.373-12-12s5.373-12 12-12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 12.955 4 4 12.955 4 24s8.955 20 20 20 20-8.955 20-20c0-1.341-.138-2.65-.389-3.917Z"/>
                    <path fill="#FF3D00" d="m6.306 14.691 6.571 4.819C14.655 15.108 18.961 12 24 12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4c-7.682 0-14.344 4.337-17.694 10.691Z"/>
                    <path fill="#4CAF50" d="M24 44c5.166 0 9.86-1.977 13.409-5.192l-6.19-5.238C29.211 35.091 26.715 36 24 36c-5.202 0-9.619-3.317-11.283-7.946l-6.522 5.025C9.505 39.556 16.227 44 24 44Z"/>
                    <path fill="#1976D2" d="M43.611 20.083H42V20H24v8h11.303a12.04 12.04 0 0 1-4.087 5.571l6.193 5.237C36.971 39.205 44 34 44 24c0-1.341-.138-2.65-.389-3.917Z"/>
                </svg>
                <span>Lanjutkan dengan Google</span>
            </button>
            <button type="button" class="social-button social-button--apple" disabled>
                <svg aria-hidden="true" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M17.05 20.28c-.98.95-2.05.8-3.08.35-1.09-.46-2.09-.48-3.24 0-1.44.62-2.2.44-3.06-.35C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.79 1.18-.24 2.31-.93 3.57-.84 1.51.12 2.65.72 3.4 1.8-3.12 1.87-2.38 5.98.48 7.13-.57 1.5-1.31 2.99-2.53 4.09ZM12.03 7.25C11.88 5.02 13.69 3.18 15.77 3c.29 2.58-2.34 4.5-3.74 4.25Z"/>
                </svg>
                <span>Lanjutkan dengan Apple</span>
            </button>
        </div>

        <p class="login-register">
            Belum punya akun?
            <Link :href="route('register')" class="login-link">Daftar sekarang</Link>
        </p>
    </GuestLayout>
</template>

<style scoped>
.login-header { margin-bottom:27px; }
.login-eyebrow { margin:0 0 8px; color:var(--accent); font-size:10.5px; font-weight:750; letter-spacing:.13em; text-transform:uppercase; }
.login-header > p:last-child { margin:9px 0 0; color:var(--text-muted); font-size:12.5px; line-height:1.6; }
.login-status { margin-bottom:18px; padding:11px 13px; border:1px solid var(--ok-border); border-radius:10px; background:var(--ok-bg); color:var(--ok-text); font-size:12px; line-height:1.5; }
.login-form { display:flex; flex-direction:column; gap:13px; }
.login-field { display:flex; flex-direction:column; gap:7px; }
.login-field label,.login-label-row label { color:var(--text); font-size:12px; font-weight:650; }
.login-field label span { color:var(--accent); }
.login-field--password { position:relative; }
.login-field--password > label { padding-right:112px; }
.login-forgot { position:absolute; top:0; right:0; }
.login-input { position:relative; }
.login-input > svg { position:absolute; z-index:2; top:50%; left:14px; width:16px; height:16px; color:#879984; transform:translateY(-50%); pointer-events:none; }
.login-input :deep(input.form-input) { padding-left:42px!important; }
.login-input :deep(input.form-input.password-input) { padding-right:44px!important; }
.login-input :deep(button) { right:2px; width:42px; justify-content:center; padding:0; border:0; background:transparent; color:var(--text-dim); cursor:pointer; }
.login-input :deep(button svg) { width:17px; height:17px; }
.login-link { color:var(--accent); font-size:11.5px; font-weight:650; text-decoration:none; }
.login-link:hover { color:var(--accent-hover); text-decoration:underline; }
.login-link:focus-visible { outline:2px solid var(--accent); outline-offset:3px; border-radius:3px; }
.login-form > .btn-primary { margin-top:7px; }
.btn-primary svg { width:16px; height:16px; }
.login-spinner { animation:loginSpin .7s linear infinite; }
.social-divider { display:flex; align-items:center; gap:12px; margin:18px 0; color:var(--text-dim); font-size:10.5px; white-space:nowrap; }
.social-divider::before,.social-divider::after { content:''; flex:1; height:1px; background:var(--border); }
.social-login { display:flex; flex-direction:column; gap:9px; }
.social-button { display:flex; align-items:center; justify-content:center; gap:9px; min-height:44px; padding:0 18px; border:1px solid var(--border-strong); border-radius:10px; background:var(--surface); color:var(--text); opacity:1; cursor:not-allowed; }
.social-button > svg { width:18px; height:18px; flex:none; }
.social-button span { font-size:11.5px; font-weight:650; line-height:1.2; }
.social-button--apple { border-color:#20231f; background:#20231f; color:#fff; }
.login-register { margin:22px 0 0; padding-top:19px; border-top:1px solid var(--border); color:var(--text-muted); font-size:11.5px; text-align:center; }
.login-eyebrow { display:inline-flex; padding:5px 8px; border:1px solid #1a3300; border-radius:6px; background:#f6d0ff; color:#1a3300; }
.login-header > p:last-child { color:rgba(26,51,0,.68); font-size:13px; }
.login-field label,.login-label-row label { color:#1a3300; }
.login-field label span { margin-left:3px; color:#cb5521; }
.login-input > svg { color:#52654d; }
.login-link { color:#1a3300; text-decoration:underline; text-decoration-color:#a8e5e5; text-decoration-thickness:3px; text-underline-offset:3px; }
.login-link:hover { color:#cb5521; }
.social-divider { color:#687563; }
.social-divider::before,.social-divider::after { background:#b6b6b6; }
.social-button { border-color:#1a3300; border-radius:8px; background:#fff; color:#1a3300; }
.social-button--apple { border-color:#20231f; background:#20231f; color:#fff; }
.login-register { border-top-color:#b6b6b6; color:#687563; }
@keyframes loginSpin { to { transform:rotate(360deg); } }
@media(max-width:480px) {
  .login-header { margin-bottom:23px; }
  .login-header h2 { white-space:nowrap; }
  .login-header > p:last-child { max-width:none; font-size:clamp(9px,2.8vw,13px); white-space:nowrap; }
  .login-form { gap:13px; }
}
@media(prefers-reduced-motion:reduce) { .login-spinner { animation:none; } }
</style>
