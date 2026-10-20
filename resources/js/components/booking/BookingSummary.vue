<script setup lang="ts">
import {
    CalendarDays,
    Clock,
    Home,
    MapPin,
    MessageCircle,
    Stethoscope,
} from '@lucide/vue';
import {
    formatDateLong,
    formatDuration,
    formatPrice,
    formatTime12,
} from '@/lib/format';
import type { ClinicInfo, Service } from '@/types';

defineProps<{
    service: Service | null;
    date: string;
    time: string;
    isHomeService: boolean;
    clinic: ClinicInfo;
}>();
</script>

<template>
    <div class="rounded-3xl border border-edge bg-panel p-6 text-white">
        <div class="flex items-center justify-between gap-3">
            <p class="text-xs font-semibold tracking-widest text-brand uppercase">
                Tu resumen
            </p>
            <span
                class="rounded-full border border-white/10 px-2.5 py-1 text-[10px] font-medium text-white/50"
            >
                Sin cargo al reservar
            </span>
        </div>

        <div class="mt-5 flex items-start gap-3">
            <span
                class="grid size-10 shrink-0 place-items-center rounded-xl bg-brand/15 text-brand"
            >
                <Stethoscope class="size-5" />
            </span>
            <div class="min-w-0">
                <p class="text-sm font-semibold">
                    {{ service?.name ?? 'Selecciona un servicio' }}
                </p>
                <p class="mt-0.5 text-xs text-white/45">
                    <template v-if="service">
                        {{ service.category_label }} ·
                        {{ formatDuration(service.duration_minutes) }}
                    </template>
                    <template v-else>Paso 1 de la agenda</template>
                </p>
            </div>
        </div>

        <dl class="mt-6 space-y-3 border-t border-white/10 pt-5 text-sm">
            <div class="flex items-start gap-3">
                <CalendarDays class="mt-0.5 size-4 shrink-0 text-brand" />
                <div>
                    <dt class="text-[11px] text-white/40">Fecha</dt>
                    <dd class="font-medium">
                        {{ date ? formatDateLong(date) : 'Sin seleccionar' }}
                    </dd>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <Clock class="mt-0.5 size-4 shrink-0 text-brand" />
                <div>
                    <dt class="text-[11px] text-white/40">Hora</dt>
                    <dd class="font-medium">
                        {{ time ? formatTime12(time) : 'Sin seleccionar' }}
                    </dd>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <component
                    :is="isHomeService ? Home : MapPin"
                    class="mt-0.5 size-4 shrink-0 text-brand"
                />
                <div>
                    <dt class="text-[11px] text-white/40">Modalidad</dt>
                    <dd class="font-medium">
                        {{ isHomeService ? 'A domicilio' : 'En clínica' }}
                    </dd>
                    <dd class="mt-0.5 text-xs leading-relaxed text-white/45">
                        {{
                            isHomeService
                                ? 'Zapopan y zonas cercanas · 8:00 a.m. – 2:00 p.m.'
                                : clinic.address
                        }}
                    </dd>
                </div>
            </div>
        </dl>

        <div
            class="mt-6 flex items-end justify-between border-t border-white/10 pt-5"
        >
            <div>
                <p class="text-[11px] text-white/40">Total estimado</p>
                <p class="font-display text-2xl font-bold">
                    {{ service ? formatPrice(service.price) : '—' }}
                </p>
            </div>
            <span
                class="rounded-full bg-brand/15 px-3 py-1 text-[11px] font-semibold text-brand"
            >
                Pago en consultorio
            </span>
        </div>

        <p class="mt-4 flex items-start gap-2 text-[11px] leading-relaxed text-white/40">
            <MessageCircle class="mt-0.5 size-3.5 shrink-0" />
            Te confirmamos la cita por WhatsApp. El precio puede ajustarse
            después de la valoración.
        </p>
    </div>
</template>
