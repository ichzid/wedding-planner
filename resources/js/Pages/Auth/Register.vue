<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowRight, LoaderCircle, LockKeyhole, Mail, UserRound } from '@lucide/vue';

const form = useForm({ name: '', email: '', password: '', password_confirmation: '' });
const submit = () => form.post(route('register'), { onFinish: () => form.reset('password', 'password_confirmation') });
</script>

<template>
    <GuestLayout>
        <Head title="Daftar" />

        <header class="register-header">
            <h2>Buat akun baru</h2>
            <p>Kelola seluruh persiapan hari bahagia dalam satu ruang.</p>
        </header>

        <form class="register-form" @submit.prevent="submit">
            <div class="register-field">
                <label for="name">Nama Lengkap <span aria-hidden="true">*</span></label>
                <div class="register-input">
                    <UserRound aria-hidden="true" />
                    <TextInput id="name" v-model="form.name" type="text" required autofocus autocomplete="name" placeholder="Masukkan nama lengkap" :aria-invalid="Boolean(form.errors.name)" />
                </div>
                <InputError :message="form.errors.name" />
            </div>

            <div class="register-field">
                <label for="email">Alamat Email <span aria-hidden="true">*</span></label>
                <div class="register-input">
                    <Mail aria-hidden="true" />
                    <TextInput id="email" v-model="form.email" type="email" required autocomplete="username" placeholder="nama@email.com" :aria-invalid="Boolean(form.errors.email)" />
                </div>
                <InputError :message="form.errors.email" />
            </div>

            <div class="register-passwords">
                <div class="register-field">
                    <label for="password">Password <span aria-hidden="true">*</span></label>
                    <div class="register-input">
                        <LockKeyhole aria-hidden="true" />
                        <TextInput id="password" v-model="form.password" type="password" required autocomplete="new-password" placeholder="Buat password" :aria-invalid="Boolean(form.errors.password)" />
                    </div>
                    <InputError :message="form.errors.password" />
                </div>

                <div class="register-field">
                    <label for="password_confirmation">Konfirmasi Password <span aria-hidden="true">*</span></label>
                    <div class="register-input">
                        <LockKeyhole aria-hidden="true" />
                        <TextInput id="password_confirmation" v-model="form.password_confirmation" type="password" required autocomplete="new-password" placeholder="Ulangi password" :aria-invalid="Boolean(form.errors.password_confirmation)" />
                    </div>
                    <InputError :message="form.errors.password_confirmation" />
                </div>
            </div>

            <button type="submit" class="btn-primary register-submit" :disabled="form.processing">
                <LoaderCircle v-if="form.processing" class="register-spinner" aria-hidden="true" />
                <span>{{ form.processing ? 'Membuat akun...' : 'Daftar Sekarang' }}</span>
                <ArrowRight v-if="!form.processing" aria-hidden="true" />
            </button>
        </form>

        <div class="social-divider"><span>Atau lanjutkan dengan</span></div>
        <div class="social-login" aria-label="Pilihan pendaftaran sosial">
            <button type="button" class="social-button" disabled>
                <svg aria-hidden="true" viewBox="0 0 48 48">
                    <path fill="#FFC107" d="M43.611 20.083H42V20H24v8h11.303C33.654 32.657 29.223 36 24 36c-6.627 0-12-5.373-12-12s5.373-12 12-12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 12.955 4 4 12.955 4 24s8.955 20 20 20 20-8.955 20-20c0-1.341-.138-2.65-.389-3.917Z"/>
                    <path fill="#FF3D00" d="m6.306 14.691 6.571 4.819C14.655 15.108 18.961 12 24 12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4c-7.682 0-14.344 4.337-17.694 10.691Z"/>
                    <path fill="#4CAF50" d="M24 44c5.166 0 9.86-1.977 13.409-5.192l-6.19-5.238C29.211 35.091 26.715 36 24 36c-5.202 0-9.619-3.317-11.283-7.946l-6.522 5.025C9.505 39.556 16.227 44 24 44Z"/>
                    <path fill="#1976D2" d="M43.611 20.083H42V20H24v8h11.303a12.04 12.04 0 0 1-4.087 5.571l6.193 5.237C36.971 39.205 44 34 44 24c0-1.341-.138-2.65-.389-3.917Z"/>
                </svg>
                <span>Lanjutkan dengan Google</span>
            </button>
            <button type="button" class="social-button social-button--apple" disabled>
                <svg aria-hidden="true" viewBox="0 0 24 24" fill="currentColor"><path d="M17.05 20.28c-.98.95-2.05.8-3.08.35-1.09-.46-2.09-.48-3.24 0-1.44.62-2.2.44-3.06-.35C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.79 1.18-.24 2.31-.93 3.57-.84 1.51.12 2.65.72 3.4 1.8-3.12 1.87-2.38 5.98.48 7.13-.57 1.5-1.31 2.99-2.53 4.09ZM12.03 7.25C11.88 5.02 13.69 3.18 15.77 3c.29 2.58-2.34 4.5-3.74 4.25Z"/></svg>
                <span>Lanjutkan dengan Apple</span>
            </button>
        </div>

        <p class="register-login">Sudah punya akun? <Link :href="route('login')" class="register-link">Masuk di sini</Link></p>
    </GuestLayout>
