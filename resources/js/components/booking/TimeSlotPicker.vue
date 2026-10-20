<script setup lang="ts">
import { CalendarClock } from '@lucide/vue';
import { computed } from 'vue';
import type { AvailabilitySlot } from '@/types';

const props = defineProps<{
    slots: AvailabilitySlot[];
    loading: boolean;
    isOpen: boolean;
    message: string | null;
    modelValue: string;
}>();

const emit = defineEmits<{
    'update:modelValue': [value: string];
}>();

const availableCount = computed(
    () => props.slots.filter((slot) => slot.available).length,
);

function slotTitle(slot: AvailabilitySlot): string {
    return {
        available: 'Horario disponible',
        pending: 'Cita pendiente de confirmación',
        booked: 'Horario ya ocupado',
        unavailable: 'Horario no disponible',
    }[slot.state];
}
</script>

<template>
    <div class="rounded-2xl border border-hairline bg-white p-4 sm:p-5">
        <div class="flex items-center justify-between gap-2">
            <p class="text-sm font-semibold text-ink">Horarios disponibles</p>
            <span
                v-if="!loading && slots.length"
                class="text-[11px] font-medium text-ink/40"
            >
                {{ availableCount }} disponibles
            </span>
        </div>

        <div v-if="loading" class="mt-4 grid grid-cols-3 gap-2">
            <div
                v-for="index in 6"
                :key="index"
                class="h-10 animate-pulse rounded-lg bg-mist"
            />
        </div>

        <div
            v-else-if="!isOpen"
            class="mt-4 flex items-start gap-2.5 rounded-xl border border-amber-200 bg-amber-50 p-3.5 text-xs leading-relaxed text-amber-800"
        >
            <CalendarClock class="mt-0.5 size-4 shrink-0" />
            {{ message ?? 'No hay horarios disponibles para esta fecha.' }}
        </div>

        <div
            v-else-if="slots.length === 0"
            class="mt-4 rounded-xl border border-dashed border-hairline p-4 text-center text-xs text-ink/45"
        >
            {{ message ?? 'No hay horarios para esta fecha.' }}
        </div>

        <div v-else class="mt-4 grid grid-cols-3 gap-2">
            <div
                v-for="slot in slots"
                :key="slot.value"
                class="group relative"
            >
                <button
                    type="button"
                    :disabled="!slot.available"
                    :aria-pressed="slot.value === modelValue"
                    :title="slotTitle(slot)"
                    class="w-full rounded-lg border px-1 py-2.5 text-sm font-semibold transition"
                    :class="
                        slot.value === modelValue
                            ? 'border-ink bg-ink text-white'
                            : slot.available
                              ? 'border-hairline bg-white text-ink hover:border-ink/40'
                              : slot.state === 'pending'
                                ? 'cursor-not-allowed border-amber-300 bg-amber-50 text-amber-800'
                                : 'cursor-not-allowed border-hairline bg-mist text-ink/25'
                    "
                    @click="emit('update:modelValue', slot.value)"
                >
                    <span
                        :class="{
                            'line-through decoration-ink/30':
                                slot.state === 'booked',
                        }"
                    >
                        {{ slot.label }}
                    </span>
                </button>

                <span
                    v-if="slot.state === 'pending'"
                    class="pointer-events-none absolute -top-9 left-1/2 z-20 hidden -translate-x-1/2 rounded-md bg-ink px-2.5 py-1.5 text-[10px] font-medium whitespace-nowrap text-white shadow-lg group-hover:block group-focus-within:block"
                >
                    Cita pendiente de confirmación
                </span>
            </div>
        </div>

        <div
            v-if="!loading && isOpen && slots.length"
            class="mt-3 space-y-1 text-[10px] leading-relaxed text-ink/40"
        >
            <p class="flex items-center gap-1.5">
                <span class="size-2 shrink-0 rounded-full bg-amber-400" />
                En amarillo: otra cita está pendiente de confirmación en ese
                horario.
            </p>
            <p>
                Los horarios tachados ya están ocupados. La duración del
                servicio reserva el bloque completo.
            </p>
        </div>
    </div>
</template>
