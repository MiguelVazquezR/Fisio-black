<script setup lang="ts">
import {
    ArrowRight,
    CalendarCheck,
    House,
    MessageCircle,
    ShieldCheck,
    Sparkles,
    Stethoscope,
} from '@lucide/vue';
import type { ClinicInfo } from '@/types';
import { whatsappUrl } from '@/lib/format';

defineProps<{
    clinic: ClinicInfo;
}>();

const plan = [
    {
        title: 'Valoración',
        description: 'Escuchamos tu caso y exploramos el movimiento.',
        icon: Stethoscope,
    },
    {
        title: 'Tratamiento',
        description: 'Terapia manual, ejercicio y tecnología según tu caso.',
        icon: Sparkles,
    },
    {
        title: 'Seguimiento',
        description: 'Medimos avances y ajustamos el plan sesión a sesión.',
        icon: ShieldCheck,
    },
];
</script>

<template>
    <section id="inicio" class="relative overflow-hidden bg-ink">
        <div
            class="pointer-events-none absolute -top-40 -right-24 size-[32rem] rounded-full bg-brand/20 blur-[140px]"
        />
        <div
            class="pointer-events-none absolute inset-0 opacity-[0.06]"
            style="
                background-image:
                    linear-gradient(to right, #ffffff 1px, transparent 1px),
                    linear-gradient(to bottom, #ffffff 1px, transparent 1px);
                background-size: 64px 64px;
            "
        />

        <div
            class="content-shell relative grid gap-14 pt-16 pb-24 lg:grid-cols-[1.05fr_0.95fr] lg:items-center lg:gap-20 lg:pt-24 lg:pb-32"
        >
            <div>
                <span
                    class="inline-flex items-center gap-2 rounded-full border border-brand/30 bg-brand/10 px-3.5 py-1.5 text-[11px] font-semibold tracking-[0.18em] text-brand uppercase"
                >
                    <span class="size-1.5 rounded-full bg-brand" />
                    Zapopan · Atención con cita
                </span>

                <h1
                    class="mt-6 font-display text-4xl leading-[1.05] font-bold tracking-tight text-white sm:text-5xl lg:text-6xl"
                >
                    Recupera tu movimiento,
                    <span class="text-brand">sin dolor.</span>
                </h1>

                <p
                    class="mt-6 max-w-xl text-base leading-relaxed text-white/65 sm:text-lg"
                >
                    Fisioterapia deportiva, rehabilitación y tratamientos
                    especializados en Zapopan. Atención uno a uno, en clínica o
                    a domicilio, con un plan claro para volver a lo que amas.
                </p>

                <div class="mt-9 flex flex-wrap items-center gap-3">
                    <a
                        href="#agendar"
                        class="inline-flex items-center gap-2 rounded-full bg-brand px-6 py-3.5 text-sm font-semibold text-ink transition hover:bg-brand-strong"
                    >
                        Agendar mi cita
                        <ArrowRight class="size-4" />
                    </a>
                    <a
                        :href="
                            whatsappUrl(
                                clinic.whatsapp,
                                'Hola, quisiera información sobre los servicios de FisioBlack.',
                            )
                        "
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center gap-2 rounded-full border border-white/15 px-6 py-3.5 text-sm font-semibold text-white transition hover:border-brand/60 hover:text-brand"
                    >
                        <MessageCircle class="size-4" />
                        WhatsApp
                    </a>
                </div>

                <dl
                    class="mt-12 grid max-w-lg grid-cols-3 gap-6 border-t border-white/10 pt-8"
                >
                    <div>
                        <dt class="text-xs text-white/50">Servicios</dt>
                        <dd
                            class="mt-1 font-display text-2xl font-bold text-white"
                        >
                            13
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs text-white/50">Horario</dt>
                        <dd
                            class="mt-1 font-display text-2xl font-bold text-white"
                        >
                            Lun–Sáb
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs text-white/50">Modalidades</dt>
                        <dd
                            class="mt-1 font-display text-2xl font-bold text-white"
                        >
                            Clínica + casa
                        </dd>
                    </div>
                </dl>
            </div>

            <div class="relative">
                <div
                    class="relative rounded-3xl border border-edge bg-panel p-6 shadow-2xl shadow-black/40 sm:p-8"
                >
                    <div
                        class="flex items-center justify-between gap-4 border-b border-white/10 pb-5"
                    >
                        <div class="flex items-center gap-3">
                            <span
                                class="grid size-10 place-items-center rounded-xl bg-brand/15 text-brand"
                            >
                                <House class="size-5" />
                            </span>
                            <div>
                                <p class="text-sm font-semibold text-white">
                                    Tu plan de recuperación
                                </p>
                                <p class="text-xs text-white/45">
                                    Clínica y domicilio
                                </p>
                            </div>
                        </div>
                        <span
                            class="rounded-full border border-brand/30 bg-brand/10 px-3 py-1 text-[11px] font-semibold text-brand"
                        >
                            Agenda abierta
                        </span>
                    </div>

                    <ol class="mt-6 space-y-3">
                        <li
                            v-for="(step, index) in plan"
                            :key="step.title"
                            class="flex gap-4 rounded-2xl border border-white/5 bg-white/[0.03] p-4"
                        >
                            <span
                                class="mt-0.5 grid size-9 shrink-0 place-items-center rounded-lg bg-brand/15 text-brand"
                            >
                                <component :is="step.icon" class="size-4" />
                            </span>
                            <div>
                                <p class="text-sm font-semibold text-white">
                                    {{ index + 1 }}. {{ step.title }}
                                </p>
                                <p
                                    class="mt-1 text-xs leading-relaxed text-white/50"
                                >
                                    {{ step.description }}
                                </p>
                            </div>
                        </li>
                    </ol>

                    <div
                        class="mt-6 flex items-center justify-between rounded-2xl bg-brand px-5 py-4 text-ink"
                    >
                        <div>
                            <p
                                class="text-xs font-semibold tracking-wide uppercase"
                            >
                                Lunes a sábado
                            </p>
                            <p class="font-display text-lg font-bold">
                                08:00 – 20:00
                            </p>
                        </div>
                        <CalendarCheck class="size-8 opacity-70" />
                    </div>
                </div>

                <div
                    class="absolute -bottom-6 -left-4 hidden rounded-2xl border border-edge bg-panel-soft px-5 py-4 shadow-xl sm:block"
                >
                    <p
                        class="text-[11px] tracking-widest text-white/40 uppercase"
                    >
                        Atención
                    </p>
                    <p class="font-display text-lg font-bold text-white">
                        100% personalizada
                    </p>
                </div>
            </div>

        </div>
    </section>
</template>
