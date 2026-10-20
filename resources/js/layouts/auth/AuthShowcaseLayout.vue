<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    Activity,
    ArrowLeft,
    CalendarCheck,
    MessageCircle,
    ShieldCheck,
} from '@lucide/vue';
import { home } from '@/routes';

defineProps<{
    title?: string;
    description?: string;
}>();

const highlights = [
    { label: 'Citas confirmadas en tiempo real', icon: CalendarCheck },
    { label: 'Recordatorios y avisos por WhatsApp', icon: MessageCircle },
    { label: 'Control de horarios y fechas bloqueadas', icon: ShieldCheck },
];
</script>

<template>
    <div
        class="relative min-h-svh overflow-hidden bg-ink font-sans text-white antialiased"
    >
        <div
            class="pointer-events-none absolute -top-40 -right-24 size-[32rem] rounded-full bg-brand/20 blur-[140px]"
        />
        <div
            class="pointer-events-none absolute -bottom-48 -left-32 size-[30rem] rounded-full bg-brand/10 blur-[140px]"
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
            class="relative content-shell grid min-h-svh items-center gap-12 py-10 lg:grid-cols-[1.05fr_0.95fr] lg:gap-20 lg:py-16"
        >
            <div class="hidden lg:block">
                <Link :href="home()" class="inline-flex items-center gap-2.5">
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
                </Link>

                <span
                    class="mt-10 inline-flex items-center gap-2 rounded-full border border-brand/30 bg-brand/10 px-3.5 py-1.5 text-[11px] font-semibold tracking-[0.18em] text-brand uppercase"
                >
                    <span class="size-1.5 rounded-full bg-brand" />
                    Panel de administración
                </span>

                <h1
                    class="mt-6 font-display text-4xl leading-[1.05] font-bold tracking-tight text-white xl:text-5xl"
                >
                    Tu agenda y tus pacientes,
                    <span class="text-brand">en un solo lugar.</span>
                </h1>

                <p
                    class="mt-6 max-w-md text-base leading-relaxed text-white/60"
                >
                    Accede para confirmar citas, reorganizar horarios y bloquear
                    fechas desde cualquier dispositivo.
                </p>

                <ul class="mt-9 max-w-md space-y-3">
                    <li
                        v-for="highlight in highlights"
                        :key="highlight.label"
                        class="flex items-center gap-3 rounded-2xl border border-white/5 bg-white/[0.03] px-4 py-3"
                    >
                        <span
                            class="grid size-9 shrink-0 place-items-center rounded-lg bg-brand/15 text-brand"
                        >
                            <component :is="highlight.icon" class="size-4" />
                        </span>
                        <span class="text-sm text-white/70">
                            {{ highlight.label }}
                        </span>
                    </li>
                </ul>

                <p
                    class="mt-10 text-xs font-semibold tracking-[0.18em] text-white/35 uppercase"
                >
                    FisioBlack · Zapopan, Jalisco
                </p>
            </div>

            <div
                class="w-full justify-self-center lg:max-w-md lg:justify-self-end"
            >
                <div
                    class="rounded-3xl border border-white/10 bg-panel/85 p-6 shadow-2xl shadow-black/40 backdrop-blur-xl sm:p-8"
                >
                    <Link
                        :href="home()"
                        class="mb-7 inline-flex items-center gap-2.5 lg:hidden"
                    >
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
                    </Link>

                    <span class="block h-1 w-10 rounded-full bg-brand" />

                    <div class="mt-5">
                        <h2
                            class="font-display text-2xl font-bold tracking-tight text-white"
                        >
                            {{ title }}
                        </h2>
                        <p class="mt-2 text-sm leading-relaxed text-white/50">
                            {{ description }}
                        </p>
                    </div>

                    <div class="mt-7">
                        <slot />
                    </div>
                </div>

                <Link
                    :href="home()"
                    class="mt-5 flex items-center justify-center gap-2 text-xs font-semibold text-white/40 transition hover:text-brand"
                >
                    <ArrowLeft class="size-3.5" />
                    Volver al sitio
                </Link>
            </div>
        </div>
    </div>
</template>
