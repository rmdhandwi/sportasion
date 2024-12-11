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
    Select,
    InputText,
    ConfirmDialog,
    InputNumber,
    FileUpload,
    useConfirm,
    Dialog,
    Image,
} from "primevue";
import { FilterMatchMode } from "@primevue/core/api";

const props = defineProps({
    title: String,
    auth: Object,
    flash: Object,
    categories: Array,
    products: Object,
});

const dt = ref([]);
const productsdata = ref([]);
const showDialog = ref(false);
const dataShow = ref("");

// Cek notifikasi saat komponen di-mount
onMounted(() => {
    productsdata.value = props.products.map((item, index) => ({
        index: index + 1,
        ...item,
    }));

    ShowToast();
});

// Inisialisasi previewImg menggunakan ref
const previewImg = ref(null);

function onFileSelect(event) {
    const file = event.files[0];
    formProducts.image = file;
    const reader = new FileReader();

    reader.onloadend = async (e) => {
        previewImg.value = e.target.result;
    };

    reader.readAsDataURL(file);
}

const formProducts = useForm({
    id: "",
    id_category: "",
    name: "",
    description: "",
    price: null,
    stock: null,
    image: null,
});

const addDialog = () => {
    formProducts.reset();
    formProducts.clearErrors();
    showDialog.value = true;
    dataShow.value = "add";
    previewImg.value = null;
};

const editDialog = (data) => {
    formProducts.reset();
    formProducts.clearErrors();
    formProducts.id = data.id;
    formProducts.id_category = data.id_category;
    formProducts.name = data.name;
    formProducts.description = data.description;
    formProducts.price = data.price;
    formProducts.stock = data.stock;
    previewImg.value = data.image || null;
    showDialog.value = true;
    dataShow.value = "edit";
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

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

const exportCSV = () => {
    dt.value.exportCSV();
};

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
        router.visit(route("products"), {
            preserveScroll: true,
            onSuccess: () => {
                refrashLoading.value = false;
                resolve(); // Resolusi promise setelah refresh berhasil
            },
        });
    });
};

