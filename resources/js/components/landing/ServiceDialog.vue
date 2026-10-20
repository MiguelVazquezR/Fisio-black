<script setup lang="ts">
import { Check, Clock } from '@lucide/vue';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { formatDuration, formatPrice } from '@/lib/format';
import type { Service } from '@/types';

defineProps<{
    service: Service | null;
    open: boolean;
}>();

const emit = defineEmits<{
    'update:open': [value: boolean];
    book: [service: Service];
}>();
</script>

<template>
    <Dialog :open="open" @update:open="emit('update:open', $event)">
        <DialogContent
            v-if="service"
            class="max-h-[88vh] overflow-y-auto border-hairline bg-white p-6 text-ink sm:max-w-lg"
        >
            <DialogHeader class="text-left">
                <div class="flex flex-wrap items-center gap-2">
                    <span
                        class="rounded-full bg-brand/15 px-2.5 py-1 text-[11px] font-semibold tracking-wide text-[#5c7418] uppercase"
                    >
                        {{ service.category_label }}
                    </span>
                    <span
                        class="inline-flex items-center gap-1.5 rounded-full border border-hairline px-2.5 py-1 text-[11px] font-medium text-ink/60"
                    >
                        <Clock class="size-3" />
                        {{ formatDuration(service.duration_minutes) }}
                    </span>
                </div>
                <DialogTitle
                    class="mt-3 font-display text-2xl font-bold tracking-tight"
                >
                    {{ service.name }}
                </DialogTitle>
                <DialogDescription class="text-sm leading-relaxed text-ink/60">
                    {{ service.description }}
                </DialogDescription>
            </DialogHeader>

            <div v-if="service.benefits.length" class="mt-2">
                <p class="text-xs font-semibold tracking-widest text-ink/45 uppercase">
                    Qué incluye
                </p>
                <ul class="mt-3 space-y-2.5">
                    <li
                        v-for="benefit in service.benefits"
                        :key="benefit"
                        class="flex items-start gap-2.5 text-sm text-ink/75"
                    >
                        <span
                            class="mt-0.5 grid size-5 shrink-0 place-items-center rounded-full bg-brand/20 text-[#5c7418]"
                        >
                            <Check class="size-3" stroke-width="3" />
                        </span>
                        {{ benefit }}
                    </li>
                </ul>
            </div>

            <div class="mt-6 flex items-end justify-between border-t border-hairline pt-5">
                <div>
                    <p class="text-xs text-ink/45">Precio</p>
                    <p class="font-display text-2xl font-bold">
                        {{ formatPrice(service.price) }}
                    </p>
                </div>
                <button
                    type="button"
                    class="inline-flex items-center gap-2 rounded-full bg-ink px-5 py-3 text-sm font-semibold text-white transition hover:bg-brand hover:text-ink"
                    @click="emit('book', service)"
                >
                    Agendar este servicio
                </button>
            </div>
            <p class="text-[11px] leading-relaxed text-ink/40">
                El precio puede ajustarse después de la valoración. Te
                confirmamos todo por WhatsApp antes de tu cita.
            </p>
        </DialogContent>
    </Dialog>
</template>
