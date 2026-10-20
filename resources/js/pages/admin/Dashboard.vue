<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import {
    CalendarDays,
    CalendarOff,
    CheckCircle2,
    ChevronLeft,
    ChevronRight,
    Clock,
    Home,
    MessageCircle,
} from '@lucide/vue';
import { computed, ref } from 'vue';
import AppointmentCard from '@/components/admin/AppointmentCard.vue';
import BlockedDatesPanel from '@/components/admin/BlockedDatesPanel.vue';
import RescheduleDialog from '@/components/admin/RescheduleDialog.vue';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { formatDateLong, todayISO, toISODate } from '@/lib/format';
import { dashboard as adminDashboard } from '@/routes/admin';
import { cancel } from '@/routes/admin/appointments';
import type {
    Appointment,
    AppointmentCounts,
    AppointmentStatusKey,
    BlockedDate,
    DashboardFilters,
    StatusOption,
    WeekDay,
} from '@/types';

const props = defineProps<{
    appointments: Appointment[];
    filters: DashboardFilters;
    dateLabel: string;
    counts: AppointmentCounts;
    week: WeekDay[];
    statusOptions: StatusOption[];
    blockedDates: BlockedDate[];
}>();

const rescheduleTarget = ref<Appointment | null>(null);
const rescheduleOpen = ref(false);
const cancelTarget = ref<Appointment | null>(null);

const maxAdvanceDays = 60;

const groups = computed(() => {
    if (props.filters.view !== 'week') {
        return [
            {
                date: props.filters.date,
                label: props.dateLabel,
                appointments: props.appointments,
            },
        ];
    }

    const map = new Map<string, Appointment[]>();

    for (const appointment of props.appointments) {
        map.set(appointment.appointment_date, [
            ...(map.get(appointment.appointment_date) ?? []),
            appointment,
        ]);
    }

    return [...map.entries()].map(([date, appointments]) => ({
        date,
        label: formatDateLong(date),
        appointments,
    }));
});

function applyFilters(overrides: Partial<DashboardFilters> = {}): void {
    router.get(
        adminDashboard.url(),
        {
            date: overrides.date ?? props.filters.date,
            status:
                (overrides.status === undefined
                    ? props.filters.status
                    : overrides.status) ?? undefined,
            view: overrides.view ?? props.filters.view,
        },
        { preserveState: true, preserveScroll: true, replace: true },
    );
}

function shift(offset: number): void {
    const base = new Date(`${props.filters.date}T12:00:00`);
    base.setDate(
        base.getDate() + offset * (props.filters.view === 'week' ? 7 : 1),
    );
    applyFilters({ date: toISODate(base) });
}

function goToday(): void {
    applyFilters({ date: todayISO() });
}

function setStatus(status: AppointmentStatusKey | null): void {
    applyFilters({ status });
}

function openReschedule(appointment: Appointment): void {
    rescheduleTarget.value = appointment;
    rescheduleOpen.value = true;
}

function openCancel(appointment: Appointment): void {
    cancelTarget.value = appointment;
}

function confirmCancel(): void {
    if (!cancelTarget.value) {
        return;
    }

    router.patch(
        cancel.url(cancelTarget.value.id),
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                cancelTarget.value = null;
            },
        },
    );
}
</script>

