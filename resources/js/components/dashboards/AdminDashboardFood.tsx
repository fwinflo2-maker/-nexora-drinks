import { useEffect, useState } from 'react';
import { motion } from 'framer-motion';
import { router, usePage } from '@inertiajs/react';
import {
    Zap, LogOut, Eye, TrendingUp, BarChart3,
    FileText, Package, Users,
    ArrowUpRight, ArrowDownRight,
} from 'lucide-react';
import { cn } from '@/lib/utils';
import ComptableDashboard from '@/components/dashboards/ComptableDashboard';
import CommercialDashboard from '@/components/dashboards/CommercialDashboard';
import MagasinierDashboard from '@/components/dashboards/MagasinierDashboard';
import AdminFoodOverview from '@/components/dashboards/tabs/AdminFoodOverview';
import AdminFoodProfiles from '@/components/dashboards/tabs/AdminFoodProfiles';
import AdminAgent from '@/components/dashboards/tabs/AdminAgent';

type FoodGodmodeView = null | 'gestionnaire-stock' | 'vendeur' | 'comptable' | 'performance';

type TeamMemberItem = { id: number; name: string; role: string };

const roleLabels: Record<string, string> = {
    'gestionnaire-stock': 'Gestionnaire Stock',
    vendeur:              'Vendeur',
    comptable:            'Comptable',
};

/* ─── Performance card par rôle ─────────────────────────────────────────── */
function PerformanceCard({
    role, icon: Icon, color, kpis,
}: {
    role: string;
    icon: React.ElementType;
    color: string;
    kpis: { label: string; value: string; delta?: string; positive?: boolean }[];
}) {
    return (
        <motion.div
            initial={{ opacity: 0, y: 16 }}
            animate={{ opacity: 1, y: 0 }}
            className="rounded-2xl border border-border bg-card p-5 flex flex-col gap-4"
        >
            <div className="flex items-center gap-3">
                <div className={cn('flex size-9 items-center justify-center rounded-xl', color)}>
                    <Icon className="size-4" />
                </div>
                <div>
                    <h3 className="text-sm font-semibold text-foreground">{roleLabels[role]}</h3>
                    <p className="text-xs text-muted-foreground">Vue performance</p>
                </div>
            </div>
            <div className="space-y-3">
                {kpis.map((kpi) => (
                    <div key={kpi.label} className="flex items-center justify-between">
                        <span className="text-xs text-muted-foreground">{kpi.label}</span>
                        <div className="flex items-center gap-1.5">
                            <span className="text-sm font-semibold text-foreground">{kpi.value}</span>
                            {kpi.delta && (
                                <span className={cn(
                                    'flex items-center text-xs font-medium',
                                    kpi.positive ? 'text-emerald-500' : 'text-rose-500',
                                )}>
                                    {kpi.positive
                                        ? <ArrowUpRight className="size-3" />
                                        : <ArrowDownRight className="size-3" />}
                                    {kpi.delta}
                                </span>
                            )}
                        </div>
                    </div>
                ))}
            </div>
        </motion.div>
    );
}

