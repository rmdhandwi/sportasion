<script setup>
import { ConfirmDialog } from "primevue";
import Navbar from "@/Components/Navbar.vue";

const props = defineProps({
    flash: Object,
});
</script>

<template>
    <div class="flex flex-col min-h-screen overflow-hidden">
        <!-- Navbar -->
        <Navbar :flash="props.flash" />
        <ConfirmDialog />
        <!-- slot -->
        <div class="w-full p-4 min-h-screen">
            <slot name="content" />
        </div>
    </div>
</template>