<template>
    <Head title="Agenda" />

    <div class="space-y-5">
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"
        >
            <div>
                <p
                    class="text-xs font-semibold tracking-[0.2em] text-[#6d8a1f] uppercase"
                >
                    Panel de citas
                </p>
                <h1 class="mt-1 font-display text-2xl font-bold sm:text-3xl">
                    Agenda
                </h1>
                <p class="mt-1 text-sm text-ink/50">{{ dateLabel }}</p>
            </div>

            <div
                class="inline-flex w-fit rounded-xl border border-hairline bg-white p-1"
            >
                <button
                    type="button"
                    class="rounded-lg px-4 py-2 text-xs font-semibold transition"
                    :class="
                        filters.view === 'day'
                            ? 'bg-ink text-white'
                            : 'text-ink/55 hover:text-ink'
                    "
                    @click="applyFilters({ view: 'day' })"
                >
                    Día
                </button>
                <button
                    type="button"
                    class="rounded-lg px-4 py-2 text-xs font-semibold transition"
                    :class="
                        filters.view === 'week'
                            ? 'bg-ink text-white'
                            : 'text-ink/55 hover:text-ink'
                    "
                    @click="applyFilters({ view: 'week' })"
                >
                    Semana
                </button>
            </div>
        </div>

        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-hairline bg-white p-4">
                <div class="flex items-center justify-between">
                    <p class="text-xs text-ink/45">
                        {{
                            filters.view === 'week'
                                ? 'En la semana'
                                : 'En el día'
                        }}
                    </p>
                    <CalendarDays class="size-4 text-ink/25" />
                </div>
                <p class="mt-2 font-display text-2xl font-bold">
                    {{ counts.total }}
                </p>
            </div>
            <div class="rounded-2xl border border-hairline bg-white p-4">
                <div class="flex items-center justify-between">
                    <p class="text-xs text-ink/45">Por confirmar</p>
                    <Clock class="size-4 text-amber-500" />
                </div>
                <p class="mt-2 font-display text-2xl font-bold">
                    {{ counts.pending }}
                </p>
            </div>
            <div class="rounded-2xl border border-hairline bg-white p-4">
                <div class="flex items-center justify-between">
                    <p class="text-xs text-ink/45">Confirmadas</p>
                    <CheckCircle2 class="size-4 text-[#6d8a1f]" />
                </div>
                <p class="mt-2 font-display text-2xl font-bold">
                    {{ counts.confirmed }}
                </p>
            </div>
            <div class="rounded-2xl border border-hairline bg-white p-4">
                <div class="flex items-center justify-between">
                    <p class="text-xs text-ink/45">A domicilio</p>
                    <Home class="size-4 text-sky-500" />
                </div>
                <p class="mt-2 font-display text-2xl font-bold">
                    {{ counts.home }}
                </p>
            </div>
        </div>

        <div
            class="grid grid-cols-7 gap-1.5 rounded-2xl border border-hairline bg-white p-2"
        >
            <button
                v-for="day in week"
                :key="day.date"
                type="button"
                class="rounded-xl px-1 py-2 text-center transition"
                :class="
                    day.is_selected
                        ? 'bg-ink text-white'
                        : 'text-ink/60 hover:bg-mist'
                "
                @click="applyFilters({ date: day.date })"
            >
                <span
                    class="block text-[10px] tracking-wide uppercase opacity-60"
                >
                    {{ day.weekday }}
                </span>
                <span class="mt-0.5 block font-display text-base font-bold">
                    {{ day.day }}
                </span>
                <span class="mt-0.5 block text-[10px] opacity-60">
                    {{ day.count }}
                </span>
            </button>
        </div>

        <div class="rounded-2xl border border-hairline bg-white p-3 sm:p-4">
            <div
                class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
            >
                <div class="flex items-center gap-1.5">
                    <button
                        type="button"
                        class="grid size-8 place-items-center rounded-lg border border-hairline text-ink/55 transition hover:text-ink"
                        aria-label="Anterior"
                        @click="shift(-1)"
                    >
                        <ChevronLeft class="size-4" />
                    </button>
                    <button
                        type="button"
                        class="rounded-lg border border-hairline px-3 py-1.5 text-xs font-semibold text-ink/60 transition hover:text-ink"
                        @click="goToday"
                    >
                        Hoy
                    </button>
                    <button
                        type="button"
                        class="grid size-8 place-items-center rounded-lg border border-hairline text-ink/55 transition hover:text-ink"
                        aria-label="Siguiente"
                        @click="shift(1)"
                    >
                        <ChevronRight class="size-4" />
                    </button>
                </div>

                <div class="flex flex-wrap gap-1.5">
                    <button
                        type="button"
                        class="rounded-full border px-3 py-1.5 text-[11px] font-semibold transition"
                        :class="
                            filters.status === null
                                ? 'border-ink bg-ink text-white'
                                : 'border-hairline text-ink/55 hover:text-ink'
                        "
                        @click="setStatus(null)"
                    >
                        Todas
                    </button>
                    <button
                        v-for="option in statusOptions"
                        :key="option.value"
                        type="button"
                        class="rounded-full border px-3 py-1.5 text-[11px] font-semibold transition"
                        :class="
                            filters.status === option.value
                                ? 'border-ink bg-ink text-white'
                                : 'border-hairline text-ink/55 hover:text-ink'
                        "
                        @click="setStatus(option.value)"
                    >
                        {{ option.label }}
                    </button>
                </div>
            </div>
        </div>

        <div class="grid gap-5 xl:grid-cols-[1fr_320px]">
            <div class="space-y-5">
                <div
                    v-for="group in groups"
                    :key="group.date"
                    class="space-y-2.5"
                >
                    <div class="flex items-center justify-between">
                        <h2
                            class="text-[11px] font-semibold tracking-widest text-ink/40 uppercase"
                        >
                            {{ group.label }}
                        </h2>
                        <span class="text-[11px] text-ink/35">
                            {{ group.appointments.length }} citas
                        </span>
                    </div>

                    <div v-if="group.appointments.length" class="space-y-2.5">
                        <AppointmentCard
                            v-for="appointment in group.appointments"
                            :key="appointment.id"
                            :appointment="appointment"
                            @reschedule="openReschedule"
                            @cancel="openCancel"
                        />
                    </div>

                    <div
                        v-else
                        class="rounded-2xl border border-dashed border-hairline bg-white p-8 text-center"
                    >
                        <CalendarOff class="mx-auto size-6 text-ink/20" />
                        <p class="mt-2 text-sm text-ink/45">
                            Sin citas para este filtro.
                        </p>
                        <p class="mt-1 text-xs text-ink/30">
                            Cuando alguien agende desde el sitio aparecerá aquí.
                        </p>
                    </div>
                </div>
            </div>

            <aside class="space-y-5">
                <BlockedDatesPanel :blocked-dates="blockedDates" />

                <div class="rounded-2xl border border-hairline bg-white p-5">
                    <p class="text-sm font-semibold text-ink">WhatsApp</p>
                    <p class="mt-1 text-xs leading-relaxed text-ink/50">
                        Envía confirmación, nueva fecha o recordatorio desde el
                        menú de cada cita. El mensaje se abre listo para enviar.
                    </p>
                    <div
                        class="mt-3 inline-flex items-center gap-1.5 rounded-full bg-brand/15 px-3 py-1 text-[11px] font-semibold text-[#5c7418]"
                    >
                        <MessageCircle class="size-3" />
                        Mensajes integrados
                    </div>
                </div>
            </aside>
        </div>
    </div>

    <RescheduleDialog
        v-model:open="rescheduleOpen"
        :appointment="rescheduleTarget"
        :blocked-dates="blockedDates.map((item) => item.date)"
        :max-advance-days="maxAdvanceDays"
    />

    <Dialog
        :open="cancelTarget !== null"
        @update:open="(open) => (!open ? (cancelTarget = null) : null)"
    >
        <DialogContent
            class="border-hairline bg-white p-6 text-ink sm:max-w-md"
        >
            <DialogHeader class="text-left">
                <DialogTitle class="font-display text-lg font-bold">
                    Cancelar cita
                </DialogTitle>
                <DialogDescription class="text-sm text-ink/55">
                    <template v-if="cancelTarget">
                        ¿Cancelar la cita de {{ cancelTarget.patient_name }} del
                        {{ cancelTarget.date_label }} a las
                        {{ cancelTarget.appointment_time }}? El horario quedará
                        libre.
                    </template>
                </DialogDescription>
            </DialogHeader>

            <div class="flex justify-end gap-2">
                <button
                    type="button"
                    class="rounded-lg border border-hairline px-4 py-2.5 text-sm font-semibold text-ink/60 transition hover:text-ink"
                    @click="cancelTarget = null"
                >
                    Volver
                </button>
                <button
                    type="button"
                    class="rounded-lg bg-rose-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-rose-700"
                    @click="confirmCancel"
                >
                    Sí, cancelar
                </button>
            </div>
        </DialogContent>
    </Dialog>
</template>
