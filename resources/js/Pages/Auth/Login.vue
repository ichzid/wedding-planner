<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="flex flex-col gap-3">
            <div>
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />

                <InputError class="mt-1" :message="form.errors.email" />
            </div>

            <div>
                <InputLabel for="password" value="Password" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />

                <InputError class="mt-1" :message="form.errors.password" />
            </div>

            <div class="flex items-center justify-between mt-1">
                <label class="flex items-center cursor-pointer">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-xs" style="color: var(--text-muted)">Ingat saya</span>
                </label>

                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-xs font-medium hover:underline focus:outline-none"
                    style="color: var(--rose)"
                >
                    Lupa password?
                </Link>
            </div>

            <div class="mt-3">
                <button
                    type="submit"
                    class="btn-primary w-full py-2.5 text-sm font-semibold"
                    :class="{ 'opacity-50 cursor-wait': form.processing }"
                    :disabled="form.processing"
                >
                    Masuk
                </button>
            </div>

            <div class="auth-divider !my-3">Atau masuk dengan</div>

            <a :href="route('login')" class="btn-outline py-2 text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="16px" height="16px">
                    <path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24c0,11.045,8.955,20,20,20c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"/>
                    <path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"/>
                    <path fill="#4CAF50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z"/>
                    <path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z"/>
                </svg>
                Google
            </a>
            
            <p class="text-center text-xs mt-3" style="color: var(--text-muted)">
                Belum punya akun? 
                <Link :href="route('register')" class="font-medium hover:underline focus:outline-none" style="color: var(--rose)">
                    Daftar di sini
                </Link>
            </p>
        </form>
    </GuestLayout>
</template>
