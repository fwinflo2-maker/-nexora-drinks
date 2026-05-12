import { Head, usePage } from '@inertiajs/react';
import AdminDashboard from '@/components/dashboards/AdminDashboard';
import CommercialDashboard from '@/components/dashboards/CommercialDashboard';
import ComptableDashboard from '@/components/dashboards/ComptableDashboard';
import LogisticienDashboard from '@/components/dashboards/LogisticienDashboard';
import MagasinierDashboard from '@/components/dashboards/MagasinierDashboard';
import { ParticlesBackground } from '@/components/ui/particles-background';

/**
 * Maps the real team role to a global dashboard role key.
 * Sector-specific apps own their own role handling.
 */
function resolveRole(
    teamRole: string | null,
    nexoraRole: string | null,
): string {
    const candidates = [teamRole, nexoraRole];
    const roleMap: Record<string, string> = {
        // Admin / Owner
        owner:        'admin',
        admin:        'admin',
        manager:      'gerant',
        gerant:       'gerant',
        // Operationals
        ops:          'magasinier',
        comptable:    'comptable',
        accountant:   'comptable',
        logisticien:  'logisticien',
        logistics:    'logisticien',
        livreur:      'logisticien',
        commercial:   'commercial',
        sales:        'commercial',
        caissier:     'commercial',
        magasinier:   'magasinier',
        warehouse:    'magasinier',
        member:       'commercial',
    };

    for (const c of candidates) {
        if (c && roleMap[c.toLowerCase()]) {
return roleMap[c.toLowerCase()];
}
    }

    return 'commercial';
}

/**
 * Determines which admin dashboard component to display based on company sector.
 * Maps sector types to their specialized dashboard component.
 *
 * NOTE : le secteur "drinks" / "boissons" / "distribution" n'est plus traité ici —
 * il dispose désormais de son propre module à `/{team}/drinks/...`.
 */
function resolveAdminDashboard(
    sector: string | null | undefined,
    userName: string,
    teamKpis: any,
    teamName: string | undefined,
    categories: any[] | undefined,
    warehouses: any[] | undefined,
    initialSection: string | undefined,
    extraProps: any = {},
) {
    return (
        <AdminDashboard
            userName={userName}
            teamKpis={teamKpis}
            teamName={teamName}
            categories={categories}
            warehouses={warehouses}
            initialSection={initialSection}
            {...extraProps}
        />
    );
}

export default function Dashboard() {
    const { auth, currentTeam, teamKpis, teamName, categories, warehouses, section } = usePage().props as any;
    const userName: string = auth?.user?.name ?? 'Utilisateur';

    // Resolve role from the real membership only.
    const teamRole    = currentTeam?.role as string | null ?? null;
    const nexoraRole  = auth?.user?.nexora_role as string | null ?? null;
    const role        = resolveRole(teamRole, nexoraRole);

    const sector = currentTeam?.sector as string | null ?? null;

    const titleMap: Record<string, string> = {
        admin:        'Tableau de bord — NEXORA',
        gerant:       'Pilotage — NEXORA',
        comptable:    'Finances & Comptabilité — NEXORA',
        logisticien:  'Logistique & Tournées — NEXORA',
        commercial:   'Activité Commerciale — NEXORA',
        magasinier:   'Gestion du Stock — NEXORA',
    };

    return (
        <>
            <ParticlesBackground />
            <Head title={titleMap[role] ?? 'Tableau de bord — NEXORA'} />
            {role === 'admin'       && resolveAdminDashboard(sector, userName, teamKpis, teamName, categories, warehouses, section, { 
                expiryAlerts: usePage().props.expiryAlerts,
                lots: usePage().props.lots,
                products: usePage().props.products,
                warehouses: usePage().props.warehouses,
                viewerRole: 'admin'
            })}
            {role === 'gerant'      && resolveAdminDashboard(sector, userName, teamKpis, teamName, categories, warehouses, section, { 
                expiryAlerts: usePage().props.expiryAlerts,
                lots: usePage().props.lots,
                products: usePage().props.products,
                warehouses: usePage().props.warehouses,
                viewerRole: 'gerant'
            })}
            {role === 'comptable'   && <ComptableDashboard  userName={userName} />}
            {role === 'logisticien' && <LogisticienDashboard userName={userName} />}
            {role === 'commercial'  && <CommercialDashboard userName={userName} />}
            {role === 'magasinier'  && <MagasinierDashboard userName={userName} />}
        </>
    );
}

Dashboard.layout = (props: { currentTeam?: { slug: string } | null }) => ({
    breadcrumbs: [
        {
            title: 'Tableau de bord',
            href: props.currentTeam ? `/${props.currentTeam.slug}/dashboard` : '/',
        },
    ],
});
