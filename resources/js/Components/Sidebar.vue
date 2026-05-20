<script setup lang="ts">
import { ref, defineExpose, computed } from 'vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
const isOpen = ref(false);

// Navigation items for todo app
const navigation = [
    { name: 'Dashboard', href: '/dashboard' },
    { name: 'Inbox', href: '/inbox' },
    { name: 'Today', href: '/today' },
    { name: 'Upcoming', href: '/upcoming' },
    { name: 'Tasks', href: '/tasks' },
    { name: 'Projects', href: '/projects' },
];

// Check if route is active
const isActive = (href: string): boolean => {
    return page.url.startsWith(href);
};

const toggle = () => {
    isOpen.value = !isOpen.value;
};

const close = () => {
    isOpen.value = false;
};

defineExpose({
    toggle,
    close,
    isOpen,
});
</script>

<template>
    <!-- Desktop Sidebar (md and up) -->
    <div class="hidden md:flex md:w-64 md:flex-col md:fixed md:inset-y-0 bg-gray-900">
        <!-- Logo -->
        <div class="flex flex-shrink-0 items-center h-16 px-4 bg-gray-900 border-b border-gray-800">
            <Link :href="route('dashboard')" class="flex items-center gap-2">
                <div class="w-8 h-8 bg-indigo-500 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
                <span class="text-white font-semibold text-lg">Todo</span>
            </Link>
        </div>

        <!-- Navigation -->
        <div class="flex-1 flex flex-col overflow-y-auto">
            <nav class="flex-1 px-3 py-4 space-y-1">
                <NavLink
                    v-for="item in navigation"
                    :key="item.name"
                    :href="item.href"
                    :active="isActive(item.href)"
                    class="text-gray-400 hover:text-white hover:bg-gray-800"
                    active-class="bg-gray-800 text-white"
                >
                    {{ item.name }}
                </NavLink>
            </nav>
        </div>

        <!-- User section -->
        <div class="flex-shrink-0 border-t border-gray-800 p-4">
            <div class="flex items-center">
                <div class="h-9 w-9 rounded-full bg-indigo-500 flex items-center justify-center text-white font-medium text-sm">
                    {{ $page.props.auth.user?.name?.charAt(0) || 'U' }}
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-white">{{ $page.props.auth.user?.name || 'User' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Overlay -->
    <div v-if="isOpen" class="fixed inset-0 z-40 flex md:hidden" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-600 bg-opacity-75 transition-opacity" @click="close"></div>

        <!-- Sidebar panel -->
        <div class="relative flex w-full max-w-xs flex-1 flex-col bg-gray-900 pt-5 pb-4">
            <!-- Close button -->
            <div class="absolute top-0 right-0 -mr-12 pt-2">
                <button @click="close"
                    class="ml-1 flex h-10 w-10 items-center justify-center rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                    <span class="sr-only">Close sidebar</span>
                    <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="flex flex-shrink-0 items-center h-16 px-4 bg-gray-900 border-b border-gray-800">
                <Link :href="route('dashboard')" @click="close" class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-indigo-500 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                    <span class="text-white font-semibold text-lg">Todo</span>
                </Link>
            </div>

            <div class="mt-5 h-0 flex-1 overflow-y-auto">
                <nav class="space-y-1 px-3">
                    <ResponsiveNavLink
                        v-for="item in navigation"
                        :key="item.name"
                        :href="item.href"
                        :active="isActive(item.href)"
                        @click="close"
                    >
                        {{ item.name }}
                    </ResponsiveNavLink>
                </nav>
            </div>
        </div>

        <div class="w-14 flex-shrink-0"></div>
    </div>
</template>
