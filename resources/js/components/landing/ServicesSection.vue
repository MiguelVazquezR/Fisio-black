<script setup lang="ts">
import { ArrowUpRight, Clock } from '@lucide/vue';
import { computed, ref } from 'vue';
import { useBookingSelection } from '@/composables/useBookingSelection';
import { formatDuration, formatPrice } from '@/lib/format';
import type { CategoryOption, Service, ServiceCategoryKey } from '@/types';
import ServiceDialog from './ServiceDialog.vue';

const props = defineProps<{
    services: Service[];
    categories: CategoryOption[];
}>();

const activeCategory = ref<'all' | ServiceCategoryKey>('all');
const dialogOpen = ref(false);
const activeService = ref<Service | null>(null);
const { selectService } = useBookingSelection();

const filteredServices = computed(() =>
    activeCategory.value === 'all'
        ? props.services
        : props.services.filter(
              (service) => service.category === activeCategory.value,
          ),
);

const filters = computed(() => [
    { value: 'all' as const, label: 'Todos' },
    ...props.categories,
]);

function openDetails(service: Service): void {
    activeService.value = service;
    dialogOpen.value = true;
}

function book(service: Service): void {
    dialogOpen.value = false;
    selectService(service.slug);
    document
        .getElementById('agendar')
        ?.scrollIntoView({ behavior: 'smooth', block: 'start' });
}
</script>

<template>
    <section id="servicios" class="bg-mist py-20 sm:py-28">
        <div class="content-shell">
            <div
                class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between"
            >
                <div class="max-w-2xl">
                    <p
                        class="text-xs font-semibold tracking-[0.2em] text-[#6d8a1f] uppercase"
                    >
                        Servicios
                    </p>
                    <h2
                        class="mt-3 font-display text-3xl leading-tight font-bold tracking-tight text-ink sm:text-4xl"
                    >
                        Tratamientos para volver a moverte bien.
                    </h2>
                    <p
                        class="mt-4 text-sm leading-relaxed text-ink/60 sm:text-base"
                    >
                        Rehabilitación, terapia manual, inyectables y
                        tratamientos especializados. Elige el servicio y agenda
                        en línea; si tienes dudas, te orientamos por WhatsApp.
                    </p>
                </div>
                <div
                    class="flex items-center gap-2 rounded-2xl border border-hairline bg-white px-4 py-3 text-xs text-ink/60"
                >
                    <span class="size-2 rounded-full bg-brand" />
                    Precios en MXN · Valoración incluida
                </div>
            </div>

            <div class="mt-8 flex flex-wrap gap-2">
                <button
                    v-for="filter in filters"
                    :key="filter.value"
                    type="button"
                    class="rounded-full border px-4 py-2 text-sm font-medium transition"
                    :class="
                        activeCategory === filter.value
                            ? 'border-ink bg-ink text-white'
                            : 'border-hairline bg-white text-ink/65 hover:border-ink/30 hover:text-ink'
                    "
                    @click="activeCategory = filter.value"
                >
                    {{ filter.label }}
                </button>
            </div>

            <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <article
                    v-for="service in filteredServices"
                    :key="service.id"
                    class="group flex flex-col rounded-2xl border border-hairline bg-white p-6 transition duration-300 hover:-translate-y-1 hover:border-brand hover:shadow-xl hover:shadow-ink/5"
                >
                    <div class="flex items-center justify-between gap-3">
                        <span
                            class="rounded-full bg-mist px-2.5 py-1 text-[11px] font-semibold tracking-wide text-[#5c7418] uppercase"
                        >
                            {{ service.category_label }}
                        </span>
                        <span
                            class="inline-flex items-center gap-1.5 text-[11px] font-medium text-ink/45"
                        >
                            <Clock class="size-3" />
                            {{ formatDuration(service.duration_minutes) }}
                        </span>
                    </div>

                    <h3
                        class="mt-4 font-display text-lg leading-snug font-bold text-ink"
                    >
                        {{ service.name }}
                    </h3>
                    <p
                        class="mt-2 line-clamp-3 text-sm leading-relaxed text-ink/55"
                    >
                        {{ service.description }}
                    </p>

                    <div
                        class="mt-6 flex items-end justify-between border-t border-hairline pt-4"
                    >
                        <div>
                            <p class="text-[11px] text-ink/40">Precio</p>
                            <p
                                class="font-display text-xl font-bold text-ink"
                            >
                                {{ formatPrice(service.price) }}
                            </p>
                        </div>
                        <button
                            type="button"
                            class="grid size-9 place-items-center rounded-full border border-hairline text-ink/50 transition group-hover:border-brand group-hover:bg-brand group-hover:text-ink"
                            :aria-label="`Ver detalles de ${service.name}`"
                            @click="openDetails(service)"
                        >
                            <ArrowUpRight class="size-4" />
                        </button>
                    </div>

                    <button
                        type="button"
                        class="mt-4 inline-flex h-10 items-center justify-center rounded-xl bg-ink text-sm font-semibold text-white transition group-hover:bg-brand group-hover:text-ink"
                        @click="book(service)"
                    >
                        Agendar
                    </button>
                </article>
            </div>

            <p
                v-if="filteredServices.length === 0"
                class="mt-10 rounded-2xl border border-dashed border-hairline bg-white p-10 text-center text-sm text-ink/50"
            >
                Estamos actualizando esta categoría. Escríbenos por WhatsApp y
                te contamos las opciones disponibles.
            </p>

        </div>

        <ServiceDialog
            v-model:open="dialogOpen"
            :service="activeService"
            @book="book"
        />
    </section>
</template>
