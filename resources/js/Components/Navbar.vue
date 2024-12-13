<script setup>
import { inject, onMounted, ref } from "vue";
import { router, useForm } from "@inertiajs/vue3";
import {
    Button,
    Dialog,
    FileUpload,
    FloatLabel,
    Image,
    InputNumber,
    InputText,
    Tag,
    Select,
    Toast,
    useToast,
    useConfirm,
} from "primevue";

const props = defineProps({
    flash: Object,
});

onMounted(() => {
    dataCart.forEach((produk) => {
        jmlhPesan.value[produk.id] = produk?.quantity;
    });

    ShowToast();
});

const showDialog = ref(false);
const showDialogTransaksi = ref(false);
const showBuktiBayar = ref(false);
const previewImg = ref(null);

const confirm = useConfirm();

const { dataCart, dataKategori, dataTransaksi } = inject("dataProps");

const metodeBayar = ref([
    { metode: "Bank Transfer", value: "bank_transfer" },
    { metode: "E-Wallet", value: "e-wallet" },
]);

const jmlhPesan = ref({});

const formCart = useForm({
    id_cart: null,
    quantity: null,
});

const formTransaksi = useForm({
    id_cart: null,
    metode_bayar: null,
    bukti_bayar: null,
});

const formatTanggal = (tanggal) => {
    const format = new Date(tanggal);

    return `${format.getDate()}/${format.getMonth()}/${format.getFullYear()}`;
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
        router.visit(route("costumerPage"), {
            preserveScroll: true,
            onSuccess: () => {
                refrashLoading.value = false;
                resolve(); // Resolusi promise setelah refresh berhasil
            },
        });
    });
};

const updateCart = (id, namaProduk) => {
    formCart.id_cart = id;
    formCart.quantity = jmlhPesan.value[id];

    confirm.require({
        message: `Update Keranjang Produk : ${namaProduk} ?`,
        header: "Peringatan",
        icon: "pi pi-exclamation-triangle",
        rejectProps: {
            label: "Batal",
            severity: "secondary",
            outlined: true,
        },
        acceptProps: {
            label: "Update",
            severity: "info",
        },
        accept: async () => {
            showDialog.value = false;
            await formCart.post(route("cart.update"), {
                onSuccess: async () => {
                    await refresh();
                    ShowToast();
                },
                onError: () => {
                    toast.add({
                        severity: "error",
                        summary: "Notifikasi",
                        detail: "Gagal menambahkan keranjang",
                        life: 4000,
                        group: "tc",
                    });
                },
            });
        },
    });
};

const hapusCart = (id, namaProduk) => {
    formCart.id_cart = id;
    formCart.quantity = jmlhPesan.value[id];

    confirm.require({
        message: `Hapus Keranjang Produk : ${namaProduk} ?`,
        header: "Peringatan",
        icon: "pi pi-exclamation-triangle",
        rejectProps: {
            label: "Batal",
            severity: "secondary",
            outlined: true,
        },
        acceptProps: {
            label: "Hapus dari keranjang",
            severity: "danger",
        },
        accept: async () => {
            showDialog.value = false;

            await formCart.post(route("cart.hapus"), {
                onSuccess: async () => {
                    await refresh();
                    ShowToast();
                },
                onError: () => {
                    toast.add({
                        severity: "error",
                        summary: "notifikasi",
                        detail: "Gagal menghapus keranjang",
                        life: 4000,
                        group: "tc",
                    });
                },
            });
        },
    });
};

const pesanCart = (id, namaProduk) => {
    formCart.id_cart = id;
    formCart.quantity = jmlhPesan.value[id];

    confirm.require({
        message: `Pesan Produk : ${namaProduk} ?`,
        header: "Peringatan",
        icon: "pi pi-exclamation-triangle",
        rejectProps: {
            label: "Batal",
            severity: "secondary",
            outlined: true,
        },
        acceptProps: {
            label: "Pesan",
            severity: "success",
        },
        accept: async () => {
            showDialog.value = false;

            await formCart.post(route("cart.pesan"), {
                onSuccess: async () => {
                    await refresh();
                    ShowToast();
                },
                onError: () => {
                    toast.add({
                        severity: "error",
                        summary: "Notifikasi",
                        detail: "Gagal menambahkan keranjang",
                        life: 4000,
                        group: "tc",
                    });
                },
            });
        },
    });
};

