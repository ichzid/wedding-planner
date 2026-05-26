<script setup>
import { ref, computed } from 'vue';
import { useForm, Head } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';

const step = ref(1);
const transitionName = ref('slide-left');

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
        transitionName.value = 'slide-left';
        step.value = 2;
    }
};

const prevStep = () => {
    transitionName.value = 'slide-right';
    if (step.value === 3) step.value = 2;
    else if (step.value === 2) step.value = 1;
};

const selectSetup = (type) => {
    form.setup_type = type;
    if (type === 'manual') {
        form.post(route('onboarding.store'));
    } else {
        transitionName.value = 'slide-left';
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

        <div class="text-center mb-6 relative">
            <div class="step-indicator mb-3">
                <span class="step-dot" :class="{'active': step >= 1}"></span>
                <span class="step-line" :class="{'active': step >= 2}"></span>
                <span class="step-dot" :class="{'active': step >= 2}"></span>
                <span class="step-line" :class="{'active': step === 3}"></span>
                <span class="step-dot" :class="{'active': step === 3}"></span>
            </div>
            
            <transition name="fade" mode="out-in">
                <div :key="step">
                    <h2 class="text-xl font-bold" style="color: var(--text); font-family: var(--font-display);">
                        {{ step === 1 ? 'Ceritakan Tentang Pernikahanmu' : (step === 2 ? 'Pilih Cara Memulai' : 'Personalisasi Otomatis') }}
                    </h2>
                    <p class="text-sm mt-1.5" style="color: var(--text-muted);">
                        {{ step === 1 ? 'Langkah pertama untuk merencanakan hari bahagiamu.' : (step === 2 ? 'Kami bisa membantumu membuat kerangka rencana.' : 'Masukkan estimasi budget untuk dibuatkan rinciannya.') }}
                    </p>
                </div>
            </transition>
        </div>

        <div class="relative overflow-hidden min-h-[300px]">
            <transition :name="transitionName">
                <!-- STEP 1: Basic Info -->
                <div v-if="step === 1" class="absolute w-full flex flex-col gap-4">
                    <div>
                        <InputLabel for="nama_cpw" value="Nama Panggilan Mempelai Wanita" />
                        <TextInput id="nama_cpw" type="text" class="mt-1" v-model="form.nama_cpw" required placeholder="Contoh: Triana" />
                        <InputError class="mt-1" :message="form.errors.nama_cpw" />
                    </div>

                    <div>
                        <InputLabel for="nama_cpp" value="Nama Panggilan Mempelai Pria" />
                        <TextInput id="nama_cpp" type="text" class="mt-1" v-model="form.nama_cpp" required placeholder="Contoh: Ihmal" />
                        <InputError class="mt-1" :message="form.errors.nama_cpp" />
                    </div>

                    <div>
                        <InputLabel for="tanggal_nikah" value="Rencana Tanggal Pernikahan" />
                        <TextInput id="tanggal_nikah" type="date" class="mt-1" v-model="form.tanggal_nikah" required />
                        <InputError class="mt-1" :message="form.errors.tanggal_nikah" />
                    </div>

                    <button @click="nextStep" type="button" class="btn-primary w-full py-3 mt-4 text-sm font-semibold" :class="{'opacity-50': !form.nama_cpw || !form.nama_cpp || !form.tanggal_nikah}" :disabled="!form.nama_cpw || !form.nama_cpp || !form.tanggal_nikah">
                        Lanjutkan <i class="fa-solid fa-arrow-right ml-2 text-xs"></i>
                    </button>
                </div>

                <!-- STEP 2: Choose Setup Type -->
                <div v-else-if="step === 2" class="absolute w-full flex flex-col gap-4">
                    <button @click="selectSetup('auto')" type="button" class="setup-card relative overflow-hidden group">
                        <div class="absolute top-0 right-0 bg-[var(--rose)] text-white text-[10px] font-bold px-2 py-1 rounded-bl-lg">REKOMENDASI</div>
                        <div class="setup-icon group-hover:scale-110 transition-transform duration-300"><i class="fa-solid fa-wand-magic-sparkles"></i></div>
                        <div class="setup-text">
                            <h3 class="font-bold text-base text-[var(--text)]">Bantu Buatkan Otomatis</h3>
                            <p class="text-xs text-[var(--text-muted)] mt-1.5 leading-relaxed">Sistem akan memecah budget dan membuat checklist otomatis berdasarkan tanggal.</p>
                        </div>
                    </button>

                    <button @click="selectSetup('manual')" type="button" class="setup-card" :class="{ 'opacity-50 cursor-wait': form.processing }">
                        <div class="setup-icon !bg-gray-100 !text-gray-400"><i class="fa-solid fa-pen-ruler"></i></div>
                        <div class="setup-text">
                            <h3 class="font-bold text-base text-[var(--text)]">Mulai dari Kertas Kosong</h3>
                            <p class="text-xs text-[var(--text-muted)] mt-1.5 leading-relaxed">Pilih ini jika kamu sudah memiliki rencana sendiri dan ingin mengisi semuanya dari nol.</p>
                        </div>
                    </button>
                    
                    <button @click="prevStep" type="button" class="btn-back mt-2">
                        <i class="fa-solid fa-arrow-left mr-2"></i> Kembali
                    </button>
                </div>

                <!-- STEP 3: Auto Setup Inputs -->
                <div v-else-if="step === 3" class="absolute w-full flex flex-col gap-4">
                    <div class="info-alert mb-2">
                        <i class="fa-solid fa-circle-info mt-0.5"></i>
                        <p class="text-xs leading-relaxed">
                            Masukkan total anggaran pernikahan. Sistem akan membaginya ke 7 kategori standar (Katering, Venue, dll) secara proporsional.
                        </p>
                    </div>

                    <div>
                        <InputLabel for="budget" value="Total Estimasi Budget (Rp)" />
                        <TextInput id="budget" type="number" class="mt-1 text-lg font-semibold" v-model="form.budget" required placeholder="Contoh: 100000000" />
                        <InputError class="mt-1" :message="form.errors.budget" />
                    </div>

                    <button @click="submitAuto" type="button" class="btn-primary w-full py-3 mt-4 text-sm font-semibold" :class="{ 'opacity-50 cursor-wait': form.processing || !form.budget }" :disabled="form.processing || !form.budget">
                        Selesai & Masuk Dashboard <i class="fa-solid fa-check ml-2"></i>
                    </button>
                    
                    <button @click="prevStep" type="button" class="btn-back mt-2" :disabled="form.processing">
                        <i class="fa-solid fa-arrow-left mr-2"></i> Kembali
                    </button>
                </div>
            </transition>
        </div>
    </GuestLayout>
</template>

<style scoped>
/* Step Indicator */
.step-indicator { display: flex; align-items: center; justify-content: center; gap: 8px; }
.step-dot { width: 10px; height: 10px; border-radius: 50%; background: var(--border); transition: all 0.4s ease; }
.step-dot.active { background: var(--rose); transform: scale(1.2); }
.step-line { height: 2px; width: 30px; background: var(--border); transition: all 0.4s ease; }
.step-line.active { background: var(--rose); }

/* Setup Cards */
.setup-card { display: flex; align-items: flex-start; gap: 16px; padding: 20px; border: 2px solid var(--border); border-radius: 12px; background: white; text-align: left; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
.setup-card:hover { border-color: var(--rose); background: var(--rose-pale); transform: translateY(-3px); box-shadow: 0 10px 25px -5px rgba(196, 149, 106, 0.15); }
.setup-icon { width: 50px; height: 50px; border-radius: 12px; background: var(--rose); color: white; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; box-shadow: 0 4px 10px rgba(196, 149, 106, 0.2); }

/* Info Alert */
.info-alert { display: flex; align-items: flex-start; gap: 12px; background: rgba(196, 149, 106, 0.1); border-left: 4px solid var(--rose); padding: 12px 16px; border-radius: 0 8px 8px 0; color: var(--text); }
.info-alert i { color: var(--rose); font-size: 16px; }

/* Buttons */
.btn-back { display: inline-flex; align-items: center; justify-content: center; width: 100%; padding: 10px; color: var(--text-muted); font-size: 13px; font-weight: 500; transition: all 0.2s; border-radius: 8px; }
.btn-back:hover { color: var(--text); background: var(--bg); }

/* Transitions */
.slide-left-enter-active, .slide-left-leave-active,
.slide-right-enter-active, .slide-right-leave-active { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
.slide-left-enter-from { opacity: 0; transform: translateX(30px); }
.slide-left-leave-to { opacity: 0; transform: translateX(-30px); }
.slide-right-enter-from { opacity: 0; transform: translateX(-30px); }
.slide-right-leave-to { opacity: 0; transform: translateX(30px); }

.fade-enter-active, .fade-leave-active { transition: opacity 0.3s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
