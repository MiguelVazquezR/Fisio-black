<script setup lang="ts">
import { ArrowRight, ArrowUpRight, House, MapPin, MessageCircle } from '@lucide/vue';
import { whatsappUrl } from '@/lib/format';
import type { BookingConfig, ClinicInfo } from '@/types';

const props = defineProps<{
    clinic: ClinicInfo;
    booking: BookingConfig;
}>();

function hours(range: [string, string]): string {
    return `${range[0]} – ${range[1]}`;
}

const homeMessage = whatsappUrl(
    props.clinic.whatsapp,
    'Hola, quisiera saber si mi domicilio está dentro de la zona de cobertura para terapia a domicilio.',
);
</script>

<template>
    <section id="modalidades" class="bg-mist py-20 sm:py-28">
        <div class="content-shell">
            <div class="max-w-2xl">
                <p
                    class="text-xs font-semibold tracking-[0.2em] text-[#6d8a1f] uppercase"
                >
                    Modalidades
                </p>
                <h2
                    class="mt-3 font-display text-3xl leading-tight font-bold tracking-tight text-ink sm:text-4xl"
                >
                    Elige dónde te atendemos.
                </h2>
                <p class="mt-4 text-sm leading-relaxed text-ink/60 sm:text-base">
                    La misma calidad de atención en consultorio o en tu
                    domicilio. Tú decides lo que mejor se adapte a tu
                    recuperación.
                </p>
            </div>

            <div class="mt-10 grid gap-5 lg:grid-cols-2">
                <article
                    class="flex flex-col rounded-3xl border border-hairline bg-white p-7 sm:p-9"
                >
                    <div class="flex items-start justify-between gap-4">
                        <span
                            class="grid size-12 place-items-center rounded-2xl bg-brand text-ink"
                        >
                            <MapPin class="size-6" />
                        </span>
                        <span
                            class="rounded-full border border-hairline px-3 py-1 text-[11px] font-semibold text-ink/55"
                        >
                            Lun–Sáb
                        </span>
                    </div>
                    <h3
                        class="mt-6 font-display text-2xl font-bold tracking-tight text-ink"
                    >
                        En clínica
                    </h3>
                    <p class="mt-2 text-sm leading-relaxed text-ink/60">
                        {{ clinic.address }}
                    </p>

                    <dl class="mt-6 space-y-3 text-sm">
                        <div class="flex items-center justify-between gap-4">
                            <dt class="text-ink/45">Horario</dt>
                            <dd class="font-semibold text-ink">
                                {{ hours(booking.clinic_hours) }}
                            </dd>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <dt class="text-ink/45">Domingo</dt>
                            <dd class="font-semibold text-ink">Cerrado</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <dt class="text-ink/45">Atención</dt>
                            <dd class="font-semibold text-ink">Solo con cita</dd>
                        </div>
                    </dl>

                    <div class="mt-7 flex flex-wrap gap-2.5">
                        <a
                            href="#agendar"
                            class="inline-flex items-center gap-2 rounded-full bg-ink px-5 py-3 text-sm font-semibold text-white transition hover:bg-brand hover:text-ink"
                        >
                            Agendar en clínica
                            <ArrowRight class="size-4" />
                        </a>
                        <a
                            :href="clinic.maps_url"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-2 rounded-full border border-hairline px-5 py-3 text-sm font-semibold text-ink transition hover:border-ink/30"
                        >
                            Ver ubicación
                            <ArrowUpRight class="size-4" />
                        </a>
                    </div>
                </article>

                <article
                    class="flex flex-col rounded-3xl border border-edge bg-panel p-7 text-white sm:p-9"
                >
                    <div class="flex items-start justify-between gap-4">
                        <span
                            class="grid size-12 place-items-center rounded-2xl bg-brand/15 text-brand"
                        >
                            <House class="size-6" />
                        </span>
                        <span
                            class="rounded-full border border-white/15 px-3 py-1 text-[11px] font-semibold text-white/60"
                        >
                            08:00 – 14:00
                        </span>
                    </div>
                    <h3
                        class="mt-6 font-display text-2xl font-bold tracking-tight"
                    >
                        A domicilio
                    </h3>
                    <p class="mt-2 text-sm leading-relaxed text-white/60">
                        Llevamos la terapia a tu casa u hotel dentro de Zapopan y
                        zonas cercanas. Ideal si te cuesta moverte o estás en
                        postoperatorio.
                    </p>

                    <dl class="mt-6 space-y-3 text-sm">
                        <div class="flex items-center justify-between gap-4">
                            <dt class="text-white/40">Horario</dt>
                            <dd class="font-semibold">
                                {{ hours(booking.home_hours) }}
                            </dd>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <dt class="text-white/40">Cobertura</dt>
                            <dd class="font-semibold">Zapopan y alrededores</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <dt class="text-white/40">Cotización</dt>
                            <dd class="font-semibold">Según distancia</dd>
                        </div>
                    </dl>

                    <div class="mt-7 flex flex-wrap gap-2.5">
                        <a
                            href="#agendar"
                            class="inline-flex items-center gap-2 rounded-full bg-brand px-5 py-3 text-sm font-semibold text-ink transition hover:bg-brand-strong"
                        >
                            Agendar a domicilio
                            <ArrowRight class="size-4" />
                        </a>
                        <a
                            :href="homeMessage"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-2 rounded-full border border-white/15 px-5 py-3 text-sm font-semibold text-white transition hover:border-brand/60 hover:text-brand"
                        >
                            <MessageCircle class="size-4" />
                            Consultar cobertura
                        </a>
                    </div>
                </article>

            </div>
        </div>
    </section>
</template>
