<script setup>
import { Head, router, useForm } from '@inertiajs/vue3'
import { onMounted, provide, ref } from 'vue'

import { 
    Button,
    Card,
    useConfirm,
    FloatLabel,
    InputNumber,
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
    dataKategori : Object,
    dataTransaksi : Object,
    dataProduk : Object,
    dataCart : Object,
    flash : Object,
})

const dataCart = props.dataCart
const dataKategori = props.dataKategori
const dataTransaksi = props.dataTransaksi

provide('dataProps',{dataCart, dataKategori, dataTransaksi })

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

            <section class="my-[4rem]">
                <h1 class="text-2xl" v-if="dataProduk.length>0">Daftar Produk</h1>
                <div class="flex justify-center items-center h-[70vh]"v-else>
                    <h1 class="text-2xl">Tidak ada produk</h1>
                </div>
                <div class="grid grid-cols-3 gap-4 py-4">
                    <Card v-for="produk in dataProduk" :key="produk.name" style="width: 18rem;overflow: hidden;">
                        <template #header>
                            <img :src="produk.image">
                        </template>
                        <template #title>
                            {{ produk.name }}
                        </template>
                        <template #content>
                            <div class="flex flex-col">
                                <span class="text-sm">{{ produk.categories.name }}</span>
                                <span>{{ formatRupiah(produk.price) }}</span>
                            </div>
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
