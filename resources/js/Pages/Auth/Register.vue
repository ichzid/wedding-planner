<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <div class="mb-6 text-center">
            <h2 class="text-xl font-semibold" style="color: var(--text)">Buat Akun Baru</h2>
            <p class="text-sm mt-1" style="color: var(--text-muted)">Mulai persiapkan hari bahagiamu hari ini</p>
        </div>

        <form @submit.prevent="submit" class="flex flex-col gap-4">
            <div>
                <InputLabel for="name" value="Nama Lengkap" />

                <TextInput
                    id="name"
                    type="text"
                    class="form-input mt-1"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="form-input mt-1"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <InputLabel for="password" value="Password" />

                <TextInput
                    id="password"
                    type="password"
                    class="form-input mt-1"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div>
                <InputLabel
                    for="password_confirmation"
                    value="Konfirmasi Password"
                />

                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="form-input mt-1"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />

                <InputError
                    class="mt-2"
                    :message="form.errors.password_confirmation"
                />
            </div>

            <div class="mt-6">
                <button
                    type="submit"
                    class="btn-primary w-full py-3 text-base font-semibold"
                    :class="{ 'opacity-50 cursor-wait': form.processing }"
                    :disabled="form.processing"
                >
                    Daftar Sekarang
                </button>
            </div>

            <p class="text-center text-sm mt-4" style="color: var(--text-muted)">
                Sudah punya akun? 
                <Link
                    :href="route('login')"
                    class="font-medium hover:underline focus:outline-none"
                    style="color: var(--rose)"
                >
                    Log in di sini
                </Link>
            </p>
        </form>
    </GuestLayout>
</template>
