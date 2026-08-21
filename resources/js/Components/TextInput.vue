<script setup>
import { computed, onMounted, ref } from 'vue';
import { Eye, EyeOff } from '@lucide/vue';

const props = defineProps({ type: { type: String, default: 'text' } });
const model = defineModel({ type: String, required: true });
const input = ref(null);
const isPasswordVisible = ref(false);
const inputType = computed(() => props.type === 'password' && isPasswordVisible.value ? 'text' : props.type);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) input.value.focus();
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <div class="relative w-full">
        <input ref="input" v-model="model" v-bind="$attrs" :type="inputType" class="w-full form-input py-1.5" :class="{ 'password-input': props.type === 'password' }" />
        <button
            v-if="props.type === 'password'"
            type="button"
            class="absolute inset-y-0 right-0 flex items-center"
            :aria-label="isPasswordVisible ? 'Sembunyikan password' : 'Tampilkan password'"
            :aria-pressed="isPasswordVisible"
            @click="isPasswordVisible = !isPasswordVisible"
        >
            <EyeOff v-if="isPasswordVisible" aria-hidden="true" />
            <Eye v-else aria-hidden="true" />
        </button>
    </div>
</template>

<style scoped>
.password-input { padding-right:44px; }
button:focus-visible { outline:2px solid var(--accent); outline-offset:-4px; border-radius:8px; }
</style>

<script>
export default { inheritAttrs: false }
</script>
