<script setup>
import { Head, router, useForm } from "@inertiajs/vue3";
import { onMounted, provide, ref } from "vue";

import {
    Button,
    Card,
    useConfirm,
    FloatLabel,
    InputNumber,
    Toast,
    useToast,
} from "primevue";

import Layout from "@/Layouts/TemplateCustomer.vue";

onMounted(() => {
    props.dataProduk.forEach((produk) => {
        jmlhPesan.value[produk.id] = 1;
    });

    ShowToast();
});

const props = defineProps({
    dataKategori: Object,
    dataTransaksi: Object,
    dataProduk: Object,
    dataCart: Object,
    flash: Object,
});

const dataCart = props.dataCart;
const dataKategori = props.dataKategori;
const dataTransaksi = props.dataTransaksi;

provide("dataProps", { dataCart, dataKategori, dataTransaksi });

const confirm = useConfirm();

const formCart = useForm({
    id_product: null,
    quantity: null,
});

const jmlhPesan = ref({});

// Fungsi untuk menampilkan notifikasi jika ada pesan flash
const toast = useToast();
const ShowToast = () => {
    if (props.flash.notif && props.flash.message) {
        toast.add({
            severity: props.flash.notif || "info", // Gunakan 'info' sebagai default jika tidak ada notif
            summary:
                props.flash.notif.charAt(0).toUpperCase() +
                props.flash.notif.slice(1), // Kapitalisasi huruf pertama
            detail: props.flash.message,
            life: 4000,
            // group: "tc",
        });
    }
};

// Menyimpan status loading saat merefresh halaman
const refrashLoading = ref(false);

// Fungsi untuk merefresh halaman
const refresh = () => {
    refrashLoading.value = true;
    formCart.reset();
    return new Promise((resolve) => {
        router.visit(route("costumerPage"), {
            preserveScroll: true,
            onSuccess: () => {
                refrashLoading.value = false;
                resolve(); // Resolusi promise setelah refresh berhasil
            },
        });
    });
};

const addCart = (id) => {
    formCart.id_product = id;
    formCart.quantity = jmlhPesan.value[id];

    confirm.require({
        message: "Tambahkan Produk Ke Dalam Keranjang ?",
        header: "Peringatan",
        icon: "pi pi-exclamation-triangle",
        rejectProps: {
            label: "Batal",
            severity: "secondary",
            outlined: true,
        },
        acceptProps: {
            label: "Tambah",
            severity: "info",
        },
        accept: async () => {
            await formCart.post(route("cart.add"), {
                onSuccess: async () => {
                    await refresh();
                    ShowToast();
                },
                onError: () => {
                    toast.add({
                        severity: "error",
                        summary: "Notifikasi",
                        detail: "Gagal menambahkan keranjang",
                        life: 4000,
                        group: "tc",
                    });
                },
            });
        },
    });
};

const formatRupiah = (angka) => {
    if (typeof angka !== "number") {
        angka = parseFloat(angka);
        if (isNaN(angka)) return "Rp 0";
    }

    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(angka);
};
</script>

<template>
    <Head title="Home" />
    <Layout :flash="props.flash">
        <template #content>
            <Toast position="top-center" />

            <section class="my-[4rem]">
                <h1 class="text-2xl" v-if="dataProduk?.length > 0">
                    Daftar Produk
                </h1>
                <div class="flex justify-center items-center h-[70vh]" v-else>
                    <h1 class="text-2xl">Tidak ada produk</h1>
                </div>
                <div class="grid grid-cols-3 gap-4 py-4">
                    <Card
                        v-for="produk in dataProduk"
                        :key="produk.name"
                        style="width: 18rem; overflow: hidden"
                    >
                        <template #header>
                            <img :src="produk.image" />
                        </template>
                        <template #title>
                            {{ produk.name }}
                        </template>
                        <template #content>
                            <div class="flex flex-col">
                                <span class="text-sm">{{
                                    produk.categories.name
                                }}</span>
                                <span>{{ formatRupiah(produk.price) }}</span>
                            </div>
                            <div class="flex items-center gap-2 mt-2">
                                <Button
                                    @click="addCart(produk.id)"
                                    severity="contrast"
                                    icon="pi pi-shopping-cart"
                                    size="small"
                                />
                                <FloatLabel variant="on">
                                    <InputNumber
                                        v-model="jmlhPesan[produk.id]"
                                        size="small"
                                        inputId="minmax-buttons"
                                        mode="decimal"
                                        showButtons
                                        :min="1"
                                        :max="produk.stock"
                                        fluid
                                    />
                                    <label for="on_label">Jumlah Pesan</label>
                                </FloatLabel>
                            </div>
                        </template>
                    </Card>
                </div>
            </section>
        </template>
    </Layout>
</template>

<style scoped></style>
