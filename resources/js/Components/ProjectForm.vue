<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';

interface Project {
    id?: number;
    name: string;
    color: string;
}

interface Props {
    project?: Project | null;
    isOpen: boolean;
    onClose: () => void;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    (e: 'close'): void;
}>();

// Available colors for projects
const colors = [
    '#ef4444', // red
    '#f97316', // orange
    '#eab308', // yellow
    '#22c55e', // green
    '#14b8a6', // teal
    '#3b82f6', // blue
    '#6366f1', // indigo
    '#a855f7', // purple
    '#ec4899', // pink
];

// Form state
const form = useForm({
    name: '',
    color: '#6366f1',
});

const isEditing = computed(() => !!props.project?.id);

// Reset form when project changes
watch(() => props.project, (newProject) => {
    if (newProject) {
        form.name = newProject.name;
        form.color = newProject.color;
    } else {
        form.name = '';
        form.color = '#6366f1';
    }
}, { immediate: true });

// Watch for modal open to reset form
watch(() => props.isOpen, (isOpen) => {
    if (isOpen && !props.project) {
        form.name = '';
        form.color = '#6366f1';
    }
});

const submit = () => {
    if (isEditing.value && props.project) {
        form.put(`/projects/${props.project.id}`, {
            onSuccess: () => {
                emit('close');
            },
        });
    } else {
        form.post('/projects', {
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
</script>

<template>
    <Modal :show="isOpen" @close="closeModal">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">
                {{ isEditing ? 'Edit Project' : 'Create New Project' }}
            </h2>

            <form @submit.prevent="submit">
                <!-- Name Input -->
                <div class="mb-4">
                    <InputLabel for="name" value="Project Name" />
                    <TextInput
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="mt-1 block w-full"
                        placeholder="Enter project name"
                        autofocus
                        required
                    />
                    <InputError :message="form.errors.name" class="mt-2" />
                </div>

                <!-- Color Selection -->
                <div class="mb-4">
                    <InputLabel for="color" value="Color" />
                    <div class="mt-2 flex items-center gap-2">
                        <button
                            v-for="color in colors"
                            :key="color"
                            type="button"
                            @click="form.color = color"
                            :class="[
                                form.color === color ? 'ring-2 ring-offset-2 ring-indigo-500' : '',
                            ]"
                            class="h-8 w-8 rounded-full focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            :style="{ backgroundColor: color }"
                        >
                            <span class="sr-only">{{ color }}</span>
                        </button>
                    </div>
                    <InputError :message="form.errors.color" class="mt-2" />
                </div>

                <!-- Preview -->
                <div class="mb-6">
                    <span class="text-sm text-gray-500">Preview:</span>
                    <div class="mt-2 flex items-center gap-2">
                        <div
                            class="h-4 w-4 rounded-full"
                            :style="{ backgroundColor: form.color }"
                        ></div>
                        <span class="text-sm font-medium text-gray-700">
                            {{ form.name || 'Project Name' }}
                        </span>
                    </div>
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
