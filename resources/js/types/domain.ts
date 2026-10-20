export type ServiceCategoryKey =
    | 'inyectables'
    | 'rehabilitacion'
    | 'especializados'
    | 'ortopedia';

export type AppointmentStatusKey =
    | 'pending'
    | 'confirmed'
    | 'rescheduled'
    | 'cancelled';

export type Service = {
    id: number;
    slug: string;
    name: string;
    category: ServiceCategoryKey;
    category_label: string;
    price: number | null;
    duration_minutes: number;
    description: string;
    benefits: string[];
    is_active: boolean;
};

export type CategoryOption = {
    value: ServiceCategoryKey;
    label: string;
};

export type ClinicInfo = {
    name: string;
    tagline: string;
    address: string;
    phone: string;
    whatsapp: string;
    email: string;
    maps_url: string;
};

export type BookingConfig = {
    clinic_hours: [string, string];
    home_hours: [string, string];
    max_advance_days: number;
    min_lead_minutes: number;
    currency: string;
};

export type AvailabilitySlotState =
    | 'available'
    | 'pending'
    | 'booked'
    | 'unavailable';

export type AvailabilitySlot = {
    value: string;
    label: string;
    available: boolean;
    state: AvailabilitySlotState;
};

export type AvailabilityDay = {
    date: string;
    is_open: boolean;
    message: string | null;
    slots: AvailabilitySlot[];
};

export type Appointment = {
    id: number;
    folio: string;
    patient_name: string;
    patient_phone: string;
    patient_email: string | null;
    is_home_service: boolean;
    address_notes: string | null;
    address_label: string;
    modality_label: string;
    appointment_date: string;
    appointment_time: string;
    date_label: string;
    status: AppointmentStatusKey;
    status_label: string;
    notes: string | null;
    service: Service;
    whatsapp: {
        confirmation: string;
        reschedule: string;
        reminder: string;
    };
};

export type StatusOption = {
    value: AppointmentStatusKey;
    label: string;
};

export type WeekDay = {
    date: string;
    weekday: string;
    day: number;
    is_selected: boolean;
    is_today: boolean;
    count: number;
};

export type BlockedDate = {
    id: number;
    date: string;
    label: string;
    reason: string | null;
};

export type AppointmentCounts = {
    total: number;
    pending: number;
    confirmed: number;
    home: number;
};

export type DashboardFilters = {
    date: string;
    status: AppointmentStatusKey | null;
    view: 'day' | 'week';
};
