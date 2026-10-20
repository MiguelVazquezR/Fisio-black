<script setup lang="ts">
import { MessageCircle } from '@lucide/vue';
import { whatsappUrl } from '@/lib/format';

const props = defineProps<{
    phone: string;
}>();

const url = whatsappUrl(
    props.phone,
    'Hola, me gustaría agendar una cita en FisioBlack.',
);
</script>

<template>
    <a
        :href="url"
        target="_blank"
        rel="noopener noreferrer"
        class="fixed right-5 bottom-5 z-40 grid size-14 place-items-center rounded-full bg-[#25D366] text-white shadow-xl shadow-black/30 transition hover:scale-105 hover:bg-[#1fb959]"
        aria-label="Escríbenos por WhatsApp"
        title="Escríbenos por WhatsApp"
    >
        <MessageCircle class="size-6" />
    </a>
</template>
