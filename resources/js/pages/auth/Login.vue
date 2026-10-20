<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { CircleCheck, LogIn } from '@lucide/vue';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { store } from '@/routes/login';
import { request } from '@/routes/password';

defineOptions({
    layout: {
        title: 'Iniciar sesión',
        description:
            'Ingresa con tu cuenta para administrar la agenda de la clínica.',
    },
});

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();
</script>

<template>
    <Head title="Iniciar sesión" />

    <div
        v-if="status"
        class="mb-6 flex items-start gap-2.5 rounded-xl border border-brand/30 bg-brand/10 px-4 py-3 text-sm font-medium text-brand"
    >
        <CircleCheck class="mt-0.5 size-4 shrink-0" />
        <span>{{ status }}</span>
    </div>

    <Form
        v-bind="store.form()"
        :reset-on-success="['password']"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-5"
    >
        <div class="grid gap-2">
            <Label for="email" class="text-white/70">Correo electrónico</Label>
            <Input
                id="email"
                type="email"
                name="email"
                required
                v-focus
                :tabindex="1"
                autocomplete="email"
                placeholder="tu@correo.com"
                class="h-11 rounded-xl border-white/10 bg-white/5 px-3.5 text-sm text-white shadow-none placeholder:text-white/30 focus-visible:border-brand/60 focus-visible:ring-brand/20"
            />
            <div class="[&_p]:text-red-400">
                <InputError :message="errors.email" />
            </div>
        </div>

        <div class="grid gap-2">
            <div class="flex items-center justify-between gap-3">
                <Label for="password" class="text-white/70">Contraseña</Label>
                <Link
                    v-if="canResetPassword"
                    :href="request()"
                    :tabindex="5"
                    class="text-xs font-semibold text-brand transition hover:text-brand-strong"
                >
                    ¿Olvidaste tu contraseña?
                </Link>
            </div>
            <PasswordInput
                id="password"
                name="password"
                required
                :tabindex="2"
                autocomplete="current-password"
                placeholder="Tu contraseña"
                class="h-11 rounded-xl border-white/10 bg-white/5 px-3.5 pr-10 text-sm text-white shadow-none placeholder:text-white/30 focus-visible:border-brand/60 focus-visible:ring-brand/20"
                toggle-class="text-white/40 hover:text-brand focus-visible:ring-brand/30"
            />
            <div class="[&_p]:text-red-400">
                <InputError :message="errors.password" />
            </div>
        </div>

        <Label
            for="remember"
            class="w-fit cursor-pointer items-center gap-3 text-white/60"
        >
            <Checkbox
                id="remember"
                name="remember"
                :tabindex="3"
                class="size-4 rounded-[5px] border-white/20 bg-white/5 focus-visible:border-brand focus-visible:ring-brand/30 data-[state=checked]:border-brand data-[state=checked]:bg-brand data-[state=checked]:text-ink"
            />
            <span class="text-sm font-medium">Mantener sesión iniciada</span>
        </Label>

        <Button
            type="submit"
            class="mt-1 h-11 w-full rounded-full bg-brand text-sm font-semibold text-ink hover:bg-brand-strong focus-visible:ring-brand/40"
            :tabindex="4"
            :disabled="processing"
            data-test="login-button"
        >
            <Spinner v-if="processing" />
            <LogIn v-else class="size-4" />
            Entrar
        </Button>
    </Form>

    <p
        class="mt-6 border-t border-white/10 pt-5 text-center text-xs leading-relaxed text-white/35"
    >
        ¿No tienes acceso? Solicita tus credenciales al administrador de la
        clínica.
    </p>
</template>
