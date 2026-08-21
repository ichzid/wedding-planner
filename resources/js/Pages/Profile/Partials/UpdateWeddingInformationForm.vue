<script setup>
import InputError from '@/Components/InputError.vue';
import { useForm } from '@inertiajs/vue3';
import { CalendarDays, LoaderCircle, MapPin, Mars, Venus } from '@lucide/vue';
import { showToast } from '@/utils.js';

const props = defineProps({ wedding: { type: Object, default: () => ({}) } });
const form = useForm({
    nama_cpw: props.wedding?.nama_cpw || '',
    nama_cpp: props.wedding?.nama_cpp || '',
    tanggal_nikah: props.wedding?.tanggal_nikah ? props.wedding.tanggal_nikah.split('T')[0] : '',
    lokasi_akad: props.wedding?.lokasi_akad || '',
    lokasi_resepsi: props.wedding?.lokasi_resepsi || '',
});

const submitWedding = () => {
    form.patch(route('profile.wedding.update'), {
        preserveScroll: true,
        onSuccess: () => showToast('Informasi pernikahan berhasil diperbarui.'),
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="section-title">Informasi Pernikahan</h2>
            <p class="section-description">Pastikan nama calon pengantin, tanggal, dan lokasi acara selalu terbaru.</p>
        </header>

        <form @submit.prevent="submitWedding">
            <div class="form-grid">
                <div class="form-field">
                    <label class="form-label" for="nama_cpw">Calon Pengantin Wanita <span aria-hidden="true">*</span></label>
                    <div class="input-shell"><Venus aria-hidden="true" /><input id="nama_cpw" v-model="form.nama_cpw" type="text" class="form-input" required placeholder="Nama panggilan CPW" /></div>
                    <InputError :message="form.errors.nama_cpw" />
                </div>
                <div class="form-field">
                    <label class="form-label" for="nama_cpp">Calon Pengantin Pria <span aria-hidden="true">*</span></label>
                    <div class="input-shell"><Mars aria-hidden="true" /><input id="nama_cpp" v-model="form.nama_cpp" type="text" class="form-input" required placeholder="Nama panggilan CPP" /></div>
                    <InputError :message="form.errors.nama_cpp" />
                </div>
            </div>

            <div class="form-field">
                <label class="form-label" for="tanggal_nikah">Tanggal Pernikahan <span aria-hidden="true">*</span></label>
                <div class="input-shell"><CalendarDays aria-hidden="true" /><input id="tanggal_nikah" v-model="form.tanggal_nikah" type="date" class="form-input" required /></div>
                <InputError :message="form.errors.tanggal_nikah" />
            </div>

            <div class="form-grid">
                <div class="form-field">
                    <label class="form-label" for="lokasi_akad">Lokasi Akad</label>
                    <div class="input-shell"><MapPin aria-hidden="true" /><input id="lokasi_akad" v-model="form.lokasi_akad" type="text" class="form-input" placeholder="Nama atau alamat lokasi" /></div>
                    <InputError :message="form.errors.lokasi_akad" />
                </div>
                <div class="form-field">
                    <label class="form-label" for="lokasi_resepsi">Lokasi Resepsi</label>
                    <div class="input-shell"><MapPin aria-hidden="true" /><input id="lokasi_resepsi" v-model="form.lokasi_resepsi" type="text" class="form-input" placeholder="Nama atau alamat lokasi" /></div>
                    <InputError :message="form.errors.lokasi_resepsi" />
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn--primary" :disabled="form.processing"><LoaderCircle v-if="form.processing" class="spin" aria-hidden="true" />{{ form.processing ? 'Menyimpan...' : 'Simpan Pernikahan' }}</button>
            </div>
        </form>
    </section>
</template>
