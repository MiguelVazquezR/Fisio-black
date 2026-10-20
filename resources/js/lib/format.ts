const currencyFormatter = new Intl.NumberFormat('es-MX', {
    style: 'currency',
    currency: 'MXN',
    maximumFractionDigits: 0,
});

const longDateFormatter = new Intl.DateTimeFormat('es-MX', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
    year: 'numeric',
});

const shortDateFormatter = new Intl.DateTimeFormat('es-MX', {
    weekday: 'short',
    day: 'numeric',
    month: 'short',
});

export function formatPrice(price: number | null): string {
    return price === null ? 'A cotizar' : currencyFormatter.format(price);
}

export function formatDateLong(date: string): string {
    const label = longDateFormatter.format(new Date(`${date}T12:00:00`));

    return label.charAt(0).toUpperCase() + label.slice(1);
}

export function formatDateShort(date: string): string {
    return shortDateFormatter.format(new Date(`${date}T12:00:00`));
}

export function formatDuration(minutes: number): string {
    if (minutes < 60) {
        return `${minutes} min`;
    }

    const hours = minutes / 60;

    return Number.isInteger(hours)
        ? `${hours} h`
        : `${Math.floor(hours)} h ${minutes % 60} min`;
}

export function toISODate(date: Date): string {
    const year = date.getFullYear();
    const month = `${date.getMonth() + 1}`.padStart(2, '0');
    const day = `${date.getDate()}`.padStart(2, '0');

    return `${year}-${month}-${day}`;
}

export function todayISO(): string {
    return toISODate(new Date());
}

export function addDaysISO(date: string, days: number): string {
    const parsed = new Date(`${date}T12:00:00`);
    parsed.setDate(parsed.getDate() + days);

    return toISODate(parsed);
}

export function whatsappUrl(phone: string, message: string): string {
    const digits = phone.replace(/\D/g, '');

    return `https://wa.me/${digits}?text=${encodeURIComponent(message)}`;
}
