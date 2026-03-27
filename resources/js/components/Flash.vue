<script setup lang="ts">
import { Alert, AlertDescription } from "@/components/ui/alert";
import { PageProps } from "@/types";
import { usePage } from "@inertiajs/vue3";
import { AlertCircle } from "lucide-vue-next";
import { computed } from "vue";

const page = usePage<PageProps>();

// 2. Gunakan 'computed' agar variabel ini selalu mengawasi perubahan data dari Laravel
const flash = computed(() => page.props.flash);
</script>

<template>
  <div
    v-if="flash?.message"
    class="fixed top-5 right-5 z-50 min-w-[300px] shadow-lg"
  >
    <Alert :variant="flash?.type || 'default'">
      <AlertCircle class="h-4 w-4" />
      <AlertDescription>
        {{ flash?.message }}
      </AlertDescription>
    </Alert>
  </div>
</template>
