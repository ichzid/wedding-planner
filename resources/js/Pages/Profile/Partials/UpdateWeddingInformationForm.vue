<script setup>
import InputError from '@/Components/InputError.vue';
import { useForm } from '@inertiajs/vue3';
import { showToast } from '@/utils.js';

const props = defineProps({
    wedding: {
        type: Object,
        default: () => ({}),
    },
});

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
        onSuccess: () => {
            showToast('Informasi pernikahan berhasil diperbarui.');
        },
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="section-title">Informasi Pernikahan</h2>
            <p class="mt-1 text-sm text-gray-500">
                Perbarui informasi detail pernikahan Anda seperti nama mempelai, tanggal, dan lokasi.
            </p>
        </header>

        <form @submit.prevent="submitWedding" class="mt-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="form-label">Nama Panggilan Mempelai Wanita</label>
                    <input
                        type="text"
                        class="form-input"
                        v-model="form.nama_cpw"
                        required
                    />
                    <InputError class="mt-2" :message="form.errors.nama_cpw" />
                </div>

                <div>
                    <label class="form-label">Nama Panggilan Mempelai Pria</label>
                    <input
                        type="text"
                        class="form-input"
                        v-model="form.nama_cpp"
                        required
                    />
                    <InputError class="mt-2" :message="form.errors.nama_cpp" />
                </div>
            </div>

            <div>
                <label class="form-label">Tanggal Pernikahan</label>
                <input
                    type="date"
                    class="form-input"
                    v-model="form.tanggal_nikah"
                    required
                />
                <InputError class="mt-2" :message="form.errors.tanggal_nikah" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="form-label">Lokasi Akad</label>
                    <input
                        type="text"
                        class="form-input"
                        v-model="form.lokasi_akad"
                    />
                    <InputError class="mt-2" :message="form.errors.lokasi_akad" />
                </div>

                <div>
                    <label class="form-label">Lokasi Resepsi</label>
                    <input
                        type="text"
                        class="form-input"
                        v-model="form.lokasi_resepsi"
                    />
                    <InputError class="mt-2" :message="form.errors.lokasi_resepsi" />
                </div>
            </div>

            <div class="flex items-center gap-4">
                <button type="submit" class="btn btn--primary" :disabled="form.processing">Simpan Perubahan</button>
            </div>
        </form>
    </section>
</template>
