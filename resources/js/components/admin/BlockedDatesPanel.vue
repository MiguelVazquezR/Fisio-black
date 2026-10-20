<script setup lang="ts">
import { router, useForm } from '@inertiajs/vue3';
import { CalendarOff, Plus, Trash2 } from '@lucide/vue';
import { todayISO } from '@/lib/format';
import { destroy, store } from '@/routes/admin/blocked-dates';
import type { BlockedDate } from '@/types';

defineProps<{
    blockedDates: BlockedDate[];
}>();

const form = useForm({
    date: '',
    reason: '',
});

const minDate = todayISO();

function submit(): void {
    form.post(store.url(), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
}

function remove(id: number): void {
    router.delete(destroy.url(id), { preserveScroll: true });
}
</script>

<template>
    <section class="rounded-2xl border border-hairline bg-white p-5">
        <div class="flex items-center gap-2.5">
            <span
                class="grid size-8 place-items-center rounded-lg bg-ink text-brand"
            >
                <CalendarOff class="size-4" />
            </span>
            <div>
                <h2 class="font-display text-sm font-bold text-ink">
                    Fechas bloqueadas
                </h2>
                <p class="text-[11px] text-ink/45">
                    Días sin atención en clínica ni domicilio
                </p>
            </div>
        </div>

        <form class="mt-4 space-y-2.5" @submit.prevent="submit">
            <div>
                <label for="blocked-date" class="sr-only">Fecha a bloquear</label>
                <input
                    id="blocked-date"
                    v-model="form.date"
                    type="date"
                    :min="minDate"
                    class="h-10 w-full rounded-lg border border-hairline bg-white px-3 text-sm text-ink outline-none transition focus:border-brand focus:ring-2 focus:ring-brand/30"
                />
                <p v-if="form.errors.date" class="mt-1 text-xs text-red-600">
                    {{ form.errors.date }}
                </p>
            </div>
            <div>
                <label for="blocked-reason" class="sr-only">Motivo</label>
                <input
                    id="blocked-reason"
                    v-model="form.reason"
                    type="text"
                    placeholder="Motivo (opcional)"
                    class="h-10 w-full rounded-lg border border-hairline bg-white px-3 text-sm text-ink outline-none transition placeholder:text-ink/30 focus:border-brand focus:ring-2 focus:ring-brand/30"
                />
                <p v-if="form.errors.reason" class="mt-1 text-xs text-red-600">
                    {{ form.errors.reason }}
                </p>
            </div>
            <button
                type="submit"
                class="inline-flex h-10 w-full items-center justify-center gap-1.5 rounded-lg bg-ink text-xs font-semibold text-white transition hover:bg-brand hover:text-ink disabled:opacity-50"
                :disabled="form.processing"
            >
                <Plus class="size-3.5" />
                Bloquear fecha
            </button>
        </form>

        <ul v-if="blockedDates.length" class="mt-4 space-y-2 border-t border-hairline pt-4">
            <li
                v-for="blockedDate in blockedDates"
                :key="blockedDate.id"
                class="flex items-start justify-between gap-3 rounded-lg bg-mist px-3 py-2.5"
            >
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-ink">
                        {{ blockedDate.label }}
                    </p>
                    <p
                        v-if="blockedDate.reason"
                        class="mt-0.5 truncate text-[11px] text-ink/45"
                    >
                        {{ blockedDate.reason }}
                    </p>
                </div>
                <button
                    type="button"
                    class="grid size-7 shrink-0 place-items-center rounded-md text-ink/35 transition hover:bg-rose-50 hover:text-rose-600"
                    aria-label="Eliminar fecha bloqueada"
                    @click="remove(blockedDate.id)"
                >
                    <Trash2 class="size-3.5" />
                </button>
            </li>
        </ul>

        <p v-else class="mt-4 border-t border-hairline pt-4 text-xs text-ink/40">
            No hay fechas bloqueadas próximamente.
        </p>
    </section>
</template>
