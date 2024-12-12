<script setup>
import { inject, onMounted, ref } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import { 
    Button,
    Card,
    Dialog,
    FloatLabel,
    InputNumber,
    InputText,
    Toast,useToast,
    useConfirm
} from 'primevue'

onMounted(()=>
{
    // dataCart.value = inject('dataCartUser')
    // checkNotif()
    dataCart.forEach((produk) => {
    jmlhPesan.value[produk.id] = produk.order_details[0]['quantity']
    })
})

const showDialog = ref(false)

const confirm = useConfirm()
const toast = useToast()

const dataCart = inject('dataCartUser')

const jmlhPesan = ref({})

const formCart = useForm({
    id_cart : null,
    quantity : null,
})

const refreshPage = () =>
{
    formCart.reset()
    toast.add({
        severity : 'success',
        summary : 'notifikasi',
        detail : 'Berhasil Update Keranjang',
        life : 4000,
        group : 'tc'
    })
    router.visit(route('customerPage'))
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

const dialogHide = () =>  dataCart.forEach((produk) => {
    jmlhPesan.value[produk.id] = produk.order_details[0]['quantity']
    })

</script>

<template>
    <Toast position="top-center" group="tc" />
    
    <Dialog v-model:visible="showDialog" :header="`Keranjang Anda (${dataCart.length.toString()})`" class="w-[52rem]" modal v-on:hide="dialogHide()">
        <form @submit.prevent class="flex gap-4 w-full my-8" autocomplete="off" v-for="cart in dataCart" :key="cart.id">
            <div class="w-1/2 overflow-hidden rounded-lg">
                <img :src="cart.order_details[0]['product']['image']" class="size-full">
            </div>
            <div class="flex flex-col gap-8 my-1 w-full">
                <div class="flex flex-col w-full">
                    <FloatLabel variant="on">
                        <InputText disabled fluid class="w-full" inputId="custom" :modelValue="cart.order_details[0]['product']['name']"/>
                        <label for="custom">Nama Produk</label>
                    </FloatLabel>
                </div>
                <div class="flex flex-col w-full">
                    <FloatLabel variant="on">
                        <InputNumber v-model="jmlhPesan[cart.id]" inputId="minmax-buttons" mode="decimal" showButtons :min="1" :max="100" fluid />
                        <label for="on_label">Jumlah Pesan</label>
                    </FloatLabel>
                </div>
                <div class="flex flex-col w-full">
                    <FloatLabel variant="on">
                        <InputNumber disabled :modelValue="cart.order_details[0]['product']['price'] * jmlhPesan[cart.id]" inputId="on_label" mode="currency" currency="IDR" locale="id-ID" fluid />
                        <label for="on_label">Total Bayar</label>
                    </FloatLabel>
                </div>
                <div class="flex items-center gap-x-2">
                    <Button label="Update" :disabled="jmlhPesan[cart.id] === cart.order_details[0]['quantity']" severity="info" @click="updateCart(cart.id,cart.order_details[0]['product']['name'])"/>
                    <Button label="Pesan Sekarang"/>
                    <Button label="Hapus" severity="danger"/>
                </div>
            </div>
        </form>
    </Dialog>

    <nav class="w-full h-[4rem] bg-slate-700 text-white flex justify-between items-center px-2 fixed top-0">
        <!-- brand -->
        <span class="text-2xl">Sportasion</span>

        <!-- menu -->
        <div class="flex items-center gap-x-4">
            <Button as="a" href="#" label="Home" unstyled/>
            <Button as="a" href="#" label="Product" unstyled/>
            <Button as="a" href="#" label="Order" unstyled/>
        </div>
        
        <!-- logout -->
        <div class="flex items-center gap-x-4">
            <Button severity="secondary" icon="pi pi-shopping-cart" :badge="dataCart.length.toString()" badgeSeverity="success" label="Keranjang" @click="showDialog=true"/>
            <Button severity="danger" label="Logout"/>
        </div>  
    </nav>
</template>

<style scoped>
</style>