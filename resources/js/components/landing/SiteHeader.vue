<script setup lang="ts">
import { Activity, CalendarCheck, MessageCircle } from '@lucide/vue';
import type { ClinicInfo } from '@/types';
import { whatsappUrl } from '@/lib/format';

defineProps<{
    clinic: ClinicInfo;
}>();

const navLinks = [
    { label: 'Servicios', href: '#servicios' },
    { label: 'Beneficios', href: '#beneficios' },
    { label: 'Modalidades', href: '#modalidades' },
    { label: 'Agendar', href: '#agendar' },
];
</script>

<template>
    <header
        class="sticky top-0 z-40 border-b border-white/10 bg-ink/85 backdrop-blur-xl"
    >
        <div class="content-shell flex h-18 items-center justify-between gap-6">
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

            <nav class="hidden items-center gap-8 lg:flex">
                <a
                    v-for="link in navLinks"
                    :key="link.href"
                    :href="link.href"
                    class="text-sm font-medium text-white/70 transition hover:text-brand"
                >
                    {{ link.label }}
                </a>
            </nav>

            <div class="flex items-center gap-2.5">
                <a
                    :href="
                        whatsappUrl(
                            clinic.whatsapp,
                            'Hola, me gustaría información para agendar una cita en FisioBlack.',
                        )
                    "
                    target="_blank"
                    rel="noopener noreferrer"
                    class="hidden items-center gap-2 rounded-full border border-white/15 px-4 py-2.5 text-sm font-semibold text-white transition hover:border-brand/60 hover:text-brand sm:inline-flex"
                >
                    <MessageCircle class="size-4" />
                    WhatsApp
                </a>
                <a
                    href="#agendar"
                    class="inline-flex items-center gap-2 rounded-full bg-brand px-4 py-2.5 text-sm font-semibold text-ink transition hover:bg-brand-strong"
                >
                    <CalendarCheck class="size-4" />
                    Agendar cita
                </a>
            </div>
        </div>
    </header>
</template>
