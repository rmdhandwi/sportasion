<script setup>
import { onMounted } from "vue";
import { useToast, Toast, Card, Button } from "primevue";
import TemplateAdmin from "@/Layouts/TemplateAdmin.vue";

const props = defineProps({
    flash: Object,
    auth: Object,
    title: String,
    data: Object,
});

// Fungsi untuk menampilkan notifikasi jika ada pesan flash
const toast = useToast();
const ShowToast = () => {
    if (props.flash.notif && props.flash.message) {
        toast.add({
            severity: props.flash.notif || "info",
            summary:
                props.flash.notif.charAt(0).toUpperCase() +
                props.flash.notif.slice(1),
            detail: props.flash.message,
            life: 4000,
        });
    }
};

// Cek notifikasi saat komponen di-mount
onMounted(() => {
    ShowToast();
});
</script>

<template>
    <TemplateAdmin :auth="props.auth" :title="props.title">
        <template #content>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <Button unstyled as="a" :href="route('kategori')">
                    <Card class="hover:translate-y-3 shadow-md transition-all">
                        <template #content>
                            <div
                                class="flex gap-4 justify-between items-center"
                            >
                                <div
                                    class="flex gap-4 justify-between items-center"
                                >
                                    <i
                                        class="pi pi-tags"
                                        style="font-size: 2rem; color: #4caf50"
                                    ></i>
                                    <h3 class="font-semibold">Kategori</h3>
                                </div>

                                <div
                                    class="ml-3 flex flex-col items-center justify-center"
                                >
                                    <h3 class="font-semibold">Total</h3>
                                    <p class="text-2xl">
                                        {{ props.data.categories }}
                                    </p>
                                </div>
                            </div>
                        </template>
                    </Card>
                </Button>

                <Button unstyled as="a" :href="route('products')">
                    <Card class="hover:translate-y-3 shadow-md transition-all">
                        <template #content>
                            <div
                                class="flex gap-4 justify-between items-center"
                            >
                                <div
                                    class="flex gap-4 justify-between items-center"
                                >
                                    <i
                                        class="pi pi-box"
                                        style="font-size: 2rem; color: #2196f3"
                                    ></i>
                                    <h3 class="font-semibold">Product</h3>
                                </div>

                                <div
                                    class="ml-3 flex flex-col items-center justify-center"
                                >
                                    <h3 class="font-semibold">Total</h3>
                                    <p class="text-2xl">
                                        {{ props.data.products }}
                                    </p>
                                </div>
                            </div>
                        </template>
                    </Card>
                </Button>

                <Button unstyled as="a" :href="route('orders')">
                    <Card class="hover:translate-y-3 shadow-md transition-all">
                        <template #content>
                            <div
                                class="flex gap-4 justify-between items-center"
                            >
                                <div
                                    class="flex gap-4 justify-between items-center"
                                >
                                    <i
                                        class="pi pi-shopping-cart"
                                        style="font-size: 2rem; color: #ff9800"
                                    ></i>
                                    <h3 class="font-semibold">Orders</h3>
                                </div>

                                <div
                                    class="ml-3 flex flex-col items-center justify-center"
                                >
                                    <h3 class="font-semibold">Total</h3>
                                    <p class="text-2xl">
                                        {{ props.data.orders }}
                                    </p>
                                </div>
                            </div>
                        </template>
                    </Card>
                </Button>

                <Button unstyled as="a" :href="route('transactions')">
                    <Card class="hover:translate-y-3 shadow-md transition-all">
                        <template #content>
                            <div
                                class="flex gap-4 justify-between items-center"
                            >
                                <div
                                    class="flex gap-4 justify-between items-center"
                                >
                                    <i
                                        class="pi pi-money-bill"
                                        style="font-size: 2rem; color: #9c27b0"
                                    ></i>
                                    <h3 class="font-semibold">Transaction</h3>
                                </div>

                                <div
                                    class="ml-3 flex flex-col items-center justify-center"
                                >
                                    <h3 class="font-semibold">Total</h3>
                                    <p class="text-2xl">
                                        {{ props.data.transactions }}
                                    </p>
                                </div>
                            </div>
                        </template>
                    </Card>
                </Button>

                <Button unstyled as="a">
                    <Card class="hover:translate-y-3 shadow-md transition-all">
                        <template #content>
                            <div
                                class="flex gap-4 justify-between items-center"
                            >
                                <div
                                    class="ml-3 flex items-center justify-between gap-4"
                                >
                                    <i
                                        class="pi pi-users"
                                        style="font-size: 2rem; color: #f44336"
                                    ></i>
                                    <h3 class="font-semibold">Costumer</h3>
                                </div>
                                <div
                                    class="ml-3 flex flex-col items-center justify-center"
                                >
                                    <h3 class="font-semibold">Total</h3>
                                    <p class="text-2xl">
                                        {{ props.data.customers }}
                                    </p>
                                </div>
                            </div>
                        </template>
                    </Card>
                </Button>
            </div>
        </template>
    </TemplateAdmin>
</template>

<style scoped>
.card {
    display: flex;
    align-items: center;
    padding: 1rem;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}
</style>
