<script lang="ts" setup>
import ConfirmationDialog from "@/components/ConfirmationDialog.vue";
import { Button } from "@/components/ui/button";
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from "@/components/ui/card";
import { Link, router } from "@inertiajs/vue3";

const props = defineProps<{
    organization: Organization;
    details: BallotDetail[];
}>();

const submit = () => {
    router.post(
        route("vote.result.confirm", {
            organization: props.organization.id,
        })
    );
};
</script>

<<template>
    <div class="min-h-dvh flex justify-center p-4 sm:p-6 lg:p-8 bg-blue-800 bg-[url('/background.webp')] bg-cover bg-center bg-fixed font-sans relative">
        
        <div class="max-w-screen-xl w-full space-y-4 relative z-10">
            <Card class="bg-white/10 backdrop-blur-lg border border-white/20 shadow-2xl">
                
                <CardHeader class="text-center border-b border-white/20 pb-6">
                    <CardTitle class="text-3xl font-extrabold text-white drop-shadow-lg">Konfirmasi Pilihan</CardTitle>
                    <CardDescription class="text-gray-200 text-lg mt-1">
                        Pastikan pilihanmu sudah sesuai sebelum melanjutkan!
                    </CardDescription>
                    
                    <div class="flex justify-center gap-4 mt-6">
                        <Link
                            :href="
                                route('vote.organization', {
                                    organization: organization.id,
                                })
                            "
                        >
                            <Button variant="ghost" class="bg-white/10 backdrop-blur-lg border border-white/10 text-white rounded-full px-8 shadow-lg hover:bg-white/20 transition-all">
                                Kembali
                            </Button>
                        </Link>
                        
                        <ConfirmationDialog
                            title="Apakah kamu yakin dengan pilihanmu?"
                            description="Pilihanmu tidak dapat diubah setelah ini."
                            @accept="submit"
                        >
                            <Button class="bg-[#FF8A00] hover:bg-[#e07a00] text-white border-none font-bold rounded-full px-8 shadow-lg transition-transform hover:-translate-y-1">
                                Konfirmasi
                            </Button>
                        </ConfirmationDialog>
                    </div>
                </CardHeader>
                
                <CardContent class="pt-6">
                    <div
                        class="grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6"
                    >
                        <div
                            v-for="detail in details"
                            class="flex flex-col gap-3"
                        >
                            <CardTitle class="text-center text-white text-lg font-bold drop-shadow-md">
                                {{ detail.group?.name }}
                            </CardTitle>
                            
                            <Card class="bg-white/10 border border-white/20 overflow-hidden shadow-lg backdrop-blur-md">
                                <div class="relative aspect-[4/5]">
                                    <img
                                        :src="`/storage/${detail.candidate?.picture}`"
                                        class="w-full h-full object-cover absolute inset-0"
                                    />
                                </div>
                                <CardHeader class="p-4 text-center border-t border-white/20 bg-black/20 text-sm">
                                    <CardTitle class="leading-normal text-white font-bold drop-shadow-md">
                                        {{ detail.candidate?.name_1 }}
                                        <template
                                            v-if="detail.candidate?.name_2"
                                        >
                                            <br />
                                            &
                                            <br />
                                            {{ detail.candidate?.name_2 }}
                                        </template>
                                    </CardTitle>
                                </CardHeader>
                            </Card>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>