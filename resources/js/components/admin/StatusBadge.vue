<script setup lang="ts">
import { computed } from 'vue';
import type { AppointmentStatusKey } from '@/types';

const props = defineProps<{
    status: AppointmentStatusKey;
}>();

const classes = computed(
    () =>
        ({
            pending: 'border-amber-200 bg-amber-50 text-amber-700',
            confirmed: 'border-brand/40 bg-brand/15 text-[#5c7418]',
            rescheduled: 'border-sky-200 bg-sky-50 text-sky-700',
            cancelled: 'border-rose-200 bg-rose-50 text-rose-600',
        })[props.status],
);

const labels: Record<AppointmentStatusKey, string> = {
    pending: 'Pendiente',
    confirmed: 'Confirmada',
    rescheduled: 'Reagendada',
    cancelled: 'Cancelada',
};
</script>

<template>
    <span
        class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-[11px] font-semibold"
        :class="classes"
    >
        <span class="size-1.5 rounded-full bg-current" />
        {{ labels[status] }}
    </span>
</template>
