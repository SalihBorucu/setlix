<script setup>
import { ref } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { DSButton } from '@/Components/UI'

const showingNavigationDropdown = ref(false)
const { auth } = usePage().props

// Basic routes that don't require band context
const navigation = [
    { name: 'Dashboard', href: route('dashboard'), icon: 'HomeIcon' },
]

// Routes that require band context - we'll show these only when a band is selected
const bandNavigation = [
    { name: 'Songs', href: (bandId) => route('songs.index', { band: bandId }), icon: 'MusicNoteIcon' },
    { name: 'Setlists', href: (bandId) => route('setlists.index', { band: bandId }), icon: 'ListBulletIcon' },
]

const userNavigation = [
    { name: 'Your Profile', href: route('profile.edit') },
    // { name: 'Settings', href: '#' },
    { name: 'Sign out', href: route('logout'), method: 'post' }
]

// Get the current band ID from the route parameters
const currentBandId = route().params.band
</script>

<template>
    <div class="min-h-screen bg-neutral-50 test">
        <!-- Navigation -->
        <nav class="bg-white border-b border-neutral-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <!-- Left Navigation -->
                    <div class="flex">
                        <!-- Logo -->
                        <div class="flex-shrink-0 flex items-center">
                            <Link :href="route('dashboard')" class="flex">
                                <img class="h-12 w-auto" src="/images/text_logo.svg" alt="AI Setlist" />
                            </Link>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden space-x-8 sm:ml-10 sm:flex">
                            <!-- Basic Navigation -->
                            <Link
                                v-for="item in navigation"
                                :key="item.name"
                                :href="item.href"
                                :class="[
                                    route().current(item.href)
                                        ? 'border-primary-500 text-neutral-900'
                                        : 'border-transparent text-neutral-500 hover:border-neutral-300 hover:text-neutral-700',
                                    'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors duration-200'
                                ]"
                            >
                                {{ item.name }}
                            </Link>

                            <!-- Band-scoped Navigation -->
                            <template v-if="currentBandId">
                                <Link
                                    v-for="item in bandNavigation"
                                    :key="item.name"
                                    :href="item.href(currentBandId)"
                                    :class="[
                                        route().current(item.href(currentBandId))
                                            ? 'border-primary-500 text-neutral-900'
                                            : 'border-transparent text-neutral-500 hover:border-neutral-300 hover:text-neutral-700',
                                        'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors duration-200'
                                    ]"
                                >
                                    {{ item.name }}
                                </Link>
                            </template>
                        </div>
                    </div>

                    <!-- Right Navigation -->
                    <div class="hidden sm:ml-6 sm:flex sm:items-center space-x-4">
                        <!-- Profile Dropdown -->
                        <div class="relative">
                            <button
                                @click="showingNavigationDropdown = !showingNavigationDropdown"
                                class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-neutral-300 transition duration-200"
                            >
                                <span class="sr-only">Open user menu</span>
                                <img
                                    class="h-8 w-8 rounded-full object-cover"
                                    :src="auth.user.avatar || 'https://ui-avatars.com/api/?name=' + auth.user.name"
                                    :alt="auth.user.name"
                                />
                            </button>

                            <!-- Dropdown Menu -->
                            <div
                                v-show="showingNavigationDropdown"
                                class="absolute right-0 mt-2 w-48 rounded-lg shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 z-10"
                                role="menu"
                            >
                                <div class="px-4 py-2 border-b border-neutral-200">
                                    <p class="text-sm font-medium text-neutral-900">{{ auth.user.name }}</p>
                                    <p class="text-xs text-neutral-500 truncate">{{ auth.user.email }}</p>
                                </div>

                                <div class="py-1">
                                    <Link
                                        v-for="item in userNavigation"
                                        :key="item.name"
                                        :href="item.href"
                                        :method="item.method"
                                        as="button"
                                        class="block w-full text-left px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-50"
                                        role="menuitem"
                                    >
                                        {{ item.name }}
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="-mr-2 flex items-center sm:hidden">
                        <button
                            @click="showingNavigationDropdown = !showingNavigationDropdown"
                            class="inline-flex items-center justify-center p-2 rounded-lg text-neutral-400 hover:text-neutral-500 hover:bg-neutral-100 focus:outline-none focus:bg-neutral-100 focus:text-neutral-500 transition duration-200"
                        >
                            <svg
                                class="h-6 w-6"
                                stroke="currentColor"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    :class="{'hidden': showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"
                                />
                                <path
                                    :class="{'hidden': !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Navigation Menu -->
            <div
                :class="{'block': showingNavigationDropdown, 'hidden': !showingNavigationDropdown}"
                class="sm:hidden"
            >
                <div class="pt-2 pb-3 space-y-1">
                    <!-- Basic Navigation -->
                    <Link
                        v-for="item in navigation"
                        :key="item.name"
                        :href="item.href"
                        :class="[
                            route().current(item.href)
                                ? 'bg-primary-50 border-primary-500 text-primary-700'
                                : 'border-transparent text-neutral-600 hover:bg-neutral-50 hover:border-neutral-300 hover:text-neutral-800',
                            'block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition-colors duration-200'
                        ]"
                    >
                        {{ item.name }}
                    </Link>

                    <!-- Band-scoped Navigation -->
                    <template v-if="currentBandId">
                        <Link
                            v-for="item in bandNavigation"
                            :key="item.name"
                            :href="item.href(currentBandId)"
                            :class="[
                                route().current(item.href(currentBandId))
                                    ? 'bg-primary-50 border-primary-500 text-primary-700'
                                    : 'border-transparent text-neutral-600 hover:bg-neutral-50 hover:border-neutral-300 hover:text-neutral-800',
                                'block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition-colors duration-200'
                            ]"
                        >
                            {{ item.name }}
                        </Link>
                    </template>
                </div>

                <!-- Mobile Profile Menu -->
                <div class="pt-4 pb-1 border-t border-neutral-200">
                    <div class="flex items-center px-4">
                        <div class="flex-shrink-0">
                            <img
                                class="h-10 w-10 rounded-full object-cover"
                                :src="auth.user.avatar || 'https://ui-avatars.com/api/?name=' + auth.user.name"
                                :alt="auth.user.name"
                            />
                        </div>

                        <div class="ml-3">
                            <div class="text-base font-medium text-neutral-800">
                                {{ auth.user.name }}
                            </div>
                            <div class="text-sm font-medium text-neutral-500">
                                {{ auth.user.email }}
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 space-y-1">
                        <Link
                            v-for="item in userNavigation"
                            :key="item.name"
                            :href="item.href"
                            :method="item.method"
                            class="block px-4 py-2 text-base font-medium text-neutral-500 hover:text-neutral-800 hover:bg-neutral-100"
                        >
                            {{ item.name }}
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="py-10">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Page Heading -->
                <header v-if="$slots.header" class="mb-8">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <slot name="header" />
                    </div>
                </header>

                <!-- Main Content -->
                <div class="px-4 sm:px-6 lg:px-8">
                    <slot />
                </div>
            </div>
        </main>
    </div>
</template>
