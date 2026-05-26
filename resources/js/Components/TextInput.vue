<script setup>
import { onMounted, ref, computed } from 'vue';

const props = defineProps({
    type: {
        type: String,
        default: 'text',
    }
});

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);
const isPasswordVisible = ref(false);

const inputType = computed(() => {
    if (props.type === 'password') {
        return isPasswordVisible.value ? 'text' : 'password';
    }
    return props.type;
});

const togglePasswordVisibility = () => {
    isPasswordVisible.value = !isPasswordVisible.value;
};

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <div class="relative w-full">
        <input
            :type="inputType"
            class="w-full form-input py-1.5"
            v-model="model"
            ref="input"
            v-bind="$attrs"
        />
        <button
            v-if="props.type === 'password'"
            type="button"
            class="absolute inset-y-0 right-0 flex items-center pr-3 focus:outline-none"
            style="color: var(--text-dim);"
            @click="togglePasswordVisibility"
            tabindex="-1"
        >
            <i class="fa-solid" :class="isPasswordVisible ? 'fa-eye-slash' : 'fa-eye'"></i>
        </button>
    </div>
</template>

<script>
export default {
    inheritAttrs: false
}
</script>
