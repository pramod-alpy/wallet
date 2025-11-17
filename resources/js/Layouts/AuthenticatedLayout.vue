<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
</script>
<template>
  <div class="min-h-screen bg-indigo-50">
    <!-- Navbar -->
    <nav class="bg-gray shadow-md border-b border-gray-200">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 justify-between items-center">
          <!-- Logo + Navigation Links -->
          <div class="flex items-center">
            <!-- Logo -->
            <div class="flex-shrink-0">
              <Link :href="route('dashboard')">
                <ApplicationLogo class="h-9 w-auto text-indigo-600" />
              </Link>
            </div>
            <!-- Navigation Links -->
            <div class="hidden sm:ml-10 sm:flex space-x-8">
              <NavLink
                :href="route('dashboard')"
                :active="route().current('dashboard')"
                class="text-gray-700 hover:text-indigo-600"
              >
                Dashboard
              </NavLink>
            </div>
          </div>

          <!-- User Dropdown -->
          <div class="hidden sm:flex sm:items-center sm:ml-6">
            <Dropdown align="right" width="48">
              <template #trigger>
                <button
                  class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none"
                >
                  {{ $page.props.auth.user.name }}
                  <svg
                    class="ml-2 h-4 w-4"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </button>
              </template>
              <template #content>
                <DropdownLink
                  :href="route('profile.edit')"
                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700"
                >
                  Profile
                </DropdownLink>
                <DropdownLink
                  :href="route('logout')"
                  method="post"
                  as="button"
                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700"
                >
                  Log Out
                </DropdownLink>
              </template>
            </Dropdown>
          </div>

          <!-- Hamburger Menu -->
          <div class="-mr-2 flex items-center sm:hidden">
            <button
              @click="showingNavigationDropdown = !showingNavigationDropdown"
              class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-indigo-600 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-indigo-600"
            >
              <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path
                  :class="{ hidden: showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M4 6h16M4 12h16M4 18h16"
                />
                <path
                  :class="{ hidden: !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
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

      <!-- Responsive Menu -->
      <div :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }" class="sm:hidden bg-white shadow-md">
        <div class="pt-2 pb-3 space-y-1">
          <ResponsiveNavLink
            :href="route('dashboard')"
            :active="route().current('dashboard')"
            class="block px-4 py-2 text-gray-700 hover:bg-indigo-50 hover:text-indigo-700"
          >
            Dashboard
          </ResponsiveNavLink>
        </div>

        <!-- Responsive User Settings -->
        <div class="pt-4 pb-1 border-t border-gray-200">
          <div class="px-4">
            <div class="font-medium text-gray-800">{{ $page.props.auth.user.name }}</div>
            <div class="font-medium text-gray-500">{{ $page.props.auth.user.email }}</div>
          </div>
          <div class="mt-3 space-y-1">
            <ResponsiveNavLink
              :href="route('profile.edit')"
              class="block px-4 py-2 text-gray-700 hover:bg-indigo-50 hover:text-indigo-700"
            >
              Profile
            </ResponsiveNavLink>
            <ResponsiveNavLink
              :href="route('logout')"
              method="post"
              as="button"
              class="block px-4 py-2 text-gray-700 hover:bg-indigo-50 hover:text-indigo-700"
            >
              Log Out
            </ResponsiveNavLink>
          </div>
        </div>
      </div>
    </nav>

    <!-- Page Heading -->
    <header v-if="$slots.header" class="bg-indigo-200 shadow">
      <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 text-white">
        <slot name="header" />
      </div>
    </header>

    <!-- Page Content -->
    <main class="p-6">
      <div class="bg-white rounded-md shadow p-6">
        <slot />
      </div>
    </main>
  </div>
</template>
