<script lang="ts" setup>
import { Button } from "@/components/ui/button";
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from "@/components/ui/card";
import { Link, useForm } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps<{
  organization: Organization;
  group: Group;
  candidates: Candidate[];
  ballotDetails: BallotDetail[] | null;
}>();

const form = useForm({
  candidate_ids: null,
});

const selected = ref(
  props.ballotDetails?.map((detail) => detail.candidate_id) ?? [],
);

const select = (id: number) => {
  if (selected.value.includes(id)) {
    selected.value = selected.value.filter((value) => value !== id);
  } else if (props.group.min_candidates === 1) {
    selected.value = [id];
  } else if (selected.value.length < props.group.min_candidates) {
    selected.value.push(id);
  }
};

const submit = () => {
  if (!selected.value) {
    return;
  }
  if (
    selected.value.length < props.group.min_candidates &&
    props.candidates.length >= props.group.min_candidates
  ) {
    form.setError(
      "candidate_ids",
      `Pilihlah minimal ${props.group.min_candidates} kandidat!`,
    );
    return;
  }
  useForm({
    candidate_ids: selected.value,
  }).post(
    route("vote.group.store", {
      organization: props.organization.id,
      group: props.group.id,
    }),
    {
      preserveState: false,
    },
  );
};
</script>

<template>
  <div
    class="min-h-dvh flex justify-center p-4 sm:p-6 lg:p-8 bg-blue-800 bg-[url('/background.webp')] bg-cover bg-center bg-fixed font-sans relative"
  >
    <div class="max-w-screen-xl w-full space-y-4 relative z-10">
      <Card
        class="bg-white/10 backdrop-blur-lg border border-white/20 shadow-2xl"
      >
        <CardHeader class="text-center border-b border-white/20 pb-6">
          <CardTitle class="text-3xl font-extrabold text-white">{{
            group.name
          }}</CardTitle>
          <CardDescription class="text-gray-200 text-lg mt-1">
            Pilihlah {{ group.min_candidates }} kandidat di bawah ini!
          </CardDescription>

          <div class="flex justify-center gap-4 mt-6">
            <Link
              :href="
                route('vote.group.previous', {
                  organization: organization.id,
                  group: group.id,
                })
              "
            >
              <Button
                variant="ghost"
                class="bg-white/10 backdrop-blur-lg border border-white/10 text-white rounded-full px-8 shadow-lg hover:bg-white/20 transition-all"
              >
                Kembali
              </Button>
            </Link>

            <Button
              :disabled="!selected"
              @click="submit"
              class="bg-[#FF8A00] hover:bg-[#e07a00] text-white border-none font-bold rounded-full px-8 shadow-lg disabled:bg-gray-400 disabled:opacity-70"
            >
              Selanjutnya
            </Button>
          </div>

          <div
            v-if="form.errors.candidate_ids"
            class="text-red-400 text-sm font-bold mt-4 drop-shadow-md"
          >
            {{ form.errors.candidate_ids }}
          </div>
        </CardHeader>

        <CardContent class="pt-6">
          <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <Card
              v-for="(candidate, number) in candidates"
              :class="[
                selected.includes(candidate.id)
                  ? 'ring-4 ring-[#FF8A00] bg-white/30 scale-[1.02] shadow-xl border-transparent'
                  : 'border-white/20 bg-white/10 hover:bg-white/20 hover:scale-[1.01] shadow-lg',
                'transition-all duration-300 backdrop-blur-md aspect-square cursor-pointer overflow-hidden',
              ]"
              @click="select(candidate.id)"
            >
              <div class="size-full grid grid-cols-2 divide-x divide-white/20">
                <div class="flex flex-col h-full">
                  <div class="relative grow overflow-hidden">
                    <div
                      class="absolute bottom-2 left-2 size-8 flex items-center justify-center shadow-md bg-[#FF8A00] text-white rounded-full text-lg font-bold z-10"
                    >
                      {{ number + 1 }}
                    </div>
                    <img
                      :src="candidate.picture"
                      :alt="candidate.name_1"
                      class="w-full h-full object-cover absolute inset-0"
                    />
                  </div>
                  <CardHeader
                    class="p-4 text-center border-t border-white/20 shrink-0 bg-black/20"
                  >
                    <CardTitle
                      class="leading-normal text-white text-sm lg:text-base font-bold drop-shadow-md"
                    >
                      {{ candidate.name_1 }}
                      <template v-if="candidate.name_2">
                        <br />
                        &
                        <br />
                        {{ candidate.name_2 }}
                      </template>
                    </CardTitle>
                  </CardHeader>
                </div>

                <div class="overflow-y-auto">
                  <CardHeader class="p-4 pb-2">
                    <CardDescription
                      class="text-white font-bold text-xs uppercase tracking-wider mb-1"
                    >
                      Visi
                    </CardDescription>
                    <pre
                      class="font-sans text-xs text-gray-200 text-wrap whitespace-pre-wrap"
                      >{{ candidate.vision }}</pre
                    >
                  </CardHeader>
                  <CardContent
                    class="px-4 pb-4 border-t border-white/10 mt-2 pt-2"
                  >
                    <CardDescription
                      class="text-white font-bold text-xs uppercase tracking-wider mb-1"
                    >
                      Misi
                    </CardDescription>
                    <pre
                      class="font-sans text-xs text-gray-200 text-wrap whitespace-pre-wrap"
                      >{{ candidate.mission }}</pre
                    >
                  </CardContent>
                </div>
              </div>
            </Card>
          </div>
        </CardContent>
      </Card>
    </div>
  </div>
</template>