const batalPesan = (id, namaProduk) => {
    formCart.id_cart = id;
    formCart.quantity = jmlhPesan.value[id];

    confirm.require({
        message: `Batalkan Pesanan Produk : ${namaProduk} ?`,
        header: "Peringatan",
        icon: "pi pi-exclamation-triangle",
        rejectProps: {
            label: "Tidak",
            severity: "secondary",
            outlined: true,
        },
        acceptProps: {
            label: "Batalkan Pesanan",
            severity: "danger",
        },
        accept: async () => {
            showDialog.value = false;

            await formCart.post(route("cart.pesan.batal"), {
                onSuccess: async () => {
                    await refresh();
                    ShowToast();
                },
                onError: () => {
                    toast.add({
                        severity: "error",
                        summary: "Notifikasi",
                        detail: "Gagal membatalkan pesanan",
                        life: 4000,
                        group: "tc",
                    });
                },
            });
        },
    });
};

const kategoriPage = (idKategori) => {
    useForm({ id: idKategori }).get(route("kategori.page"), {
        onError: () => refreshPage(),
    });
};

const submitTransaksi = (id) => {
    formTransaksi.id_cart = id;

    confirm.require({
        message: `Konfirmasi Pembayaran ?`,
        header: "Peringatan",
        icon: "pi pi-exclamation-triangle",
        rejectProps: {
            label: "Batal",
            severity: "secondary",
            outlined: true,
        },
        acceptProps: {
            label: "Konfirmasi",
            severity: "success",
        },
        accept: async () => {
            showDialogTransaksi.value = false;

            await formTransaksi.post(route("transaksi.add"), {
                onSuccess: async () => {
                    await refresh();
                    ShowToast();
                },
                onError: () => {
                    toast.add({
                        severity: "error",
                        summary: "notifikasi",
                        detail: "Gagal Konfirmasi pembayaran",
                        life: 4000,
                        group: "tc",
                    });
                },
            });
        },
    });
};

const dialogHide = () =>
    dataCart.forEach((produk) => {
        jmlhPesan.value[produk.id] = produk.quantity;
    });

const uploadBuktiTF = (event) => {
    formTransaksi.bukti_bayar = event.files[0];
    const reader = new FileReader();

    reader.onloadend = async (e) => {
        previewImg.value = e.target.result;
    };

    reader.readAsDataURL(formTransaksi.bukti_bayar);
};

const logout = () => {
    confirm.require({
        message: `Yakin ingin logout ?`,
        header: "Peringatan",
        icon: "pi pi-exclamation-triangle",
        rejectProps: {
            label: "Batal",
            severity: "secondary",
            outlined: true,
        },
        acceptProps: {
            label: "Logout",
            severity: "danger",
        },
        accept: () => {
            router.post(route("logout"));
        },
    });
};
</script>

