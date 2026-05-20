<script setup lang="ts">
import { ref, watch, onUnmounted } from 'vue';
import { usePage } from '@inertiajs/vue3';

interface Props {
    placeholder?: string;
    delay?: number;
}

const props = withDefaults(defineProps<Props>(), {
    placeholder: 'Search...',
    delay: 300,
});

const searchQuery = ref((usePage().props as any).filters?.search || '');
let debounceTimer: ReturnType<typeof setTimeout> | null = null;

// Custom debounced search
const performSearch = (value: string) => {
    if (debounceTimer) {
        clearTimeout(debounceTimer);
    }

    debounceTimer = setTimeout(() => {
        const url = new URL(window.location.href);
        if (value) {
            url.searchParams.set('search', value);
        } else {
            url.searchParams.delete('search');
        }

        // Preserve other query parameters
        ['status', 'priority', 'project', 'due_date'].forEach(param => {
            const paramValue = new URL(window.location.href).searchParams.get(param);
            if (paramValue && param !== 'search') {
                url.searchParams.set(param, paramValue);
            }
        });

        window.location.href = url.toString();
    }, props.delay);
};

watch(searchQuery, (value) => {
    performSearch(value);
});

onUnmounted(() => {
    if (debounceTimer) {
        clearTimeout(debounceTimer);
    }
});

const clearSearch = () => {
    searchQuery.value = '';
    const url = new URL(window.location.href);
    url.searchParams.delete('search');
    window.location.href = url.toString();
};
</script>

<template>
    <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>
        <input
            v-model="searchQuery"
            type="text"
            :placeholder="placeholder"
            class="pl-10 pr-8 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm w-64"
        />
        <button
            v-if="searchQuery"
            @click="clearSearch"
            class="absolute inset-y-0 right-0 pr-2 flex items-center text-gray-400 hover:text-gray-600"
        >
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</template>
