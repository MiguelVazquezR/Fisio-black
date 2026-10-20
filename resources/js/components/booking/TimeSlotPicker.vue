<script setup lang="ts">
import { CalendarClock } from '@lucide/vue';
import type { AvailabilitySlot } from '@/types';

defineProps<{
    slots: AvailabilitySlot[];
    loading: boolean;
    isOpen: boolean;
    message: string | null;
    modelValue: string;
}>();

const emit = defineEmits<{
    'update:modelValue': [value: string];
}>();
</script>

<template>
    <div class="rounded-2xl border border-hairline bg-white p-4 sm:p-5">
        <div class="flex items-center justify-between gap-2">
            <p class="text-sm font-semibold text-ink">Horarios disponibles</p>
            <span
                v-if="!loading && slots.length"
                class="text-[11px] font-medium text-ink/40"
            >
                {{ slots.length }} opciones
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
            <button
                v-for="slot in slots"
                :key="slot.value"
                type="button"
                :disabled="!slot.available"
                :aria-pressed="slot.value === modelValue"
                class="rounded-lg border px-2 py-2.5 text-sm font-semibold transition"
                :class="
                    slot.value === modelValue
                        ? 'border-ink bg-ink text-white'
                        : slot.available
                          ? 'border-hairline bg-white text-ink hover:border-ink/40'
                          : 'cursor-not-allowed border-hairline bg-mist text-ink/25 line-through'
                "
                @click="emit('update:modelValue', slot.value)"
            >
                {{ slot.value }}
            </button>
        </div>

        <p v-if="!loading && isOpen && slots.length" class="mt-3 text-[10px] text-ink/40">
            Los horarios tachados ya están ocupados. La duración del servicio
            reserva el bloque completo.
        </p>
    </div>
</template>
