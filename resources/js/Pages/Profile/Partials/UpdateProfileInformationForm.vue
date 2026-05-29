<script setup>
import InputError from '@/Components/InputError.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { showToast } from '@/utils.js';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});

const submitProfile = () => {
    form.patch(route('profile.update'), {
        preserveScroll: true,
        onSuccess: () => {
            showToast('Profil akun berhasil diperbarui.');
        },
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="section-title">Profil Akun</h2>
            <p class="mt-1 text-sm text-gray-500">
                Perbarui informasi profil dan alamat email akun Anda.
            </p>
        </header>

        <form
            @submit.prevent="submitProfile"
            class="mt-6 space-y-6"
        >
            <div>
                <label class="form-label">Name</label>

                <input
                    id="name"
                    type="text"
                    class="form-input"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <label class="form-label">Email</label>

                <input
                    id="email"
                    type="email"
                    class="form-input"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="mt-2 text-sm text-gray-800 dark:text-gray-200">
                    Your email address is unverified.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:text-gray-400 dark:hover:text-gray-100 dark:focus:ring-offset-gray-800"
                    >
                        Click here to re-send the verification email.
                    </Link>
                </p>

                <div
                    v-show="status === 'verification-link-sent'"
                    class="mt-2 text-sm font-medium text-green-600 dark:text-green-400"
                >
                    A new verification link has been sent to your email address.
                </div>
            </div>

            <div class="flex items-center gap-4">
                <button type="submit" class="btn btn--primary" :disabled="form.processing">Simpan Perubahan</button>
            </div>
        </form>
    </section>
</template>
