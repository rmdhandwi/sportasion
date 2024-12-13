<script setup>
import { onMounted, ref } from "vue";
import { Button, useToast, Toast } from "primevue";
import { Head, router } from "@inertiajs/vue3";
import TemplateAdmin from "@/Layouts/TemplateAdmin.vue";

const props = defineProps({
    flash: Object,
    auth: Object,
    title: String,
});

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

// Cek notifikasi saat komponen di-mount
onMounted(() => {
    ShowToast();
});
</script>

<template>
    <TemplateAdmin :auth="props.auth" :title="props.title">
        <template #content>
            <Toast />
            <h2>Ini Costumer</h2>
            <Button label="logout" icon="pi pi-door-close" @click="Logout" />
        </template>
    </TemplateAdmin>
</template>
