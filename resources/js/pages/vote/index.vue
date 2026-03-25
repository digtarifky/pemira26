<script lang="ts" setup>
import Flash from "@/components/Flash.vue";
import { Avatar, AvatarImage } from "@/components/ui/avatar";
import { Button } from "@/components/ui/button";
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from "@/components/ui/card";
import { PageProps } from "@/types";
import { Link, usePage } from "@inertiajs/vue3";
import { LogOut } from "lucide-vue-next";

const props = defineProps<{
    verification: UserVerification | null;
    organizations: Organization[];
}>();

const page = usePage<PageProps>();
const user = page.props.auth.user;
</script>

<template>
    <div class="min-h-dvh flex items-center justify-center p-4 relative overflow-hidden">
        
        <img
            src="/background.webp"
            class="absolute size-full object-cover -z-10"
        />

        <div class="max-w-md w-full space-y-4 relative z-10">
            
            <Card class="bg-white/10 backdrop-blur-lg border border-white/20 shadow-2xl">
                <CardHeader class="space-y-2 border-b border-white/20 text-center text-pretty">
                    <Avatar class="size-24 mx-auto border-2 border-white/20 shadow-md">
                        <AvatarImage
                            v-if="user.avatar"
                            :alt="user.name"
                            :src="user.avatar"
                            referrerpolicy="no-referrer"
                        />
                    </Avatar>
                    <div class="space-y-1.5">
                        <CardDescription class="text-gray-200 font-medium">Selamat datang!</CardDescription>
                        <CardTitle class="text-2xl font-extrabold text-white">{{ user.name }}</CardTitle>
                    </div>
                </CardHeader>

                <CardContent
                    v-if="
                        verification === null ||
                        verification.ktm === null ||
                        verification.verification === null
                    "
                    class="p-6 space-y-4 border-b border-white/20"
                >
                    <div class="text-center space-y-1.5">
                        <CardTitle class="text-white text-xl">Upload KTM</CardTitle>
                        <CardDescription class="text-gray-200">
                            Sebelum pemilihan silakan upload KTM kamu dulu yaa!
                        </CardDescription>
                    </div>
                    <div class="flex justify-center space-x-4">
                        <Link :href="route('verification')" class="w-full">
                            <Button class="w-full bg-[#FF8A00] hover:bg-[#e07a00] text-white border-none font-bold rounded-full py-6 text-md shadow-lg transition-transform hover:-translate-y-1">
                                Selanjutnya
                            </Button>
                        </Link>
                    </div>
                </CardContent>

                <CardContent
                    v-else-if="organizations.length > 0"
                    class="p-6 space-y-4 border-b border-white/20"
                >
                    <Flash />
                    <div class="text-center">
                        <CardTitle class="text-white text-xl">Organisasi</CardTitle>
                        <CardDescription class="text-gray-200">
                            Pilih organisasi yang ingin kamu buka
                        </CardDescription>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <Link
                            v-for="organization in organizations"
                            :href="
                                route('vote.organization', {
                                    organization: organization.id,
                                })
                            "
                        >
                            <Card
                                class="overflow-hidden bg-white/10 border border-white/20 cursor-pointer hover:bg-white/30 transition-colors shadow-md"
                            >
                                <img :src="`/storage/${organization.logo}`" class="w-full object-cover" />
                                <CardHeader class="p-3">
                                    <CardTitle class="text-center text-white text-sm">
                                        {{ organization.name }}
                                    </CardTitle>
                                </CardHeader>
                            </Card>
                        </Link>
                    </div>
                </CardContent>

                <CardContent v-else class="p-6 border-b border-white/20">
                    <div class="text-center space-y-1.5">
                        <CardTitle class="text-white text-xl">Belum ada pemilihan</CardTitle>
                        <CardDescription class="text-gray-200">
                            Wah kamu rajin sekali! tunggu info selanjutnya yaa! 🤩
                        </CardDescription>
                    </div>
                </CardContent>

                <CardFooter
                    class="p-6 flex flex-col md:flex-row items-center justify-between space-y-4 md:space-y-0"
                >
                    <CardDescription class="text-gray-200">
                        Butuh bantuan?
                        <a
                            href="https://wa.me/62859183994546"
                            target="_blank"
                            class="text-[#FF8A00] hover:text-[#e07a00] underline font-semibold ml-1 transition-colors"
                        >
                            Hubungi kami disini
                        </a>
                    </CardDescription>
                    <Link :href="route('logout')" class="w-full md:w-fit">
                        <Button variant="outline" class="w-full gap-2 bg-transparent text-white border-white/30 hover:bg-white/10 hover:text-white rounded-full">
                            <LogOut class="size-4" />
                            Keluar Akun
                        </Button>
                    </Link>
                </CardFooter>
            </Card>

            <Card v-if="user.type === 'admin'" class="max-w-md mx-auto bg-white/10 backdrop-blur-lg border border-white/20 shadow-xl">
                <CardHeader>
                    <CardTitle class="text-white text-xl">Admin</CardTitle>
                    <CardDescription class="text-gray-200">Kamu punya akses admin</CardDescription>
                </CardHeader>
                <CardFooter class="grid pb-6">
                    <Link :href="route('admin.dashboard')">
                        <Button class="w-full bg-[#FF8A00] hover:bg-[#e07a00] text-white border-none font-bold rounded-full py-6 text-md shadow-lg transition-transform hover:-translate-y-1">
                            Masuk ke Dashboard
                        </Button>
                    </Link>
                </CardFooter>
            </Card>
        </div>
    </div>
</template>