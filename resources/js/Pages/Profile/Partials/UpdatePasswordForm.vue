<script setup>
import InputError from '@/Components/InputError.vue';
import { useForm } from '@inertiajs/vue3';
import { KeyRound, LoaderCircle, LockKeyhole } from '@lucide/vue';
import { ref } from 'vue';
import { showToast } from '@/utils.js';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);
const form = useForm({ current_password: '', password: '', password_confirmation: '' });

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => { form.reset(); showToast('Password berhasil diubah.'); },
        onError: () => {
            if (form.errors.password) { form.reset('password', 'password_confirmation'); passwordInput.value?.focus(); }
            if (form.errors.current_password) { form.reset('current_password'); currentPasswordInput.value?.focus(); }
        },
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="section-title">Keamanan Akun</h2>
            <p class="section-description">Gunakan password yang kuat dan berbeda dari akun lainnya.</p>
        </header>

        <form @submit.prevent="updatePassword">
            <div class="form-field">
                <label class="form-label" for="current_password">Password Saat Ini <span aria-hidden="true">*</span></label>
                <div class="input-shell"><KeyRound aria-hidden="true" /><input id="current_password" ref="currentPasswordInput" v-model="form.current_password" type="password" class="form-input" required autocomplete="current-password" placeholder="Masukkan password saat ini" /></div>
                <InputError :message="form.errors.current_password" />
            </div>
            <div class="form-field">
                <label class="form-label" for="password">Password Baru <span aria-hidden="true">*</span></label>
                <div class="input-shell"><LockKeyhole aria-hidden="true" /><input id="password" ref="passwordInput" v-model="form.password" type="password" class="form-input" required autocomplete="new-password" minlength="8" placeholder="Minimal 8 karakter" /></div>
                <InputError :message="form.errors.password" />
            </div>
            <div class="form-field">
                <label class="form-label" for="password_confirmation">Konfirmasi Password <span aria-hidden="true">*</span></label>
                <div class="input-shell"><LockKeyhole aria-hidden="true" /><input id="password_confirmation" v-model="form.password_confirmation" type="password" class="form-input" required autocomplete="new-password" minlength="8" placeholder="Ulangi password baru" /></div>
                <InputError :message="form.errors.password_confirmation" />
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn--primary" :disabled="form.processing"><LoaderCircle v-if="form.processing" class="spin" aria-hidden="true" />{{ form.processing ? 'Menyimpan...' : 'Perbarui Password' }}</button>
            </div>
        </form>
    </section>
</template>
