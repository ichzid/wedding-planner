<script setup>
import { computed, ref } from 'vue';
import InputError from '@/Components/InputError.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { LoaderCircle, Mail, MapPin, Phone, UserRound } from '@lucide/vue';
import { showToast } from '@/utils.js';

defineProps({ mustVerifyEmail: Boolean, status: String });
const user = usePage().props.auth.user;
const avatarFailed = ref(false);
const userInitials = computed(() => {
    const nameParts = user.name?.trim().split(/\s+/).filter(Boolean) || [];

    if (!nameParts.length) return 'U';

    return [nameParts[0], nameParts.at(-1)]
        .slice(0, nameParts.length > 1 ? 2 : 1)
        .map((part) => part.charAt(0))
        .join('')
        .toUpperCase();
});
const form = useForm({
    name: user.name,
    email: user.email,
    phone: user.phone ?? '',
    address: user.address ?? '',
});

const submitProfile = () => {
    form.patch(route('profile.update'), {
        preserveScroll: true,
        onSuccess: () => showToast('Profil akun berhasil diperbarui.'),
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="section-title">Profil Akun</h2>
            <p class="section-description">Perbarui identitas dan informasi kontak yang digunakan pada akun Anda.</p>
        </header>

        <div class="account-avatar">
            <img
                v-if="user.avatar && !avatarFailed"
                :src="user.avatar"
                :alt="`Foto profil ${user.name}`"
                class="account-avatar__image"
                @error="avatarFailed = true"
            />
            <span v-else class="account-avatar__fallback" aria-hidden="true">{{ userInitials }}</span>
            <div>
                <strong>Foto Profil</strong>
                <p>{{ user.avatar ? 'Foto dari akun Google Anda.' : 'Avatar inisial dibuat otomatis dari nama Anda.' }}</p>
            </div>
        </div>

        <form @submit.prevent="submitProfile">
            <div class="form-field">
                <label class="form-label" for="name">Nama Lengkap <span aria-hidden="true">*</span></label>
                <div class="input-shell">
                    <UserRound aria-hidden="true" />
                    <input id="name" v-model="form.name" type="text" class="form-input" required autofocus autocomplete="name" placeholder="Masukkan nama lengkap" />
                </div>
                <InputError :message="form.errors.name" />
            </div>

            <div class="form-field">
                <label class="form-label" for="email">Alamat Email <span aria-hidden="true">*</span></label>
                <div class="input-shell">
                    <Mail aria-hidden="true" />
                    <input id="email" v-model="form.email" type="email" class="form-input" required autocomplete="username" placeholder="nama@email.com" />
                </div>
                <InputError :message="form.errors.email" />
            </div>

            <div class="form-field">
                <label class="form-label" for="phone">Nomor Telepon</label>
                <div class="input-shell">
                    <Phone aria-hidden="true" />
                    <input id="phone" v-model="form.phone" type="tel" class="form-input" autocomplete="tel" maxlength="20" placeholder="Contoh: 0812 3456 7890" />
                </div>
                <InputError :message="form.errors.phone" />
            </div>

            <div class="form-field">
                <label class="form-label" for="address">Alamat Domisili</label>
                <div class="input-shell">
                    <MapPin aria-hidden="true" />
                    <input id="address" v-model="form.address" type="text" class="form-input" autocomplete="street-address" maxlength="1000" placeholder="Masukkan alamat domisili" />
                </div>
                <InputError :message="form.errors.address" />
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null" class="verification-note">
                Alamat email Anda belum diverifikasi.
                <Link :href="route('verification.send')" method="post" as="button" class="verification-link">Kirim ulang email verifikasi</Link>.
                <p v-show="status === 'verification-link-sent'" class="verification-success">Tautan verifikasi baru telah dikirim ke alamat email Anda.</p>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn--primary" :disabled="form.processing">
                    <LoaderCircle v-if="form.processing" class="spin" aria-hidden="true" />
                    {{ form.processing ? 'Menyimpan...' : 'Simpan Profil' }}
                </button>
            </div>
        </form>
    </section>
</template>

<style scoped>
.account-avatar {
    display: flex;
    margin-top: 24px;
    padding: 14px;
    align-items: center;
    gap: 14px;
    border: 1px solid #d4ccd4;
    border-radius: 12px;
    background: #fffafd;
}

.account-avatar__image,
.account-avatar__fallback {
    width: 58px;
    height: 58px;
    flex: none;
    border: 1px solid #6d526d;
    border-radius: 50%;
}

.account-avatar__image {
    object-fit: cover;
}

.account-avatar__fallback {
    display: grid;
    background: #ffbdd3;
    color: #240029;
    font-size: 18px;
    font-weight: 800;
    place-items: center;
}

.account-avatar strong {
    display: block;
    color: #240029;
    font-size: 14px;
}

.account-avatar p {
    margin: 4px 0 0;
    color: #6d526d;
    font-size: 12px;
    line-height: 1.5;
}
</style>
