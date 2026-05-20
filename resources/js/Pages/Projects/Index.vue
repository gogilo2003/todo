<script setup lang="ts">
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import TodoLayout from '@/Layouts/TodoLayout.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Modal from '@/Components/Modal.vue';
import ProjectForm from '@/Components/ProjectForm.vue';

interface Project {
    id: number;
    name: string;
    color: string;
    created_at: string;
    updated_at: string;
}

interface Props {
    projects: Project[];
}

const props = defineProps<Props>();

const page = usePage();

// Modal state
const showModal = ref(false);
const showDeleteModal = ref(false);
const editingProject = ref<Project | null>(null);
const deletingProject = ref<Project | null>(null);

// Open create modal
const openCreateModal = () => {
    editingProject.value = null;
    showModal.value = true;
};

// Open edit modal
const openEditModal = (project: Project) => {
    editingProject.value = project;
    showModal.value = true;
};

// Open delete confirmation
const openDeleteModal = (project: Project) => {
    deletingProject.value = project;
    showDeleteModal.value = true;
};

// Close modals
const closeModal = () => {
    showModal.value = false;
    showDeleteModal.value = false;
    editingProject.value = null;
    deletingProject.value = null;
};
</script>

<template>
    <TodoLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Projects
                </h2>
                <PrimaryButton @click="openCreateModal">
                    <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    New Project
                </PrimaryButton>
            </div>
        </template>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Project Cards -->
            <div
                v-for="project in projects"
                :key="project.id"
                class="bg-white rounded-lg shadow p-4 hover:shadow-md transition-shadow"
            >
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <div
                            class="h-4 w-4 rounded-full flex-shrink-0"
                            :style="{ backgroundColor: project.color }"
                        ></div>
                        <h3 class="font-medium text-gray-900">
                            {{ project.name }}
                        </h3>
                    </div>
                    <div class="flex items-center gap-1">
                        <!-- Edit Button -->
                        <button
                            @click="openEditModal(project)"
                            class="p-1 text-gray-400 hover:text-gray-600 rounded-md hover:bg-gray-100"
                            title="Edit project"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </button>
                        <!-- Delete Button -->
                        <button
                            @click="openDeleteModal(project)"
                            class="p-1 text-gray-400 hover:text-red-600 rounded-md hover:bg-gray-100"
                            title="Delete project"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="mt-2 text-xs text-gray-500">
                    Created {{ new Date(project.created_at).toLocaleDateString() }}
                </div>
            </div>

            <!-- Empty State -->
            <div
                v-if="projects.length === 0"
                class="col-span-full bg-white rounded-lg shadow p-8 text-center"
            >
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No projects yet</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating your first project.</p>
                <div class="mt-6">
                    <PrimaryButton @click="openCreateModal">
                        Create Project
                    </PrimaryButton>
                </div>
            </div>
        </div>

        <!-- Create/Edit Project Modal -->
        <ProjectForm
            :is-open="showModal"
            :project="editingProject"
            @close="closeModal"
        />

        <!-- Delete Confirmation Modal -->
        <Modal :show="showDeleteModal" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-2">
                    Delete Project
                </h2>
                <p class="text-gray-500 mb-6">
                    Are you sure you want to delete "{{ deletingProject?.name }}"? This action cannot be undone and all tasks in this project will also be deleted.
                </p>
                <div class="flex justify-end gap-3">
                    <SecondaryButton @click="closeModal">
                        Cancel
                    </SecondaryButton>
                    <Link
                        :href="`/projects/${deletingProject?.id}`"
                        method="delete"
                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-medium text-sm text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:opacity50"
                        as="button"
                    >
                        Delete
                    </Link>
                </div>
            </div>
        </Modal>
    </TodoLayout>
</template>
