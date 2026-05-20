<script setup lang="ts">
import { ref, nextTick } from 'vue';
import { useForm } from '@inertiajs/vue3';

interface Props {
    placeholder?: string;
    autofocus?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    placeholder: 'Add a task... (press Enter)',
    autofocus: true,
});

const emit = defineEmits<{
    (e: 'added'): void;
}>();

const inputRef = ref<HTMLInputElement | null>(null);
const isFocused = ref(false);

const form = useForm({
    title: '',
    project_id: null,
    priority: 'medium',
    completed: false,
});

// Focus input on mount
const focus = () => {
    nextTick(() => {
        inputRef.value?.focus();
    });
};

defineExpose({ focus });

const submit = () => {
    if (!form.title.trim()) return;

    form.post('/tasks', {
        onSuccess: () => {
            form.reset();
            emit('added');
            nextTick(() => {
                inputRef.value?.focus();
            });
        },
    });
};
</script>

<template>
    <div
        class="relative"
        @click="inputRef?.focus()"
    >
        <form @submit.prevent="submit" class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
            </div>
            <input
                ref="inputRef"
                v-model="form.title"
                type="text"
                :placeholder="placeholder"
                class="w-full pl-10 pr-4 py-3 bg-white border-2 rounded-lg focus:outline-none focus:ring-0 transition-colors text-gray-900 placeholder-gray-400"
                :class="[
                    isFocused
                        ? 'border-indigo-500 focus:border-indigo-500'
                        : 'border-gray-200 hover:border-gray-300'
                ]"
                @focus="isFocused = true"
                @blur="isFocused = false"
                :autofocus="autofocus"
            />
            <!-- Loading indicator -->
            <div
                v-if="form.processing"
                class="absolute inset-y-0 right-0 pr-3 flex items-center"
            >
                <svg class="animate-spin h-5 w-5 text-indigo-500" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
        </form>
    </div>
</template>
