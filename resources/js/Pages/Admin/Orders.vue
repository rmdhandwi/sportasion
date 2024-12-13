<script setup>
import { onMounted, ref } from "vue";
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
    Textarea,
    ConfirmDialog,
    Image,
    Tag,
    useConfirm,
    Dialog,
    FloatLabel,
    Message,
    InputText,
} from "primevue";
import { FilterMatchMode } from "@primevue/core/api";

const props = defineProps({
    title: String,
    auth: Object,
    flash: Object,
    orders: Object,
});

const dt = ref([]);
const ordersData = ref([]);

onMounted(() => {
    ordersData.value = props.orders.map((item, index) => ({
        index: index + 1,
        ...item,
    }));
});

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
    router.visit(route("orders"), {
        preserveScroll: true,
        onSuccess: () => {
            refrashLoading.value = false;
            resolve(); // Resolusi promise setelah refresh berhasil
        },
    });
};

const confirm = useConfirm();

// Fungsi untuk menangani pengiriman form add
const acceptOrder = (data) => {
    confirm.require({
        message: `Terima Orderan dari ${data.user.name} ?`,
        header: "Peringatan",
        icon: "pi pi-exclamation-triangle",
        rejectProps: {
            label: "Batal",
            severity: "danger",
            outlined: true,
        },
        acceptProps: {
            label: "Terima",
            severity: "success",
        },
        accept: async () => {
            await router.put(`Orders/${data.id}/accepted`, data.id, {
                onSuccess: async () => {
                    await refresh();
                    setTimeout(() => {
                        ShowToast();
                    }, 500);
                },
                onError: () => {
                    toast.add({
                        severity: "error",
                        summary: "notifikasi",
                        detail: "Gagal menerima orderan",
                        life: 4000,
                        group: "tc",
                    });
                },
            });
        },
        reject: () => {
            toast.add({
                severity: "info",
                summary: "Dibatalkan",
                detail: "Anda membatalkan Konfirmasi pesanan.",
                life: 3000,
                group: "tc",
            });
        },
    });
};
const formCancel = useForm({
    id: "",
    catatan: "",
});
const visible = ref(false);
const cancelOrder = (data) => {
    visible.value = true;
    formCancel.reset();
    formCancel.clearErrors();
    formCancel.id = data.id;
};

const cancelSubmit = async () => {
    await formCancel.put(route("cancelOrder", formCancel.id), {
        onSuccess: async () => {
            visible.value = false;
            await refresh();
            setTimeout(() => {
                ShowToast();
            }, 500);
        },
        onError: () => {
            visible.value = true;
            ShowToast();
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
            </div>

            <Dialog
                v-model:visible="visible"
                modal
                header="Tolak Pesanan"
                :style="{ width: '25vw' }"
                :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
            >
                <span class="text-sm">Pesanan Ditolak karena?</span>
                <form @submit.prevent="cancelSubmit">
                    <div class="mb-4 mt-2">
                        <FloatLabel variant="on">
                            <Textarea
                                id="over_label"
                                v-model="formCancel.catatan"
                                rows="5"
                                cols="30"
                                :invalid="!!formCancel.errors.catatan"
                                style="resize: none"
                            />
                            <label for="on_label">Catatan</label>
                        </FloatLabel>
                        <Message
                            v-if="formCancel.errors.catatan"
                            severity="error"
                            size="small"
                            variant="simple"
                        >
                            {{ formCancel.errors.catatan }}
                        </Message>
                    </div>
                    <div class="flex justify-end gap-2">
                        <Button
                            type="button"
                            label="Cancel"
                            severity="secondary"
                            @click="visible = false"
                        ></Button>
                        <Button type="submit" label="Save"></Button>
                    </div>
                </form>
            </Dialog>

            <!-- tabele -->
            <Card>
                <template #content>
                    <DataTable
                        removableSort
                        v-model:filters="filters"
                        ref="dt"
                        :value="ordersData"
                        paginator
                        resizableColumns
                        columnResizeMode="fit"
                        scrollable
                        :scrollHeight="'400px'"
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
                                        placeholder="Cari Data Order"
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
                        <Column
                            sortable
                            field="user.name"
                            header="Nama Costumer"
                        />
                        <Column header="Nama Produk">
                            <template #body="{ data }">
                                {{ data.product.name }}
                            </template>
                        </Column>
                        <Column header="Harga Satuan">
                            <template #body="{ data }">
                                {{ formatRupiah(data.product.price) }}
                            </template>
                        </Column>
                        <Column header="Quantity">
                            <template #body="{ data }">
                                {{ data.quantity }}
                            </template>
                        </Column>
                        <Column header="Gambar Produk">
                            <template #body="{ data }">
                                <Image
                                    v-if="data.product.image"
                                    :src="data.product.image"
                                    alt="Image"
                                    width="150"
                                    class="border-1"
                                    preview
                                />
                                <span v-else><i>Image tidak ditemukan</i></span>
                            </template>
                        </Column>
                        <Column header="Harga Total">
                            <template #body="{ data }">
                                {{ formatRupiah(data.total_price) }}
                            </template>
                        </Column>
                        <Column header="Status">
                            <template #body="{ data }">
                                <Tag
                                    severity="danger"
                                    v-if="data.status == 3"
                                    value="Pesanan Ditolak"
                                />
                                <Tag
                                    severity="info"
                                    v-if="data.status == 2"
                                    value="Menunggu Pesanan"
                                />
                                <Tag
                                    severity="warn"
                                    v-else-if="data.status == 1"
                                    value="Menunggu Konfirmasi"
                                />
                                <Tag
                                    severity="success"
                                    v-else-if="data.status == 0"
                                    value="Pesanan Telah diterima"
                                />
                            </template>
                        </Column>
                        <Column header="Catatan">
                            <template #body="{ data }">
                                <span v-if="data.status == 3">{{
                                    data.catatan
                                }}</span>
                                <span v-else><i>Tidak ada catatan</i></span>
                            </template>
                        </Column>
                        <Column frozen align-frozen="right" header="Opsi">
                            <template #body="{ data }">
                                <div
                                    class="flex gap-2 items-center"
                                    v-if="data.status == 1"
                                >
                                    <Button
                                        size="small"
                                        @click="acceptOrder(data)"
                                        icon="pi pi-check-circle"
                                        iconPos="right"
                                        severity="success"
                                        outlined
                                    />
                                    <Button
                                        size="small"
                                        @click="cancelOrder(data)"
                                        icon="pi pi-times"
                                        iconPos="right"
                                        severity="danger"
                                        outlined
                                    />
                                </div>
                                <span v-else
                                    ><i class="text-sm">Tidak ada Opsi</i></span
                                >
                            </template>
                        </Column>
                    </DataTable>
                </template>
            </Card>
        </template>
    </TemplateAdmin>
</template>
