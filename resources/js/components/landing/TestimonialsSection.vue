<script setup lang="ts">
import { Star } from '@lucide/vue';

const testimonials = [
    {
        quote: 'Llegué con dolor lumbar que no me dejaba dormir. En pocas sesiones ya podía trabajar sin molestias. El trato es cercano y explican todo con claridad.',
        name: 'Mariana G.',
        context: 'Dolor lumbar',
    },
    {
        quote: 'Después de mi cirugía de rodilla, la rehabilitación fue clave. Me dieron ejercicios claros para casa y medimos avances en cada visita.',
        name: 'Carlos R.',
        context: 'Rehabilitación de rodilla',
    },
    {
        quote: 'Soy corredor y la descarga muscular junto con el vendaje me ayudaron a volver a entrenar sin miedo. Muy recomendado para deportistas.',
        name: 'Ana P.',
        context: 'Recuperación deportiva',
    },
];
</script>

<template>
    <section id="testimonios" class="bg-panel py-20 sm:py-28">
        <div class="content-shell">
            <div class="max-w-2xl">
                <p
                    class="text-xs font-semibold tracking-[0.2em] text-brand uppercase"
                >
                    Testimonios
                </p>
                <h2
                    class="mt-3 font-display text-3xl leading-tight font-bold tracking-tight text-white sm:text-4xl"
                >
                    Pacientes que vuelven a moverse.
                </h2>
            </div>

            <div class="mt-10 grid gap-4 lg:grid-cols-3">
                <figure
                    v-for="testimonial in testimonials"
                    :key="testimonial.name"
                    class="flex flex-col rounded-2xl border border-edge bg-ink p-6"
                >
                    <div class="flex gap-1">
                        <Star
                            v-for="star in 5"
                            :key="star"
                            class="size-4 fill-brand text-brand"
                        />
                    </div>
                    <blockquote
                        class="mt-4 flex-1 text-sm leading-relaxed text-white/70"
                    >
                        “{{ testimonial.quote }}”
                    </blockquote>
                    <figcaption class="mt-6 flex items-center gap-3">
                        <span
                            class="grid size-10 place-items-center rounded-full bg-brand/15 font-display text-sm font-bold text-brand"
                        >
                            {{ testimonial.name.charAt(0) }}
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-white">
                                {{ testimonial.name }}
                            </p>
                            <p class="text-xs text-white/45">
                                {{ testimonial.context }}
                            </p>
                        </div>
                    </figcaption>
                </figure>
            </div>

            <p class="mt-6 text-xs text-white/35">
                Testimonios de pacientes atendidos en FisioBlack. Los resultados
                varían según cada caso y su valoración.
            </p>
        </div>
    </section>
</template>