<template>
    <Toast position="top-center" group="tc" />

    <!-- dialog keranjang -->
    <Dialog
        v-model:visible="showDialog"
        :header="`Keranjang Anda (${dataCart.length.toString()})`"
        class="w-[52rem]"
        modal
        v-on:hide="dialogHide()"
    >
        <form
            @submit.prevent
            class="flex gap-4 w-full my-8"
            autocomplete="off"
            v-for="(cart,index) in dataCart"
            :key="cart.id"
        >
            <span class="text-xl">{{ index+1 }}</span>
            <div class="flex flex-col w-[75%] gap-2">
                <div class="overflow-hidden rounded-lg">
                    <img
                        :src="cart.product.image"
                        class="size-full"
                    />
                </div>
                <Tag
                    v-if="cart.status === 1"
                    value="Menunggu Konfirmasi Admin"
                    severity="warn"
                />
                <Tag
                    v-if="cart.status === 3"
                    value="Pesanan Ditolak"
                    severity="danger"
                />
                <span v-if="cart.catatan" class="capitalize">*Catatan : {{ cart.catatan }}</span>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <div class="flex flex-col w-[16rem]">
                    <FloatLabel variant="on">
                        <InputText
                            disabled
                            fluid
                            class="w-full"
                            inputId="custom"
                            :modelValue="
                                cart.product.name
                            "
                        />
                        <label for="custom">Nama Produk</label>
                    </FloatLabel>
                </div>
                <div class="flex flex-col w-[16rem]">
                    <FloatLabel variant="on">
                        <InputText
                            disabled
                            fluid
                            class="w-full"
                            inputId="custom"
                            :modelValue="formatTanggal(cart.order_date)"
                        />
                        <label for="custom">Tanggal Order</label>
                    </FloatLabel>
                </div>
                <div class="flex flex-col w-[16rem]">
                    <FloatLabel variant="on">
                        <InputNumber
                            v-model="jmlhPesan[cart.id]"
                            :disabled="cart.status === 1"
                            inputId="minmax-buttons"
                            mode="decimal"
                            showButtons
                            :min="1"
                            :max="100"
                            fluid
                        />
                        <label for="on_label">Jumlah Pesan</label>
                    </FloatLabel>
                </div>
                <div class="flex flex-col w-[16rem]">
                    <FloatLabel variant="on">
                        <InputNumber
                            disabled
                            :modelValue="
                                cart.product.price *
                                jmlhPesan[cart.id]
                            "
                            inputId="on_label"
                            mode="currency"
                            currency="IDR"
                            locale="id-ID"
                            fluid
                        />
                        <label for="on_label">Total Bayar</label>
                    </FloatLabel>
                </div>
                <div class="flex items-center gap-x-2">
                    <Button
                        label="Update"
                        :disabled="
                            jmlhPesan[cart.id] ===
                            cart.quantity
                        "
                        severity="info"
                        @click="
                            updateCart(
                                cart.id,
                                cart.product['name']
                            )
                        "
                    />
                    <Button
                        v-if="cart.status === 2 || cart.status === 3"
                        label="Pesan Sekarang"
                        severity="success"
                        @click="
                            pesanCart(
                                cart.id,
                                cart.product['name']
                            )
                        "
                    />
                    <Button
                        v-if="cart.status === 2 || cart.status === 3"
                        label="Hapus"
                        severity="danger"
                        @click="
                            hapusCart(
                                cart.id,
                                cart.product['name']
                            )
                        "
                        outlined
                    />
                    <Button
                        v-if="cart.status === 1"
                        label="Batalkan Pesanan"
                        severity="danger"
                        @click="
                            batalPesan(
                                cart.id,
                                cart.product['name']
                            )
                        "
                    />
                </div>
            </div>
        </form>
    </Dialog>

    <!-- dialog transaksi -->
    <Dialog
        v-model:visible="showDialogTransaksi"
        :header="`Transaksi Anda (${dataTransaksi?.length.toString()})`"
        class="w-[52rem]"
        modal
    >
        <form
            @submit.prevent
            class="flex gap-4 my-4"
            autocomplete="off"
            v-for="(transaksi, index) in dataTransaksi"
            :key="transaksi.id"
        >
            <span class="text-xl">{{ index+1 }}</span>
            <div class="flex flex-col gap-2 w-[90%]">
                <div class="overflow-hidden rounded-lg">
                    <img
                        :src="transaksi.product.image"
                    />
                </div>
                <Tag
                    v-if="transaksi.transaksi.length < 1"
                    value="Silahkan Upload Bukti Pembayaran"
                    severity="warn"
                />
                <Tag
                    v-if="transaksi.transaksi[0]?.status == 1"
                    value="Pembayaran Ditolak"
                    severity="danger"
                />
                <span v-if="transaksi.transaksi[0]?.catatan">*{{ transaksi.transaksi[0]?.catatan }}</span>
            </div>
            <div class="flex flex-wrap items-center gap-y-2 gap-x-8">
                <div class="flex flex-col w-[12rem]">
                    <FloatLabel variant="on">
                        <InputText
                            disabled
                            fluid
                            class="w-full"
                            inputId="custom"
                            :modelValue="
                                transaksi.product.name
                            "
                        />
                        <label for="custom">Nama Produk</label>
                    </FloatLabel>
                </div>
                <div class="flex flex-col w-[12rem]">
                    <FloatLabel variant="on">
                        <InputText
                            disabled
                            fluid
                            class="w-full"
                            inputId="custom"
                            :modelValue="formatTanggal(transaksi.order_date)"
                        />
                        <label for="custom">Tanggal Order</label>
                    </FloatLabel>
                </div>
                <div class="flex flex-col w-[12rem]">
                    <FloatLabel variant="on">
                        <InputNumber
                            v-model="transaksi.quantity"
                            disabled
                            inputId="minmax-buttons"
                            mode="decimal"
                            showButtons
                            :min="1"
                            :max="100"
                            fluid
                        />
                        <label for="on_label">Jumlah Pesan</label>
                    </FloatLabel>
                </div>
                <div class="flex flex-col w-[12rem]">
                    <FloatLabel variant="on">
                        <InputNumber
                            disabled
                            :modelValue="transaksi.total_price"
                            inputId="on_label"
                            mode="currency"
                            currency="IDR"
                            locale="id-ID"
                            fluid
                        />
                        <label for="on_label">Total Bayar</label>
                    </FloatLabel>
                </div>
                <div class="flex flex-col w-[12rem]">
                    <FloatLabel variant="on">
                        <Select
                            fluid
                            class="w-full"
                            :disabled="transaksi.transaksi[0]?.status === 2"
                            v-model="formTransaksi.metode_bayar"
                            :options="metodeBayar"
                            optionLabel="metode"
                            optionValue="value"
                        />
                        <label for="on_label">Metode Pembayaran</label>
                    </FloatLabel>
                </div>
                <div class="flex flex-col w-[12rem]">
                    <FileUpload
                        mode="basic"
                        auto
                        choose-label="Pilih file"
                        @uploader="uploadBuktiTF($event)"
                        accept="image/*"
                        customUpload
                        severity="secondary"
                        class="w-full p-button-outlined"
                        :multiple="false"
                        :disabled="
                            !formTransaksi.metode_bayar ||
                            transaksi.transaksi.length > 0
                        "
                        fluid
                    />
                </div>
                <Tag
                    v-if="transaksi.transaksi[0]?.status == 2"
                    value="Pembayaran Berhasil"
                    severity="success"
                />
                <Button
                    label="Lihat"
                    icon="pi pi-eye"
                    @click="showBuktiBayar = true"
                    severity="success"
                    v-if="formTransaksi.bukti_bayar"
                />
                <div
                    class="flex items-center gap-x-2"
                    v-if="transaksi.transaksi.length < 1"
                >
                    <Button
                        :disabled="!formTransaksi.bukti_bayar"
                        label="Konfirmasi Pembayaran"
                        @click="submitTransaksi(transaksi.id)"
                    />
                </div>
            </div>
        </form>
    </Dialog>

    <Dialog
        v-model:visible="showBuktiBayar"
        class="flex justify-center gap-2"
        modal
    >
        <span class="text-2xl text-center">Bukti Pembayaran Anda</span>
        <div class="size-[24rem] overflow-hidden">
            <Image preview :src="previewImg" fluid class="size-full" />
        </div>
    </Dialog>

    <nav
        class="w-full h-[4rem] bg-slate-700 text-white flex justify-between items-center px-4 fixed top-0"
    >
        <!-- brand -->
        <span class="text-2xl">Sportasion</span>

        <!-- menu -->
        <div class="flex items-center gap-x-4">
            <Button
                label="Semua"
                @click="router.visit(route('costumerPage'))"
                unstyled
            />
            <Button
                v-for="kategori in dataKategori"
                :label="kategori.name"
                @click="kategoriPage(kategori.id)"
                unstyled
            />
        </div>

        <!-- logout -->
        <div class="flex items-center gap-x-4">
            <Button
                severity="secondary"
                icon="pi pi-shopping-cart"
                :badge="dataCart.length.toString()"
                badgeSeverity="success"
                label="Keranjang"
                :disabled="dataCart.length < 1"
                @click="showDialog = true"
            />
            <Button
                severity="contrast"
                :badge="dataTransaksi?.length.toString()"
                :disabled="dataTransaksi?.length < 1"
                label="Transaksi"
                @click="showDialogTransaksi = true"
            />
            <Button severity="danger" label="Logout" @click="logout" />
        </div>
    </nav>
</template>

<style scoped></style>
