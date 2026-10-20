<script setup lang="ts">
import { Activity, Mail, MapPin, MessageCircle, Phone } from '@lucide/vue';
import { whatsappUrl } from '@/lib/format';
import type { ClinicInfo } from '@/types';

const props = defineProps<{
    clinic: ClinicInfo;
}>();

const navLinks = [
    { label: 'Servicios', href: '#servicios' },
    { label: 'Beneficios', href: '#beneficios' },
    { label: 'Modalidades', href: '#modalidades' },
    { label: 'Agendar cita', href: '#agendar' },
];

const message = whatsappUrl(
    props.clinic.whatsapp,
    'Hola, me gustaría agendar una cita en FisioBlack.',
);
</script>

<template>
    <footer class="border-t border-white/10 bg-ink">
        <div class="content-shell py-14">
            <div class="grid gap-10 lg:grid-cols-[1.4fr_1fr_1fr_1.2fr]">
                <div>
                    <a href="#inicio" class="flex items-center gap-2.5">
                        <span
                            class="grid size-9 place-items-center rounded-xl bg-brand text-ink"
                        >
                            <Activity class="size-5" stroke-width="2.5" />
                        </span>
                        <span
                            class="font-display text-xl font-bold tracking-tight text-white"
                        >
                            Fisio<span class="text-brand">Black</span>
                        </span>
                    </a>
                    <p class="mt-4 max-w-xs text-sm leading-relaxed text-white/50">
                        {{ clinic.tagline }} en Zapopan. Recupera tu movimiento
                        con un plan de atención hecho para ti.
                    </p>
                    <a
                        :href="message"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="mt-6 inline-flex items-center gap-2 rounded-full bg-brand px-5 py-3 text-sm font-semibold text-ink transition hover:bg-brand-strong"
                    >
                        <MessageCircle class="size-4" />
                        Agendar por WhatsApp
                    </a>
                </div>

                <div>
                    <p
                        class="text-[11px] font-semibold tracking-widest text-white/35 uppercase"
                    >
                        Navegación
                    </p>
                    <ul class="mt-4 space-y-2.5">
                        <li v-for="link in navLinks" :key="link.href">
                            <a
                                :href="link.href"
                                class="text-sm text-white/60 transition hover:text-brand"
                            >
                                {{ link.label }}
                            </a>
                        </li>
                    </ul>
                </div>

                <div>
                    <p
                        class="text-[11px] font-semibold tracking-widest text-white/35 uppercase"
                    >
                        Contacto
                    </p>
                    <ul class="mt-4 space-y-3 text-sm text-white/60">
                        <li class="flex items-start gap-2.5">
                            <Phone class="mt-0.5 size-4 shrink-0 text-brand" />
                            <a
                                :href="message"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="transition hover:text-brand"
                            >
                                {{ clinic.phone }}
                            </a>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <Mail class="mt-0.5 size-4 shrink-0 text-brand" />
                            <a
                                :href="`mailto:${clinic.email}`"
                                class="break-all transition hover:text-brand"
                            >
                                {{ clinic.email }}
                            </a>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <MapPin class="mt-0.5 size-4 shrink-0 text-brand" />
                            <a
                                :href="clinic.maps_url"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="transition hover:text-brand"
                            >
                                {{ clinic.address }}
                            </a>
                        </li>
                    </ul>
                </div>

                <div>
                    <p
                        class="text-[11px] font-semibold tracking-widest text-white/35 uppercase"
                    >
                        Horarios
                    </p>
                    <dl class="mt-4 space-y-2.5 text-sm">
                        <div class="flex items-center justify-between gap-4">
                            <dt class="text-white/45">Clínica</dt>
                            <dd class="text-white/75">Lun–Sáb · 08:00–20:00</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <dt class="text-white/45">Domicilio</dt>
                            <dd class="text-white/75">Lun–Sáb · 08:00–14:00</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <dt class="text-white/45">Domingo</dt>
                            <dd class="text-white/75">Cerrado</dd>
                        </div>
                    </dl>
                    <p class="mt-4 text-xs leading-relaxed text-white/35">
                        Atención exclusivamente con cita previa. Confirma tu
                        horario por WhatsApp.
                    </p>
                </div>

            </div>

            <div
                class="mt-12 flex flex-col gap-2 border-t border-white/10 pt-6 text-xs text-white/35 sm:flex-row sm:items-center sm:justify-between"
            >
                <p>
                    © {{ new Date().getFullYear() }} FisioBlack. Todos los
                    derechos reservados.
                </p>
                <p>Zapopan, Jalisco · México</p>
            </div>
        </div>
    </footer>
</template>
