<script setup lang="ts">
import { Activity, HeartHandshake, House, Trophy } from '@lucide/vue';

const benefits = [
    {
        title: 'Atención personalizada',
        description:
            'Cada sesión se adapta a tu diagnóstico, tu dolor y tus objetivos. Sin rutinas genéricas.',
        icon: HeartHandshake,
    },
    {
        title: 'Técnicas y tecnología',
        description:
            'Terapia manual, ejercicio terapéutico, punción seca, inyectables y vendaje neuromuscular.',
        icon: Activity,
    },
    {
        title: 'En clínica o en casa',
        description:
            'Atendemos en consultorio en Zapopan o a domicilio dentro de la zona de cobertura.',
        icon: House,
    },
    {
        title: 'Enfoque deportivo',
        description:
            'Recuperación de lesiones, descarga muscular y readaptación para volver a entrenar.',
        icon: Trophy,
    },
];

const stats = [
    { value: '13', label: 'servicios' },
    { value: '2', label: 'modalidades' },
    { value: '6', label: 'días a la semana' },
    { value: '1 a 1', label: 'atención' },
];
</script>

<template>
    <section id="beneficios" class="bg-ink py-20 sm:py-28">
        <div class="content-shell">
            <div class="max-w-2xl">
                <p
                    class="text-xs font-semibold tracking-[0.2em] text-brand uppercase"
                >
                    Por qué FisioBlack
                </p>
                <h2
                    class="mt-3 font-display text-3xl leading-tight font-bold tracking-tight text-white sm:text-4xl"
                >
                    Terapia seria, trato cercano.
                </h2>
                <p class="mt-4 text-sm leading-relaxed text-white/60 sm:text-base">
                    No vendemos sesiones sueltas: construimos un plan de
                    recuperación contigo, con objetivos claros y seguimiento en
                    cada visita.
                </p>
            </div>

            <div class="mt-12 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <article
                    v-for="benefit in benefits"
                    :key="benefit.title"
                    class="rounded-2xl border border-edge bg-panel p-6 transition hover:border-brand/40"
                >
                    <span
                        class="grid size-11 place-items-center rounded-xl bg-brand/15 text-brand"
                    >
                        <component :is="benefit.icon" class="size-5" />
                    </span>
                    <h3 class="mt-5 font-display text-lg font-bold text-white">
                        {{ benefit.title }}
                    </h3>
                    <p class="mt-2 text-sm leading-relaxed text-white/50">
                        {{ benefit.description }}
                    </p>
                </article>
            </div>

            <dl
                class="mt-12 grid grid-cols-2 gap-px overflow-hidden rounded-2xl border border-edge bg-edge lg:grid-cols-4"
            >
                <div
                    v-for="stat in stats"
                    :key="stat.label"
                    class="bg-panel px-6 py-6 text-center"
                >
                    <dd class="font-display text-3xl font-bold text-brand">
                        {{ stat.value }}
                    </dd>
                    <dt
                        class="mt-1 text-[11px] tracking-widest text-white/45 uppercase"
                    >
                        {{ stat.label }}
                    </dt>
                </div>
            </dl>
        </div>
    </section>
</template>