</template>

<style scoped>
.register-header { margin-bottom:23px; }
.register-eyebrow { margin:0 0 7px; color:var(--accent); font-size:10.5px; font-weight:750; letter-spacing:.13em; text-transform:uppercase; }
.register-header > p:last-child { margin:8px 0 0; color:var(--text-muted); font-size:12px; line-height:1.55; }
.register-form { display:flex; flex-direction:column; gap:12px; }
.register-field { display:flex; flex-direction:column; gap:6px; }
.register-field label { color:var(--text); font-size:11.5px; font-weight:650; }
.register-field label span { color:var(--accent); }
.register-input { position:relative; }
.register-input > svg { position:absolute; z-index:2; top:50%; left:14px; width:16px; height:16px; color:#879984; transform:translateY(-50%); pointer-events:none; }
.register-input :deep(input.form-input) { padding-left:42px!important; }
.register-input :deep(input.form-input.password-input) { padding-right:44px!important; }
.register-input :deep(button) { right:2px; width:42px; justify-content:center; padding:0; border:0; background:transparent; color:var(--text-dim); cursor:pointer; }
.register-input :deep(button svg) { width:17px; height:17px; }
.register-passwords { display:grid; grid-template-columns:1fr 1fr; gap:11px; }
.register-submit { margin-top:7px; }
.register-submit svg { width:16px; height:16px; }
.register-spinner { animation:registerSpin .7s linear infinite; }
.social-divider { display:flex; align-items:center; gap:12px; margin:17px 0; color:var(--text-dim); font-size:10.5px; white-space:nowrap; }
.social-divider::before,.social-divider::after { content:''; flex:1; height:1px; background:var(--border); }
.social-login { display:flex; flex-direction:column; gap:8px; }
.social-button { display:flex; align-items:center; justify-content:center; gap:9px; min-height:42px; padding:0 18px; border:1px solid var(--border-strong); border-radius:10px; background:var(--surface); color:var(--text); opacity:1; cursor:not-allowed; }
.social-button > svg { width:18px; height:18px; flex:none; }
.social-button span { font-size:11.5px; font-weight:650; }
.social-button--apple { border-color:#20231f; background:#20231f; color:#fff; }
.register-login { margin:19px 0 0; padding-top:17px; border-top:1px solid var(--border); color:var(--text-muted); font-size:11.5px; text-align:center; }
.register-link { color:var(--accent); font-weight:650; text-decoration:none; }
.register-link:hover { color:var(--accent-hover); text-decoration:underline; }
.register-link:focus-visible { outline:2px solid var(--accent); outline-offset:3px; border-radius:3px; }
.register-eyebrow { display:inline-flex; padding:5px 8px; border:1px solid #1a3300; border-radius:6px; background:#f6d0ff; color:#1a3300; }
.register-header > p:last-child { color:rgba(26,51,0,.68); font-size:13px; }
.register-field label { color:#1a3300; }
.register-field label span { margin-left:3px; color:#cb5521; }
.register-input > svg { color:#52654d; }
.social-divider { color:#687563; }
.social-divider::before,.social-divider::after { background:#b6b6b6; }
.social-button { border-color:#1a3300; border-radius:8px; background:#fff; color:#1a3300; }
.social-button--apple { border-color:#20231f; background:#20231f; color:#fff; }
.register-login { border-top-color:#b6b6b6; color:#687563; }
.register-link { color:#1a3300; text-decoration:underline; text-decoration-color:#a8e5e5; text-decoration-thickness:3px; text-underline-offset:3px; }
.register-link:hover { color:#cb5521; }
@keyframes registerSpin { to { transform:rotate(360deg); } }
@media(max-width:520px) { .register-header h2 { white-space:nowrap; } .register-header > p:last-child { font-size:clamp(9px,2.8vw,13px); white-space:nowrap; } .register-passwords { grid-template-columns:1fr; gap:12px; } }
@media(prefers-reduced-motion:reduce) { .register-spinner { animation:none; } }
</style>
