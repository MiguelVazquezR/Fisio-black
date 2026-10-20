<script setup lang="ts">
import { Link, router, usePage } from '@inertiajs/vue3';
import {
    Activity,
    CalendarDays,
    ExternalLink,
    LogOut,
    Menu,
    X,
} from '@lucide/vue';
import { computed, ref } from 'vue';
import { Toaster } from '@/components/ui/sonner';
import { home, logout } from '@/routes';
import { dashboard } from '@/routes/admin';
import type { User } from '@/types';

const page = usePage<{ auth: { user: User } }>();
const user = computed(() => page.props.auth.user);
const mobileOpen = ref(false);

function closeSession(): void {
    router.post(logout.url());
}
</script>

<template>
    <div class="min-h-screen bg-mist text-ink">
        <aside
            class="fixed inset-y-0 left-0 z-40 hidden w-64 flex-col border-r border-hairline bg-white lg:flex"
        >
            <div class="flex h-18 items-center border-b border-hairline px-6">
                <Link :href="dashboard.url()" class="flex items-center gap-2.5">
                    <span
                        class="grid size-9 place-items-center rounded-xl bg-ink text-brand"
                    >
                        <Activity class="size-5" stroke-width="2.5" />
                    </span>
                    <span class="font-display text-lg font-bold tracking-tight">
                        Fisio<span class="text-[#6d8a1f]">Black</span>
                    </span>
                </Link>
            </div>

            <nav class="flex-1 space-y-1 p-4">
                <Link
                    :href="dashboard.url()"
                    class="flex items-center gap-3 rounded-xl bg-ink px-3.5 py-3 text-sm font-semibold text-white"
                >
                    <CalendarDays class="size-4" />
                    Agenda
                </Link>
                <a
                    :href="home.url()"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="flex items-center gap-3 rounded-xl px-3.5 py-3 text-sm font-medium text-ink/60 transition hover:bg-mist hover:text-ink"
                >
                    <ExternalLink class="size-4" />
                    Ver sitio
                </a>
            </nav>

            <div class="border-t border-hairline p-4">
                <div class="rounded-xl bg-mist px-3.5 py-3">
                    <p class="truncate text-sm font-semibold">{{ user.name }}</p>
                    <p class="truncate text-xs text-ink/45">{{ user.email }}</p>
                </div>
                <button
                    type="button"
                    class="mt-2 flex w-full items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-medium text-ink/60 transition hover:bg-red-50 hover:text-red-600"
                    @click="closeSession"
                >
                    <LogOut class="size-4" />
                    Cerrar sesión
                </button>
            </div>
        </aside>

        <div class="lg:pl-64">
            <header
                class="sticky top-0 z-30 flex h-16 items-center justify-between gap-4 border-b border-hairline bg-white/90 px-4 backdrop-blur sm:px-6 lg:px-8"
            >
                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        class="grid size-9 place-items-center rounded-lg border border-hairline text-ink/60 lg:hidden"
                        aria-label="Abrir menú"
                        @click="mobileOpen = true"
                    >
                        <Menu class="size-4" />
                    </button>
                    <div>
                        <p
                            class="text-[11px] tracking-widest text-ink/40 uppercase"
                        >
                            Panel de administración
                        </p>
                        <p class="font-display text-sm font-bold">
                            FisioBlack · Zapopan
                        </p>
                    </div>
                </div>

                <a
                    :href="home.url()"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="hidden items-center gap-2 rounded-full border border-hairline px-4 py-2 text-xs font-semibold text-ink/60 transition hover:border-ink/30 hover:text-ink sm:inline-flex"
                >
                    <ExternalLink class="size-3.5" />
                    Ver sitio
                </a>
            </header>

            <main class="mx-auto w-full max-w-7xl p-4 sm:p-6 lg:p-8">
                <slot />
            </main>
        </div>

        <div v-if="mobileOpen" class="fixed inset-0 z-50 lg:hidden">
            <div
                class="absolute inset-0 bg-ink/50 backdrop-blur-sm"
                @click="mobileOpen = false"
            />
            <aside
                class="absolute inset-y-0 left-0 flex w-72 max-w-[85%] flex-col border-r border-hairline bg-white"
            >
                <div
                    class="flex h-16 items-center justify-between border-b border-hairline px-5"
                >
                    <span class="font-display text-lg font-bold">
                        Fisio<span class="text-[#6d8a1f]">Black</span>
                    </span>
                    <button
                        type="button"
                        class="grid size-8 place-items-center rounded-lg border border-hairline text-ink/60"
                        aria-label="Cerrar menú"
                        @click="mobileOpen = false"
                    >
                        <X class="size-4" />
                    </button>
                </div>

                <nav class="flex-1 space-y-1 p-4">
                    <Link
                        :href="dashboard.url()"
                        class="flex items-center gap-3 rounded-xl bg-ink px-3.5 py-3 text-sm font-semibold text-white"
                        @click="mobileOpen = false"
                    >
                        <CalendarDays class="size-4" />
                        Agenda
                    </Link>
                    <a
                        :href="home.url()"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex items-center gap-3 rounded-xl px-3.5 py-3 text-sm font-medium text-ink/60"
                    >
                        <ExternalLink class="size-4" />
                        Ver sitio
                    </a>
                </nav>

                <div class="border-t border-hairline p-4">
                    <p class="truncate text-sm font-semibold">{{ user.name }}</p>
                    <p class="truncate text-xs text-ink/45">{{ user.email }}</p>
                    <button
                        type="button"
                        class="mt-3 flex w-full items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-medium text-red-600"
                        @click="closeSession"
                    >
                        <LogOut class="size-4" />
                        Cerrar sesión
                    </button>
                </div>
            </aside>
        </div>


        <Toaster position="top-center" />
    </div>
</template>
