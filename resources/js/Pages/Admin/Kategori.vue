<script setup>
import { onMounted, ref, computed } from "vue";
import { router, useForm } from "@inertiajs/vue3";
import TemplateAdmin from "@/Layouts/TemplateAdmin.vue";
import {
    useToast,
    Toast,
    Card,
    Button,
    DataTable,
    Column,
    IconField,
    InputIcon,
    FloatLabel,
    Message,
    InputText,
    ConfirmDialog,
    useConfirm,
    Drawer,
} from "primevue";
import { FilterMatchMode } from "@primevue/core/api";

const props = defineProps({
    flash: Object,
    auth: Object,
    title: String,
    category: Object,
});

const dt = ref([]);
const categorydata = ref([]);
const showDrawer = ref(false);
const dtDrawer = ref("");

const addDrawer = () => {
    formAdd.reset();
    formAdd.clearErrors();
    showDrawer.value = true;
    dtDrawer.value = "add";
};

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

const exportCSV = () => {
    dt.value.exportCSV();
};

// Cek notifikasi saat komponen di-mount
onMounted(() => {
    categorydata.value = props.category.map((item, index) => ({
        index: index + 1,
        ...item,
    }));

    ShowToast();
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
            group: "tc",
        });
    }
};

// Menyimpan status loading saat merefresh halaman
const refrashLoading = ref(false);

// Fungsi untuk merefresh halaman
const refresh = () => {
    refrashLoading.value = true;
    return new Promise((resolve) => {
        router.visit(route("kategori"), {
            preserveScroll: true,
            onSuccess: () => {
                refrashLoading.value = false;
                resolve(); // Resolusi promise setelah refresh berhasil
            },
        });
    });
};

const activeForm = computed(() =>
    dtDrawer.value === "add" ? formAdd : formEdit
);

const formAdd = useForm({
    name: "",
});

const formEdit = useForm({
    id: "",
    name: "",
});

const editRow = (data) => {
    if (data) {
        formEdit.reset();
        formEdit.clearErrors();
        formEdit.name = data.name;
        formEdit.id = data.id;
        showDrawer.value = true;
        dtDrawer.value = "edit";
    }
};

// Fungsi untuk menangani pengiriman form add
const AddKategori = async () => {
    await formAdd.post(route("addKategori"), {
        onSuccess: async () => {
            await refresh(); // Tunggu hingga refresh selesai
            ShowToast(); // Tampilkan toast setelah refresh
        },
        onError: () => {
            ShowToast(); // Tampilkan toast jika ada error
        },
    });
};

// Fungsi untuk menangani pengiriman form edit
const EditKategori = async () => {
    await formEdit.put(route("editKategori", formEdit.id), {
        onSuccess: async () => {
            showDrawer.value = false;
            await refresh(); // Tunggu hingga refresh selesai
            ShowToast(); // Tampilkan toast setelah refresh
        },
        onError: () => {
            showDrawer.value = true;
            ShowToast(); // Tampilkan toast jika ada error
        },
    });
};

const confirm = useConfirm();
const deleteData = (data) => {
    confirm.require({
        message: `Yakin ingin menghapus data record ${data.name}?`,
        header: "Peringatan",
        icon: "pi pi-info-circle",
        rejectLabel: "Cancel",
        rejectProps: {
            label: "Cancel",
            severity: "secondary",
            outlined: true,
        },
        acceptProps: {
            label: "Delete",
            severity: "danger",
        },
        accept: async () => {
            await router.delete(route("deleteKategori", data.id), {
                onSuccess: async () => {
                    await refresh(); // Tunggu hingga refresh selesai
                    ShowToast(); // Tampilkan toast setelah refresh
                },
                onError: () => {
                    ShowToast(); // Tampilkan toast jika ada error
                },
            });
        },
        reject: () => {
            toast.add({
                severity: "error",
                summary: "Batal",
                detail: "Batal menghapus data ",
                life: 3000,
                group: "tc",
            });
        },
    });
};
</script>

