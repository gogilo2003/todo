<script setup lang="ts">
import { ref, computed } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import TodoLayout from '@/Layouts/TodoLayout.vue';
import TaskForm from '@/Components/TaskForm.vue';
import SearchInput from '@/Components/SearchInput.vue';
import TaskFilters from '@/Components/TaskFilters.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
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
    tasks: Task[];
    projects: Project[];
    filters?: {
        status: string;
        priority: string;
        project: string;
        due_date: string;
        search: string;
    };
}

const props = defineProps<Props>();

const filters = computed(() => props.filters || {
    status: '',
    priority: '',
    project: '',
    due_date: '',
    search: '',
});

// Modal state
const showModal = ref(false);
const showDeleteModal = ref(false);
const editingTask = ref<Task | null>(null);
const deletingTask = ref<Task | null>(null);

// Open create modal
const openCreateModal = () => {
    editingTask.value = null;
    showModal.value = true;
};

// Open edit modal
const openEditModal = (task: Task) => {
    editingTask.value = task;
    showModal.value = true;
};

// Open delete confirmation
const openDeleteModal = (task: Task) => {
    deletingTask.value = task;
    showDeleteModal.value = true;
};

// Close modals
const closeModal = () => {
    showModal.value = false;
    showDeleteModal.value = false;
    editingTask.value = null;
    deletingTask.value = null;
};

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

// Filter incomplete tasks
const incompleteTasks = computed(() =>
    props.tasks.filter(t => !t.completed)
);

// Filter completed tasks
const completedTasks = computed(() =>
    props.tasks.filter(t => t.completed)
);
</script>

<template>
    <TodoLayout>
<template #header>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Tasks
                </h2>
                <div class="flex items-center gap-2">
                    <SearchInput placeholder="Search tasks..." />
                    <PrimaryButton @click="openCreateModal" :disabled="projects.length === 0">
                        <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        New Task
                    </PrimaryButton>
                </div>
            </div>
        </template>

        <!-- Filters -->
        <div class="mb-4">
            <TaskFilters :projects="projects" />
        </div>

        <!-- No Projects Warning -->
        <div v-if="projects.length === 0" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <div class="flex">
                <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        You need to create a project before adding tasks.
                    </p>
                    <Link href="/projects" class="text-sm font-medium text-yellow-700 hover:text-yellow-600 underline">
                        Create a project
                    </Link>
                </div>
            </div>
        </div>

        <!-- Pending Tasks -->
        <div v-if="incompleteTasks.length > 0" class="mb-8">
            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">
                Pending ({{ incompleteTasks.length }})
            </h3>
            <div class="bg-white rounded-lg shadow divide-y">
                <div
                    v-for="task in incompleteTasks"
                    :key="task.id"
                    class="p-4 hover:bg-gray-50 transition-colors"
                >
                    <div class="flex items-start gap-3">
                        <!-- Checkbox -->
                        <button
                            @click="toggleTask(task)"
                            class="mt-0.5 h-5 w-5 rounded border-2 border-gray-300 hover:border-indigo-500 flex items-center justify-center flex-shrink-0 transition-colors"
                            :class="{ 'bg-indigo-500 border-indigo-500': task.completed }"
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
                                    :class="getPriorityStyles(task.priority)"
                                    class="px-2 py-0.5 rounded-full text-xs font-medium"
                                >
                                    {{ task.priority }}
                                </span>
                            </div>

                            <div class="flex items-center gap-3 mt-1 text-sm text-gray-500">
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
                                    :class="{ 'text-red-600 font-medium': isOverdue(task.due_date) }"
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
                                @click="openEditModal(task)"
                                class="p-1 text-gray-400 hover:text-gray-600 rounded-md hover:bg-gray-100"
                                title="Edit task"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </button>
                            <button
                                @click="openDeleteModal(task)"
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
        </div>

        <!-- Completed Tasks -->
        <div v-if="completedTasks.length > 0">
            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">
                Completed ({{ completedTasks.length }})
            </h3>
            <div class="bg-white rounded-lg shadow divide-y opacity-75">
                <div
                    v-for="task in completedTasks"
                    :key="task.id"
                    class="p-4 hover:bg-gray-50 transition-colors"
                >
                    <div class="flex items-start gap-3">
                        <!-- Checkbox -->
                        <button
                            @click="toggleTask(task)"
                            class="mt-0.5 h-5 w-5 rounded border-2 border-gray-300 flex items-center justify-center flex-shrink-0 bg-gray-100"
                        >
                            <svg class="h-3 w-3 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </button>

                        <!-- Task Content -->
                        <div class="flex-1 min-w-0">
                            <span class="line-through text-gray-400 font-medium">
                                {{ task.title }}
                            </span>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center gap-1">
                            <button
                                @click="openDeleteModal(task)"
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
        </div>

        <!-- Empty State -->
        <div
            v-if="tasks.length === 0"
            class="bg-white rounded-lg shadow p-8 text-center"
        >
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No tasks yet</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by creating your first task.</p>
            <div class="mt-6" v-if="projects.length > 0">
                <PrimaryButton @click="openCreateModal">
                    Create Task
                </PrimaryButton>
            </div>
        </div>

        <!-- Create/Edit Task Modal -->
        <TaskForm
            :is-open="showModal"
            :task="editingTask"
            :projects="projects"
            @close="closeModal"
        />

        <!-- Delete Confirmation Modal -->
        <Modal :show="showDeleteModal" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-2">
                    Delete Task
                </h2>
                <p class="text-gray-500 mb-6">
                    Are you sure you want to delete "{{ deletingTask?.title }}"? This action cannot be undone.
                </p>
                <div class="flex justify-end gap-3">
                    <SecondaryButton @click="closeModal">
                        Cancel
                    </SecondaryButton>
                    <Link
                        :href="`/tasks/${deletingTask?.id}`"
                        method="delete"
                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-medium text-sm text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                        as="button"
                    >
                        Delete
                    </Link>
                </div>
            </div>
        </Modal>
    </TodoLayout>
</template>
