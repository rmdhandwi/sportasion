<script setup>
import { ref } from 'vue'
import { 
    Button,
    Card,
    Dialog
} from 'primevue'
const showDialog = ref(false)

</script>

<template>
    <Dialog v-model:visible="showDialog" header="Daftar Produk">
       
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
            <Button severity="secondary" label="Pesan Sekarang" @click="showDialog=true"/>
            <Button severity="danger" label="Logout"/>
        </div>  
    </nav>
</template>

<style scoped>
</style>