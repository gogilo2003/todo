/**
 * Date utility composable for formatting and manipulating dates.
 */

import { computed } from 'vue';

export function useDateUtils() {
    /**
     * Format a date string to a human-readable format.
     */
    const formatDueDate = (date: string | null): string | null => {
        if (!date) return null;

        const d = new Date(date);
        const today = new Date();
        const tomorrow = new Date(today);

        tomorrow.setDate(tomorrow.getDate() + 1);

        if (d.toDateString() === today.toDateString()) {
            return 'Today';
        }
        if (d.toDateString() === tomorrow.toDateString()) {
            return 'Tomorrow';
        }

        return d.toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
        });
    };

    /**
     * Check if a date is overdue.
     */
    const isOverdue = (date: string | null): boolean => {
        if (!date) return false;

        const d = new Date(date);
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        return d < today;
    };

    /**
     * Check if a date is due today.
     */
    const isDueToday = (date: string | null): boolean => {
        if (!date) return false;

        const d = new Date(date);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        d.setHours(0, 0, 0, 0);

        return d.getTime() === today.getTime();
    };

    /**
     * Check if a date is due tomorrow.
     */
    const isDueTomorrow = (date: string | null): boolean => {
        if (!date) return false;

        const d = new Date(date);
        const tomorrow = new Date();
        tomorrow.setHours(0, 0, 0, 0);
        tomorrow.setDate(tomorrow.getDate() + 1);
        d.setHours(0, 0, 0, 0);

        return d.getTime() === tomorrow.getTime();
    };

    /**
     * Get the relative date label (e.g., "Today", "Tomorrow", "Overdue").
     */
    const getRelativeDateLabel = (date: string | null): string | null => {
        if (!date) return null;

        if (isOverdue(date)) return 'Overdue';
        if (isDueToday(date)) return 'Today';
        if (isDueTomorrow(date)) return 'Tomorrow';

        return formatDueDate(date);
    };

    /**
     * Format date for input field (YYYY-MM-DD).
     */
    const formatDateForInput = (date: string | null): string => {
        if (!date) return '';
        return date.split('T')[0];
    };

    /**
     * Check if date needs urgent styling (overdue or due today).
     */
    const needsUrgentStyling = (date: string | null, completed: boolean): boolean => {
        if (completed) return false;
        return isOverdue(date) || isDueToday(date);
    };

    return {
        formatDueDate,
        isOverdue,
        isDueToday,
        isDueTomorrow,
        getRelativeDateLabel,
        formatDateForInput,
        needsUrgentStyling,
    };
}
