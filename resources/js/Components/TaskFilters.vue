<script setup lang="ts">
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

interface Project {
    id: number;
    name: string;
    color: string;
}

interface Props {
    projects: Project[];
}

const props = defineProps<Props>();

const filters = computed(() => (usePage().props as any).filters || {
    status: '',
    priority: '',
    project: '',
    due_date: '',
    search: '',
});

const updateFilter = (key: string, value: string) => {
    const url = new URL(window.location.href);
    if (value && value !== '') {
        url.searchParams.set(key, value);
    } else {
        url.searchParams.delete(key);
    }

    // Clear search when changing other filters if needed
    if (key !== 'search') {
        // Keep search as is
    }

    window.location.href = url.toString();
};

const clearAllFilters = () => {
    const url = new URL(window.location.href);
    url.searchParams.delete('status');
    url.searchParams.delete('priority');
    url.searchParams.delete('project');
    url.searchParams.delete('due_date');
    url.searchParams.delete('search');
    window.location.href = url.toString();
};

const hasActiveFilters = computed(() => {
    const f = filters.value;
    return f.status || f.priority || f.project || f.due_date || f.search;
});
</script>

<template>
    <div class="flex flex-wrap gap-2 items-center">
        <!-- Status Filter -->
        <select
            :value="filters.status"
            @change="updateFilter('status', ($event.target as HTMLSelectElement).value)"
            class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm bg-white"
        >
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="completed">Completed</option>
        </select>

        <!-- Priority Filter -->
        <select
            :value="filters.priority"
            @change="updateFilter('priority', ($event.target as HTMLSelectElement).value)"
            class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm bg-white"
        >
            <option value="">All Priorities</option>
            <option value="low">Low</option>
            <option value="medium">Medium</option>
            <option value="high">High</option>
        </select>

        <!-- Project Filter -->
        <select
            :value="filters.project"
            @change="updateFilter('project', ($event.target as HTMLSelectElement).value)"
            class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm bg-white"
        >
            <option value="">All Projects</option>
            <option v-for="project in projects" :key="project.id" :value="project.id">
                {{ project.name }}
            </option>
        </select>

        <!-- Due Date Filter -->
        <select
            :value="filters.due_date"
            @change="updateFilter('due_date', ($event.target as HTMLSelectElement).value)"
            class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm bg-white"
        >
            <option value="">All Due Dates</option>
            <option value="today">Due Today</option>
            <option value="overdue">Overdue</option>
        </select>

        <!-- Clear Filters -->
        <button
            v-if="hasActiveFilters"
            @click="clearAllFilters"
            class="px-3 py-2 text-sm text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
        >
            Clear Filters
        </button>
    </div>
</template>
