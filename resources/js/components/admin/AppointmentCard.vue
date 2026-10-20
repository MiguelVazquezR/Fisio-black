<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import {
    CalendarClock,
    CheckCircle2,
    Clock,
    Home,
    MessageCircle,
    Phone,
    RotateCcw,
    XCircle,
} from '@lucide/vue';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import StatusBadge from '@/components/admin/StatusBadge.vue';
import { confirm } from '@/routes/admin/appointments';
import type { Appointment } from '@/types';

const props = defineProps<{
    appointment: Appointment;
}>();

const emit = defineEmits<{
    reschedule: [appointment: Appointment];
    cancel: [appointment: Appointment];
}>();

function confirmAppointment(): void {
    router.patch(
        confirm.url(props.appointment.id),
        {},
        { preserveScroll: true },
    );
}

function openWhatsApp(url: string): void {
    window.open(url, '_blank', 'noopener,noreferrer');
}
</script>

<template>
    <article class="rounded-2xl border border-hairline bg-white p-4 sm:p-5">
        <div
            class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between"
        >
            <div class="flex min-w-0 gap-4">
                <div class="w-16 shrink-0 rounded-xl bg-mist py-2 text-center">
                    <p class="font-display text-base font-bold text-ink">
                        {{ appointment.appointment_time }}
                    </p>
                    <p class="text-[10px] tracking-wide text-ink/40 uppercase">
                        {{ appointment.service.duration_minutes }} min
                    </p>
                </div>

                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <h3 class="text-sm font-bold text-ink">
                            {{ appointment.patient_name }}
                        </h3>
                        <StatusBadge :status="appointment.status" />
                        <span
                            v-if="appointment.is_home_service"
                            class="inline-flex items-center gap-1 rounded-full bg-mist px-2 py-0.5 text-[10px] font-semibold text-ink/55"
                        >
                            <Home class="size-3" />
                            Domicilio
                        </span>
                    </div>

                    <p class="mt-1 text-sm text-ink/60">
                        {{ appointment.service.name }}
                    </p>

                    <div
                        class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-xs text-ink/45"
                    >
                        <a
                            :href="`tel:${appointment.patient_phone}`"
                            class="inline-flex items-center gap-1.5 transition hover:text-ink"
                        >
                            <Phone class="size-3.5" />
                            {{ appointment.patient_phone }}
                        </a>
                        <span class="inline-flex items-start gap-1.5">
                            <Clock class="mt-0.5 size-3.5" />
                            {{ appointment.date_label }}
                        </span>
                        <span
                            v-if="appointment.is_home_service"
                            class="inline-flex items-start gap-1.5"
                        >
                            <Home class="mt-0.5 size-3.5" />
                            {{ appointment.address_label }}
                        </span>
                        <span v-else class="inline-flex items-center gap-1.5">
                            <CalendarClock class="size-3.5" />
                            {{ appointment.modality_label }}
                        </span>
                    </div>

                    <p
                        v-if="appointment.notes"
                        class="mt-2 rounded-lg bg-mist px-3 py-2 text-xs leading-relaxed text-ink/60"
                    >
                        {{ appointment.notes }}
                    </p>
                </div>
            </div>

            <div class="flex shrink-0 flex-wrap items-center gap-2">
                <button
                    v-if="
                        appointment.status === 'pending' ||
                        appointment.status === 'rescheduled'
                    "
                    type="button"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-brand px-3 py-2 text-xs font-semibold text-ink transition hover:bg-brand-strong"
                    @click="confirmAppointment"
                >
                    <CheckCircle2 class="size-3.5" />
                    Confirmar
                </button>

                <button
                    v-if="appointment.status !== 'cancelled'"
                    type="button"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-hairline px-3 py-2 text-xs font-semibold text-ink/60 transition hover:border-ink/30 hover:text-ink"
                    @click="emit('reschedule', appointment)"
                >
                    <RotateCcw class="size-3.5" />
                    Reagendar
                </button>

                <button
                    v-if="appointment.status !== 'cancelled'"
                    type="button"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-rose-200 px-3 py-2 text-xs font-semibold text-rose-600 transition hover:bg-rose-50"
                    @click="emit('cancel', appointment)"
                >
                    <XCircle class="size-3.5" />
                    Cancelar
                </button>

                <DropdownMenu>
                    <DropdownMenuTrigger as-child>
                        <button
                            type="button"
                            class="inline-flex items-center gap-1.5 rounded-lg border border-hairline px-3 py-2 text-xs font-semibold text-ink/60 transition hover:border-brand/60 hover:text-[#5c7418]"
                        >
                            <MessageCircle class="size-3.5" />
                            WhatsApp
                        </button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end" class="w-60">
                        <DropdownMenuLabel>
                            Mensajes para
                            {{ appointment.patient_name.split(' ')[0] }}
                        </DropdownMenuLabel>
                        <DropdownMenuSeparator />
                        <DropdownMenuItem
                            @click="
                                openWhatsApp(appointment.whatsapp.confirmation)
                            "
                        >
                            <CheckCircle2 />
                            Confirmación de cita
                        </DropdownMenuItem>
                        <DropdownMenuItem
                            @click="
                                openWhatsApp(appointment.whatsapp.reschedule)
                            "
                        >
                            <RotateCcw />
                            Nueva fecha y hora
                        </DropdownMenuItem>
                        <DropdownMenuItem
                            @click="openWhatsApp(appointment.whatsapp.reminder)"
                        >
                            <CalendarClock />
                            Recordatorio
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            </div>

        </div>
    </article>
</template>
