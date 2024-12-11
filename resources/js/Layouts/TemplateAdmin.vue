<script setup>
import { ref } from "vue";


const isSidebarVisible = ref(false); // State untuk menampilkan/menyembunyikan sidebar
</script>

<template>
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside
            :class="[ 
                'fixed top-0 left-0 h-full bg-gray-800 text-white transition-transform duration-300',
                isSidebarVisible ? 'translate-x-0' : '-translate-x-full',
                'lg:translate-x-0 lg:static lg:block'
            ]"
            class="w-64 z-50"
        >
            <div class="p-4 font-bold text-lg bg-gray-900">
                Admin Panel
            </div>
            <nav class="p-4">
                <ul class="space-y-2">
                    <li>
                        <a
                            href="#"
                            class="block px-4 py-2 rounded hover:bg-gray-700"
                        >
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a
                            href="#"
                            class="block px-4 py-2 rounded hover:bg-gray-700"
                        >
                            Settings
                        </a>
                    </li>
                    <li>
                        <a
                            href="#"
                            class="block px-4 py-2 rounded hover:bg-gray-700"
                        >
                            Profile
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <div
            class="flex flex-col flex-1 transition-all duration-300"
            :class="{ 'ml-64': isSidebarVisible }"
        >
            <!-- Navbar -->
            <header class="flex items-center justify-between bg-gray-900 text-white p-4">
                <button
                    @click="isSidebarVisible = !isSidebarVisible"
                    class="text-white bg-gray-700 p-2 rounded-md hover:bg-gray-600 lg:hidden"
                >
                    <i class="pi pi-bars"></i> <!-- Replace with FontAwesome or Heroicons -->
                </button>
                <h1 class="text-xl font-bold">Admin Dashboard</h1>
                <div class="flex items-center space-x-4">
                    <i class="pi pi-user"></i> <!-- Replace with FontAwesome or Heroicons -->
                    <span>Admin</span>
                </div>
            </header>

            <!-- Scrollable Content -->
            <main class="flex-1 overflow-y-auto p-6 bg-gray-100">
                <slot name="content">
                    
                </slot>
            </main>
        </div>
    </div>
</template>

<style>
/* Tidak ada utility tambahan, semua berbasis Tailwind CSS */
</style>
