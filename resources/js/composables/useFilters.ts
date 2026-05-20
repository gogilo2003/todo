/**
 * Filter utility composable for managing query parameters.
 */

import { ref, watch } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import type { TaskFilters } from '../types';

export function useFilters() {
    const page = usePage();
    const searchQuery = ref('');

    // Initialize search from page props
    const initializeSearch = () => {
        const props = page.props as Record<string, unknown>;
        searchQuery.value = (props.filters as TaskFilters)?.search || '';
    };

    /**
     * Get current filter values from URL.
     */
    const getFilters = (): TaskFilters => {
        const props = page.props as Record<string, unknown>;
        const filters = (props.filters as TaskFilters) || {
            status: '',
            priority: '',
            project: '',
            due_date: '',
            search: '',
        };
        return filters;
    };

    /**
     * Update a single filter and navigate to the new URL.
     */
    const updateFilter = (key: keyof TaskFilters, value: string) => {
        const params: Record<string, string> = {};

        if (value && value !== '') {
            params[key] = value;
        }

        // Preserve other filter values
        const currentFilters = getFilters();
        Object.entries(currentFilters).forEach(([k, v]) => {
            if (k !== key && v) {
                params[k] = v;
            }
        });

        router.get(route().current() || '/', params, { replace: true });
    };

    /**
     * Clear all filters.
     */
    const clearAllFilters = () => {
        router.get(route().current() || '/', {}, { replace: true });
    };

    /**
     * Check if any filters are active.
     */
    const hasActiveFilters = (): boolean => {
        const filters = getFilters();
        return !!(filters.status || filters.priority || filters.project || filters.due_date || filters.search);
    };

    /**
     * Perform debounced search.
     */
    const performSearch = (value: string, delay = 300) => {
        let debounceTimer: ReturnType<typeof setTimeout> | null = null;

        if (debounceTimer) {
            clearTimeout(debounceTimer);
        }

        debounceTimer = setTimeout(() => {
            updateFilter('search', value);
        }, delay);
    };

    /**
     * Update search with debounce.
     */
    const debouncedSearch = (delay = 300) => {
        watch(searchQuery, (value) => {
            performSearch(value, delay);
        });
    };

    return {
        searchQuery,
        getFilters,
        updateFilter,
        clearAllFilters,
        hasActiveFilters,
        performSearch,
        debouncedSearch,
        initializeSearch,
    };
}
