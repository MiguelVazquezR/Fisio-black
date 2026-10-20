<script setup lang="ts">
import {
    CalendarDays,
    CheckCircle2,
    Home,
    Loader2,
    MapPin,
    MessageCircle,
} from '@lucide/vue';
import { useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import BookingCalendar from '@/components/booking/BookingCalendar.vue';
import BookingSummary from '@/components/booking/BookingSummary.vue';
import TimeSlotPicker from '@/components/booking/TimeSlotPicker.vue';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogTitle,
} from '@/components/ui/dialog';
import { useBookingSelection } from '@/composables/useBookingSelection';
import { formatDuration, formatPrice, formatTime12, whatsappUrl } from '@/lib/format';
import { store } from '@/routes/appointments';
import { availability } from '@/routes/booking';
import type {
    Appointment,
    AvailabilityDay,
    BookingConfig,
    ClinicInfo,
    Service,
} from '@/types';

const props = defineProps<{
    services: Service[];
    blockedDates: string[];
    clinic: ClinicInfo;
    booking: BookingConfig;
    selectedService: string | null;
    recentAppointment: Appointment | null;
}>();

const { selectedServiceSlug, consumeService } = useBookingSelection();

const initialSlug = selectedServiceSlug.value ?? props.selectedService;
const initialService: Service | null =
    props.services.find((service) => service.slug === initialSlug) ??
    props.services[0] ??
    null;

type BookingFormData = {
    service_id: number | null;
    is_home_service: boolean;
    appointment_date: string;
    appointment_time: string;
    patient_name: string;
    patient_phone: string;
    patient_email: string;
    address_notes: string;
    notes: string;
};

const form = useForm<BookingFormData>({
    service_id: initialService?.id ?? null,
    is_home_service: false,
    appointment_date: '',
    appointment_time: '',
    patient_name: '',
    patient_phone: '',
    patient_email: '',
    address_notes: '',
    notes: '',
});

const selectedService = computed(
    () =>
        props.services.find((service) => service.id === form.service_id) ?? null,
);

const serviceGroups = computed(() => {
    const groups = new Map<string, { label: string; services: Service[] }>();

    for (const service of props.services) {
        const group = groups.get(service.category) ?? {
            label: service.category_label,
            services: [],
        };

        group.services.push(service);
        groups.set(service.category, group);
    }

    return [...groups.values()];
});

const availabilityResult = ref<AvailabilityDay | null>(null);
const loadingSlots = ref(false);
const slotError = ref<string | null>(null);
let slotsRequest: AbortController | null = null;

const successOpen = ref(false);
const successAppointment = ref<Appointment | null>(null);

function confirmationWhatsApp(appointment: Appointment): string {
    return whatsappUrl(
        props.clinic.whatsapp,
        `Hola, acabo de agendar mi cita (folio ${appointment.folio}) para el ${appointment.date_label} a las ${appointment.appointment_time}. Quisiera confirmarla.`,
    );
}

const successWhatsApp = computed(() =>
    props.recentAppointment
        ? confirmationWhatsApp(props.recentAppointment)
        : whatsappUrl(props.clinic.whatsapp, 'Hola, quisiera confirmar mi cita.'),
);

function resetBookingForm(): void {
    form.reset();
    form.service_id = null;
    form.appointment_date = '';
    form.appointment_time = '';
    form.is_home_service = false;
    form.clearErrors();
    availabilityResult.value = null;
    slotError.value = null;
}

watch(
    () => props.recentAppointment,
    (appointment) => {
        if (!appointment) {
            return;
        }

        successAppointment.value = appointment;
        successOpen.value = true;
        resetBookingForm();
    },
    { immediate: true },
);

