<script setup>
import { ref } from 'vue';
import { useForm, Head } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';

const step = ref(1);

const form = useForm({
    nama_cpw: '',
    nama_cpp: '',
    tanggal_nikah: '',
    setup_type: '', // 'manual' or 'auto'
    budget: '',
});

const nextStep = () => {
    if (step.value === 1) {
        if (!form.nama_cpw || !form.nama_cpp || !form.tanggal_nikah) return;
        step.value = 2;
    }
};

const selectSetup = (type) => {
    form.setup_type = type;
    if (type === 'manual') {
        form.post(route('onboarding.store'));
    } else {
        step.value = 3;
    }
};

const submitAuto = () => {
    form.post(route('onboarding.store'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Setup Pernikahan" />

        <div class="text-center mb-6">
            <h2 class="text-xl font-bold" style="color: var(--text); font-family: var(--font-display);">
                {{ step === 1 ? 'Ceritakan Tentang Pernikahanmu' : (step === 2 ? 'Pilih Cara Memulai' : 'Personalisasi Otomatis') }}
            </h2>
            <p class="text-sm mt-1" style="color: var(--text-muted);">
                {{ step === 1 ? 'Langkah pertama untuk merencanakan hari bahagiamu.' : (step === 2 ? 'Kami bisa membantumu membuat kerangka rencana.' : 'Masukkan estimasi budget untuk dibuatkan rinciannya.') }}
            </p>
        </div>

        <!-- STEP 1: Basic Info -->
        <div v-if="step === 1" class="flex flex-col gap-4">
            <div>
                <InputLabel for="nama_cpw" value="Nama Panggilan Mempelai Wanita" />
                <TextInput id="nama_cpw" type="text" class="form-input mt-1" v-model="form.nama_cpw" required placeholder="Contoh: Triana" />
                <InputError class="mt-2" :message="form.errors.nama_cpw" />
            </div>

            <div>
                <InputLabel for="nama_cpp" value="Nama Panggilan Mempelai Pria" />
                <TextInput id="nama_cpp" type="text" class="form-input mt-1" v-model="form.nama_cpp" required placeholder="Contoh: Ihmal" />
                <InputError class="mt-2" :message="form.errors.nama_cpp" />
            </div>

            <div>
                <InputLabel for="tanggal_nikah" value="Rencana Tanggal Pernikahan" />
                <TextInput id="tanggal_nikah" type="date" class="form-input mt-1" v-model="form.tanggal_nikah" required />
                <InputError class="mt-2" :message="form.errors.tanggal_nikah" />
            </div>

            <button @click="nextStep" type="button" class="btn-primary mt-4" :disabled="!form.nama_cpw || !form.nama_cpp || !form.tanggal_nikah">
                Lanjutkan
            </button>
        </div>

        <!-- STEP 2: Choose Setup Type -->
        <div v-if="step === 2" class="flex flex-col gap-4">
            <button @click="selectSetup('auto')" type="button" class="setup-card">
                <div class="setup-icon"><i class="fa-solid fa-wand-magic-sparkles"></i></div>
                <div class="setup-text">
                    <h3 class="font-bold text-md">Bantu Buatkan Rencana Otomatis</h3>
                    <p class="text-sm text-gray-500 mt-1">Sistem akan membuatkan persentase budget dan checklist secara otomatis. (Rekomendasi)</p>
                </div>
            </button>

            <button @click="selectSetup('manual')" type="button" class="setup-card" :class="{ 'opacity-50': form.processing }">
                <div class="setup-icon bg-gray-100 text-gray-500"><i class="fa-solid fa-pen-ruler"></i></div>
                <div class="setup-text">
                    <h3 class="font-bold text-md">Mulai dari Kertas Kosong</h3>
                    <p class="text-sm text-gray-500 mt-1">Saya sudah punya rencana sendiri dan akan mengisi semuanya dari nol.</p>
                </div>
            </button>
            
            <button @click="step = 1" type="button" class="text-sm text-center mt-2" style="color: var(--text-muted)">Kembali</button>
        </div>

        <!-- STEP 3: Auto Setup Inputs -->
        <div v-if="step === 3" class="flex flex-col gap-4">
            <div class="bg-rose-50 p-4 rounded-lg mb-2 border border-rose-100">
                <p class="text-sm text-rose-800">
                    Masukkan total anggaran pernikahan. Kami akan membaginya ke dalam 7 kategori standar (Katering, Venue, Dekorasi, dll) secara proporsional.
                </p>
            </div>

            <div>
                <InputLabel for="budget" value="Total Estimasi Budget (Rp)" />
                <TextInput id="budget" type="number" class="form-input mt-1" v-model="form.budget" required placeholder="Contoh: 100000000" />
                <InputError class="mt-2" :message="form.errors.budget" />
            </div>

            <button @click="submitAuto" type="button" class="btn-primary mt-4" :disabled="form.processing || !form.budget">
                Selesai & Masuk Dashboard
            </button>
            
            <button @click="step = 2" type="button" class="text-sm text-center mt-2" style="color: var(--text-muted)">Kembali</button>
        </div>
    </GuestLayout>
</template>

<style scoped>
.setup-card {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    padding: 16px;
    border: 2px solid var(--border);
    border-radius: 12px;
    background: white;
    text-align: left;
    transition: all 0.2s;
}
.setup-card:hover {
    border-color: var(--rose);
    background: var(--rose-pale);
    transform: translateY(-2px);
}
.setup-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: var(--rose);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}
</style>
