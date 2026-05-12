import { Head, usePage } from '@inertiajs/react';
import SuperAdminDashboard from '@/components/dashboards/SuperAdminDashboard';
import { ParticlesBackground } from '@/components/ui/particles-background';

/**
 * Page dédiée au Super Administrateur NEXORA.
 */
export default function SuperAdminDashboardPage(props: any) {
    return (
        <>
            <ParticlesBackground />
            <SuperAdminDashboard {...props} />
        </>
    );
}

// Pas de layout externe — on gère le notre directement
SuperAdminDashboardPage.layout = undefined;
