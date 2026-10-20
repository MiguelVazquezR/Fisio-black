<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { Loader2 } from '@lucide/vue';
import { ref, watch } from 'vue';
import BookingCalendar from '@/components/booking/BookingCalendar.vue';
import TimeSlotPicker from '@/components/booking/TimeSlotPicker.vue';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { availability } from '@/routes/admin';
import { reschedule } from '@/routes/admin/appointments';
import type { Appointment, AvailabilityDay } from '@/types';

const props = defineProps<{
    open: boolean;
    appointment: Appointment | null;
    blockedDates: string[];
    maxAdvanceDays: number;
}>();

const emit = defineEmits<{
    'update:open': [value: boolean];
}>();

const form = useForm({
    appointment_date: '',
    appointment_time: '',
});

const day = ref<AvailabilityDay | null>(null);
const loading = ref(false);
const requestError = ref<string | null>(null);
let slotsRequest: AbortController | null = null;

async function loadSlots(): Promise<void> {
    slotsRequest?.abort();
    day.value = null;
    requestError.value = null;

    if (!props.appointment || !form.appointment_date) {
        loading.value = false;

        return;
    }

    const request = new AbortController();
    slotsRequest = request;
    loading.value = true;

    try {
        const response = await fetch(
            availability.url({
                query: {
                    date: form.appointment_date,
                    service_id: props.appointment.service.id,
                    modality: props.appointment.is_home_service
                        ? 'home'
                        : 'clinic',
                    ignore: props.appointment.id,
                },
            }),
            {
                signal: request.signal,
                headers: { Accept: 'application/json' },
            },
        );

        if (!response.ok) {
            throw new Error('availability');
        }

        day.value = (await response.json()) as AvailabilityDay;
    } catch (error) {
        if ((error as Error).name !== 'AbortError') {
            requestError.value =
                'No pudimos consultar la disponibilidad. Intenta de nuevo.';
        }
    } finally {
        if (!request.signal.aborted) {
            loading.value = false;
        }
    }
}

watch(
    () => props.open,
    (isOpen) => {
        if (!isOpen || !props.appointment) {
            return;
        }

        form.appointment_date = props.appointment.appointment_date;
        form.appointment_time = '';
        form.clearErrors();
        void loadSlots();
    },
);

watch(
    () => form.appointment_date,
    (value, previous) => {
        if (!value || value === previous) {
            return;
        }

        form.appointment_time = '';
        void loadSlots();
    },
);

function submit(): void {
    if (!props.appointment) {
        return;
    }

    form.patch(reschedule.url(props.appointment.id), {
        preserveScroll: true,
        onSuccess: () => emit('update:open', false),
    });
}
</script>

<template>
    <Dialog :open="open" @update:open="emit('update:open', $event)">
        <DialogContent
            v-if="appointment"
            class="max-h-[90vh] overflow-y-auto border-hairline bg-white p-6 text-ink sm:max-w-lg"
        >
            <DialogHeader class="text-left">
                <DialogTitle class="font-display text-xl font-bold">
                    Reagendar cita
                </DialogTitle>
                <DialogDescription class="text-sm text-ink/55">
                    {{ appointment.patient_name }} ·
                    {{ appointment.service.name }} · Actual:
                    {{ appointment.date_label }} a las
                    {{ appointment.appointment_time }}
                </DialogDescription>
            </DialogHeader>

            <div class="space-y-4">
                <BookingCalendar
                    v-model="form.appointment_date"
                    :blocked-dates="blockedDates"
                    :max-advance-days="maxAdvanceDays"
                />

                <TimeSlotPicker
                    v-model="form.appointment_time"
                    :slots="day?.slots ?? []"
                    :loading="loading"
                    :is-open="day?.is_open ?? false"
                    :message="requestError ?? day?.message ?? null"
                />

                <p
                    v-if="
                        form.errors.appointment_time ||
                        form.errors.appointment_date
                    "
                    class="text-xs text-red-600"
                >
                    {{
                        form.errors.appointment_time ??
                        form.errors.appointment_date
                    }}
                </p>

                <div
                    class="flex justify-end gap-2 border-t border-hairline pt-4"
                >
                    <button
                        type="button"
                        class="rounded-lg border border-hairline px-4 py-2.5 text-sm font-semibold text-ink/60 transition hover:text-ink"
                        @click="emit('update:open', false)"
                    >
                        Cerrar
                    </button>
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-lg bg-ink px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-brand hover:text-ink disabled:cursor-not-allowed disabled:opacity-50"
                        :disabled="
                            form.processing ||
                            !form.appointment_time ||
                            !day?.is_open
                        "
                        @click="submit"
                    >
                        <Loader2
                            v-if="form.processing"
                            class="size-4 animate-spin"
                        />
                        Guardar nueva fecha
                    </button>
                </div>
            </div>
        </DialogContent>
    </Dialog>
</template>

