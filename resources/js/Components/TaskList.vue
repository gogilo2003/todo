<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import TaskForm from '@/Components/TaskForm.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

interface Project {
    id: number;
    name: string;
    color: string;
}

interface Task {
    id: number;
    title: string;
    description: string | null;
    project_id: number;
    priority: string;
    due_date: string | null;
    completed: boolean;
    created_at: string;
    project?: Project;
}

interface Props {
    title: string;
    tasks: Task[];
    projects: Project[];
    emptyMessage?: string;
    showProjectColumn?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    emptyMessage: 'No tasks',
    showProjectColumn: true,
});

const emit = defineEmits<{
    (e: 'edit', task: Task): void;
    (e: 'delete', task: Task): void;
}>();

// Toggle task completion
const toggleTask = (task: Task) => {
    const form = useForm({});
    form.patch(`/tasks/${task.id}/toggle`);
};

// Get project color
const getProjectColor = (projectId: number) => {
    const project = props.projects.find(p => p.id === projectId);
    return project?.color || '#6366f1';
};

// Get project name
const getProjectName = (projectId: number) => {
    const project = props.projects.find(p => p.id === projectId);
    return project?.name || 'Unknown';
};

// Priority styles
const getPriorityStyles = (priority: string) => {
    const styles: Record<string, string> = {
        low: 'bg-gray-100 text-gray-600',
        medium: 'bg-yellow-100 text-yellow-700',
        high: 'bg-red-100 text-red-700',
    };
    return styles[priority] || styles.medium;
};

// Format due date
const formatDueDate = (date: string | null) => {
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
    return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
};

// Check if overdue
const isOverdue = (date: string | null) => {
    if (!date) return false;
    const d = new Date(date);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    return d < today;
};

// Check if due today
const isDueToday = (date: string | null) => {
    if (!date) return false;
    const d = new Date(date);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    d.setHours(0, 0, 0, 0);
    return d.getTime() === today.getTime();
};
</script>

<template>
    <div class="bg-white rounded-lg shadow divide-y">
        <!-- Header -->
        <div class="px-4 py-3 border-b border-gray-200">
            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">
                {{ title }} ({{ tasks.length }})
            </h3>
        </div>

        <!-- Task List -->
        <div v-if="tasks.length > 0">
            <div
                v-for="task in tasks"
                :key="task.id"
                class="p-4 hover:bg-gray-50 transition-colors"
            >
                <div class="flex items-start gap-3">
                    <!-- Checkbox -->
                    <button
                        @click="toggleTask(task)"
                        class="mt-0.5 h-5 w-5 rounded border-2 flex items-center justify-center flex-shrink-0 transition-colors"
                        :class="[
                            task.completed
                                ? 'bg-indigo-500 border-indigo-500'
                                : 'border-gray-300 hover:border-indigo-500'
                        ]"
                    >
                        <svg v-if="task.completed" class="h-3 w-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <!-- Task Content -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span
                                :class="{ 'line-through text-gray-400': task.completed }"
                                class="font-medium text-gray-900"
                            >
                                {{ task.title }}
                            </span>
                            <span
                                v-if="task.priority === 'high'"
                                :class="getPriorityStyles(task.priority)"
                                class="px-2 py-0.5 rounded-full text-xs font-medium"
                            >
                                {{ task.priority }}
                            </span>
                        </div>

                        <div v-if="showProjectColumn" class="flex items-center gap-3 mt-1 text-sm text-gray-500">
                            <!-- Project -->
                            <div class="flex items-center gap-1">
                                <div
                                    class="h-2 w-2 rounded-full"
                                    :style="{ backgroundColor: getProjectColor(task.project_id) }"
                                ></div>
                                <span>{{ getProjectName(task.project_id) }}</span>
                            </div>

                            <!-- Due Date -->
                            <div
                                v-if="task.due_date"
                                class="flex items-center gap-1"
                                :class="{
                                    'text-red-600 font-medium': isOverdue(task.due_date) && !task.completed,
                                    'text-indigo-600 font-medium': isDueToday(task.due_date) && !task.completed
                                }"
                            >
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>{{ formatDueDate(task.due_date) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-1">
                        <button
                            @click="emit('edit', task)"
                            class="p-1 text-gray-400 hover:text-gray-600 rounded-md hover:bg-gray-100"
                            title="Edit task"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </button>
                        <button
                            @click="emit('delete', task)"
                            class="p-1 text-gray-400 hover:text-red-600 rounded-md hover:bg-gray-100"
                            title="Delete task"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else class="p-8 text-center text-gray-500">
            {{ emptyMessage }}
        </div>
    </div>
</template>
