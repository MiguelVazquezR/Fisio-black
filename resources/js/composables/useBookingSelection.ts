import { ref } from 'vue';

const selectedServiceSlug = ref<string | null>(null);

export function useBookingSelection() {
    function selectService(slug: string): void {
        selectedServiceSlug.value = slug;
    }

    function consumeService(): string | null {
        const slug = selectedServiceSlug.value;
        selectedServiceSlug.value = null;

        return slug;
    }

    return {
        selectedServiceSlug,
        selectService,
        consumeService,
    };
}