async function loadAvailability(): Promise<void> {
    slotsRequest?.abort();
    availabilityResult.value = null;
    slotError.value = null;

    if (!form.service_id || !form.appointment_date) {
        loadingSlots.value = false;

        return;
    }

    const request = new AbortController();
    slotsRequest = request;
    loadingSlots.value = true;

    try {
        const response = await fetch(
            availability.url({
                query: {
                    date: form.appointment_date,
                    service_id: form.service_id,
                    modality: form.is_home_service ? 'home' : 'clinic',
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

        availabilityResult.value = (await response.json()) as AvailabilityDay;
    } catch (error) {
        if ((error as Error).name !== 'AbortError') {
            slotError.value =
                'No pudimos consultar los horarios. Intenta de nuevo o escríbenos por WhatsApp.';
        }
    } finally {
        if (!request.signal.aborted) {
            loadingSlots.value = false;
        }
    }
}

watch(
    () => [form.service_id, form.appointment_date, form.is_home_service],
    () => {
        form.appointment_time = '';
        void loadAvailability();
    },
);

watch(selectedServiceSlug, (slug) => {
    if (!slug) {
        return;
    }

    const match = props.services.find((service) => service.slug === slug);

    if (match) {
        form.service_id = match.id;
        form.appointment_time = '';
    }

    consumeService();
});

const fieldClass =
    'h-11 w-full rounded-xl border border-hairline bg-white px-3.5 text-sm text-ink outline-none transition placeholder:text-ink/30 focus:border-brand focus:ring-2 focus:ring-brand/30';
const labelClass = 'mb-1.5 block text-xs font-semibold text-ink/60';
const errorClass = 'mt-1 text-xs text-red-600';

function submit(): void {
    form.post(store.url(), { preserveScroll: true });
}
</script>

<template>
    <section id="agendar" class="scroll-mt-24 bg-mist py-20 sm:py-28">
        <div class="content-shell">
            <div class="max-w-2xl">
                <p
                    class="text-xs font-semibold tracking-[0.2em] text-[#6d8a1f] uppercase"
                >
                    Agenda
                </p>
                <h2
                    class="mt-3 font-display text-3xl leading-tight font-bold tracking-tight text-ink sm:text-4xl"
                >
                    Reserva tu cita en línea.
                </h2>
                <p class="mt-4 text-sm leading-relaxed text-ink/60 sm:text-base">
                    Elige el servicio, la fecha y el horario que te acomoden. Te
                    confirmamos por WhatsApp y resolvemos cualquier duda antes
                    de tu visita.
                </p>
            </div>

            <div
                v-if="recentAppointment"
                class="mt-8 overflow-hidden rounded-3xl border border-brand/50 bg-white"
            >
                <div
                    class="flex flex-col gap-5 p-6 sm:flex-row sm:items-start sm:justify-between sm:p-7"
                >
                    <div class="flex gap-4">
                        <span
                            class="grid size-11 shrink-0 place-items-center rounded-full bg-brand text-ink"
                        >
                            <CheckCircle2 class="size-6" />
                        </span>
                        <div>
                            <p
                                class="text-[11px] font-semibold tracking-widest text-[#5c7418] uppercase"
                            >
                                Cita registrada · {{ recentAppointment.folio }}
                            </p>
                            <h3
                                class="mt-1 font-display text-xl font-bold text-ink"
                            >
                                ¡Te esperamos,
                                {{ recentAppointment.patient_name.split(' ')[0] }}!
                            </h3>
                            <p class="mt-2 text-sm text-ink/60">
                                {{ recentAppointment.service.name }} ·
                                {{ recentAppointment.date_label }} ·
                                {{ recentAppointment.appointment_time }} ·
                                {{ recentAppointment.modality_label }}
                            </p>
                            <p class="mt-1 text-xs text-ink/45">
                                Estatus: {{ recentAppointment.status_label }}. Te
                                confirmaremos por WhatsApp.
                            </p>
                        </div>
                    </div>
                    <a
                        :href="successWhatsApp"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex shrink-0 items-center justify-center gap-2 rounded-full bg-ink px-5 py-3 text-sm font-semibold text-white transition hover:bg-brand hover:text-ink"
                    >
                        <MessageCircle class="size-4" />
                        Confirmar por WhatsApp
                    </a>
                </div>
            </div>

            <div class="mt-10 grid gap-6 lg:grid-cols-[1.35fr_0.65fr]">
                <div class="space-y-5">
                    <div class="rounded-3xl border border-hairline bg-white p-6 sm:p-7">
                        <div class="flex items-center gap-3">
                            <span
                                class="grid size-8 shrink-0 place-items-center rounded-full bg-ink text-xs font-bold text-white"
                            >
                                1
                            </span>
                            <div>
                                <h3 class="font-display text-lg font-bold text-ink">
                                    Servicio y modalidad
                                </h3>
                                <p class="text-xs text-ink/45">
                                    ¿Qué necesitas y dónde te atendemos?
                                </p>
                            </div>
                        </div>

                        <div class="mt-5">
                            <label for="service" :class="labelClass">
                                Servicio
                            </label>
                            <select
                                id="service"
                                v-model="form.service_id"
                                :class="fieldClass"
                            >
                                <option :value="null" disabled>
                                    Selecciona un servicio
                                </option>
                                <optgroup
                                    v-for="group in serviceGroups"
                                    :key="group.label"
                                    :label="group.label"
                                >
                                    <option
                                        v-for="service in group.services"
                                        :key="service.id"
                                        :value="service.id"
                                    >
                                        {{ service.name }} —
                                        {{ formatPrice(service.price) }}
                                    </option>
                                </optgroup>
                            </select>
                            <p v-if="form.errors.service_id" :class="errorClass">
                                {{ form.errors.service_id }}
                            </p>

                            <div
                                v-if="selectedService"
                                class="mt-3 rounded-xl bg-mist px-3.5 py-3"
                            >
                                <p class="text-xs leading-relaxed text-ink/60">
                                    {{ selectedService.description }}
                                </p>
                                <p class="mt-2 text-[11px] font-medium text-ink/40">
                                    {{ formatDuration(selectedService.duration_minutes) }}
                                    · {{ selectedService.category_label }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-5 grid gap-2 sm:grid-cols-2">
                            <button
                                type="button"
                                class="flex items-start gap-3 rounded-xl border p-3.5 text-left transition"
                                :class="
                                    !form.is_home_service
                                        ? 'border-brand bg-brand/10'
                                        : 'border-hairline bg-white hover:border-ink/20'
                                "
                                @click="form.is_home_service = false"
                            >
                                <MapPin class="mt-0.5 size-4 shrink-0 text-[#5c7418]" />
                                <span>
                                    <span class="block text-sm font-semibold text-ink">
                                        En clínica
                                    </span>
                                    <span class="block text-[11px] text-ink/45">
                                        {{ formatTime12(booking.clinic_hours[0]) }}
                                        –
                                        {{ formatTime12(booking.clinic_hours[1]) }}
                                    </span>
                                </span>
                            </button>
                            <button
                                type="button"
                                class="flex items-start gap-3 rounded-xl border p-3.5 text-left transition"
                                :class="
                                    form.is_home_service
                                        ? 'border-brand bg-brand/10'
                                        : 'border-hairline bg-white hover:border-ink/20'
                                "
                                @click="form.is_home_service = true"
                            >
                                <Home class="mt-0.5 size-4 shrink-0 text-[#5c7418]" />
                                <span>
                                    <span class="block text-sm font-semibold text-ink">
                                        A domicilio
                                    </span>
                                    <span class="block text-[11px] text-ink/45">
                                        {{ formatTime12(booking.home_hours[0]) }}
                                        –
                                        {{ formatTime12(booking.home_hours[1]) }}
                                    </span>
                                </span>
                            </button>
                        </div>
                    </div>

                    <div class="rounded-3xl border border-hairline bg-white p-6 sm:p-7">
                        <div class="flex items-center gap-3">
                            <span
                                class="grid size-8 shrink-0 place-items-center rounded-full bg-ink text-xs font-bold text-white"
                            >
                                2
                            </span>
                            <div>
                                <h3 class="font-display text-lg font-bold text-ink">
                                    Fecha y hora
                                </h3>
                                <p class="text-xs text-ink/45">
                                    Disponibilidad en tiempo real
                                </p>
                            </div>
                        </div>

                        <div class="mt-5 grid gap-4 xl:grid-cols-2">
                            <div>
                                <BookingCalendar
                                    v-model="form.appointment_date"
                                    :blocked-dates="blockedDates"
                                    :max-advance-days="booking.max_advance_days"
                                />
                                <p
                                    v-if="form.errors.appointment_date"
                                    :class="errorClass"
                                >
                                    {{ form.errors.appointment_date }}
                                </p>
                            </div>

                            <div>
                                <TimeSlotPicker
                                    v-if="form.appointment_date"
                                    v-model="form.appointment_time"
                                    :slots="availabilityResult?.slots ?? []"
                                    :loading="loadingSlots"
                                    :is-open="availabilityResult?.is_open ?? false"
                                    :message="
                                        slotError ??
                                        availabilityResult?.message ??
                                        null
                                    "
                                />
                                <div
                                    v-else
                                    class="flex min-h-44 flex-col items-center justify-center rounded-2xl border border-dashed border-hairline bg-white p-6 text-center"
                                >
                                    <CalendarDays class="size-6 text-ink/20" />
                                    <p class="mt-3 text-sm font-medium text-ink/50">
                                        Elige una fecha
                                    </p>
                                    <p class="mt-1 text-xs text-ink/35">
                                        Verás aquí los horarios disponibles.
                                    </p>
                                </div>
                                <p
                                    v-if="form.errors.appointment_time"
                                    :class="errorClass"
                                >
                                    {{ form.errors.appointment_time }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-3xl border border-hairline bg-white p-6 sm:p-7">
                        <div class="flex items-center gap-3">
                            <span
                                class="grid size-8 shrink-0 place-items-center rounded-full bg-ink text-xs font-bold text-white"
                            >
                                3
                            </span>
                            <div>
                                <h3 class="font-display text-lg font-bold text-ink">
                                    Tus datos
                                </h3>
                                <p class="text-xs text-ink/45">
                                    Solo lo necesario para confirmar tu cita
                                </p>
                            </div>
                        </div>

                        <div class="mt-5 grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="patient_name" :class="labelClass">
                                    Nombre completo
                                    <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="patient_name"
                                    v-model="form.patient_name"
                                    type="text"
                                    autocomplete="name"
                                    placeholder="Tu nombre y apellido"
                                    :class="fieldClass"
                                />
                                <p
                                    v-if="form.errors.patient_name"
                                    :class="errorClass"
                                >
                                    {{ form.errors.patient_name }}
                                </p>
                            </div>

                            <div>
                                <label for="patient_phone" :class="labelClass">
                                    Teléfono / WhatsApp
                                    <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="patient_phone"
                                    v-model="form.patient_phone"
                                    type="tel"
                                    inputmode="tel"
                                    autocomplete="tel"
                                    placeholder="+52 33 1234 5678"
                                    :class="fieldClass"
                                />
                                <p
                                    v-if="form.errors.patient_phone"
                                    :class="errorClass"
                                >
                                    {{ form.errors.patient_phone }}
                                </p>
                            </div>

                            <div class="sm:col-span-2">
                                <label for="patient_email" :class="labelClass">
                                    Correo electrónico
                                    <span class="font-normal text-ink/35">
                                        (opcional)
                                    </span>
                                </label>
                                <input
                                    id="patient_email"
                                    v-model="form.patient_email"
                                    type="email"
                                    autocomplete="email"
                                    placeholder="tu@correo.com"
                                    :class="fieldClass"
                                />
                                <p
                                    v-if="form.errors.patient_email"
                                    :class="errorClass"
                                >
                                    {{ form.errors.patient_email }}
                                </p>
                            </div>

                            <div
                                v-if="form.is_home_service"
                                class="sm:col-span-2"
                            >
                                <label for="address_notes" :class="labelClass">
                                    Dirección para la visita
                                    <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="address_notes"
                                    v-model="form.address_notes"
                                    type="text"
                                    autocomplete="street-address"
                                    placeholder="Calle, número, colonia y referencias"
                                    :class="fieldClass"
                                />
                                <p
                                    v-if="form.errors.address_notes"
                                    :class="errorClass"
                                >
                                    {{ form.errors.address_notes }}
                                </p>
                            </div>

                            <div class="sm:col-span-2">
                                <label for="notes" :class="labelClass">
                                    Notas para tu terapeuta
                                    <span class="font-normal text-ink/35">
                                        (opcional)
                                    </span>
                                </label>
                                <textarea
                                    id="notes"
                                    v-model="form.notes"
                                    rows="3"
                                    class="w-full rounded-xl border border-hairline bg-white px-3.5 py-3 text-sm text-ink outline-none transition placeholder:text-ink/30 focus:border-brand focus:ring-2 focus:ring-brand/30"
                                    placeholder="Diagnóstico, cirugía reciente, dolor o indicaciones especiales."
                                />
                                <p v-if="form.errors.notes" :class="errorClass">
                                    {{ form.errors.notes }}
                                </p>
                            </div>

                        </div>

                        <button
                            type="button"
                            class="mt-5 inline-flex h-12 w-full items-center justify-center gap-2 rounded-xl bg-brand text-sm font-bold text-ink transition hover:bg-brand-strong disabled:cursor-not-allowed disabled:opacity-60"
                            :disabled="form.processing"
                            @click="submit"
                        >
                            <Loader2
                                v-if="form.processing"
                                class="size-4 animate-spin"
                            />
                            {{
                                form.processing
                                    ? 'Registrando tu cita...'
                                    : 'Confirmar cita'
                            }}
                        </button>
                        <p
                            class="mt-3 text-center text-[11px] leading-relaxed text-ink/40"
                        >
                            Al agendar aceptas que te contactemos por WhatsApp
                            para confirmar tu cita. No se cobra nada en línea.
                        </p>

                    </div>

                </div>

                <aside class="space-y-4 lg:sticky lg:top-24 lg:self-start">
                    <BookingSummary
                        :service="selectedService"
                        :date="form.appointment_date"
                        :time="form.appointment_time"
                        :is-home-service="form.is_home_service"
                        :clinic="clinic"
                    />

                    <div class="rounded-2xl border border-hairline bg-white p-5">
                        <div class="flex gap-3">
                            <MessageCircle
                                class="mt-0.5 size-5 shrink-0 text-[#5c7418]"
                            />
                            <div>
                                <p class="text-sm font-semibold text-ink">
                                    ¿Necesitas ayuda?
                                </p>
                                <p class="mt-1 text-xs leading-relaxed text-ink/55">
                                    Escríbenos y te ayudamos a elegir el
                                    servicio o resolver dudas sobre tu cita.
                                </p>
                                <a
                                    :href="
                                        whatsappUrl(
                                            clinic.whatsapp,
                                            'Hola, necesito ayuda para agendar una cita.',
                                        )
                                    "
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="mt-3 inline-flex items-center gap-1.5 text-xs font-semibold text-ink underline decoration-brand decoration-2 underline-offset-4 transition hover:text-[#5c7418]"
                                >
                                    Escribir por WhatsApp
                                </a>
                            </div>
                        </div>
                    </div>
                </aside>

            </div>
        </div>

        <Dialog v-model:open="successOpen">
            <DialogContent class="max-w-md border-hairline bg-white p-6 text-ink">
                <div v-if="successAppointment" class="text-center">
                    <span
                        class="mx-auto grid size-14 place-items-center rounded-full bg-brand text-ink"
                    >
                        <CheckCircle2 class="size-7" />
                    </span>

                    <DialogTitle
                        class="mt-4 font-display text-2xl leading-tight font-bold tracking-tight text-ink"
                    >
                        ¡Tu cita quedó registrada!
                    </DialogTitle>
                    <DialogDescription
                        class="mt-2 text-sm leading-relaxed text-ink/55"
                    >
                        Estatus: {{ successAppointment.status_label }}. Te
                        confirmaremos por WhatsApp muy pronto.
                    </DialogDescription>

                    <dl
                        class="mt-5 space-y-2.5 rounded-2xl bg-mist p-4 text-left text-sm"
                    >
                        <div class="flex items-start justify-between gap-3">
                            <dt class="text-ink/50">Folio</dt>
                            <dd class="font-semibold text-ink">
                                {{ successAppointment.folio }}
                            </dd>
                        </div>
                        <div class="flex items-start justify-between gap-3">
                            <dt class="shrink-0 text-ink/50">Servicio</dt>
                            <dd class="text-right font-semibold text-ink">
                                {{ successAppointment.service.name }}
                            </dd>
                        </div>
                        <div class="flex items-start justify-between gap-3">
                            <dt class="text-ink/50">Fecha</dt>
                            <dd class="text-right font-semibold text-ink">
                                {{ successAppointment.date_label }}
                            </dd>
                        </div>
                        <div class="flex items-start justify-between gap-3">
                            <dt class="text-ink/50">Hora</dt>
                            <dd class="font-semibold text-ink">
                                {{ successAppointment.appointment_time }}
                            </dd>
                        </div>
                        <div class="flex items-start justify-between gap-3">
                            <dt class="text-ink/50">Modalidad</dt>
                            <dd class="font-semibold text-ink">
                                {{ successAppointment.modality_label }}
                            </dd>
                        </div>
                    </dl>

                    <p class="mt-3 text-[11px] leading-relaxed text-ink/45">
                        Tu horario queda apartado mientras el equipo revisa la
                        agenda. Si necesitas cambiarlo, escríbenos por
                        WhatsApp.
                    </p>

                    <a
                        :href="confirmationWhatsApp(successAppointment)"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="mt-5 inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl bg-ink text-sm font-semibold text-white transition hover:bg-[#2c2e28]"
                    >
                        <MessageCircle class="size-4" />
                        Confirmar por WhatsApp
                    </a>

                    <button
                        type="button"
                        class="mt-2 inline-flex h-11 w-full items-center justify-center rounded-xl border border-hairline bg-white text-sm font-semibold text-ink transition hover:border-ink/30"
                        @click="successOpen = false"
                    >
                        Agendar otra cita
                    </button>
                </div>
            </DialogContent>
        </Dialog>
    </section>
</template>


