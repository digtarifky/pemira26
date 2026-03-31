<script setup lang="ts">
import ConfirmationDialog from "@/components/ConfirmationDialog.vue";
import { Button } from "@/components/ui/button";
import {
  Card,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from "@/components/ui/card";
import { Link, useForm } from "@inertiajs/vue3";
import { BookImage } from "lucide-vue-next";
import { reactive, ref } from "vue";
import imageCompression from "browser-image-compression";
const form = useForm<{
  ktm: File | undefined;
  verification: File | undefined;
}>({
  ktm: undefined,
  verification: undefined,
});

const urls = reactive<{
  ktm: string | undefined;
  verification: string | undefined;
}>({
  ktm: "",
  verification: "",
});
const errorMessage = ref<string | null>(null);
const isCompressing = ref<boolean>(false);

const ktmInput = ref<HTMLInputElement | null>(null);
const verificationInput = ref<HTMLInputElement | null>(null);

const handleInput = async (e: Event) => {
  const target = e.target as HTMLInputElement;
  const file = target.files?.[0];

  errorMessage.value = null;
  if (!file) return;

  const options = {
    maxSizeMB: 1,
    maxWidthOrHeight: 1920,
    useWebWorker: true,
  };

  try {
    isCompressing.value = true; // Nyalakan loading

    const compressedFile = await imageCompression(file, options);

    if (target === ktmInput.value) {
      form.ktm = compressedFile;
      urls.ktm = URL.createObjectURL(compressedFile); // Tampilkan preview instan
    } else {
      form.verification = compressedFile;
      urls.verification = URL.createObjectURL(compressedFile); // Tampilkan preview instan
    }
  } catch (error) {
    errorMessage.value =
      "Gagal mengompres gambar. Pastikan format gambar didukung.";
  } finally {
    isCompressing.value = false; // Matikan loading
    target.value = ""; // Reset input agar bisa pilih file yang sama lagi jika perlu
  }
};

const submit = () => {
  errorMessage.value = null;
  if (form.ktm && form.verification) {
    form.post(route("verification.store"), {
      preserveScroll: true,
      onError: (errors) => {
        // Tangkap pesan error spesifik dari validasi Laravel
        if (errors.ktm) {
          errorMessage.value = `KTM: ${errors.ktm}`;
        } else if (errors.verification) {
          errorMessage.value = `Wajah: ${errors.verification}`;
        } else {
          errorMessage.value = "Server gagal memproses gambar";
        }
      },
    });
  }
};
</script>

<template>
  <div
    v-if="isCompressing"
    class="text-center text-white font-bold animate-pulse mb-4"
  >
    Sedang mengoptimasi gambar... Mohon tunggu.
  </div>
  <input
    ref="ktmInput"
    type="file"
    class="absolute -top-full -left-full"
    accept="image/*"
    @change="handleInput"
  />
  <input
    ref="verificationInput"
    type="file"
    class="absolute -top-full -left-full"
    accept="image/*"
    @change="handleInput"
  />

  <div
    class="min-h-screen w-full relative overflow-x-hidden flex flex-col bg-blue-800 bg-[url('/background.webp')] bg-cover bg-center font-sans"
  >
    <img
      src="/bubles.webp"
      alt="Gelembung Air"
      class="absolute top-[10%] left-[5%] md:left-[10%] w-28 md:w-40 lg:w-48 h-auto opacity-80 pointer-events-none mix-blend-screen animate-pulse z-0"
    />

    <div
      class="relative z-10 flex-1 flex flex-col items-center pt-20 pb-12 px-6 w-full"
    >
      <div class="max-w-md w-full space-y-4">
        <div
          v-if="errorMessage"
          class="bg-red-500/10 border border-red-500/50 backdrop-blur-md rounded-xl p-4 flex items-center gap-3 animate-in fade-in slide-in-from-top-4"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="size-6 text-red-400 shrink-0"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
            />
          </svg>
          <p class="text-white text-sm font-medium">{{ errorMessage }}</p>
        </div>
        <Card
          class="bg-white/10 backdrop-blur-lg border border-white/10 rounded-2xl shadow-xl overflow-hidden"
        >
          <CardHeader>
            <CardTitle
              class="text-white text-2xl font-extrabold tracking-widest"
              >Proses Verifikasi</CardTitle
            >
            <CardDescription class="text-gray-100 text-base font-medium mt-1">
              Pastikan foto KTM dan wajah kamu terlihat jelas yaa!
            </CardDescription>
          </CardHeader>
        </Card>

        <Card
          class="bg-white/10 backdrop-blur-lg border border-white/10 rounded-2xl shadow-xl overflow-hidden"
        >
          <CardHeader>
            <CardTitle
              class="text-white text-2xl font-extrabold tracking-widest"
              >Foto KTM</CardTitle
            >
            <CardDescription class="text-gray-100 text-base font-medium mt-1">
              Pastikan foto KTM kamu terlihat jelas yaa!
            </CardDescription>
          </CardHeader>
          <CardContent v-if="urls.ktm" class="px-6 pb-2">
            <img :src="urls.ktm" class="w-full object-cover rounded-lg" />
          </CardContent>
          <CardFooter class="grid gap-2 px-6 pb-6">
            <Button
              :variant="form.ktm ? 'outline' : 'default'"
              class="gap-1 bg-[#FF8A00] text-white hover:bg-[#e07a00] rounded-full font-bold shadow-md transition-colors duration-300 text-lg py-2.5 px-6"
              @click="ktmInput?.click()"
            >
              <BookImage class="size-4" />
              Ambil gambar
            </Button>
          </CardFooter>
        </Card>

        <Card
          class="bg-white/10 backdrop-blur-lg border border-white/10 rounded-2xl shadow-xl overflow-hidden"
        >
          <CardHeader>
            <CardTitle
              class="text-white text-2xl font-extrabold tracking-widest"
              >Foto KTM dan Wajah</CardTitle
            >
            <CardDescription class="text-gray-100 text-base font-medium mt-1">
              Pastikan foto KTM dan wajah kamu terlihat jelas yaa!
            </CardDescription>
          </CardHeader>
          <CardContent v-if="urls.verification" class="px-6 pb-2">
            <img
              :src="urls.verification"
              class="w-full object-cover rounded-lg"
            />
          </CardContent>
          <CardFooter class="grid gap-2 px-6 pb-6">
            <Button
              :variant="form.verification ? 'outline' : 'default'"
              class="gap-1 bg-[#FF8A00] text-white hover:bg-[#e07a00] rounded-full font-bold shadow-md transition-colors duration-300 text-lg py-2.5 px-6"
              @click="verificationInput?.click()"
            >
              <BookImage class="size-4" />
              Ambil gambar
            </Button>
          </CardFooter>
        </Card>

        <div
          class="relative z-10 bg-white/10 backdrop-blur-lg border border-white/10 rounded-2xl shadow-xl overflow-hidden p-6"
        >
          <div class="flex flex-row-reverse justify-between gap-2">
            <Button
              v-if="!form.ktm || !form.verification"
              disabled
              class="bg-gray-400 text-gray-700 cursor-not-allowed rounded-full font-bold shadow-md text-lg py-2.5 px-6"
            >
              Proses
            </Button>
            <ConfirmationDialog
              v-else
              title="Apakah kamu yakin?"
              description="Kedua foto akan dikirimkan untuk proses verifikasi dari panitia"
              @accept="submit"
            >
              <Button
                class="bg-[#FF8A00] text-white hover:bg-[#e07a00] rounded-full font-bold shadow-md transition-colors duration-300 text-lg py-2.5 px-6"
              >
                Proses
              </Button>
            </ConfirmationDialog>

            <Link :href="route('index')">
              <Button
                variant="outline"
                class="bg-white text-[#FF8A00] hover:bg-gray-100 px-8 py-2.5 rounded-full font-bold shadow-md transition-colors duration-300 text-lg"
              >
                Kembali
              </Button>
            </Link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
