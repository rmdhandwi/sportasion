<script setup>
import { ref } from "vue";
import { Sidebar, Button } from "primevue";
import { router, Head } from "@inertiajs/vue3";

const props = defineProps({
    auth: Object,
    title: String,
});

const role = props.auth.user.role;
const isSidebarVisible = ref(false); // State untuk menampilkan/menyembunyikan drawer

// Fungsi logout
const Logout = () => {
    router.post(route("logout"), {
        onSuccess: () => {
            router.visit(route("login")); // Redirect setelah logout sukses
        },
        onError: () => {
            console.error("Logout failed"); // Log error jika logout gagal
        },
    });
};
</script>

<template>
    <Head :title="props.title" />
    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar as Drawer (Mobile) -->
        <Sidebar
            v-model:visible="isSidebarVisible"
            class="lg:hidden"
            style="width: 250px"
        >
            <div class="p-4 font-bold text-lg bg-gray-600 text-white">
                Sportasion Panel
            </div>
            <nav class="p-4 space-y-3">
                <Button
                    as="a"
                    :href="route('dashboard')"
                    unstyled
                    :class="[
                        'flex items-center gap-4 w-full text-left px-4 py-3 rounded-md',
                        props.title === 'Dashboard'
                            ? 'bg-gray-300 text-gray-800'
                            : 'hover:bg-gray-200 text-gray-800 transition-all',
                    ]"
                >
                    <i class="pi pi-home"></i>
                    <span>Dashboard</span>
                </Button>
                <Button
                    as="a"
                    :href="route('kategori')"
                    unstyled
                    :class="[
                        'flex items-center gap-4 w-full text-left px-4 py-3 rounded-md',
                        props.title === 'Kategori'
                            ? 'bg-gray-300 text-gray-800'
                            : 'hover:bg-gray-200 text-gray-800 transition-all',
                    ]"
                >
                    <i class="pi pi-list"></i>
                    <span>Kategori</span>
                </Button>
                <Button
                    as="a"
                    :href="route('products')"
                    unstyled
                    :class="[
                        'flex items-center gap-4 w-full text-left px-4 py-3 rounded-md',
                        props.title === 'Products'
                            ? 'bg-gray-300 text-gray-800'
                            : 'hover:bg-gray-200 text-gray-800 transition-all',
                    ]"
                >
                    <i class="pi pi-shop"></i>
                    <span>Products</span>
                </Button>
                <hr />
                <Button
                    unstyled
                    @click="Logout"
                    class="flex items-center gap-4 w-full text-left px-4 py-3 hover:bg-red-200 transition-all text-gray-800 rounded-md"
                >
                    <i class="pi pi-sign-out"></i>
                    <span>Logout</span>
                </Button>
            </nav>
        </Sidebar>

        <!-- Sidebar (Desktop) -->
        <aside
            class="hidden lg:flex flex-col w-64 bg-gray-900 text-white h-full"
        >
            <div class="p-4 font-bold text-lg bg-gray-800">
                Sportasion Panel
            </div>
            <nav class="p-4 space-y-3">
                <Button
                    as="a"
                    :href="route('dashboard')"
                    unstyled
                    :class="[
                        'flex items-center gap-4 w-full text-left px-4 py-3 rounded-md',
                        props.title === 'Dashboard'
                            ? 'bg-gray-600'
                            : 'hover:bg-gray-600 transition-all',
                    ]"
                >
                    <i class="pi pi-home"></i>
                    <span>Dashboard</span>
                </Button>
                <Button
                    as="a"
                    :href="route('kategori')"
                    unstyled
                    :class="[
                        'flex items-center gap-4 w-full text-left px-4 py-3 rounded-md',
                        props.title === 'Kategori'
                            ? 'bg-gray-600'
                            : 'hover:bg-gray-600 transition-all',
                    ]"
                >
                    <i class="pi pi-list"></i>
                    <span>Kategori</span>
                </Button>
                <Button
                    as="a"
                    :href="route('products')"
                    unstyled
                    :class="[
                        'flex items-center gap-4 w-full text-left px-4 py-3 rounded-md',
                        props.title === 'Products'
                            ? 'bg-gray-600'
                            : 'hover:bg-gray-600 transition-all',
                    ]"
                >
                    <i class="pi pi-shop"></i>
                    <span>Products</span>
                </Button>
                <hr />
                <Button
                    unstyled
                    @click="Logout"
                    class="flex items-center gap-4 w-full text-left px-4 py-3 hover:text-red-600 transition-all rounded-md"
                >
                    <i class="pi pi-sign-out"></i>
                    <span>Logout</span>
                </Button>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- Navbar -->
            <header
                class="flex items-center justify-between bg-gray-800 text-white p-4 shadow-md"
            >
                <Button
                    unstyled
                    icon="pi pi-bars"
                    class="lg:hidden text-white"
                    @click="isSidebarVisible = true"
                />
                <h1 class="text-xl font-bold">{{ props.title }}</h1>
                <div class="flex items-center space-x-4">
                    <i class="pi pi-user"></i>
                    <span>{{ props.auth.user.name }}</span>
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

<style scoped>
/* Styling tambahan untuk memperhalus transisi */
aside,
.p-sidebar {
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}
</style>
