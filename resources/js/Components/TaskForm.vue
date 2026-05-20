<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';

interface Project {
    id: number;
    name: string;
    color: string;
}

interface Task {
    id?: number;
    title: string;
    description: string | null;
    project_id: number;
    priority: string;
    due_date: string | null;
    completed: boolean;
}

interface Props {
    task?: Task | null;
    projects: Project[];
    isOpen: boolean;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    (e: 'close'): void;
}>();

const priorities = [
    { value: 'low', label: 'Low', color: 'bg-gray-100 text-gray-600' },
    { value: 'medium', label: 'Medium', color: 'bg-yellow-100 text-yellow-700' },
    { value: 'high', label: 'High', color: 'bg-red-100 text-red-700' },
];

const form = useForm({
    title: '',
    description: '',
    project_id: props.projects[0]?.id || 0,
    priority: 'medium',
    due_date: '',
    completed: false,
});

const isEditing = computed(() => !!props.task?.id);

watch(() => props.task, (newTask) => {
    if (newTask) {
        form.title = newTask.title;
        form.description = newTask.description || '';
        form.project_id = newTask.project_id;
        form.priority = newTask.priority;
        form.due_date = newTask.due_date ? newTask.due_date.split('T')[0] : '';
        form.completed = newTask.completed;
    } else {
        form.reset();
        if (props.projects.length > 0) {
            form.project_id = props.projects[0].id;
        }
    }
}, { immediate: true });

watch(() => props.isOpen, (isOpen) => {
    if (isOpen && !props.task) {
        form.reset();
        if (props.projects.length > 0) {
            form.project_id = props.projects[0].id;
        }
    }
});

const submit = () => {
    if (isEditing.value && props.task) {
        form.put(`/tasks/${props.task.id}`, {
            onSuccess: () => {
                emit('close');
            },
        });
    } else {
        form.post('/tasks', {
            onSuccess: () => {
                emit('close');
            },
        });
    }
};

const closeModal = () => {
    form.reset();
    emit('close');
};

const getProjectColor = (projectId: number) => {
    const project = props.projects.find(p => p.id === projectId);
    return project?.color || '#6366f1';
};
</script>

<template>
    <Modal :show="isOpen" @close="closeModal" max-width="lg">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">
                {{ isEditing ? 'Edit Task' : 'Create New Task' }}
            </h2>

            <form @submit.prevent="submit">
                <!-- Title -->
                <div class="mb-4">
                    <InputLabel for="title" value="Task Title" />
                    <TextInput
                        id="title"
                        v-model="form.title"
                        type="text"
                        class="mt-1 block w-full"
                        placeholder="Enter task title"
                        autofocus
                        required
                    />
                    <InputError :message="form.errors.title" class="mt-2" />
                </div>

                <!-- Project Selection -->
                <div class="mb-4">
                    <InputLabel for="project" value="Project" />
                    <select
                        id="project"
                        v-model="form.project_id"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option
                            v-for="project in projects"
                            :key="project.id"
                            :value="project.id"
                        >
                            {{ project.name }}
                        </option>
                    </select>
                    <InputError :message="form.errors.project_id" class="mt-2" />
                </div>

                <!-- Priority -->
                <div class="mb-4">
                    <InputLabel for="priority" value="Priority" />
<div class="mt-2 flex gap-2">
                        <button
                            v-for="p in priorities"
                            :key="p.value"
                            type="button"
                            @click="form.priority = p.value"
                            class="px-3 py-1.5 rounded-full text-sm font-medium transition-colors"
                            :class="[
                                p.color,
                                form.priority === p.value
                                    ? 'ring-2 ring-indigo-500'
                                    : 'hover:bg-gray-50',
                            ]"
                        >
                            {{ p.label }}
                        </button>
                    </div>
                </div>

                <!-- Due Date -->
                <div class="mb-4">
                    <InputLabel for="due_date" value="Due Date" />
                    <TextInput
                        id="due_date"
                        v-model="form.due_date"
                        type="date"
                        class="mt-1 block w-full"
                    />
                    <InputError :message="form.errors.due_date" class="mt-2" />
                </div>

                <!-- Description -->
                <div class="mb-4">
                    <InputLabel for="description" value="Description" />
                    <textarea
                        id="description"
                        v-model="form.description"
                        rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Add a description (optional)"
                    ></textarea>
                    <InputError :message="form.errors.description" class="mt-2" />
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-3">
                    <SecondaryButton type="button" @click="closeModal">
                        Cancel
                    </SecondaryButton>
                    <PrimaryButton :disabled="form.processing">
                        {{ isEditing ? 'Update' : 'Create' }}
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </Modal>
</template>
