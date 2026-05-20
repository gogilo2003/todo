<script setup lang="ts">
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

interface Props {
    sidebarRef?: {
        toggle: () => void;
    };
}

const props = defineProps<Props>();
const page = usePage();
const showUserMenu = ref(false);

const toggleUserMenu = () => {
    showUserMenu.value = !showUserMenu.value;
};

const closeUserMenu = () => {
    showUserMenu.value = false;
};
</script>

<template>
    <header class="sticky top-0 z-30 flex h-16 w-full items-center justify-between border-b border-gray-200 bg-white px-4 shadow-sm md:hidden">
        <!-- Mobile menu button -->
        <div class="flex items-center">
            <button
                @click="props.sidebarRef?.toggle()"
                class="rounded-md p-2 text-gray-600 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

        <!-- Logo (mobile) -->
        <div class="flex items-center">
            <Link href="/dashboard" class="flex items-center gap-2">
                <div class="w-8 h-8 bg-indigo-500 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
                <span class="text-gray-900 font-semibold text-lg">Todo</span>
            </Link>
        </div>

        <!-- User menu (mobile) -->
        <div class="relative">
            <button
                @click="toggleUserMenu"
                class="flex items-center justify-center rounded-full focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
                <div class="h-8 w-8 rounded-full bg-indigo-500 flex items-center justify-center text-white font-medium text-sm">
                    {{ page.props.auth.user?.name?.charAt(0) || 'U' }}
                </div>
            </button>

            <!-- Dropdown menu -->
            <div
                v-if="showUserMenu"
                class="absolute end-0 mt-2 w-48 rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5"
            >
                <Link
                    href="/profile"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    @click="closeUserMenu"
                >
                    Profile
                </Link>
                <Link
                    href="/logout"
                    method="post"
                    as="button"
                    class="block w-full px-4 py-2 text-start text-sm text-gray-700 hover:bg-gray-100"
                    @click="closeUserMenu"
                >
                    Sign out
                </Link>
            </div>
        </div>
    </header>
</template>
