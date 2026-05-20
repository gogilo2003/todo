/**
 * Task utility composable for task-related operations.
 */

import { computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import type { Task } from '../types';

export function useTasks() {
    /**
     * Toggle task completion status.
     */
    const toggleTask = (task: Task) => {
        const form = useForm({});
        form.patch(`/tasks/${task.id}/toggle`);
    };

    /**
     * Delete a task.
     */
    const deleteTask = (task: Task) => {
        const form = useForm({});
        form.delete(`/tasks/${task.id}`);
    };

    /**
     * Filter incomplete tasks.
     */
    const incompleteTasks = (tasks: Task[]) => {
        return tasks.filter(t => !t.completed);
    };

    /**
     * Filter completed tasks.
     */
    const completedTasks = (tasks: Task[]) => {
        return tasks.filter(t => t.completed);
    };

    /**
     * Get overdue tasks.
     */
    const overdueTasks = (tasks: Task[]) => {
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        return tasks.filter(t => {
            if (t.completed || !t.due_date) return false;
            const dueDate = new Date(t.due_date);
            return dueDate < today;
        });
    };

    /**
     * Get tasks due today.
     */
    const dueTodayTasks = (tasks: Task[]) => {
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        return tasks.filter(t => {
            if (t.completed || !t.due_date) return false;
            const dueDate = new Date(t.due_date);
            dueDate.setHours(0, 0, 0, 0);
            return dueDate.getTime() === today.getTime();
        });
    };

    /**
     * Get high priority tasks.
     */
    const highPriorityTasks = (tasks: Task[]) => {
        return tasks.filter(t => t.priority === 'high' && !t.completed);
    };

    /**
     * Sort tasks by due date.
     */
    const sortByDueDate = (tasks: Task[], ascending = true) => {
        return [...tasks].sort((a, b) => {
            if (!a.due_date && !b.due_date) return 0;
            if (!a.due_date) return 1;
            if (!b.due_date) return -1;

            const dateA = new Date(a.due_date).getTime();
            const dateB = new Date(b.due_date).getTime();

            return ascending ? dateA - dateB : dateB - dateA;
        });
    };

    /**
     * Sort tasks by priority.
     */
    const sortByPriority = (tasks: Task[], descending = true) => {
        const priorityOrder = { high: 3, medium: 2, low: 1 };

        return [...tasks].sort((a, b) => {
            const orderA = priorityOrder[a.priority as keyof typeof priorityOrder] || 2;
            const orderB = priorityOrder[b.priority as keyof typeof priorityOrder] || 2;

            return descending ? orderB - orderA : orderA - orderB;
        });
    };

    /**
     * Count tasks by status.
     */
    const countByStatus = (tasks: Task[]) => {
        return {
            total: tasks.length,
            completed: tasks.filter(t => t.completed).length,
            pending: tasks.filter(t => !t.completed).length,
        };
    };

    return {
        toggleTask,
        deleteTask,
        incompleteTasks,
        completedTasks,
        overdueTasks,
        dueTodayTasks,
        highPriorityTasks,
        sortByDueDate,
        sortByPriority,
        countByStatus,
    };
}
