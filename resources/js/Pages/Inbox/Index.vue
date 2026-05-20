<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link, useForm, router } from '@inertiajs/vue3';
import TodoLayout from '@/Layouts/TodoLayout.vue';
import QuickAddTask from '@/Components/QuickAddTask.vue';
import SearchInput from '@/Components/SearchInput.vue';
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
    project_id: number | null;
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
        search: string;
    };
}

const props = defineProps<Props>();

const filters = computed(() => props.filters || {
    status: '',
    priority: '',
    search: '',
});

// Modal state
const showModal = ref(false);
const editingTask = ref<Task | null>(null);
const quickAddRef = ref<InstanceType<typeof QuickAddTask> | null>(null);

// Keyboard shortcut
const handleKeydown = (e: KeyboardEvent) => {
    // Cmd/Ctrl + N to focus new task input
    if ((e.metaKey || e.ctrlKey) && e.key === 'n') {
        e.preventDefault();
        quickAddRef.value?.focus();
    }
};

// Computed values
const pendingTasks = computed(() =>
    props.tasks.filter(t => !t.completed)
);

const completedTasks = computed(() =>
    props.tasks.filter(t => t.completed)
);

// Open edit modal
const openEditModal = (task: Task) => {
    editingTask.value = task;
    showModal.value = true;
};

// Close modal
const closeModal = () => {
    showModal.value = false;
    editingTask.value = null;
};

// Handle task added event
const handleTaskAdded = () => {
    // Could show a brief success indicator
};

// On mount/unmount for keyboard shortcuts
onMounted(() => {
    document.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
    document.removeEventListener('keydown', handleKeydown);
});
</script>

<template>
    <TodoLayout>
<template #header>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Inbox
                </h2>
                <div class="flex items-center gap-2">
                    <SearchInput placeholder="Search tasks..." />
                </div>
            </div>
        </template>

<!-- Filters -->
        <div class="mb-4 flex flex-wrap gap-2 items-center">
            <!-- Status Filter -->
            <select
                :value="filters.status"
                @change="router.get(route('inbox.index'), { status: ($event.target as HTMLSelectElement).value }, { replace: true })"
                class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm bg-white"
            >
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="completed">Completed</option>
            </select>

            <!-- Priority Filter -->
            <select
                :value="filters.priority"
                @change="router.get(route('inbox.index'), { priority: ($event.target as HTMLSelectElement).value }, { replace: true })"
                class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm bg-white"
            >
                <option value="">All Priorities</option>
                <option value="low">Low</option>
                <option value="medium">Medium</option>
                <option value="high">High</option>
            </select>

            <!-- Clear Filters -->
            <button
                v-if="filters.status || filters.priority || filters.search"
                @click="router.get(route('inbox.index'), {}, { replace: true })"
                class="px-3 py-2 text-sm text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
            >
                Clear Filters
            </button>
        </div>

        <!-- Quick Add Input -->
        <div class="mb-6">
            <QuickAddTask
                ref="quickAddRef"
                placeholder="Add a task... (press Enter to save)"
                @added="handleTaskAdded"
            />
        </div>

        <!-- Keyboard Hint -->
        <div class="mb-4 text-xs text-gray-400 text-center">
            Tip: Press Enter to quickly save a task without leaving the keyboard
        </div>

        <!-- Task Groups -->
        <div class="space-y-6">
            <!-- Pending Tasks -->
            <div v-if="pendingTasks.length > 0">
                <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">
                    Inbox ({{ pendingTasks.length }})
                </h3>
                <div class="bg-white rounded-lg shadow divide-y">
                    <div
                        v-for="task in pendingTasks"
                        :key="task.id"
                        class="p-4 hover:bg-gray-50 transition-colors"
                    >
                        <div class="flex items-start gap-3">
                            <!-- Checkbox -->
                            <button
                                @click="useForm({}).patch(`/tasks/${task.id}/toggle`)"
                                class="mt-0.5 h-5 w-5 rounded border-2 border-gray-300 hover:border-indigo-500 flex items-center justify-center flex-shrink-0 transition-colors"
                            >
                            </button>

                            <!-- Task Content -->
                            <div class="flex-1 min-w-0">
                                <span class="font-medium text-gray-900">
                                    {{ task.title }}
                                </span>
                                <div class="flex items-center gap-3 mt-1 text-sm text-gray-500">
                                    <span v-if="task.priority !== 'medium'" :class="{
                                        'bg-gray-100 text-gray-600': task.priority === 'low',
                                        'bg-red-100 text-red-700': task.priority === 'high',
                                    }" class="px-2 py-0.5 rounded-full text-xs font-medium">
                                        {{ task.priority }}
                                    </span>
                                    <span v-if="task.due_date">
                                        Due {{ new Date(task.due_date).toLocaleDateString() }}
                                    </span>
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
                                <Link
                                    :href="`/tasks/${task.id}`"
                                    method="delete"
                                    as="button"
                                    class="p-1 text-gray-400 hover:text-red-600 rounded-md hover:bg-gray-100"
                                    title="Delete task"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </Link>
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
                                @click="useForm({}).patch(`/tasks/${task.id}/toggle`)"
                                class="mt-0.5 h-5 w-5 rounded border-2 bg-gray-100 flex items-center justify-center flex-shrink-0"
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
                            <Link
                                :href="`/tasks/${task.id}`"
                                method="delete"
                                as="button"
                                class="p-1 text-gray-400 hover:text-red-600 rounded-md hover:bg-gray-100"
                                title="Delete task"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </Link>
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Inbox is empty</h3>
                <p class="mt-1 text-sm text-gray-500">Capture a quick thought or task below.</p>
            </div>
        </div>
    </TodoLayout>
</template>
