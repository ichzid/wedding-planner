<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import UpdateWeddingInformationForm from './Partials/UpdateWeddingInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Pengaturan Akun" />

    <AppLayout>
        <div class="page-header">
            <h1 class="page-title">Pengaturan Akun</h1>
            <p class="page-sub">Kelola informasi profil, password, dan detail pernikahan Anda.</p>
        </div>

        <div class="space-y-6">
            <div class="card p-6">
                <UpdateProfileInformationForm
                    :must-verify-email="mustVerifyEmail"
                    :status="status"
                    class="max-w-xl"
                />
            </div>

            <div class="card p-6">
                <UpdateWeddingInformationForm 
                    :wedding="$page.props.wedding"
                    class="max-w-xl" 
                />
            </div>

            <div class="card p-6">
                <UpdatePasswordForm class="max-w-xl" />
            </div>
            
            <!-- We hide DeleteUserForm or you can uncomment if needed
            <div class="card p-6">
                <DeleteUserForm class="max-w-xl" />
            </div>
            -->
        </div>
    </AppLayout>
</template>

<style scoped>
.space-y-6 > * + * {
    margin-top: 1.5rem;
}
.p-6 {
    padding: 1.5rem;
}
.max-w-xl {
    max-width: 36rem;
}
</style>
