<script setup lang="ts">
import { Alert, AlertDescription } from "@/components/ui/alert";
import { PageProps } from "@/types";
import { usePage } from "@inertiajs/vue3";
import { AlertCircle } from "lucide-vue-next";
import { computed, watch, ref } from "vue";

const page = usePage<PageProps>();
const flash = computed(() => page.props.flash);
const visible = ref(false);

// Munculkan alert hanya jika ada pesan baru
watch(
  () => flash.value?.message,
  (newMsg) => {
    if (newMsg) {
      visible.value = true; 
      // Otomatis tutup setelah 5 detik
      setTimeout(() => {
        visible.value = false;
      }, 5000);
    }
  },
  { immediate: true },
);
</script>

<template>
  <Transition
    enter-active-class="transform transition duration-300 ease-out"
    enter-from-class="translate-y-[-20px] opacity-0"
    enter-to-class="translate-y-0 opacity-100"
    leave-active-class="transform transition duration-200 ease-in"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
    <div
      v-if="visible && flash?.message"
      class="fixed top-5 right-5 z-[100] min-w-[300px] shadow-2xl"
    >
      <Alert
        :variant="flash?.type === 'destructive' ? 'destructive' : 'default'"
      >
        <AlertCircle class="h-4 w-4" />
        <AlertDescription>
          {{ flash?.message }}
        </AlertDescription>
      </Alert>
    </div>
  </Transition>
</template>
