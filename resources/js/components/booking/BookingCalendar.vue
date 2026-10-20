<script setup lang="ts">
import { ChevronLeft, ChevronRight } from '@lucide/vue';
import { computed, ref, watch } from 'vue';
import { addDaysISO, todayISO, toISODate } from '@/lib/format';

const props = defineProps<{
    modelValue: string;
    blockedDates: string[];
    maxAdvanceDays: number;
}>();

const emit = defineEmits<{
    'update:modelValue': [value: string];
}>();

const weekdayLabels = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
const today = todayISO();
const maxDate = addDaysISO(today, props.maxAdvanceDays);

const initialDate = props.modelValue
    ? new Date(`${props.modelValue}T12:00:00`)
    : new Date();
const cursor = ref(
    new Date(initialDate.getFullYear(), initialDate.getMonth(), 1),
);

const monthLabel = computed(() => {
    const label = new Intl.DateTimeFormat('es-MX', {
        month: 'long',
        year: 'numeric',
    }).format(cursor.value);

    return label.charAt(0).toUpperCase() + label.slice(1);
});

const cells = computed(() => {
    const year = cursor.value.getFullYear();
    const month = cursor.value.getMonth();
    const firstWeekday = (new Date(year, month, 1).getDay() + 6) % 7;
    const totalDays = new Date(year, month + 1, 0).getDate();
    const rows: (null | {
        date: string;
        day: number;
        disabled: boolean;
        isBlocked: boolean;
    })[] = Array.from({ length: firstWeekday }, () => null);

    for (let day = 1; day <= totalDays; day += 1) {
        const current = new Date(year, month, day);
        const date = toISODate(current);
        const isSunday = current.getDay() === 0;
        const isBlocked = props.blockedDates.includes(date);

        rows.push({
            date,
            day,
            disabled: date < today || date > maxDate || isSunday || isBlocked,
            isBlocked,
        });
    }

    return rows;
});

const canGoPrevious = computed(() => {
    const now = new Date();

    return (
        cursor.value.getFullYear() > now.getFullYear() ||
        (cursor.value.getFullYear() === now.getFullYear() &&
            cursor.value.getMonth() > now.getMonth())
    );
});

const canGoNext = computed(() => {
    const limit = new Date(`${maxDate}T12:00:00`);

    return (
        cursor.value.getFullYear() < limit.getFullYear() ||
        (cursor.value.getFullYear() === limit.getFullYear() &&
            cursor.value.getMonth() < limit.getMonth())
    );
});

function shiftMonth(months: number): void {
    cursor.value = new Date(
        cursor.value.getFullYear(),
        cursor.value.getMonth() + months,
        1,
    );
}

watch(
    () => props.modelValue,
    (value) => {
        if (!value) {
            return;
        }

        const date = new Date(`${value}T12:00:00`);
        cursor.value = new Date(date.getFullYear(), date.getMonth(), 1);
    },
);
</script>

<template>
    <div class="rounded-2xl border border-hairline bg-white p-4 sm:p-5">
        <div class="flex items-center justify-between gap-2">
            <button
                type="button"
                class="grid size-9 place-items-center rounded-full border border-hairline text-ink/60 transition hover:border-ink/30 hover:text-ink disabled:cursor-not-allowed disabled:opacity-30"
                :disabled="!canGoPrevious"
                aria-label="Mes anterior"
                @click="shiftMonth(-1)"
            >
                <ChevronLeft class="size-4" />
            </button>
            <p class="font-display text-sm font-bold text-ink">
                {{ monthLabel }}
            </p>
            <button
                type="button"
                class="grid size-9 place-items-center rounded-full border border-hairline text-ink/60 transition hover:border-ink/30 hover:text-ink disabled:cursor-not-allowed disabled:opacity-30"
                :disabled="!canGoNext"
                aria-label="Mes siguiente"
                @click="shiftMonth(1)"
            >
                <ChevronRight class="size-4" />
            </button>
        </div>

        <div class="mt-4 grid grid-cols-7 gap-1 text-center">
            <span
                v-for="label in weekdayLabels"
                :key="label"
                class="py-1 text-[10px] font-semibold tracking-wide text-ink/35 uppercase"
            >
                {{ label }}
            </span>
        </div>

        <div class="grid grid-cols-7 gap-1">
            <template
                v-for="(cell, index) in cells"
                :key="cell?.date ?? `blank-${index}`"
            >
                <span v-if="!cell" />
                <button
                    v-else
                    type="button"
                    :disabled="cell.disabled"
                    :title="cell.isBlocked ? 'Fecha no disponible' : undefined"
                    :aria-pressed="cell.date === modelValue"
                    :aria-label="cell.date"
                    class="relative grid aspect-square place-items-center rounded-lg text-sm font-medium transition"
                    :class="[
                        cell.date === modelValue
                            ? 'bg-ink text-white'
                            : 'text-ink/70 hover:bg-mist',
                        cell.isBlocked && cell.date !== modelValue
                            ? 'bg-mist/70'
                            : '',
                        cell.disabled
                            ? 'cursor-not-allowed text-ink/20 line-through hover:bg-transparent'
                            : '',
                    ]"
                    @click="emit('update:modelValue', cell.date)"
                >
                    {{ cell.day }}
                </button>
            </template>
        </div>

        <p class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-[10px] text-ink/40">
            <span class="inline-flex items-center gap-1">
                <span class="size-2 rounded-full bg-ink" /> Seleccionado
            </span>
            <span>Domingos y fechas bloqueadas no disponibles</span>
        </p>

    </div>
</template>
