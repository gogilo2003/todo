<script setup lang="ts">
import { ref, computed } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import TodoLayout from '@/Layouts/TodoLayout.vue';
import TaskList from '@/Components/TaskList.vue';
import TaskForm from '@/Components/TaskForm.vue';
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

interface Stats {
    total: number;
    completed: number;
    pending: number;
    completion_rate: number;
}

interface Props {
    dueToday: Task[];
    overdue: Task[];
    highPriority: Task[];
    projects: Project[];
    stats: Stats;
}

const props = defineProps<Props>();

// Modal state
const showModal = ref(false);
const showDeleteModal = ref(false);
const editingTask = ref<Task | null>(null);

// Computed values
const totalDisplayTasks = computed(() => {
    return props.overdue.length + props.dueToday.length;
});

const completionPercentage = computed(() => {
    return props.stats.completion_rate;
});

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
    editingTask.value = task;
    showDeleteModal.value = true;
};

// Close modals
const closeModal = () => {
    showModal.value = false;
    showDeleteModal.value = false;
    editingTask.value = null;
};
</script>

<template>
    <TodoLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Today
                </h2>
                <PrimaryButton @click="openCreateModal" :disabled="projects.length === 0">
                    <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    New Task
                </PrimaryButton>
            </div>
        </template>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Total Tasks -->
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-500">Total Tasks</div>
                <div class="text-2xl font-bold text-gray-900">{{ stats.total }}</div>
            </div>

            <!-- Pending Tasks -->
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-500">Pending</div>
                <div class="text-2xl font-bold text-indigo-600">{{ stats.pending }}</div>
            </div>

            <!-- Completed Tasks -->
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-500">Completed</div>
                <div class="text-2xl font-bold text-green-600">{{ stats.completed }}</div>
            </div>

            <!-- Completion Rate -->
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-500">Completion Rate</div>
                <div class="flex items-center gap-2">
                    <div class="text-2xl font-bold text-gray-900">{{ stats.completion_rate }}%</div>
                    <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div
                            class="h-full bg-green-500 transition-all"
                            :style="{ width: `${completionPercentage}%` }"
                        ></div>
                    </div>
                </div>
            </div>
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

        <!-- Task Groups -->
        <div class="space-y-6">
            <!-- Overdue Tasks -->
            <div v-if="overdue.length > 0">
                <div class="flex items-center gap-2 mb-3">
                    <svg class="h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-lg font-medium text-red-600">
                        Overdue ({{ overdue.length }})
                    </h3>
                </div>
                <TaskList
                    title=""
                    :tasks="overdue"
                    :projects="projects"
                    :show-project-column="true"
                    empty-message=""
                    @edit="openEditModal"
                    @delete="openDeleteModal"
                />
            </div>

            <!-- Due Today -->
            <div v-if="dueToday.length > 0">
                <div class="flex items-center gap-2 mb-3">
                    <svg class="h-5 w-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <h3 class="text-lg font-medium text-indigo-600">
                        Due Today ({{ dueToday.length }})
                    </h3>
                </div>
                <TaskList
                    title=""
                    :tasks="dueToday"
                    :projects="projects"
                    :show-project-column="true"
                    empty-message=""
                    @edit="openEditModal"
                    @delete="openDeleteModal"
                />
            </div>

            <!-- High Priority -->
            <div v-if="highPriority.length > 0">
                <div class="flex items-center gap-2 mb-3">
                    <svg class="h-5 w-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <h3 class="text-lg font-medium text-yellow-600">
                        High Priority ({{ highPriority.length }})
                    </h3>
                </div>
                <TaskList
                    title=""
                    :tasks="highPriority"
                    :projects="projects"
                    :show-project-column="true"
                    empty-message=""
                    @edit="openEditModal"
                    @delete="openDeleteModal"
                />
            </div>

            <!-- Empty State: No tasks to show -->
            <div
                v-if="overdue.length === 0 && dueToday.length === 0 && highPriority.length === 0"
                class="bg-white rounded-lg shadow p-8 text-center"
            >
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">All caught up!</h3>
                <p class="mt-1 text-sm text-gray-500">You have no tasks due today or overdue.</p>
                <div class="mt-6" v-if="projects.length > 0">
                    <PrimaryButton @click="openCreateModal">
                        Create Task
                    </PrimaryButton>
                </div>
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
                    Are you sure you want to delete "{{ editingTask?.title }}"? This action cannot be undone.
                </p>
                <div class="flex justify-end gap-3">
                    <SecondaryButton @click="closeModal">
                        Cancel
                    </SecondaryButton>
                    <Link
                        :href="`/tasks/${editingTask?.id}`"
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
