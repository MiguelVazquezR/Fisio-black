<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import BenefitsSection from '@/components/landing/BenefitsSection.vue';
import BookingSection from '@/components/landing/BookingSection.vue';
import HeroSection from '@/components/landing/HeroSection.vue';
import ModalitiesSection from '@/components/landing/ModalitiesSection.vue';
import ServicesSection from '@/components/landing/ServicesSection.vue';
import SiteFooter from '@/components/landing/SiteFooter.vue';
import SiteHeader from '@/components/landing/SiteHeader.vue';
import TestimonialsSection from '@/components/landing/TestimonialsSection.vue';
import WhatsAppFab from '@/components/landing/WhatsAppFab.vue';
import { Toaster } from '@/components/ui/sonner';
import type {
    Appointment,
    BookingConfig,
    CategoryOption,
    ClinicInfo,
    Service,
} from '@/types';

defineProps<{
    services: Service[];
    categories: CategoryOption[];
    blockedDates: string[];
    selectedService: string | null;
    clinic: ClinicInfo;
    booking: BookingConfig;
    recentAppointment: Appointment | null;
}>();
</script>

<template>
    <Head title="FisioBlack · Fisioterapia y rehabilitación en Zapopan">
        <meta
            name="description"
            content="Fisioterapia deportiva, rehabilitación y tratamientos especializados en Zapopan. Agenda en línea, en clínica o a domicilio. WhatsApp 33 2070 5157."
        />
    </Head>

    <div class="min-h-screen bg-ink font-sans antialiased">
        <SiteHeader :clinic="clinic" />

        <main>
            <HeroSection :clinic="clinic" />
            <ServicesSection :services="services" :categories="categories" />
            <BenefitsSection />
            <ModalitiesSection :clinic="clinic" :booking="booking" />
            <TestimonialsSection />
            <BookingSection
                :services="services"
                :blocked-dates="blockedDates"
                :clinic="clinic"
                :booking="booking"
                :selected-service="selectedService"
                :recent-appointment="recentAppointment"
            />
        </main>

        <SiteFooter :clinic="clinic" />
        <WhatsAppFab :phone="clinic.whatsapp" />
        <Toaster position="top-center" />
    </div>
</template>
