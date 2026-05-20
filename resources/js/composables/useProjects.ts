/**
 * Project utility composable for project-related operations.
 */

import { computed } from 'vue';
import type { Project } from '../types';
import { DEFAULT_PROJECT_COLOR } from '../types';

export function useProjects(projects: Project[]) {
    /**
     * Get project by ID.
     */
    const getProjectById = (id: number | null | undefined): Project | undefined => {
        if (!id) return undefined;
        return projects.find(p => p.id === id);
    };

    /**
     * Get project color by ID.
     */
    const getProjectColor = (id: number | null | undefined): string => {
        const project = getProjectById(id);
        return project?.color || DEFAULT_PROJECT_COLOR;
    };

    /**
     * Get project name by ID.
     */
    const getProjectName = (id: number | null | undefined): string => {
        const project = getProjectById(id);
        return project?.name || 'Unknown';
    };

    /**
     * Get active projects (that can have tasks).
     */
    const activeProjects = computed(() => {
        return projects.filter(p => p.id && p.id > 0);
    });

    /**
     * Check if user has any projects.
     */
    const hasProjects = computed(() => projects.length > 0);

    /**
     * Get projects sorted by name.
     */
    const sortedProjects = computed(() => {
        return [...projects].sort((a, b) => a.name.localeCompare(b.name));
    });

    return {
        getProjectById,
        getProjectColor,
        getProjectName,
        activeProjects,
        hasProjects,
        sortedProjects,
    };
}
