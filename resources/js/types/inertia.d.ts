import type { Auth } from '@/types/auth';
import type { Team } from '@/types/teams';

// ── Drinks-extended Team ──────────────────────────────────────────────────────
// Adds fields injected by HandleInertiaRequests for Drinks module context.

export type DrinkTeam = Team & {
    logo_url?: string | null;
    settings?: {
        phone?: string;
        address?: string;
        rccm?: string;
        tax_id?: string;
        currency?: string;
        vat_rate?: number;
    } | null;
};

// ── Flash notifications ───────────────────────────────────────────────────────

export type Flash = {
    success?: string | null;
    error?: string | null;
    warning?: string | null;
    info?: string | null;
};

// ── Shared Inertia page props ─────────────────────────────────────────────────
// Augment @inertiajs/core so usePage().props is properly typed everywhere.
// Usage: const { auth, currentTeam } = usePage<InertiaPageProps>().props;
// Or just: usePage().props.auth  (auto-inferred via module augmentation)

export type InertiaPageProps = {
    auth: Auth;
    currentTeam: DrinkTeam | null;
    teams: Team[];
    flash: Flash;
    name: string;
    sidebarOpen?: boolean;
    [key: string]: unknown;
};

// Use InertiaPageProps explicitly: usePage<InertiaPageProps>().props
// Don't augment @inertiajs/core PageProps globally — breaks test fixtures.
