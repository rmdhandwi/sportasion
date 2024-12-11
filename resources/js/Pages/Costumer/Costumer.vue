<script setup>
import { Head } from '@inertiajs/vue3'

import { 
    Button,
    Card 
} from 'primevue'

import contohImg from '../../../img/contoh-produk.webp'

const contohProduk = [
    {img : contohImg, nama : 'Produk 1'},
    {img : contohImg, nama : 'Produk 2'},
    {img : contohImg, nama : 'Produk 3'},
    {img : contohImg, nama : 'Produk 4'},
]

import Layout from '@/Layouts/TemplateCustomer.vue'
</script>

<template>
    <Head title="Home" />
    <Layout>
        <template #content>
            <section class="my-[4rem]">
                <h1 class="text-2xl">Daftar Produk</h1>
                <div class="grid grid-cols-3 gap-4 py-4">
                    <Card v-for="produk in contohProduk" :key="produk.nama" style="width: 18rem;overflow: hidden;">
                        <template #header>
                            <img :src="produk.img">
                        </template>
                        <template #title>
                            {{ produk.nama }}
                        </template>
                        <template #content>
                            <div class="flex items-center gap-2">
                                <Button severity="contrast" icon="pi pi-shopping-cart" size="small" />
                                <Button severity="danger" outlined icon="pi pi-heart" size="small" />
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