<script setup>
import { onMounted } from "vue";
import { Head, useForm, router } from "@inertiajs/vue3";
import {
    Dialog,
    Card,
    InputText,
    Password,
    FloatLabel,
    Button,
    Message,
    Toast,
    useToast,
    Textarea,
} from "primevue";
import { ref } from "vue";

const showRegister = ref(false);

const clikshow = () => {
    showRegister.value = true;
    formRegister.clearErrors();
    formRegister.reset();
};

const props = defineProps({
    title: String,
    flash: Object,
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

// Cek notifikasi saat komponen di-mount
onMounted(() => {
    // Panggil ShowToast langsung saat komponen di-mount
    ShowToast();
});

// Menyimpan status loading saat merefresh halaman
const refrashLoading = ref(false);

// Fungsi untuk merefresh halaman
const refresh = () => {
    refrashLoading.value = true;
    return new Promise((resolve) => {
        router.visit(route("login"), {
            preserveScroll: true,
            onSuccess: () => {
                refrashLoading.value = false;
                formLogin.reset();
                resolve(); // Resolusi promise setelah refresh berhasil
            },
        });
    });
};

const formLogin = useForm({
    username: "",
    password: "",
});

const formRegister = useForm({
    name: "",
    username: "",
    password: "",
    confirm: "",
    phone: "",
    address: "",
    role: "3",
});

// Fungsi untuk menangani pengiriman form login
const onSubmit = async () => {
    await formLogin.post(route("login"), {
        onSuccess: async () => {
            await refresh(); // Tunggu hingga refresh selesai
            ShowToast(); // Tampilkan toast setelah refresh
        },
        onError: () => {
            ShowToast(); // Tampilkan toast jika ada error
        },
    });
};

// Fungsi untuk menangani pengiriman form register
const onRegisterSubmit = async () => {
    await formRegister.post(route("register"), {
        onSuccess: async () => {
            showRegister.value = false;
            await refresh(); // Tunggu hingga refresh selesai
            ShowToast(); // Tampilkan toast setelah refresh
        },
        onError: () => {
            showRegister.value = true;
            ShowToast(); // Tampilkan toast jika ada error
        },
    });
};
</script>

<template>
    <Head :title="props.title" />
    <Toast group="tc" />
    <div
        class="login-container flex justify-center items-center w-screen h-screen bg-slate-400"
    >
        <div class="sm:w-full md:w-[30%]">
            <Card class="w-full">
                <template #title>
                    <h2 class="text-center">Login Sportasion</h2>
                </template>
                <template #content>
                    <form @submit.prevent="onSubmit">
                        <div class="my-4 grid grid-cols-1 gap-4">
                            <div>
                                <FloatLabel variant="on">
                                    <InputText
                                        :autocomplete="false"
                                        v-model="formLogin.username"
                                        id="username"
                                        class="w-full"
                                    />
                                    <label for="username">Username</label>
                                </FloatLabel>
                                <Message
                                    v-if="formLogin.errors.username"
                                    severity="error"
                                    size="small"
                                    variant="simple"
                                >
                                    {{ formLogin.errors.username }}
                                </Message>
                            </div>

                            <div>
                                <FloatLabel variant="on">
                                    <Password
                                        fluid
                                        v-model="formLogin.password"
                                        id="password"
                                        :feedback="false"
                                        toggle-mask
                                    />
                                    <label for="password">Password</label>
                                </FloatLabel>
                                <Message
                                    v-if="formLogin.errors.password"
                                    severity="error"
                                    size="small"
                                    variant="simple"
                                >
                                    {{ formLogin.errors.password }}
                                </Message>
                            </div>

                            <Button
                                label="Login"
                                type="submit"
                                class="w-full mt-3"
                            />
                        </div>
                        <div class="register-link text-center">
                            <Button
                                label="Register"
                                class="p-button-text"
                                @click="clikshow"
                            />
                        </div>
                    </form>
                </template>
            </Card>

            <Dialog
                v-model:visible="showRegister"
                modal
                header="Register"
                :style="{ width: '30vw' }"
                :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
            >
                <span class="text-surface-500 dark:text-surface-400 block mb-8">
                    Silahkan daftarkan akun anda.
                </span>
                <div class="flex flex-col gap-4 mb-4">
                    <div>
                        <FloatLabel variant="on">
                            <InputText
                                fluid
                                v-model="formRegister.name"
                                id="name"
                                autocomplete="off"
                            />
                            <label for="name" class="font-semibold"
                                >Nama lengkap</label
                            >
                        </FloatLabel>
                        <Message
                            v-if="formRegister.errors.name"
                            severity="error"
                            size="small"
                            variant="simple"
                        >
                            {{ formRegister.errors.name }}
                        </Message>
                    </div>

                    <div>
                        <FloatLabel variant="on">
                            <InputText
                                fluid
                                v-model="formRegister.username"
                                id="edit_username"
                                autocomplete="off"
                            />
                            <label for="edit_username" class="font-semibold"
                                >Username</label
                            >
                        </FloatLabel>
                        <Message
                            v-if="formRegister.errors.username"
                            severity="error"
                            size="small"
                            variant="simple"
                        >
                            {{ formRegister.errors.username }}
                        </Message>
                    </div>

                    <div>
                        <FloatLabel variant="on">
                            <InputText
                                fluid
                                v-model="formRegister.phone"
                                id="phone"
                                class="flex-auto"
                                autocomplete="off"
                            />
                            <label for="phone" class="font-semibold"
                                >Phone</label
                            >
                        </FloatLabel>
                        <Message
                            v-if="formRegister.errors.phone"
                            severity="error"
                            size="small"
                            variant="simple"
                        >
                            {{ formRegister.errors.phone }}
                        </Message>
                    </div>

                    <div>
                        <FloatLabel variant="on">
                            <Textarea
                                id="over_label"
                                v-model="formRegister.address"
                                rows="5"
                                cols="47"
                                style="resize: none"
                                :invalid="!!formRegister.errors.address"
                            />
                            <label for="on_label">Alamat</label>
                        </FloatLabel>
                        <Message
                            v-if="formRegister.errors.address"
                            severity="error"
                            size="small"
                            variant="simple"
                        >
                            {{ formRegister.errors.address }}
                        </Message>
                    </div>

                    <div>
                        <FloatLabel variant="on">
                            <Password
                                fluid
                                v-model="formRegister.password"
                                id="registerPassword"
                                :feedback="false"
                                toggle-mask
                            />
                            <label for="registerPassword">Password</label>
                        </FloatLabel>
                        <Message
                            v-if="formRegister.errors.password"
                            severity="error"
                            size="small"
                            variant="simple"
                        >
                            {{ formRegister.errors.password }}
                        </Message>
                    </div>
                </div>

                <template #footer>
                    <div class="flex justify-end gap-2 mt-2">
                        <Button
                            type="button"
                            label="Cancel"
                            severity="secondary"
                            size="small"
                            @click="showRegister = false"
                        />
                        <Button
                            type="button"
                            label="Save"
                            size="small"
                            @click="onRegisterSubmit"
                        />
                    </div>
                </template>
            </Dialog>
        </div>
    </div>
</template>
