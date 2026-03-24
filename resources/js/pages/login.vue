<script setup lang="ts">
import Flash from "@/components/Flash.vue";
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
import { Icon } from "@iconify/vue";
import { usePage } from "@inertiajs/vue3";

const page = usePage<PageProps>();
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Bungee&family=Irish+Grover&display=swap');

.font-bungee {
  font-family: 'Bungee', cursive;
}

.font-irish-grover {
  font-family: 'Irish Grover', cursive;
}
</style>

<template>
    <div class="min-h-dvh flex items-center justify-center p-4 relative overflow-hidden">
        <img
            src="/background.webp"
            class="absolute size-full object-cover -z-10"
        />
        
        <Card class="max-w-sm w-full bg-white/10 backdrop-blur-lg border border-white/20 shadow-2xl">
            <CardHeader class="border-b border-white/20 text-center text-pretty">
                <img
                    src="/logo.webp"
                    alt="Logo PEMIRA Fasilkom 2026"
                    class="size-28 mx-auto mb-2 drop-shadow-md"
                />
                <CardTitle class="text-3xl font-extrabold text-white">PEMIRA</CardTitle>
                <CardTitle class="text-white">Fakultas Ilmu Komputer 2026</CardTitle>
                <CardDescription class="text-gray-200 font-medium">
                    Universitas Pembangunan Nasional "Veteran" Jawa Timur
                </CardDescription>
            </CardHeader>
            
            <CardContent class="p-6 border-b border-white/20 space-y-2">
                <a :href="route('auth.redirect')" class="block">
                    <Button size="lg" class="w-full gap-2 bg-[#FF8A00] hover:bg-[#e07a00] text-white border-none">
                        <Icon icon="fa6-brands:google" class="size-5" />
                        Masuk dengan Google
                    </Button>
                </a>
                <Flash />
            </CardContent>
            
            <CardFooter class="p-6 justify-center">
                <CardDescription class="text-gray-200">
                    Butuh bantuan?
                    <a
                        href="https://wa.me/62859183994546"
                        target="_blank"
                        class="text-[#FF8A00] hover:text-[#e07a00] underline font-semibold ml-1"
                        >Hubungi kami disini</a
                    >
                </CardDescription>
            </CardFooter>
        </Card>
    </div>
</template>