<script setup lang="ts">
import { ref } from 'vue';
import AppSidebar from '@/Components/Sidebar.vue';
import AppNavbar from '@/Components/AppNavbar.vue';

const sidebarRef = ref<InstanceType<typeof AppSidebar> | null>(null);

const toggleSidebar = () => {
    sidebarRef.value?.toggle();
};

const closeSidebar = () => {
    sidebarRef.value?.close();
};
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Desktop Sidebar -->
        <AppSidebar ref="sidebarRef" />

        <!-- Main Content Area -->
        <div class="flex flex-col md:ps-64">
            <!-- Mobile Top Navbar -->
            <AppNavbar :sidebar-ref="{ toggle: toggleSidebar }" />

            <!-- Page Content -->
            <main class="flex-1">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <!-- Page Header -->
                    <header v-if="$slots.header" class="mb-6">
                        <slot name="header" />
                    </header>

                    <!-- Main Content Slot -->
                    <slot />
                </div>
            </main>
        </div>

        <!-- Mobile Sidebar (Overlay) -->
        <div v-if="sidebarRef?.isOpen" class="fixed inset-0 z-30 md:hidden">
            <div class="fixed inset-0" @click="closeSidebar"></div>
        </div>
    </div>
</template>