export default function AdminDashboardFood({
    userName,
    teamName,
    teamKpis,
    initialSection,
}: {
    userName: string;
    teamName?: string;
    teamKpis?: any;
    categories?: any[];
    warehouses?: any[];
    initialSection?: string;
}) {
    const section = initialSection ?? 'overview';
    const [godmodeView, setGodmodeView] = useState<FoodGodmodeView>(null);

    const {
        commercialKpis,
        topClients,
        productMix,
        zonePerformance,
        stockKpis,
        stockItems,
        currentTeam,
        isGodMode,
        teamMembers,
    } = usePage().props as any;

    const membersByRole = (teamMembers ?? []).reduce(
        (acc: Record<string, TeamMemberItem[]>, m: TeamMemberItem) => {
            if (!acc[m.role]) acc[m.role] = [];
            acc[m.role].push(m);
            return acc;
        },
        {} as Record<string, TeamMemberItem[]>,
    );

    const [selectedMember, setSelectedMember] = useState<Record<string, TeamMemberItem | null>>({});

    const getViewUserName = (role: string): string => {
        const members = membersByRole[role] ?? [];
        if (members.length === 0) return userName;
        const picked = selectedMember[role] ?? members[0];
        return picked.name;
    };

    useEffect(() => {
        const slug = currentTeam?.slug;
        if (!slug) return;
        const legacyRedirects: Record<string, string> = {
            stock:      `/${slug}/stocks`,
            mouvements: `/${slug}/stocks/mouvements`,
            commercial: `/${slug}/factures`,
            finance:    `/${slug}/finances`,
        };
        if (legacyRedirects[section]) {
            router.replace(legacyRedirects[section]);
        }
    }, [section, currentTeam?.slug]);

    const toggleView = (view: Exclude<FoodGodmodeView, null>) => {
        setGodmodeView(prev => prev === view ? null : view);
    };

    const performanceData = [
        {
            role: 'comptable',
            icon: FileText,
            color: 'bg-blue-500/10 text-blue-500',
            kpis: [
                { label: 'CA du mois', value: teamKpis?.ca_month_xaf != null ? `${(teamKpis.ca_month_xaf / 1_000_000).toFixed(1)}M XAF` : '—', delta: '+14%', positive: true },
                { label: 'Factures impayées', value: '2', delta: '-1', positive: true },
                { label: 'Taux recouvrement', value: '91%', delta: '+3%', positive: true },
                { label: 'Dépenses du mois', value: '1.8M XAF', delta: '+1%', positive: false },
            ],
        },
        {
            role: 'vendeur',
            icon: TrendingUp,
            color: 'bg-emerald-500/10 text-emerald-500',
            kpis: [
                { label: 'Clients actifs', value: teamKpis?.clients_actifs ?? '—' as any, delta: '+8', positive: true },
                { label: 'Commandes auj.', value: teamKpis?.orders_today ?? '—' as any, delta: '+4', positive: true },
                { label: 'Panier moyen', value: '72K XAF', delta: '+6%', positive: true },
                { label: 'Taux conversion', value: '78%', delta: '+3%', positive: true },
            ],
        },
        {
            role: 'gestionnaire-stock',
            icon: Package,
            color: 'bg-teal-500/10 text-teal-500',
            kpis: [
                { label: 'Alertes stock', value: teamKpis?.stock_alerts ?? '—' as any, delta: '-2', positive: true },
                { label: 'Mouvements auj.', value: teamKpis?.orders_today ?? '—' as any, delta: '+3', positive: true },
                { label: 'Produits en catalogue', value: teamKpis?.total_products ?? '—' as any, positive: true },
                { label: 'Lots FEFO à surveiller', value: '4', delta: '-1', positive: true },
            ],
        },
    ];

    return (
        <div className="flex flex-col gap-6 p-6 lg:p-8 w-full">

            {/* Bannière God Mode impersonation */}
            {isGodMode && (
                <motion.div
                    initial={{ opacity: 0, y: -8 }}
                    animate={{ opacity: 1, y: 0 }}
                    className="flex items-center justify-between rounded-xl border border-amber-400/40 bg-amber-400/10 px-4 py-3 text-sm"
                >
                    <div className="flex items-center gap-2 text-amber-600 dark:text-amber-400">
                        <Zap className="size-4 shrink-0" />
                        <span className="font-semibold">Mode Godmode actif</span>
                        <span className="text-amber-600/70 dark:text-amber-400/70">
                            — Vous consultez ce compte en tant que Super Admin.
                        </span>
                    </div>
                    <button
                        onClick={() => router.post('/super-admin/stop-impersonating')}
                        className="flex items-center gap-1.5 rounded-lg border border-amber-400/40 bg-amber-400/20 px-3 py-1.5 text-xs font-medium text-amber-700 transition hover:bg-amber-400/30 dark:text-amber-300"
                    >
                        <LogOut className="size-3.5" />
                        Quitter le Godmode
                    </button>
                </motion.div>
            )}

            {/* Header */}
            <motion.div initial={{ opacity: 0, y: -8 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.4 }}>
                <div className="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <h1 className="text-2xl font-bold tracking-tight text-foreground">
                            Bonjour, {userName} 👋
                        </h1>
                        <p className="text-sm text-muted-foreground mt-1">
                            Tableau de bord administrateur
                            {teamName && <> · <span className="capitalize">{teamName}</span></>}
                        </p>
                    </div>

                    <div className="flex flex-wrap items-center gap-2">
                        <div className="flex items-center gap-1.5 mr-1">
                            <Users className="size-3.5 text-muted-foreground" />
                            <span className="text-xs text-muted-foreground font-medium">Vues employés :</span>
                        </div>

                        {/* Bouton Performance */}
                        <button
                            onClick={() => toggleView('performance')}
                            className={cn(
                                'flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium transition-colors',
                                godmodeView === 'performance'
                                    ? 'bg-primary text-primary-foreground'
                                    : 'bg-muted text-muted-foreground hover:bg-muted/80',
                            )}
                        >
                            <BarChart3 className="size-3.5" />
                            Performance
                        </button>

                        {/* Boutons rôle individuel */}
                        {(['gestionnaire-stock', 'vendeur', 'comptable'] as const).map((role) => (
                            <button
                                key={role}
                                onClick={() => toggleView(role)}
                                className={cn(
                                    'rounded-lg px-3 py-1.5 text-xs font-medium transition-colors',
                                    godmodeView === role
                                        ? 'bg-primary text-primary-foreground'
                                        : 'bg-muted text-muted-foreground hover:bg-muted/80',
                                )}
                            >
                                {roleLabels[role]}
                            </button>
                        ))}

                        {/* Badge système */}
                        <div className="flex items-center gap-2 rounded-xl border border-border bg-card px-4 py-2 text-sm">
                            <div className="h-2 w-2 rounded-full bg-emerald-400 animate-pulse" />
                            <span className="text-muted-foreground">Système opérationnel</span>
                        </div>
                    </div>
                </div>
            </motion.div>

            {/* ── Vue Performance ── */}
            {godmodeView === 'performance' && (
                <div className="w-full space-y-4">
                    <div className="flex items-center gap-2 rounded-xl border border-primary/20 bg-primary/5 px-4 py-2.5 text-sm">
                        <BarChart3 className="size-4 text-primary" />
                        <span className="font-medium text-primary">Vue Performance — tous les rôles</span>
                        <span className="text-muted-foreground">Synthèse comparative des indicateurs clés par profil.</span>
                    </div>
                    <div className="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                        {performanceData.map((d) => (
                            <PerformanceCard key={d.role} {...d} />
                        ))}
                    </div>
                </div>
            )}

            {/* ── Vue rôle individuel ── */}
            {godmodeView !== null && godmodeView !== 'performance' && (
                <div className="w-full">
                    <div className="mb-4 flex flex-wrap items-center gap-3 rounded-xl border border-primary/20 bg-primary/5 px-4 py-2.5 text-sm">
                        <div className="flex items-center gap-2">
                            <Eye className="size-4 text-primary" />
                            <span className="font-medium text-primary">Vue {roleLabels[godmodeView]}</span>
                            <span className="text-muted-foreground">— Accès complet au tableau de bord.</span>
                        </div>
                        {(membersByRole[godmodeView] ?? []).length > 1 && (
                            <div className="ml-auto flex items-center gap-2">
                                <span className="text-xs text-muted-foreground">Voir en tant que :</span>
                                <select
                                    className="rounded-lg border border-border bg-background px-2 py-1 text-xs text-foreground"
                                    value={selectedMember[godmodeView]?.id ?? (membersByRole[godmodeView]?.[0]?.id ?? '')}
                                    onChange={(e) => {
                                        const member = (membersByRole[godmodeView] ?? []).find(
                                            (m: TeamMemberItem) => m.id === Number(e.target.value),
                                        );
                                        setSelectedMember(prev => ({ ...prev, [godmodeView!]: member ?? null }));
                                    }}
                                >
                                    {(membersByRole[godmodeView] ?? []).map((m: TeamMemberItem) => (
                                        <option key={m.id} value={m.id}>{m.name}</option>
                                    ))}
                                </select>
                            </div>
                        )}
                    </div>
                    {godmodeView === 'gestionnaire-stock' && <MagasinierDashboard userName={getViewUserName('gestionnaire-stock')} />}
                    {godmodeView === 'vendeur'             && <CommercialDashboard  userName={getViewUserName('vendeur')} />}
                    {godmodeView === 'comptable'           && <ComptableDashboard   userName={getViewUserName('comptable')} />}
                </div>
            )}

            {/* ── Vue admin normale ── */}
            {godmodeView === null && (
                <motion.div
                    key={section}
                    initial={{ opacity: 0, y: 8 }}
                    animate={{ opacity: 1, y: 0 }}
                    exit={{ opacity: 0, y: -8 }}
                    transition={{ duration: 0.3 }}
                >
                    {(section === 'overview' || section === 'food-overview') && <AdminFoodOverview teamKpis={teamKpis} />}
                    {section === 'food-commercial' && (
                        <CommercialDashboard
                            userName={userName}
                        />
                    )}
                    {section === 'food-stock' && (
                        <MagasinierDashboard
                            userName={userName}
                        />
                    )}
                    {section === 'profiles' && <AdminFoodProfiles teamKpis={teamKpis} />}
                    {section === 'agent'    && <AdminAgent userName={userName} teamName={teamName} />}
                </motion.div>
            )}
        </div>
    );
}
