<script setup>
import { inject, onMounted, ref } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import { 
    Button,
    Dialog,
    FloatLabel,
    InputNumber,
    InputText,
    Tag,
    Toast,useToast,
    useConfirm
} from 'primevue'

onMounted(()=>
{
    dataCart.forEach((produk) => {
        jmlhPesan.value[produk.id] = produk.order_details[0]['quantity']
    })
})
const showDialog = ref(false)

const confirm = useConfirm()
const toast = useToast()

const {dataCart, dataKategori} = inject('dataProps')

const jmlhPesan = ref({})

const formCart = useForm({
    id_cart : null,
    quantity : null,
})

const formatTanggal = tanggal =>
{
    const format = new Date(tanggal)

    return `${format.getDate()}/${format.getMonth()}/${format.getFullYear()}`
}

const refreshPage = () =>
{
    setTimeout(() => router.visit(route('costumerPage')) ,500)
}

const updateCart = (id,namaProduk) => 
{
    
    formCart.id_cart = id 
    formCart.quantity = jmlhPesan.value[id]

    confirm.require({
        message: `Update Keranjang Produk : ${namaProduk} ?`,
        header: 'Peringatan',
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: 'Batal',
            severity: 'secondary',
            outlined: true,
        },
        acceptProps: {
            label: 'Update',
            severity: 'info'
        },
        accept: () => {
            showDialog.value = false

            formCart.post(route('cart.update'), {
                onSuccess : refreshPage(),
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

const hapusCart = (id,namaProduk) => 
{
    
    formCart.id_cart = id 
    formCart.quantity = jmlhPesan.value[id]

    confirm.require({
        message: `Hapus Keranjang Produk : ${namaProduk} ?`,
        header: 'Peringatan',
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: 'Batal',
            severity: 'secondary',
            outlined: true,
        },
        acceptProps: {
            label: 'Hapus dari keranjang',
            severity: 'danger'
        },
        accept: () => {
            showDialog.value = false

            formCart.post(route('cart.hapus'), {
                onSuccess : refreshPage(),
                onError : () => { 
                    toast.add({
                        severity : 'error',
                        summary : 'notifikasi',
                        detail : 'Gagal menghapus keranjang',
                        life : 4000,
                        group : 'tc'
                    })
                } 
            })
        },
    })
}

const pesanCart = (id,namaProduk) => 
{
    
    formCart.id_cart = id 
    formCart.quantity = jmlhPesan.value[id]

    confirm.require({
        message: `Pesan Produk : ${namaProduk} ?`,
        header: 'Peringatan',
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: 'Batal',
            severity: 'secondary',
            outlined: true,
        },
        acceptProps: {
            label: 'Pesan',
            severity: 'success'
        },
        accept: () => {
            showDialog.value = false

            formCart.post(route('cart.pesan'), {
                onSuccess : refreshPage(),
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

const batalPesan = (id,namaProduk) => 
{
    
    formCart.id_cart = id 
    formCart.quantity = jmlhPesan.value[id]

    confirm.require({
        message: `Batalkan Pesanan Produk : ${namaProduk} ?`,
        header: 'Peringatan',
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: 'Tidak',
            severity: 'secondary',
            outlined: true,
        },
        acceptProps: {
            label: 'Batalkan Pesanan',
            severity: 'danger'
        },
        accept: () => {
            showDialog.value = false

            formCart.post(route('cart.pesan.batal'), {
                onSuccess : refreshPage(),
                onError : () => { 
                    toast.add({
                        severity : 'error',
                        summary : 'notifikasi',
                        detail : 'Gagal membatalkan pesanan',
                        life : 4000,
                        group : 'tc'
                    })
                } 
            })
        },
    })
}

const kategoriPage = idKategori => {
    // useForm({id : idKategori}).post(route('kategori.page'),{
    //     onError : () => refreshPage()
    // })
    useForm({id : idKategori}).get(route('kategori.page'),{
        onError : () => refreshPage()
    })
    // router.visit('produk/kategori/'+idKategori)
}

const dialogHide = () =>  dataCart.forEach((produk) => {
    jmlhPesan.value[produk.id] = produk.order_details[0]['quantity']
})

const logout = () =>
{
    confirm.require({
        message: `Yakin ingin logout ?`,
        header: 'Peringatan',
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: 'Batal',
            severity: 'secondary',
            outlined: true,
        },
        acceptProps: {
            label: 'Logout',
            severity: 'danger'
        },
        accept: () => {
            showDialog.value = false
            router.post(route("logout"))
        },
    })
}

</script>

<template>
    <Toast position="top-center" group="tc" />
    
    <Dialog v-model:visible="showDialog" :header="`Keranjang Anda (${dataCart.length.toString()})`" class="w-[52rem]" modal v-on:hide="dialogHide()">
        <form @submit.prevent class="flex gap-4 w-full my-8" autocomplete="off" v-for="cart in dataCart" :key="cart.id">
            <div class="flex flex-col w-[75%] gap-2">
                <div class="overflow-hidden rounded-lg">
                    <img :src="cart.order_details[0]['product']['image']" class="size-full">
                </div>
                <Tag v-if="cart.status===1" value="Menunggu Konfirmasi Admin" severity="warn"/>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <div class="flex flex-col w-[16rem]">
                    <FloatLabel variant="on">
                        <InputText disabled fluid class="w-full" inputId="custom" :modelValue="cart.order_details[0]['product']['name']"/>
                        <label for="custom">Nama Produk</label>
                    </FloatLabel>
                </div>
                <div class="flex flex-col w-[16rem]">
                    <FloatLabel variant="on">
                        <InputText disabled fluid class="w-full" inputId="custom" :modelValue="formatTanggal(cart.order_date)"/>
                        <label for="custom">Tanggal Order</label>
                    </FloatLabel>
                </div>
                <div class="flex flex-col w-[16rem]">
                    <FloatLabel variant="on">
                        <InputNumber v-model="jmlhPesan[cart.id]" :disabled="cart.status===1" inputId="minmax-buttons" mode="decimal" showButtons :min="1" :max="100" fluid />
                        <label for="on_label">Jumlah Pesan</label>
                    </FloatLabel>
                </div>
                <div class="flex flex-col w-[16rem]">
                    <FloatLabel variant="on">
                        <InputNumber disabled :modelValue="cart.order_details[0]['product']['price'] * jmlhPesan[cart.id]" inputId="on_label" mode="currency" currency="IDR" locale="id-ID" fluid />
                        <label for="on_label">Total Bayar</label>
                    </FloatLabel>
                </div>
                <div class="flex items-center gap-x-2">
                    <Button label="Update" :disabled="jmlhPesan[cart.id] === cart.order_details[0]['quantity']" severity="info" @click="updateCart(cart.id,cart.order_details[0]['product']['name'])"/>
                    <Button v-if="cart.status===2" label="Pesan Sekarang" severity="success" @click="pesanCart(cart.id,cart.order_details[0]['product']['name'])"/>
                    <Button v-if="cart.status===2" label="Hapus" severity="danger" @click="hapusCart(cart.id,cart.order_details[0]['product']['name'])" outlined/>
                    <Button v-if="cart.status===1" label="Batalkan Pesanan" severity="danger" @click="batalPesan(cart.id,cart.order_details[0]['product']['name'])"/>
                </div>
            </div>
        </form>
    </Dialog>

    <nav class="w-full h-[4rem] bg-slate-700 text-white flex justify-between items-center px-4 fixed top-0">
        <!-- brand -->
        <span class="text-2xl">Sportasion</span>

        <!-- menu -->
        <div class="flex items-center gap-x-4">
            <Button label="Semua" @click="router.visit(route('costumerPage'))" unstyled/>
            <Button v-for="kategori in dataKategori" :label="kategori.name" @click="kategoriPage(kategori.id)" unstyled/>
        </div>
        
        <!-- logout -->
        <div class="flex items-center gap-x-4">
            <Button severity="secondary" icon="pi pi-shopping-cart" :badge="dataCart.length.toString()" badgeSeverity="success" label="Keranjang" :disabled="dataCart.length < 1" @click="showDialog=true"/>
            <Button severity="danger" label="Logout" @click="logout"/>
        </div>  
    </nav>
</template>

<style scoped>
</style>