<template>
    <TemplateAdmin :auth="props.auth" :title="props.title">
        <template #content>
            <Toast group="tc" />
            <ConfirmDialog></ConfirmDialog>
            <div class="flex justify-between items-center mb-4">
                <h1 class="font-semibold text-xl">Daftar {{ props.title }}</h1>
                <Button
                    unstyled
                    label="Tambah data"
                    class="px-2 py-1 flex gap-2 hover:translate-x-2 items-center bg-gray-800 text-white hover:bg-gray-700 transition-all rounded-md"
                    size="small"
                    @click="addDrawer"
                >
                    <i class="pi pi-plus-circle"></i>
                    <span>Tambah data</span>
                </Button>
            </div>

            <!-- tabele -->
            <Card>
                <template #content>
                    <DataTable
                        removableSort
                        v-model:filters="filters"
                        ref="dt"
                        :value="categorydata"
                        paginator
                        :rowsPerPageOptions="[5, 10, 20, 50, 100]"
                        :rows="10"
                    >
                        <template #header>
                            <div class="flex items-center justify-between">
                                <Button
                                    unstyled
                                    class="px-2 py-1 flex gap-2 items-center bg-gray-800 text-white hover:bg-gray-700 transition-all hover:translate-x-2 rounded-md"
                                    @click="exportCSV($event)"
                                    size="small"
                                >
                                    <i class="pi pi-file"></i>
                                    <span>Export</span>
                                </Button>
                                <IconField>
                                    <InputIcon>
                                        <i class="pi pi-search me-4" />
                                    </InputIcon>
                                    <InputText
                                        v-model="filters['global'].value"
                                        placeholder="Cari Data Kriteria"
                                    />
                                </IconField>
                            </div>
                        </template>
                        <template #empty>
                            <span
                                class="flex justify-center items-center text-lg"
                                >Tidak ada data</span
                            >
                        </template>
                        <Column sortable field="index" header="No" />
                        <Column sortable field="name" header="Nama Kategori" />
                        <Column header="Opsi">
                            <template #body="{ data }">
                                <div class="flex gap-2 items-center">
                                    <Button
                                        size="small"
                                        @click="editRow(data)"
                                        icon="pi pi-pen-to-square"
                                        iconPos="right"
                                        severity="info"
                                        outlined
                                    />
                                    <Button
                                        size="small"
                                        @click="deleteData(data)"
                                        icon="pi pi-trash"
                                        iconPos="right"
                                        severity="danger"
                                        outlined
                                    />
                                </div>
                            </template>
                        </Column>
                    </DataTable>
                </template>
            </Card>
            <!-- endtabel -->

            <!-- drawer -->
            <Drawer
                v-model:visible="showDrawer"
                :header="
                    dtDrawer == 'add' ? 'Tambah Kategori' : 'Edit Kategori'
                "
                position="right"
            >
                <form
                    @submit.prevent="
                        dtDrawer == 'add' ? AddKategori() : EditKategori()
                    "
                >
                    <div class="grid grid-cols-1 gap-4">
                        <div class="mt-2">
                            <FloatLabel variant="on">
                                <InputText
                                    fluid
                                    id="nama"
                                    v-model="activeForm.name"
                                    :invalid="
                                        dtDrawer == 'add'
                                            ? !!formAdd.errors.name
                                            : !!formEdit.errors.name
                                    "
                                />
                                <label for="nama">Nama Kategori</label>
                            </FloatLabel>
                            <Message
                                v-if="
                                    dtDrawer == 'add'
                                        ? formAdd.errors.name
                                        : formEdit.errors.name
                                "
                                severity="error"
                                variant="simple"
                                size="small"
                            >
                                {{
                                    dtDrawer == "add"
                                        ? formAdd.errors.name
                                        : formEdit.errors.name
                                }}
                            </Message>
                        </div>

                        <Button
                            unstyled
                            class="p-2 bg-gray-800 text-white flex justify-center items-center gap-2 hover:bg-gray-700 transition rounded-md"
                            type="submit"
                        >
                            <i class="pi pi-save"></i>
                            <span>Simpan</span>
                        </Button>
                    </div>
                </form>
            </Drawer>
            <!-- end drawer -->
        </template>
    </TemplateAdmin>
</template>
