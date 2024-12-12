<script setup>
import { Head, router, useForm } from '@inertiajs/vue3'
import { onMounted, provide, ref } from 'vue'

import { 
    Button,
    Card,
    useConfirm,
    Dialog,
    FloatLabel,
    InputNumber,
    InputText,
    Toast,
    useToast
} from 'primevue'


import Layout from '@/Layouts/TemplateCustomer.vue'

onMounted(() =>
{   
    checkNotif()
    props.dataProduk.forEach((produk) => {
    jmlhPesan.value[produk.id] = 1
    })
})

const props = defineProps({
    dataProduk : Object,
    dataCart : Object,
    flash : Object,
})

provide('dataCartUser', props.dataCart)

const confirm = useConfirm()
const toast = useToast()

const formCart = useForm({
    id_product : null,
    quantity : null,
})

const jmlhPesan = ref({})

const showDialog = ref(false)

const checkNotif = () =>
{
    if(props.flash.notif)
    {
        setTimeout(()=> 
            toast.add({
                severity : props.flash.notif,
                summary : 'notifikasi',
                detail : props.flash.message,
                life : 4000,
                group : 'tc'
            })   
        ,1000)
    }
}

const refreshPage = () => 
{
    formCart.reset()
    checkNotif()
    router.visit(route('costumerPage'))
}

const addCart = id =>
{
    // showDialog.value = true
    formCart.id_product = id 
    formCart.quantity = jmlhPesan.value[id]

    confirm.require({
        message: 'Tambahkan Produk Ke Dalam Keranjang ?',
        header: 'Peringatan',
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: 'Batal',
            severity: 'secondary',
            outlined: true,
        },
        acceptProps: {
            label: 'Tambah',
            severity: 'info'
        },
        accept: () => {
            formCart.post(route('cart.add'), {
                onSuccess : () => refreshPage(),
                onError : () => { 
                    toast.add({
                        severity : 'error',
                        summary : 'notifikasi',
                        detail : 'Gagal menambahkan keranjang',
                        life : 4000,
                        group : 'tc'
                    })
                } 
            })
        },
    })


}

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
    <Head title="Home"/>
    <Layout>
        <template #content>
            <Toast position="top-center" group="tc" />
            <!--Dialog keranjang  -->
            <!-- <Dialog v-model:visible="showDialog" header="Tambah Produk Ke Keranjang" class="w-[52rem]">
                <form @submit.prevent class="flex gap-4" autocomplete="off">
                    <div class="w-1/2 overflow-hidden rounded-lg">
                        <img :src="formCart.img_product" class="size-full">
                    </div>
                    <div class="flex flex-col gap-8 my-1 w-1/2">
                        <div class="flex flex-col w-full">
                            <FloatLabel variant="on">
                                <InputText disabled fluid class="w-full" inputId="custom" v-model="formCart.nama_product"/>
                                <label for="custom">Nama Produk</label>
                            </FloatLabel>
                        </div>
                        <div class="flex flex-col w-full">
                            <FloatLabel variant="on">
                                <InputNumber v-model="formCart.quantity" inputId="minmax-buttons" mode="decimal" showButtons :min="1" :max="100" fluid />
                                <label for="on_label">Jumlah Pesan</label>
                            </FloatLabel>
                        </div>
                        <div class="flex flex-col w-full">
                            <FloatLabel variant="on">
                                <InputNumber disabled :model-value="formCart.price * formCart.quantity" inputId="on_label" mode="currency" currency="IDR" locale="id-ID" fluid />
                                <label for="on_label">Total Bayar</label>
                            </FloatLabel>
                        </div>
                    </div>
                </form>
            </Dialog> -->

            <section class="my-[4rem]">
                <h1 class="text-2xl">Daftar Produk</h1>
                <div class="grid grid-cols-3 gap-4 py-4">
                    <Card v-for="produk in dataProduk" :key="produk.name" style="width: 18rem;overflow: hidden;">
                        <template #header>
                            <img :src="produk.image">
                        </template>
                        <template #title>
                            {{ produk.name }}
                        </template>
                        <template #content>
                            <span>{{ formatRupiah(produk.price) }}</span>
                            <div class="flex items-center gap-2 mt-2">
                                <Button @click="addCart(produk.id)" severity="contrast" icon="pi pi-shopping-cart" size="small" />
                                <FloatLabel variant="on">
                                    <InputNumber v-model="jmlhPesan[produk.id]" size="small" inputId="minmax-buttons" mode="decimal" showButtons :min="1" :max="produk.stock" fluid />
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

<style scoped>
</style>
