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
};

const handleStep2Next = () => {
    if (!form.setup_type) return;

    transitionName.value = 'slide-left';
    step.value = 3;
};

const goToStep = (targetStep) => {
    if (form.processing) return;
    
    // Only allow going back or to next step if current is valid
    if (targetStep < step.value) {
        transitionName.value = 'slide-right';
        step.value = targetStep;
    } else if (targetStep === 2 && step.value === 1 && form.nama_cpw && form.nama_cpp && form.tanggal_nikah) {
        transitionName.value = 'slide-left';
        step.value = targetStep;
    } else if (targetStep === 3 && step.value === 2 && form.setup_type) {
        transitionName.value = 'slide-left';
        step.value = targetStep;
    }
};

const formatBudgetInput = (e) => {
    // Remove all non-numeric characters
    let val = e.target.value.replace(/\D/g, '');
    
    // Update the v-model directly with unformatted number for submission
    form.budget = val;
    
    // Format display value with dots
    e.target.value = val ? Number(val).toLocaleString('id-ID') : '';
};

const submitAuto = () => {
    form.post(route('onboarding.store'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Setup Pernikahan" />

        <div class="text-center mb-6 relative">
            <div class="step-indicator mb-4">
                <span class="step-dot" :class="{'active': step >= 1, 'cursor-pointer': true}" @click="goToStep(1)"></span>
                <span class="step-line" :class="{'active': step >= 2}"></span>
                <span class="step-dot" :class="{'active': step >= 2, 'cursor-pointer': step >= 1 && form.nama_cpw && form.nama_cpp && form.tanggal_nikah}" @click="goToStep(2)"></span>
                <span class="step-line" :class="{'active': step === 3}"></span>
                <span class="step-dot" :class="{'active': step === 3, 'cursor-pointer': step >= 2 && form.setup_type}" @click="goToStep(3)"></span>
            </div>
            
            <transition name="fade" mode="out-in">
                <div :key="step">
                    <h2 class="text-xl font-bold" style="color: var(--text); font-family: var(--font-display);">
                        {{ step === 1 ? 'Ceritakan Tentang Pernikahanmu' : (step === 2 ? 'Pilih Cara Memulai' : (form.setup_type === 'auto' ? 'Personalisasi Otomatis' : 'Konfirmasi Manual')) }}
                    </h2>
                    <p class="text-sm mt-1" style="color: var(--text-muted);">
                        {{ step === 1 ? 'Langkah pertama untuk merencanakan hari bahagiamu.' : (step === 2 ? 'Kami bisa membantumu membuat kerangka rencana.' : (form.setup_type === 'auto' ? 'Masukkan estimasi budget untuk dibuatkan rinciannya.' : 'Kamu siap merencanakan semuanya dari nol.')) }}
                    </p>
                </div>
            </transition>
        </div>

        <div class="relative overflow-visible" :style="{ height: step === 1 ? '340px' : (step === 2 ? '360px' : '310px'), transition: 'height 0.4s ease' }">
            <transition :name="transitionName">
                <!-- STEP 1: Basic Info -->
                <div v-if="step === 1" class="absolute w-full top-0 flex flex-col gap-4">
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
                    
                    <a :href="route('logout')" @click.prevent="form.post(route('logout'))" class="btn-back mt-1 text-red-500 hover:text-red-700 hover:bg-red-50">
                        <i class="fa-solid fa-arrow-right-from-bracket mr-2"></i> Batalkan & Keluar
                    </a>
                </div>

                <!-- STEP 2: Choose Setup Type -->
                <div v-else-if="step === 2" class="absolute w-full top-0 flex flex-col gap-4">
                    <button @click="selectSetup('auto')" type="button" class="setup-card relative overflow-hidden group" :class="{ 'selected': form.setup_type === 'auto' }">
                        <div class="absolute top-0 right-0 bg-[var(--rose)] text-white text-[10px] font-bold px-2 py-1 rounded-bl-lg">REKOMENDASI</div>
                        <div class="setup-icon group-hover:scale-110 transition-transform duration-300"><i class="fa-solid fa-wand-magic-sparkles"></i></div>
                        <div class="setup-text">
                            <h3 class="font-bold text-base text-[var(--text)]">Bantu Buatkan Otomatis</h3>
                            <p class="text-xs text-[var(--text-muted)] mt-1.5 leading-relaxed">Sistem akan memecah budget dan membuat checklist otomatis berdasarkan tanggal.</p>
                        </div>
                    </button>

                    <button @click="selectSetup('manual')" type="button" class="setup-card" :class="{ 'selected': form.setup_type === 'manual' }">
                        <div class="setup-icon !bg-gray-100 !text-gray-400" :class="{ '!bg-[var(--rose)] !text-white': form.setup_type === 'manual' }"><i class="fa-solid fa-pen-ruler"></i></div>
                        <div class="setup-text">
                            <h3 class="font-bold text-base text-[var(--text)]">Mulai dari Kertas Kosong</h3>
                            <p class="text-xs text-[var(--text-muted)] mt-1.5 leading-relaxed">Pilih ini jika kamu sudah memiliki rencana sendiri dan ingin mengisi semuanya dari nol.</p>
                        </div>
                    </button>
                    
                    <button @click="handleStep2Next" type="button" class="btn-primary w-full py-3 mt-2 text-sm font-semibold" :class="{'opacity-50': !form.setup_type || form.processing}" :disabled="!form.setup_type || form.processing">
                        Lanjutkan <i class="fa-solid fa-arrow-right ml-2 text-xs"></i>
                    </button>

                    <button @click="prevStep" type="button" class="btn-back mt-1">
                        <i class="fa-solid fa-arrow-left mr-2"></i> Kembali
                    </button>
                </div>

                <!-- STEP 3: Auto Setup Inputs -->
                <div v-else-if="step === 3" class="absolute w-full top-0 flex flex-col gap-4">
                    <div v-if="form.setup_type === 'auto'">
                        <InputLabel for="budget" value="Total Estimasi Budget" />
                        <div class="relative mt-1">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 font-medium">Rp</span>
                            </div>
                            <TextInput 
                                id="budget" 
                                type="text" 
                                class="pl-10 !text-lg !font-bold" 
                                v-model="form.budget" 
                                @input="formatBudgetInput"
                                required 
                                placeholder="0" 
                            />
                        </div>
                        <InputError class="mt-1" :message="form.errors.budget" />
                        
                        <div class="bg-[var(--surface)] border border-[var(--border)] rounded-lg p-4 mt-4 mb-2 text-left">
                            <h4 class="text-xs font-bold text-[var(--text)] mb-2 uppercase tracking-wider">Yang akan dibuat:</h4>
                            <ul class="text-sm text-[var(--text-muted)] space-y-2">
                                <li><i class="fa-solid fa-check text-[var(--rose)] mr-2"></i> Timeline persiapan dari hari ini s/d hari-H</li>
                                <li><i class="fa-solid fa-check text-[var(--rose)] mr-2"></i> Pembagian persentase budget ideal (Katering, Dekorasi, dll)</li>
                                <li><i class="fa-solid fa-check text-[var(--rose)] mr-2"></i> Template seserahan & dokumen KUA</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div v-else class="bg-[var(--surface)] border border-[var(--border)] rounded-lg p-6 mt-2 mb-2 text-center">
                        <div class="w-16 h-16 rounded-full bg-[var(--rose-pale)] text-[var(--rose)] flex items-center justify-center mx-auto mb-4 text-2xl">
                            <i class="fa-solid fa-rocket"></i>
                        </div>
                        <h4 class="font-bold text-[var(--text)] mb-2">Persiapan Kustom</h4>
                        <p class="text-sm text-[var(--text-muted)] leading-relaxed">
                            Aplikasi siap digunakan. Anda bisa menambahkan timeline, menyusun rincian budget, dan mengelola tamu undangan secara bertahap nanti.
                        </p>
                    </div>

                    <button @click="submitAuto" type="button" class="btn-primary w-full py-3 mt-2 text-sm font-semibold" :class="{'opacity-50 cursor-wait': form.processing || (form.setup_type === 'auto' && !form.budget)}" :disabled="form.processing || (form.setup_type === 'auto' && !form.budget)">
                        Selesai & Masuk Dashboard <i class="fa-solid fa-check ml-2 text-xs"></i>
                    </button>
                    
                    <button @click="prevStep" type="button" class="btn-back mt-1" :disabled="form.processing">
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
.setup-card:hover { border-color: rgba(196, 149, 106, 0.5); transform: translateY(-2px); }
.setup-card.selected { border-color: var(--rose); background: var(--rose-pale); box-shadow: 0 10px 25px -5px rgba(196, 149, 106, 0.15); }
.setup-icon { width: 50px; height: 50px; border-radius: 12px; background: var(--rose); color: white; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; box-shadow: 0 4px 10px rgba(196, 149, 106, 0.2); transition: all 0.3s; }

/* Info Alert */
.info-alert { display: flex; align-items: flex-start; gap: 12px; background: rgba(196, 149, 106, 0.1); border-left: 4px solid var(--rose); padding: 12px 16px; border-radius: 0 8px 8px 0; color: var(--text); }
.info-alert i { color: var(--rose); font-size: 16px; }

/* Buttons */
.btn-back { display: inline-flex; align-items: center; justify-content: center; width: 100%; padding: 10px; color: var(--text-muted); font-size: 13px; font-weight: 500; transition: all 0.2s; border-radius: 8px; }
.btn-back:hover { color: var(--text); background: var(--bg); }

/* Transitions */
.slide-left-enter-active, .slide-left-leave-active,
.slide-right-enter-active, .slide-right-leave-active { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }

.slide-left-enter-from { opacity: 0; transform: translateX(40px); }
.slide-left-leave-to { opacity: 0; transform: translateX(-40px); }
.slide-right-enter-from { opacity: 0; transform: translateX(-40px); }
.slide-right-leave-to { opacity: 0; transform: translateX(40px); }

/* Make sure absolute positioning doesn't ruin the flow */
.absolute { position: absolute; left: 0; right: 0; }

.fade-enter-active, .fade-leave-active { transition: opacity 0.3s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