// Fungsi untuk menangani pengiriman form add
const AddProduuct = async () => {
    await formProducts.post(route("addProduct"), {
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
const EditProduct = async () => {
    await formProducts.post(route("editProduct", formProducts.id), {
        onSuccess: async () => {
            showDialog.value = false;
            await refresh(); // Tunggu hingga refresh selesai
            ShowToast(); // Tampilkan toast setelah refresh
        },
        onError: () => {
            showDialog.value = true;
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
            await router.delete(route("deleteProduct", data.id), {
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
                    @click="addDialog"
                >
                    <i class="pi pi-plus-circle"></i>
                    <span>Tambah data</span>
                </Button>
            </div>

            <!-- Dialog -->
            <Dialog
                v-model:visible="showDialog"
                modal
                :header="
                    dataShow === 'add' ? 'Tambah Data Product' : 'Edit Data'
                "
                :style="{ width: '40vw' }"
                :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
            >
                <form
                    @submit.prevent="
                        dataShow === 'add' ? AddProduuct() : EditProduct()
                    "
                >
                    <div class="space-y-4">
                        <!-- name -->
                        <div class="mt-2">
                            <FloatLabel variant="on">
                                <InputText
                                    id="name"
                                    v-model="formProducts.name"
                                    class="w-full"
                                    :invalid="
                                        dataShow === 'add'
                                            ? !!formProducts.errors.name
                                            : !!formProducts.errors.name
                                    "
                                />
                                <label for="name">Nama Produk</label>
                            </FloatLabel>
                            <Message
                                v-if="
                                    dataShow === 'add'
                                        ? formProducts.errors.name
                                        : formProducts.errors.name
                                "
                                severity="error"
                                size="small"
                                variant="simple"
                            >
                                {{
                                    dataShow == "add"
                                        ? formProducts.errors.name
                                        : formProducts.errors.name
                                }}
                            </Message>
                        </div>

                        <!-- category -->
                        <div class="mt-2">
                            <FloatLabel class="w-full" variant="on">
                                <Select
                                    v-model="formProducts.id_category"
                                    inputId="kategori"
                                    :options="props.categories"
                                    optionLabel="name"
                                    optionValue="id"
                                    class="w-full"
                                    :invalid="
                                        dataShow === 'add'
                                            ? !!formProducts.errors.id_category
                                            : !!formProducts.errors.id_category
                                    "
                                    fluid
                                />
                                <label for="kategori">Kategori</label>
                            </FloatLabel>
                            <Message
                                v-if="
                                    dataShow === 'add'
                                        ? formProducts.errors.id_category
                                        : formProducts.errors.id_category
                                "
                                severity="error"
                                size="small"
                                variant="simple"
                            >
                                {{
                                    dataShow == "add"
                                        ? formProducts.errors.id_category
                                        : formProducts.errors.id_category
                                }}
                            </Message>
                        </div>

                        <!-- price -->
                        <div class="mt-2">
                            <FloatLabel variant="on">
                                <InputNumber
                                    v-model="formProducts.price"
                                    inputId="harga"
                                    mode="currency"
                                    currency="IDR"
                                    locale="id-ID"
                                    fluid
                                    :invalid="
                                        dataShow === 'add'
                                            ? !!formProducts.errors.price
                                            : !!formProducts.errors.price
                                    "
                                />
                                <label for="harga">Harga</label>
                            </FloatLabel>
                            <Message
                                v-if="
                                    dataShow === 'add'
                                        ? formProducts.errors.price
                                        : formProducts.errors.price
                                "
                                severity="error"
                                size="small"
                                variant="simple"
                            >
                                {{
                                    dataShow == "add"
                                        ? formProducts.errors.price
                                        : formProducts.errors.price
                                }}
                            </Message>
                        </div>

                        <!-- Stock -->
                        <div class="mt-2">
                            <FloatLabel variant="on">
                                <InputNumber
                                    v-model="formProducts.stock"
                                    inputId="stok"
                                    fluid
                                    :invalid="
                                        dataShow === 'add'
                                            ? !!formProducts.errors.stock
                                            : !!formProducts.errors.stock
                                    "
                                />
                                <label for="stok">Stok</label>
                            </FloatLabel>
                            <Message
                                v-if="
                                    dataShow === 'add'
                                        ? formProducts.errors.stock
                                        : formProducts.errors.stock
                                "
                                severity="error"
                                size="small"
                                variant="simple"
                            >
                                {{
                                    dataShow == "add"
                                        ? formProducts.errors.stock
                                        : formProducts.errors.stock
                                }}
                            </Message>
                        </div>

                        <div class="mt-2">
                            <div class="card flex flex-col items-center gap-4">
                                <FileUpload
                                    mode="basic"
                                    @select="onFileSelect"
                                    customUpload
                                    auto
                                    severity="secondary"
                                    class="p-button-outlined"
                                />
                                <Message
                                    v-if="
                                        dataShow === 'add'
                                            ? formProducts.errors.image
                                            : formProducts.errors.image
                                    "
                                    severity="error"
                                    size="small"
                                    variant="simple"
                                >
                                    {{
                                        dataShow == "add"
                                            ? formProducts.errors.image
                                            : formProducts.errors.image
                                    }}
                                </Message>
                                <img
                                    v-if="previewImg"
                                    :src="previewImg"
                                    alt="Image"
                                    class="shadow-md rounded-xl w-full sm:w-64"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end items-center gap-2 mt-3">
                        <Button
                            unstyled
                            @click="showDialog = false"
                            class="py-1 px-2 border border-red-700 flex text-red-700 items-center gap-2 hover:bg-red-700 hover:text-white transition-all rounded-md"
                        >
                            <i class="pi pi-times"></i>
                            <span>Batal</span>
                        </Button>
                        <Button
                            unstyled
                            type="submit"
                            class="py-1 px-2 bg-gray-800 flex items-center gap-2 text-white hover:bg-gray-700 transition-all rounded-md"
                        >
                            <i class="pi pi-save"></i>
                            <span>Simpan</span>
                        </Button>
                    </div>
                </form>
            </Dialog>
            <!-- EndDialog -->

            <!-- tabele -->
            <Card>
                <template #content>
                    <DataTable
                        removableSort
                        v-model:filters="filters"
                        ref="dt"
                        :value="productsdata"
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
                        <Column sortable field="name" header="Products" />
                        <Column
                            sortable
                            field="categories.name"
                            header="Nama Kategori"
                        />
                        <Column header="Harga">
                            <template #body="{ data }">
                                {{ formatRupiah(data.price) }}
                            </template>
                        </Column>
                        <Column header="Stock" field="stock" />
                        <Column header="Gambar">
                            <template #body="{ data }">
                                <Image
                                    v-if="data.image"
                                    :src="data.image"
                                    alt="Image"
                                    width="150"
                                    class="border-1"
                                    preview
                                />
                                <span v-else><i>Image tidak ditemukan</i></span>
                            </template>
                        </Column>
                        <Column frozen header="Opsi">
                            <template #body="{ data }">
                                <div class="flex gap-2 items-center">
                                    <Button
                                        size="small"
                                        @click="editDialog(data)"
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
        </template>
    </TemplateAdmin>
</template>
