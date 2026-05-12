import { Head, router, usePage } from '@inertiajs/react';
import { motion, AnimatePresence } from 'framer-motion';
import {
    LayoutDashboard, Building2, Users, Server,
    ScrollText, UserCircle, LogOut,
    TrendingUp, TrendingDown, AlertTriangle, CheckCircle2,
    ArrowUpRight, Search, Eye, Ban,
    Activity, Zap, Settings2,
    Database, HardDrive, Clock, Wifi, Shield, Bell, ToggleLeft, ToggleRight, Send, Trash2,
} from 'lucide-react';
import { useState } from 'react';
import {
    LineChart, Line, PieChart, Pie, Cell,
    XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer,
} from 'recharts';
import AppLogoIcon from '@/components/app-logo-icon';
import { ParticlesBackground } from '@/components/ui/particles-background';

// ─── Palette Light (fond blanc pur) ──────────────────────────────────────────
const C = {
    // ── Accent corail
    coral:        '#E05A3D',
    coralLight:   '#C94D32',
    coralDim:     'rgba(224,90,61,0.08)',
    coralBorder:  'rgba(224,90,61,0.20)',
    coralHover:   'rgba(224,90,61,0.14)',

    // ── Fonds blancs
    bgRoot:       '#FFFFFF',
    bgSide:       '#FAFAFA',
    bgHeader:     'rgba(255,255,255,0.92)',
    bgCard:       '#FFFFFF',
    bgCardHover:  '#FFF7F5',
    bgInput:      '#F7F7F7',

    // ── Bordures légères
    border:       'rgba(0,0,0,0.08)',
    borderMid:    'rgba(0,0,0,0.12)',
    borderStrong: 'rgba(224,90,61,0.28)',

    // ── Texte sur fond blanc
    textPrimary:  '#111111',
    textMuted:    '#6B6B6B',
    textFaint:    '#AAAAAA',

    // ── Sémantique
    green:        '#1A7F4B',
    greenDim:     'rgba(26,127,75,0.08)',
    greenBorder:  'rgba(26,127,75,0.20)',
    red:          '#C0392B',
    redDim:       'rgba(192,57,43,0.08)',
    redBorder:    'rgba(192,57,43,0.20)',
} as const;

type Module = 'apercu' | 'entreprises' | 'utilisateurs' | 'systeme' | 'journal' | 'parametres' | 'compte';

interface NetworkKpis {
    total_tenants: number;
    active_tenants: number;
    total_users: number;
    open_incidents: number;
    mrr_xaf: number;
}

interface Tenant {
    id: number;
    name: string;
    type: string;
    plan: string;
    status: 'active' | 'pending' | 'suspendu';
    users_count: number;
    users_limit: number;
    joined_at: string;
}

interface AppUser {
    id: number;
    name: string;
    email: string;
    nexora_role: string | null;
    email_verified_at: string | null;
    created_at: string;
    teams: Array<{ id: number; name: string; role: string | null }>;
}

interface SystemHealth {
    database: { status: 'healthy' | 'unhealthy'; error?: string };
    cache: { status: 'healthy' | 'unhealthy' };
    maintenance_enabled: boolean;
    pending_jobs: number;
    failed_jobs: number;
    disk_free_pct: number;
}

interface AuditLog {
    id: number;
    action: string;
    super_admin: string;
    target_team: string | null;
    ip_address: string | null;
    changes: Record<string, unknown> | null;
    created_at: string;
}

interface Props {
    userName?: string;
    networkKpis?: NetworkKpis;
    tenants?: Tenant[];
    users?: AppUser[];
    systemHealth?: SystemHealth;
    recentAuditLogs?: AuditLog[];
}

const fadeUp = {
    hidden: { opacity: 0, y: 16 },
    visible: (i: number) => ({
        opacity: 1, y: 0,
        transition: { duration: 0.42, delay: i * 0.065 },
    }),
};

// ─── Badge statut ─────────────────────────────────────────────────────────────
function StatutBadge({ statut }: { statut: string }) {
    const styles: Record<string, React.CSSProperties> = {
        active:   { background: C.greenDim,  border: `1px solid ${C.greenBorder}`,  color: C.green },
        pending:  { background: C.coralDim,  border: `1px solid ${C.coralBorder}`,  color: C.coralLight },
        suspendu: { background: C.redDim,    border: `1px solid ${C.redBorder}`,     color: C.red },
    };
    const labels: Record<string, string> = { active: 'Actif', pending: 'En attente', suspendu: 'Suspendu' };

    return (
        <span style={{
            ...( styles[statut] ?? { background: C.coralDim, border: `1px solid ${C.coralBorder}`, color: C.textMuted }),
            display: 'inline-flex', alignItems: 'center', borderRadius: '99px',
            padding: '2px 9px', fontSize: '10px', letterSpacing: '0.06em',
            fontWeight: 600, textTransform: 'uppercase',
        }}>
            {labels[statut] ?? statut}
        </span>
    );
}

// ─── KPI Card ─────────────────────────────────────────────────────────────────
function KpiCard({ label, value, delta, icon: Icon, alert }: {
    label: string; value: string; delta?: string; icon: React.ElementType; alert?: boolean;
}) {
    const positive = delta?.startsWith('+');

    return (
        <motion.div
            whileHover={{ scale: 1.015 }}
            style={{
                background: C.bgCard,
                border: `1px solid ${alert ? C.redBorder : C.border}`,
                borderRadius: '14px', padding: '18px 20px',
                display: 'flex', flexDirection: 'column', gap: '14px',
                backdropFilter: 'blur(16px)',
                transition: 'background 0.2s',
            }}
        >
            <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
                <span style={{ fontSize: '10px', fontWeight: 600, color: C.textFaint, textTransform: 'uppercase', letterSpacing: '0.11em' }}>
                    {label}
                </span>
                <div style={{
                    height: '30px', width: '30px', borderRadius: '8px',
                    display: 'flex', alignItems: 'center', justifyContent: 'center',
                    background: alert ? C.redDim : C.coralDim,
                    border: `1px solid ${alert ? C.redBorder : C.coralBorder}`,
                    color: alert ? C.red : C.coralLight,
                }}>
                    <Icon style={{ height: '14px', width: '14px' }} />
                </div>
            </div>
            <div>
                <div style={{ fontSize: '24px', fontWeight: 600, color: C.textPrimary, letterSpacing: '-0.02em', lineHeight: 1 }}>
                    {value}
                </div>
                {delta && (
                    <div style={{
                        display: 'flex', alignItems: 'center', gap: '4px',
                        fontSize: '11px', marginTop: '6px', fontWeight: 500,
                        color: positive ? C.green : C.red,
                    }}>
                        {positive
                            ? <ArrowUpRight style={{ height: '12px', width: '12px' }} />
                            : <TrendingDown style={{ height: '12px', width: '12px' }} />}
                        {delta} vs mois préc.
                    </div>
                )}
            </div>
        </motion.div>
    );
}

// ─── Carte de santé système ───────────────────────────────────────────────────
function HealthCard({ label, status, icon: Icon, detail }: {
    label: string;
    status: 'healthy' | 'unhealthy' | 'warning' | 'ok';
    icon: React.ElementType;
    detail?: string;
}) {
    const isOk = status === 'healthy' || status === 'ok';

    return (
        <div style={{
            background: C.bgCard,
            border: `1px solid ${isOk ? C.greenBorder : C.redBorder}`,
            borderRadius: '14px', padding: '18px 20px',
            display: 'flex', alignItems: 'center', gap: '14px',
        }}>
            <div style={{
                height: '38px', width: '38px', borderRadius: '10px', flexShrink: 0,
                display: 'flex', alignItems: 'center', justifyContent: 'center',
                background: isOk ? C.greenDim : C.redDim,
                border: `1px solid ${isOk ? C.greenBorder : C.redBorder}`,
                color: isOk ? C.green : C.red,
            }}>
                <Icon style={{ height: '16px', width: '16px' }} />
            </div>
            <div style={{ flex: 1, minWidth: 0 }}>
                <div style={{ fontSize: '12px', fontWeight: 500, color: C.textPrimary }}>{label}</div>
                {detail && (
                    <div style={{ fontSize: '11px', color: C.textMuted, marginTop: '2px' }}>{detail}</div>
                )}
            </div>
            <span style={{
                display: 'inline-flex', alignItems: 'center', borderRadius: '99px',
                padding: '2px 9px', fontSize: '10px', letterSpacing: '0.06em',
                fontWeight: 600, textTransform: 'uppercase',
                background: isOk ? C.greenDim : C.redDim,
                border: `1px solid ${isOk ? C.greenBorder : C.redBorder}`,
                color: isOk ? C.green : C.red,
            }}>
                {isOk ? 'OK' : 'Erreur'}
            </span>
        </div>
    );
}

// ─── Formatage des actions d'audit ────────────────────────────────────────────
function formatAuditAction(action: string): string {
    const map: Record<string, string> = {
        impersonate_tenant: 'Impersonation tenant',
        activate_tenant: 'Activation tenant',
        suspend_tenant: 'Suspension tenant',
        create_tenant: 'Création tenant',
        delete_tenant: 'Suppression tenant',
        update_tenant: 'Modification tenant',
        login: 'Connexion',
        logout: 'Déconnexion',
        broadcast_message: 'Message diffusé',
        toggle_maintenance: 'Mode maintenance',
    };

    return map[action] ?? action.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
}

// ─── Composant principal ──────────────────────────────────────────────────────
export default function SuperAdminDashboard({
    userName, networkKpis, tenants, users, systemHealth, recentAuditLogs,
}: Props) {

    const page = usePage<{ auth: { user: { email: string; name: string } } }>();
    const authUser = page.props.auth?.user;

    const [activeModule, setActiveModule] = useState<Module>('apercu');
    const [search, setSearch] = useState('');

    // Module utilisateurs
    const [userSearch, setUserSearch] = useState('');

    // Module journal
    const [journalFilter, setJournalFilter] = useState('');

    // Module système — broadcast
    const [broadcastMessage, setBroadcastMessage] = useState('');
    const [broadcastType, setBroadcastType] = useState<'info' | 'warning' | 'critical'>('info');

    const mrrData = [
        { mois: 'Nov', mrr: 1_820_000 }, { mois: 'Déc', mrr: 2_140_000 },
        { mois: 'Jan', mrr: 2_430_000 }, { mois: 'Fév', mrr: 2_680_000 },
        { mois: 'Mar', mrr: 3_050_000 }, { mois: 'Avr', mrr: networkKpis?.mrr_xaf || 3_420_000 },
    ];

    const offresData = [
        { name: 'Starter',    value: tenants?.filter(t => t.plan === 'Starter').length    || 4,  color: C.coral },
        { name: 'Pro',        value: tenants?.filter(t => t.plan === 'Pro').length        || 9,  color: 'rgba(200,82,58,0.50)' },
        { name: 'Enterprise', value: tenants?.filter(t => t.plan === 'Enterprise').length || 3,  color: 'rgba(200,82,58,0.22)' },
    ];

    const filteredTenants = (tenants ?? []).filter(t =>
        t.name?.toLowerCase().includes(search.toLowerCase())
    );

    const filteredUsers = (users ?? []).filter(u =>
        u.name?.toLowerCase().includes(userSearch.toLowerCase()) ||
        u.email?.toLowerCase().includes(userSearch.toLowerCase())
    );

    const filteredLogs = (recentAuditLogs ?? []).filter(log =>
        journalFilter === '' ||
        log.action?.toLowerCase().includes(journalFilter.toLowerCase()) ||
        formatAuditAction(log.action).toLowerCase().includes(journalFilter.toLowerCase())
    );

    const navItems: { id: Module; label: string; icon: React.ElementType; badge?: number }[] = [
        { id: 'apercu',       label: 'Aperçu réseau',   icon: LayoutDashboard },
        { id: 'entreprises',  label: 'Entreprises',     icon: Building2, badge: tenants?.filter(t => t.status === 'pending').length || 0 },
        { id: 'utilisateurs', label: 'Utilisateurs',    icon: Users },
        { id: 'systeme',      label: 'Système',         icon: Server },
        { id: 'journal',      label: "Journal d'audit", icon: ScrollText },
        { id: 'parametres',   label: 'Paramètres',      icon: Settings2 },
        { id: 'compte',       label: 'Mon compte',      icon: UserCircle },
    ];

    // ── Section header réutilisable ───────────────────────────────────────────
    const sectionTitle = (title: string, sub?: string) => (
        <div style={{ marginBottom: '20px' }}>
            <h2 style={{ fontSize: '16px', fontWeight: 500, color: C.textPrimary, margin: 0, letterSpacing: '-0.01em' }}>
                {title}
            </h2>
            {sub && (
                <p style={{ fontSize: '12px', color: C.textMuted, margin: '3px 0 0' }}>{sub}</p>
            )}
        </div>
    );

    return (
        <>
            <Head title="SuperAdmin — NEXORA" />
            <ParticlesBackground />

            <style>{`
                @import url('https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500&family=DM+Mono:wght@300;400&display=swap');

                /* Fond blanc pur */
                html, body { background: #FFFFFF !important; }

                .sa-root * { font-family: 'DM Sans', sans-serif; box-sizing: border-box; }
                .sa-root .mono { font-family: 'DM Mono', monospace; }

                /* Scrollbar discrète */
                .sa-root ::-webkit-scrollbar { width: 3px; height: 3px; }
                .sa-root ::-webkit-scrollbar-track { background: transparent; }
                .sa-root ::-webkit-scrollbar-thumb { background: rgba(224,90,61,0.18); border-radius: 99px; }
                .sa-nav-btn { transition: background 0.18s ease, color 0.18s ease; }
                .sa-nav-btn:hover { background: rgba(224,90,61,0.07) !important; color: #111111 !important; }
                .sa-tr { transition: background 0.15s ease; }
                .sa-tr:hover { background: rgba(224,90,61,0.04) !important; }
                .sa-input { outline: none; transition: border-color 0.2s, box-shadow 0.2s; color: #111111; }
                .sa-input::placeholder { color: #AAAAAA; }
                .sa-input:focus { border-color: rgba(200,82,58,0.36) !important; box-shadow: 0 0 0 3px rgba(200,82,58,0.07); }

                /* Bouton action */
                .sa-btn { transition: filter 0.15s, transform 0.12s; }
                .sa-btn:hover { filter: brightness(1.12); }
                .sa-btn:active { transform: scale(0.97); }

                @keyframes sa-fade-in { from { opacity:0; transform: translateY(8px); } to { opacity:1; transform: translateY(0); } }
                .sa-section { animation: sa-fade-in 0.32s ease both; }
            `}</style>

            <div className="sa-root relative z-10 min-h-[100dvh] flex flex-1"
                style={{ color: C.textPrimary, background: '#FFFFFF' }}>

                {/* ── Sidebar ─────────────────────────────────────────────── */}
                <motion.aside
                    initial={{ x: -32, opacity: 0 }} animate={{ x: 0, opacity: 1 }}
                    transition={{ duration: 0.38 }}
                    className="hidden lg:flex w-[220px] shrink-0 flex-col"
                    style={{
                        background: C.bgSide,
                        borderRight: `1px solid rgba(0,0,0,0.08)`,
                        backdropFilter: 'blur(40px)',
                    }}
                >
                    {/* Logo */}
                    <div style={{ padding: '24px 22px 20px', borderBottom: `1px solid ${C.border}` }}>
                        <div style={{ display: 'flex', alignItems: 'center', gap: '10px' }}>
                            <div style={{
                                height: '32px', width: '32px', borderRadius: '9px',
                                display: 'flex', alignItems: 'center', justifyContent: 'center',
                                background: C.coralDim, border: `1px solid ${C.coralBorder}`,
                            }}>
                                <AppLogoIcon style={{ height: '15px', width: '15px', color: C.coral }} />
                            </div>
                            <div>
                                <div style={{ fontSize: '14px', fontWeight: 500, letterSpacing: '0.05em', color: C.textPrimary }}>
                                    NEXORA
                                </div>
                                <div style={{ fontSize: '9px', fontWeight: 600, letterSpacing: '0.14em', textTransform: 'uppercase', color: C.coralLight, marginTop: '1px' }}>
                                    SuperAdmin
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Nav */}
                    <nav style={{ flex: 1, padding: '12px 10px', overflowY: 'auto', display: 'flex', flexDirection: 'column', gap: '2px' }}>
                        {navItems.map((item, i) => {
                            const Icon = item.icon;
                            const active = activeModule === item.id;

                            return (
                                <motion.button
                                    key={item.id} custom={i} variants={fadeUp} initial="hidden" animate="visible"
                                    onClick={() => setActiveModule(item.id)}
                                    className="sa-nav-btn w-full"
                                    style={{
                                        display: 'flex', alignItems: 'center', gap: '10px',
                                        padding: '9px 12px', borderRadius: '9px', fontSize: '13px', fontWeight: 500,
                                        textAlign: 'left', border: 'none', cursor: 'pointer',
                                        background: active ? C.coralDim : 'transparent',
                                        color: active ? C.coralLight : C.textMuted,
                                        borderLeft: active ? `2px solid ${C.coral}` : '2px solid transparent',
                                    }}
                                >
                                    <Icon style={{ height: '15px', width: '15px', flexShrink: 0 }} />
                                    <span style={{ flex: 1, overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap' }}>
                                        {item.label}
                                    </span>
                                    {item.badge ? (
                                        <span style={{
                                            height: '16px', minWidth: '16px', borderRadius: '99px',
                                            background: C.redDim, border: `1px solid ${C.redBorder}`,
                                            color: C.red, fontSize: '9px', fontWeight: 700,
                                            display: 'flex', alignItems: 'center', justifyContent: 'center', padding: '0 4px',
                                        }}>
                                            {item.badge}
                                        </span>
                                    ) : null}
                                </motion.button>
                            );
                        })}
                    </nav>

                    {/* Logout */}
                    <div style={{ padding: '12px 10px 20px', borderTop: `1px solid ${C.border}` }}>
                        <button
                            onClick={() => router.post('/logout')}
                            className="sa-nav-btn w-full"
                            style={{
                                display: 'flex', alignItems: 'center', gap: '10px',
                                padding: '9px 12px', borderRadius: '9px', fontSize: '13px', fontWeight: 500,
                                color: C.textFaint, background: 'transparent', border: 'none', cursor: 'pointer',
                            }}
                        >
                            <LogOut style={{ height: '15px', width: '15px', flexShrink: 0 }} />
                            Déconnexion
                        </button>
                    </div>
                </motion.aside>

                {/* ── Zone principale ──────────────────────────────────────── */}
                <div style={{ flex: 1, display: 'flex', flexDirection: 'column', minWidth: 0 }}>

                    {/* Header */}
                    <motion.header
                        initial={{ y: -16, opacity: 0 }} animate={{ y: 0, opacity: 1 }}
                        transition={{ duration: 0.36 }}
                        style={{
                            display: 'flex', alignItems: 'center', justifyContent: 'space-between',
                            padding: '18px 32px', position: 'sticky', top: 0, zIndex: 10,
                            background: C.bgHeader,
                            borderBottom: `1px solid ${C.border}`,
                            backdropFilter: 'blur(28px)',
                        }}
                    >
                        <div>
                            <h1 style={{ fontSize: '18px', fontWeight: 500, letterSpacing: '-0.01em', color: C.textPrimary, margin: 0 }}>
                                Bonjour, {userName || 'Admin'} ✦
                            </h1>
                            <p style={{ fontSize: '12px', color: C.textMuted, margin: '3px 0 0', letterSpacing: '0.02em' }}>
                                Portail administrateur global · NEXORA
                            </p>
                        </div>
                        <div style={{ display: 'flex', alignItems: 'center', gap: '10px' }}>
                            {/* Indicateur système */}
                            <div style={{
                                display: 'flex', alignItems: 'center', gap: '6px',
                                padding: '5px 12px', borderRadius: '99px', fontSize: '11px', fontWeight: 500,
                                background: C.greenDim, border: `1px solid ${C.greenBorder}`,
                                color: C.green,
                            }}>
                                <span style={{ height: '6px', width: '6px', borderRadius: '99px', background: C.green, display: 'inline-block' }} />
                                Système nominal
                            </div>
                        </div>
                    </motion.header>

                    {/* Main */}
                    <main style={{ flex: 1, overflowY: 'auto', padding: '28px 32px' }}>
                        <AnimatePresence mode="wait">

                            {/* ── Module Aperçu ── */}
                            {activeModule === 'apercu' && (
                                <div key="apercu" className="sa-section" style={{ display: 'flex', flexDirection: 'column', gap: '20px' }}>

                                    {/* KPIs */}
                                    <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(200px, 1fr))', gap: '12px' }}>
                                        {[
                                            { label: 'MRR réseau', value: `${((networkKpis?.mrr_xaf || 3_420_000) / 1_000_000).toFixed(1)}M XAF`, delta: '+12%', icon: TrendingUp },
                                            { label: 'Entreprises actives', value: `${networkKpis?.active_tenants || 16}`, delta: '+3', icon: Building2 },
                                            { label: 'Utilisateurs actifs', value: `${networkKpis?.total_users || 94}`, delta: '+8', icon: Users },
                                            { label: 'Incidents ouverts', value: `${networkKpis?.open_incidents ?? 0}`, icon: AlertTriangle, alert: (networkKpis?.open_incidents ?? 0) > 0 },
                                        ].map((kpi, i) => (
                                            <motion.div key={kpi.label} custom={i} variants={fadeUp} initial="hidden" animate="visible">
                                                <KpiCard {...kpi} />
                                            </motion.div>
                                        ))}
                                    </div>

                                    {/* Charts */}
                                    <div style={{ display: 'grid', gridTemplateColumns: '1fr 300px', gap: '16px' }}>

                                        {/* MRR line chart */}
                                        <motion.div custom={4} variants={fadeUp} initial="hidden" animate="visible" style={{
                                            background: C.bgCard, border: `1px solid ${C.border}`,
                                            borderRadius: '14px', padding: '20px 20px 12px',
                                            backdropFilter: 'blur(16px)',
                                        }}>
                                            <div style={{ fontSize: '11px', fontWeight: 600, color: C.textFaint, textTransform: 'uppercase', letterSpacing: '0.10em', marginBottom: '16px' }}>
                                                MRR — 6 derniers mois (XAF)
                                            </div>
                                            <ResponsiveContainer width="100%" height={160}>
                                                <LineChart data={mrrData} margin={{ top: 4, right: 8, left: 0, bottom: 0 }}>
                                                    <CartesianGrid strokeDasharray="3 3" stroke={C.border} vertical={false} />
                                                    <XAxis dataKey="mois" tick={{ fontSize: 11, fill: C.textFaint }} axisLine={false} tickLine={false} />
                                                    <YAxis tick={{ fontSize: 11, fill: C.textFaint }} tickFormatter={(v: number) => `${(v / 1_000_000).toFixed(1)}M`} width={40} axisLine={false} tickLine={false} />
                                                    <Tooltip
                                                        contentStyle={{
                                                            fontSize: 12, borderRadius: '8px',
                                                            border: `1px solid ${C.coralBorder}`,
                                                            background: '#FFFFFF',
                                                            color: C.textPrimary,
                                                        }}
                                                        formatter={(v) => {
                                                            const value = typeof v === 'number' ? v : Number(v ?? 0);

                                                            return [`${(value / 1_000_000).toFixed(2)}M XAF`, 'MRR'];
                                                        }}
                                                    />
                                                    <Line
                                                        type="monotone" dataKey="mrr"
                                                        stroke={C.coral} strokeWidth={1.8}
                                                        dot={{ r: 3, fill: C.coral, strokeWidth: 0 }}
                                                        activeDot={{ r: 5, stroke: C.textPrimary, strokeWidth: 1.5 }}
                                                    />
                                                </LineChart>
                                            </ResponsiveContainer>
                                        </motion.div>

                                        {/* Pie offres */}
                                        <motion.div custom={5} variants={fadeUp} initial="hidden" animate="visible" style={{
                                            background: C.bgCard, border: `1px solid ${C.border}`,
                                            borderRadius: '14px', padding: '20px',
                                            backdropFilter: 'blur(16px)',
                                        }}>
                                            <div style={{ fontSize: '11px', fontWeight: 600, color: C.textFaint, textTransform: 'uppercase', letterSpacing: '0.10em', marginBottom: '14px' }}>
                                                Répartition des offres
                                            </div>
                                            <ResponsiveContainer width="100%" height={120}>
                                                <PieChart>
                                                    <Pie data={offresData} cx="50%" cy="50%" innerRadius={36} outerRadius={52} dataKey="value" stroke="none" paddingAngle={3}>
                                                        {offresData.map((e, i) => <Cell key={i} fill={e.color} />)}
                                                    </Pie>
                                                    <Tooltip contentStyle={{
                                                        fontSize: 12, borderRadius: '8px',
                                                        border: `1px solid ${C.coralBorder}`,
                                                        background: '#FFFFFF',
                                                        color: C.textPrimary,
                                                    }} />
                                                </PieChart>
                                            </ResponsiveContainer>
                                            <div style={{ display: 'flex', flexDirection: 'column', gap: '7px', marginTop: '12px' }}>
                                                {offresData.map(o => (
                                                    <div key={o.name} style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
                                                        <div style={{ display: 'flex', alignItems: 'center', gap: '7px' }}>
                                                            <span style={{ height: '7px', width: '7px', borderRadius: '99px', background: o.color, display: 'inline-block' }} />
                                                            <span style={{ fontSize: '12px', color: C.textMuted }}>{o.name}</span>
                                                        </div>
                                                        <span className="mono" style={{ fontSize: '12px', color: C.textPrimary }}>{o.value}</span>
                                                    </div>
                                                ))}
                                            </div>
                                        </motion.div>
                                    </div>
                                </div>
                            )}

                            {/* ── Module Entreprises ── */}
                            {activeModule === 'entreprises' && (
                                <div key="entreprises" className="sa-section" style={{ display: 'flex', flexDirection: 'column', gap: '14px' }}>

                                    {/* Recherche */}
                                    <div style={{ display: 'flex', alignItems: 'center', gap: '12px' }}>
                                        <div style={{ position: 'relative', flex: 1 }}>
                                            <Search style={{
                                                position: 'absolute', left: '12px', top: '50%',
                                                transform: 'translateY(-50%)', height: '14px', width: '14px',
                                                color: C.textFaint,
                                            }} />
                                            <input
                                                className="sa-input"
                                                value={search}
                                                onChange={e => setSearch(e.target.value)}
                                                placeholder="Rechercher une entreprise…"
                                                style={{
                                                    width: '100%', height: '38px', paddingLeft: '38px', paddingRight: '14px',
                                                    borderRadius: '10px', fontSize: '13px',
                                                    background: C.bgInput,
                                                    border: `1px solid ${C.border}`,
                                                    boxSizing: 'border-box',
                                                }}
                                            />
                                        </div>
                                        <div style={{ fontSize: '11px', color: C.textFaint, whiteSpace: 'nowrap' }}>
                                            {filteredTenants.length} résultat{filteredTenants.length !== 1 ? 's' : ''}
                                        </div>
                                    </div>

                                    {/* Table */}
                                    <div style={{
                                        background: C.bgCard, border: `1px solid ${C.border}`,
                                        borderRadius: '14px', overflow: 'hidden',
                                        backdropFilter: 'blur(16px)',
                                    }}>
                                        <table style={{ width: '100%', borderCollapse: 'collapse', fontSize: '13px' }}>
                                            <thead>
                                                <tr style={{ borderBottom: `1px solid ${C.border}` }}>
                                                    {['Entreprise', 'Offre', 'Statut', 'Utilisateurs', 'Actions'].map(h => (
                                                        <th key={h} style={{
                                                            padding: '12px 16px', textAlign: 'left', fontSize: '10px',
                                                            fontWeight: 600, color: C.textFaint,
                                                            textTransform: 'uppercase', letterSpacing: '0.09em',
                                                        }}>{h}</th>
                                                    ))}
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {filteredTenants.map((e, i) => (
                                                    <motion.tr
                                                        key={e.id} custom={i} variants={fadeUp} initial="hidden" animate="visible"
                                                        className="sa-tr"
                                                        style={{ borderBottom: `1px solid ${C.border}` }}
                                                    >
                                                        <td style={{ padding: '13px 16px' }}>
                                                            <div style={{ fontWeight: 500, color: C.textPrimary }}>{e.name}</div>
                                                            <div className="mono" style={{ fontSize: '10px', color: C.textFaint, marginTop: '2px' }}>ID #{e.id}</div>
                                                        </td>
                                                        <td style={{ padding: '13px 16px' }}>
                                                            <span style={{
                                                                fontSize: '11px', padding: '2px 8px', borderRadius: '5px',
                                                                background: C.coralDim, border: `1px solid ${C.coralBorder}`,
                                                                color: C.coralLight, fontWeight: 500,
                                                            }}>{e.plan}</span>
                                                        </td>
                                                        <td style={{ padding: '13px 16px' }}><StatutBadge statut={e.status} /></td>
                                                        <td style={{ padding: '13px 16px' }}>
                                                            <div style={{ display: 'flex', alignItems: 'center', gap: '6px' }}>
                                                                <div style={{
                                                                    height: '4px', width: '60px', borderRadius: '99px',
                                                                    background: C.border, overflow: 'hidden',
                                                                }}>
                                                                    <div style={{
                                                                        height: '100%', borderRadius: '99px',
                                                                        width: `${Math.min(100, ((e.users_count || 0) / (e.users_limit || 10)) * 100)}%`,
                                                                        background: C.coral,
                                                                    }} />
                                                                </div>
                                                                <span style={{ fontSize: '11px', color: C.textMuted }}>
                                                                    {e.users_count}/{e.users_limit}
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td style={{ padding: '13px 16px' }}>
                                                            <div style={{ display: 'flex', gap: '6px', flexWrap: 'wrap' }}>
                                                                {(e.status === 'pending' || e.status === 'suspendu') && (
                                                                    <button
                                                                        onClick={() => router.post(`/super-admin/tenants/${e.id}/activate`)}
                                                                        className="sa-btn"
                                                                        style={{
                                                                            display: 'flex', alignItems: 'center', gap: '5px',
                                                                            height: '28px', padding: '0 10px', borderRadius: '7px', fontSize: '11px', fontWeight: 500,
                                                                            background: C.greenDim, border: `1px solid ${C.greenBorder}`,
                                                                            color: C.green, cursor: 'pointer',
                                                                        }}
                                                                    >
                                                                        <CheckCircle2 style={{ height: '12px', width: '12px' }} /> Activer
                                                                    </button>
                                                                )}
                                                                {e.status === 'active' && (
                                                                    <button
                                                                        onClick={() => router.post(`/super-admin/tenants/${e.id}/suspend`)}
                                                                        className="sa-btn"
                                                                        style={{
                                                                            display: 'flex', alignItems: 'center', gap: '5px',
                                                                            height: '28px', padding: '0 10px', borderRadius: '7px', fontSize: '11px', fontWeight: 500,
                                                                            background: C.redDim, border: `1px solid ${C.redBorder}`,
                                                                            color: C.red, cursor: 'pointer',
                                                                        }}
                                                                    >
                                                                        <Ban style={{ height: '12px', width: '12px' }} /> Suspendre
                                                                    </button>
                                                                )}
                                                                    <button
                                                                        onClick={() => {
                                                                            if (confirm(`Êtes-vous sûr de vouloir supprimer ${e.name} ? Cette action est réversible via la base de données.`)) {
                                                                                router.delete(`/super-admin/tenants/${e.id}/delete`);
                                                                            }
                                                                        }}
                                                                        className="sa-btn"
                                                                        style={{
                                                                            display: 'flex', alignItems: 'center', gap: '5px',
                                                                            height: '28px', padding: '0 10px', borderRadius: '7px', fontSize: '11px', fontWeight: 500,
                                                                            background: C.redDim, border: `1px solid ${C.redBorder}`,
                                                                            color: C.red, cursor: 'pointer',
                                                                        }}
                                                                    >
                                                                        <Trash2 style={{ height: '12px', width: '12px' }} /> Supprimer
                                                                    </button>
                                                                    <button
                                                                        onClick={() => router.post(`/super-admin/tenants/${e.id}/impersonate`)}
                                                                    className="sa-btn"
                                                                    style={{
                                                                        display: 'flex', alignItems: 'center', gap: '5px',
                                                                        height: '28px', padding: '0 10px', borderRadius: '7px', fontSize: '11px', fontWeight: 500,
                                                                        background: C.coralDim, border: `1px solid ${C.coralBorder}`,
                                                                        color: C.coralLight, cursor: 'pointer',
                                                                    }}
                                                                >
                                                                    <Eye style={{ height: '12px', width: '12px' }} /> God Mode
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </motion.tr>
                                                ))}
                                                {filteredTenants.length === 0 && (
                                                    <tr>
                                                        <td colSpan={5} style={{ textAlign: 'center', padding: '40px 16px', color: C.textFaint, fontSize: '13px' }}>
                                                            {search ? `Aucun résultat pour "${search}"` : 'Aucune entreprise enregistrée'}
                                                        </td>
                                                    </tr>
                                                )}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            )}

                            {/* ── Module Utilisateurs ── */}
                            {activeModule === 'utilisateurs' && (
                                <div key="utilisateurs" className="sa-section" style={{ display: 'flex', flexDirection: 'column', gap: '14px' }}>

                                    {/* En-tête + compteur */}
                                    <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
                                        <div>
                                            <h2 style={{ fontSize: '16px', fontWeight: 500, color: C.textPrimary, margin: 0, letterSpacing: '-0.01em' }}>
                                                Utilisateurs
                                            </h2>
                                            <p style={{ fontSize: '12px', color: C.textMuted, margin: '3px 0 0' }}>
                                                {users?.length ?? 0} utilisateur{(users?.length ?? 0) !== 1 ? 's' : ''} au total
                                            </p>
                                        </div>
                                    </div>

                                    {/* Barre de recherche */}
                                    <div style={{ position: 'relative' }}>
                                        <Search style={{
                                            position: 'absolute', left: '12px', top: '50%',
                                            transform: 'translateY(-50%)', height: '14px', width: '14px',
                                            color: C.textFaint,
                                        }} />
                                        <input
                                            className="sa-input"
                                            value={userSearch}
                                            onChange={e => setUserSearch(e.target.value)}
                                            placeholder="Rechercher par nom ou email…"
                                            style={{
                                                width: '100%', height: '38px', paddingLeft: '38px', paddingRight: '14px',
                                                borderRadius: '10px', fontSize: '13px',
                                                background: C.bgInput,
                                                border: `1px solid ${C.border}`,
                                                boxSizing: 'border-box',
                                            }}
                                        />
                                    </div>

                                    {/* Table */}
                                    <div style={{
                                        background: C.bgCard, border: `1px solid ${C.border}`,
                                        borderRadius: '14px', overflow: 'hidden',
                                        backdropFilter: 'blur(16px)',
                                    }}>
                                        <table style={{ width: '100%', borderCollapse: 'collapse', fontSize: '13px' }}>
                                            <thead>
                                                <tr style={{ borderBottom: `1px solid ${C.border}` }}>
                                                    {['Nom', 'Email', 'Entreprise(s)', 'Rôle plateforme', 'Vérifié', 'Inscrit le'].map(h => (
                                                        <th key={h} style={{
                                                            padding: '12px 16px', textAlign: 'left', fontSize: '10px',
                                                            fontWeight: 600, color: C.textFaint,
                                                            textTransform: 'uppercase', letterSpacing: '0.09em',
                                                        }}>{h}</th>
                                                    ))}
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {filteredUsers.map((u, i) => (
                                                    <motion.tr
                                                        key={u.id} custom={i} variants={fadeUp} initial="hidden" animate="visible"
                                                        className="sa-tr"
                                                        style={{ borderBottom: `1px solid ${C.border}` }}
                                                    >
                                                        <td style={{ padding: '13px 16px' }}>
                                                            <div style={{ fontWeight: 500, color: C.textPrimary }}>{u.name}</div>
                                                        </td>
                                                        <td style={{ padding: '13px 16px' }}>
                                                            <span className="mono" style={{ fontSize: '12px', color: C.textMuted }}>{u.email}</span>
                                                        </td>
                                                        <td style={{ padding: '13px 16px' }}>
                                                            {u.teams.length > 0 ? (
                                                                <div style={{ display: 'flex', flexDirection: 'column', gap: '3px' }}>
                                                                    {u.teams.map(t => (
                                                                        <span key={t.id} style={{ fontSize: '11px', color: C.textMuted }}>
                                                                            {t.name}{t.role ? ` — ${t.role}` : ''}
                                                                        </span>
                                                                    ))}
                                                                </div>
                                                            ) : (
                                                                <span style={{ fontSize: '11px', color: C.textFaint }}>—</span>
                                                            )}
                                                        </td>
                                                        <td style={{ padding: '13px 16px' }}>
                                                            {u.nexora_role === 'super_admin' ? (
                                                                <span style={{
                                                                    display: 'inline-flex', alignItems: 'center', borderRadius: '99px',
                                                                    padding: '2px 9px', fontSize: '10px', letterSpacing: '0.06em',
                                                                    fontWeight: 600, textTransform: 'uppercase',
                                                                    background: C.coralDim, border: `1px solid ${C.coralBorder}`,
                                                                    color: C.coralLight,
                                                                }}>
                                                                    Super Admin
                                                                </span>
                                                            ) : (
                                                                <span style={{ fontSize: '11px', color: C.textFaint }}>—</span>
                                                            )}
                                                        </td>
                                                        <td style={{ padding: '13px 16px' }}>
                                                            {u.email_verified_at ? (
                                                                <span style={{
                                                                    display: 'inline-flex', alignItems: 'center', borderRadius: '99px',
                                                                    padding: '2px 9px', fontSize: '10px', letterSpacing: '0.06em',
                                                                    fontWeight: 600, textTransform: 'uppercase',
                                                                    background: C.greenDim, border: `1px solid ${C.greenBorder}`,
                                                                    color: C.green,
                                                                }}>
                                                                    Vérifié
                                                                </span>
                                                            ) : (
                                                                <span style={{
                                                                    display: 'inline-flex', alignItems: 'center', borderRadius: '99px',
                                                                    padding: '2px 9px', fontSize: '10px', letterSpacing: '0.06em',
                                                                    fontWeight: 600, textTransform: 'uppercase',
                                                                    background: C.coralDim, border: `1px solid ${C.coralBorder}`,
                                                                    color: C.coralLight,
                                                                }}>
                                                                    Non vérifié
                                                                </span>
                                                            )}
                                                        </td>
                                                        <td style={{ padding: '13px 16px' }}>
                                                            <span className="mono" style={{ fontSize: '11px', color: C.textMuted }}>
                                                                {new Date(u.created_at).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' })}
                                                            </span>
                                                        </td>
                                                    </motion.tr>
                                                ))}
                                                {filteredUsers.length === 0 && (
                                                    <tr>
                                                        <td colSpan={6} style={{ textAlign: 'center', padding: '40px 16px', color: C.textFaint, fontSize: '13px' }}>
                                                            {userSearch ? `Aucun résultat pour "${userSearch}"` : 'Aucun utilisateur enregistré'}
                                                        </td>
                                                    </tr>
                                                )}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            )}

                            {/* ── Module Système ── */}
                            {activeModule === 'systeme' && (
                                <div key="systeme" className="sa-section" style={{ display: 'flex', flexDirection: 'column', gap: '24px' }}>

                                    {/* Santé système */}
                                    <div>
                                        {sectionTitle('Santé du système', 'État en temps réel des composants critiques')}
                                        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(240px, 1fr))', gap: '12px' }}>
                                            <HealthCard
                                                label="Base de données"
                                                status={systemHealth?.database?.status ?? 'healthy'}
                                                icon={Database}
                                                detail={systemHealth?.database?.error ?? 'Connexion établie'}
                                            />
                                            <HealthCard
                                                label="Cache"
                                                status={systemHealth?.cache?.status ?? 'healthy'}
                                                icon={Wifi}
                                                detail="Redis / Mémoire"
                                            />
                                            <HealthCard
                                                label="Jobs en attente"
                                                status={(systemHealth?.pending_jobs ?? 0) > 50 ? 'unhealthy' : 'ok'}
                                                icon={Clock}
                                                detail={`${systemHealth?.pending_jobs ?? 0} en attente · ${systemHealth?.failed_jobs ?? 0} échoué${(systemHealth?.failed_jobs ?? 0) !== 1 ? 's' : ''}`}
                                            />
                                            <HealthCard
                                                label="Espace disque libre"
                                                status={(systemHealth?.disk_free_pct ?? 100) < 15 ? 'unhealthy' : 'ok'}
                                                icon={HardDrive}
                                                detail={`${systemHealth?.disk_free_pct ?? '—'}% disponible`}
                                            />
                                        </div>
                                    </div>

                                    {/* Mode maintenance */}
                                    <div style={{
                                        background: C.bgCard, border: `1px solid ${systemHealth?.maintenance_enabled ? C.redBorder : C.border}`,
                                        borderRadius: '14px', padding: '20px 22px',
                                    }}>
                                        {sectionTitle('Mode maintenance', 'Contrôle de la disponibilité de la plateforme')}

                                        {systemHealth?.maintenance_enabled && (
                                            <div style={{
                                                display: 'flex', alignItems: 'center', gap: '10px',
                                                padding: '10px 14px', borderRadius: '10px', marginBottom: '16px',
                                                background: C.redDim, border: `1px solid ${C.redBorder}`,
                                            }}>
                                                <AlertTriangle style={{ height: '14px', width: '14px', color: C.red, flexShrink: 0 }} />
                                                <span style={{ fontSize: '12px', color: C.red, fontWeight: 500 }}>
                                                    La plateforme est actuellement en mode maintenance. Les utilisateurs ne peuvent pas se connecter.
                                                </span>
                                            </div>
                                        )}

                                        <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
                                            <div>
                                                <div style={{ fontSize: '13px', fontWeight: 500, color: C.textPrimary }}>
                                                    {systemHealth?.maintenance_enabled ? 'Maintenance activée' : 'Maintenance désactivée'}
                                                </div>
                                                <div style={{ fontSize: '11px', color: C.textMuted, marginTop: '3px' }}>
                                                    {systemHealth?.maintenance_enabled
                                                        ? 'Désactiver pour rétablir l\'accès utilisateurs'
                                                        : 'Activer pour bloquer temporairement l\'accès'}
                                                </div>
                                            </div>
                                            <button
                                                onClick={() => router.post('/super-admin/system/maintenance')}
                                                className="sa-btn"
                                                style={{
                                                    display: 'flex', alignItems: 'center', gap: '8px',
                                                    height: '36px', padding: '0 16px', borderRadius: '9px', fontSize: '12px', fontWeight: 500,
                                                    background: systemHealth?.maintenance_enabled ? C.greenDim : C.redDim,
                                                    border: `1px solid ${systemHealth?.maintenance_enabled ? C.greenBorder : C.redBorder}`,
                                                    color: systemHealth?.maintenance_enabled ? C.green : C.red,
                                                    cursor: 'pointer',
                                                }}
                                            >
                                                {systemHealth?.maintenance_enabled
                                                    ? <><ToggleRight style={{ height: '14px', width: '14px' }} /> Désactiver</>
                                                    : <><ToggleLeft style={{ height: '14px', width: '14px' }} /> Activer</>
                                                }
                                            </button>
                                        </div>
                                    </div>

                                    {/* Message global */}
                                    <div style={{
                                        background: C.bgCard, border: `1px solid ${C.border}`,
                                        borderRadius: '14px', padding: '20px 22px',
                                    }}>
                                        {sectionTitle('Message global', 'Diffuser un message à tous les tenants de la plateforme')}

                                        <div style={{ display: 'flex', flexDirection: 'column', gap: '12px' }}>
                                            <div style={{ display: 'flex', gap: '12px' }}>
                                                <div style={{ flex: 1 }}>
                                                    <label style={{ fontSize: '11px', fontWeight: 600, color: C.textFaint, textTransform: 'uppercase', letterSpacing: '0.09em', display: 'block', marginBottom: '6px' }}>
                                                        Type
                                                    </label>
                                                    <select
                                                        value={broadcastType}
                                                        onChange={e => setBroadcastType(e.target.value as 'info' | 'warning' | 'critical')}
                                                        className="sa-input"
                                                        style={{
                                                            width: '100%', height: '38px', padding: '0 12px',
                                                            borderRadius: '10px', fontSize: '13px',
                                                            background: C.bgInput, border: `1px solid ${C.border}`,
                                                            cursor: 'pointer',
                                                        }}
                                                    >
                                                        <option value="info">Information</option>
                                                        <option value="warning">Avertissement</option>
                                                        <option value="critical">Critique</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div>
                                                <label style={{ fontSize: '11px', fontWeight: 600, color: C.textFaint, textTransform: 'uppercase', letterSpacing: '0.09em', display: 'block', marginBottom: '6px' }}>
                                                    Message
                                                </label>
                                                <textarea
                                                    value={broadcastMessage}
                                                    onChange={e => setBroadcastMessage(e.target.value)}
                                                    placeholder="Saisissez votre message destiné à tous les tenants…"
                                                    rows={4}
                                                    className="sa-input"
                                                    style={{
                                                        width: '100%', padding: '10px 14px',
                                                        borderRadius: '10px', fontSize: '13px',
                                                        background: C.bgInput, border: `1px solid ${C.border}`,
                                                        resize: 'vertical', lineHeight: '1.5',
                                                        boxSizing: 'border-box',
                                                    }}
                                                />
                                            </div>

                                            <div style={{ display: 'flex', justifyContent: 'flex-end' }}>
                                                <button
                                                    onClick={() => {
                                                        if (!broadcastMessage.trim()) {
return;
}

                                                        router.post('/super-admin/settings/broadcast-message', {
                                                            message: broadcastMessage,
                                                            type: broadcastType,
                                                        });
                                                        setBroadcastMessage('');
                                                    }}
                                                    className="sa-btn"
                                                    style={{
                                                        display: 'flex', alignItems: 'center', gap: '8px',
                                                        height: '36px', padding: '0 18px', borderRadius: '9px', fontSize: '12px', fontWeight: 500,
                                                        background: C.coral, border: 'none',
                                                        color: '#FFFFFF', cursor: 'pointer',
                                                        opacity: broadcastMessage.trim() ? 1 : 0.5,
                                                    }}
                                                    disabled={!broadcastMessage.trim()}
                                                >
                                                    <Send style={{ height: '13px', width: '13px' }} />
                                                    Diffuser à tous les tenants
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            )}

                            {/* ── Module Journal d'audit ── */}
                            {activeModule === 'journal' && (
                                <div key="journal" className="sa-section" style={{ display: 'flex', flexDirection: 'column', gap: '14px' }}>

                                    {/* En-tête */}
                                    <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
                                        <div>
                                            <h2 style={{ fontSize: '16px', fontWeight: 500, color: C.textPrimary, margin: 0, letterSpacing: '-0.01em' }}>
                                                Journal d'audit
                                            </h2>
                                            <p style={{ fontSize: '12px', color: C.textMuted, margin: '3px 0 0' }}>
                                                {recentAuditLogs?.length ?? 0} action{(recentAuditLogs?.length ?? 0) !== 1 ? 's' : ''} enregistrée{(recentAuditLogs?.length ?? 0) !== 1 ? 's' : ''} · 50 dernières
                                            </p>
                                        </div>
                                    </div>

                                    {/* Filtre par action */}
                                    <div style={{ position: 'relative' }}>
                                        <Search style={{
                                            position: 'absolute', left: '12px', top: '50%',
                                            transform: 'translateY(-50%)', height: '14px', width: '14px',
                                            color: C.textFaint,
                                        }} />
                                        <input
                                            className="sa-input"
                                            value={journalFilter}
                                            onChange={e => setJournalFilter(e.target.value)}
                                            placeholder="Filtrer par action…"
                                            style={{
                                                width: '100%', height: '38px', paddingLeft: '38px', paddingRight: '14px',
                                                borderRadius: '10px', fontSize: '13px',
                                                background: C.bgInput,
                                                border: `1px solid ${C.border}`,
                                                boxSizing: 'border-box',
                                            }}
                                        />
                                    </div>

                                    {/* Table */}
                                    <div style={{
                                        background: C.bgCard, border: `1px solid ${C.border}`,
                                        borderRadius: '14px', overflow: 'hidden',
                                        backdropFilter: 'blur(16px)',
                                    }}>
                                        {filteredLogs.length === 0 ? (
                                            <div style={{ textAlign: 'center', padding: '48px 16px', color: C.textFaint, fontSize: '13px' }}>
                                                {journalFilter ? `Aucune action correspondant à "${journalFilter}"` : 'Aucune action enregistrée'}
                                            </div>
                                        ) : (
                                            <table style={{ width: '100%', borderCollapse: 'collapse', fontSize: '13px' }}>
                                                <thead>
                                                    <tr style={{ borderBottom: `1px solid ${C.border}` }}>
                                                        {['Date / Heure', 'Action', 'Effectué par', 'Tenant ciblé', 'IP'].map(h => (
                                                            <th key={h} style={{
                                                                padding: '12px 16px', textAlign: 'left', fontSize: '10px',
                                                                fontWeight: 600, color: C.textFaint,
                                                                textTransform: 'uppercase', letterSpacing: '0.09em',
                                                            }}>{h}</th>
                                                        ))}
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    {filteredLogs.map((log, i) => (
                                                        <motion.tr
                                                            key={log.id} custom={i} variants={fadeUp} initial="hidden" animate="visible"
                                                            className="sa-tr"
                                                            style={{ borderBottom: `1px solid ${C.border}` }}
                                                        >
                                                            <td style={{ padding: '13px 16px' }}>
                                                                <span className="mono" style={{ fontSize: '11px', color: C.textMuted }}>
                                                                    {new Date(log.created_at).toLocaleString('fr-FR', {
                                                                        day: '2-digit', month: 'short',
                                                                        hour: '2-digit', minute: '2-digit',
                                                                    })}
                                                                </span>
                                                            </td>
                                                            <td style={{ padding: '13px 16px' }}>
                                                                <span style={{
                                                                    fontSize: '11px', padding: '2px 8px', borderRadius: '5px',
                                                                    background: C.coralDim, border: `1px solid ${C.coralBorder}`,
                                                                    color: C.coralLight, fontWeight: 500,
                                                                }}>
                                                                    {formatAuditAction(log.action)}
                                                                </span>
                                                            </td>
                                                            <td style={{ padding: '13px 16px' }}>
                                                                <div style={{ fontSize: '13px', fontWeight: 500, color: C.textPrimary }}>{log.super_admin}</div>
                                                            </td>
                                                            <td style={{ padding: '13px 16px' }}>
                                                                {log.target_team ? (
                                                                    <span style={{ fontSize: '12px', color: C.textMuted }}>{log.target_team}</span>
                                                                ) : (
                                                                    <span style={{ fontSize: '12px', color: C.textFaint }}>—</span>
                                                                )}
                                                            </td>
                                                            <td style={{ padding: '13px 16px' }}>
                                                                <span className="mono" style={{ fontSize: '11px', color: C.textFaint }}>
                                                                    {log.ip_address ?? '—'}
                                                                </span>
                                                            </td>
                                                        </motion.tr>
                                                    ))}
                                                </tbody>
                                            </table>
                                        )}
                                    </div>
                                </div>
                            )}

                            {/* ── Module Paramètres ── */}
                            {activeModule === 'parametres' && (
                                <div key="parametres" className="sa-section" style={{ display: 'flex', flexDirection: 'column', gap: '20px' }}>

                                    {/* Section Plateforme */}
                                    <div style={{
                                        background: C.bgCard, border: `1px solid ${C.border}`,
                                        borderRadius: '14px', padding: '20px 22px',
                                    }}>
                                        <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', marginBottom: '18px' }}>
                                            <div>
                                                <h2 style={{ fontSize: '14px', fontWeight: 500, color: C.textPrimary, margin: 0 }}>Plateforme</h2>
                                                <p style={{ fontSize: '12px', color: C.textMuted, margin: '3px 0 0' }}>Informations générales de la plateforme</p>
                                            </div>
                                            <span style={{
                                                display: 'inline-flex', alignItems: 'center', borderRadius: '99px',
                                                padding: '3px 10px', fontSize: '10px', letterSpacing: '0.06em',
                                                fontWeight: 600, textTransform: 'uppercase',
                                                background: C.coralDim, border: `1px solid ${C.coralBorder}`,
                                                color: C.coralLight,
                                            }}>
                                                Bientôt modifiable
                                            </span>
                                        </div>

                                        <div style={{ display: 'flex', flexDirection: 'column', gap: '14px' }}>
                                            <div>
                                                <label style={{ fontSize: '11px', fontWeight: 600, color: C.textFaint, textTransform: 'uppercase', letterSpacing: '0.09em', display: 'block', marginBottom: '6px' }}>
                                                    Nom de la plateforme
                                                </label>
                                                <input
                                                    readOnly
                                                    value="NEXORA"
                                                    className="sa-input"
                                                    style={{
                                                        width: '100%', height: '38px', padding: '0 14px',
                                                        borderRadius: '10px', fontSize: '13px',
                                                        background: C.bgInput, border: `1px solid ${C.border}`,
                                                        boxSizing: 'border-box', cursor: 'not-allowed',
                                                        opacity: 0.7,
                                                    }}
                                                />
                                            </div>
                                            <div>
                                                <label style={{ fontSize: '11px', fontWeight: 600, color: C.textFaint, textTransform: 'uppercase', letterSpacing: '0.09em', display: 'block', marginBottom: '6px' }}>
                                                    Version
                                                </label>
                                                <input
                                                    readOnly
                                                    value="v2.0.0"
                                                    className="sa-input mono"
                                                    style={{
                                                        width: '160px', height: '38px', padding: '0 14px',
                                                        borderRadius: '10px', fontSize: '13px',
                                                        background: C.bgInput, border: `1px solid ${C.border}`,
                                                        boxSizing: 'border-box', cursor: 'not-allowed',
                                                        opacity: 0.7,
                                                    }}
                                                />
                                            </div>
                                        </div>
                                    </div>

                                    {/* Section Inscription */}
                                    <div style={{
                                        background: C.bgCard, border: `1px solid ${C.border}`,
                                        borderRadius: '14px', padding: '20px 22px',
                                    }}>
                                        <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', marginBottom: '16px' }}>
                                            <div>
                                                <h2 style={{ fontSize: '14px', fontWeight: 500, color: C.textPrimary, margin: 0 }}>Inscription</h2>
                                                <p style={{ fontSize: '12px', color: C.textMuted, margin: '3px 0 0' }}>Contrôle de l'accès à la plateforme</p>
                                            </div>
                                            <span style={{
                                                display: 'inline-flex', alignItems: 'center', borderRadius: '99px',
                                                padding: '3px 10px', fontSize: '10px', letterSpacing: '0.06em',
                                                fontWeight: 600, textTransform: 'uppercase',
                                                background: C.coralDim, border: `1px solid ${C.coralBorder}`,
                                                color: C.coralLight,
                                            }}>
                                                Bientôt modifiable
                                            </span>
                                        </div>

                                        <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
                                            <div>
                                                <div style={{ fontSize: '13px', fontWeight: 500, color: C.textPrimary }}>Inscriptions ouvertes</div>
                                                <div style={{ fontSize: '11px', color: C.textMuted, marginTop: '3px' }}>
                                                    Les nouveaux tenants peuvent actuellement s'inscrire
                                                </div>
                                            </div>
                                            <div style={{ display: 'flex', alignItems: 'center', gap: '8px', opacity: 0.6 }}>
                                                <ToggleRight style={{ height: '22px', width: '22px', color: C.green }} />
                                                <span style={{ fontSize: '12px', color: C.green, fontWeight: 500 }}>Ouvertes</span>
                                            </div>
                                        </div>
                                    </div>

                                    {/* Zone Danger */}
                                    <div style={{
                                        background: C.bgCard, border: `1px solid ${C.redBorder}`,
                                        borderRadius: '14px', padding: '20px 22px',
                                    }}>
                                        <div style={{ marginBottom: '16px' }}>
                                            <h2 style={{ fontSize: '14px', fontWeight: 500, color: C.red, margin: 0 }}>Zone de danger</h2>
                                            <p style={{ fontSize: '12px', color: C.textMuted, margin: '3px 0 0' }}>Actions critiques à utiliser avec précaution</p>
                                        </div>

                                        <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
                                            <div>
                                                <div style={{ fontSize: '13px', fontWeight: 500, color: C.textPrimary }}>Mode maintenance</div>
                                                <div style={{ fontSize: '11px', color: C.textMuted, marginTop: '3px' }}>
                                                    {systemHealth?.maintenance_enabled
                                                        ? 'La plateforme est actuellement en maintenance'
                                                        : 'Bloquer temporairement l\'accès à tous les utilisateurs'}
                                                </div>
                                            </div>
                                            <button
                                                onClick={() => router.post('/super-admin/system/maintenance')}
                                                className="sa-btn"
                                                style={{
                                                    display: 'flex', alignItems: 'center', gap: '8px',
                                                    height: '36px', padding: '0 16px', borderRadius: '9px', fontSize: '12px', fontWeight: 500,
                                                    background: systemHealth?.maintenance_enabled ? C.greenDim : C.redDim,
                                                    border: `1px solid ${systemHealth?.maintenance_enabled ? C.greenBorder : C.redBorder}`,
                                                    color: systemHealth?.maintenance_enabled ? C.green : C.red,
                                                    cursor: 'pointer',
                                                }}
                                            >
                                                <Shield style={{ height: '13px', width: '13px' }} />
                                                {systemHealth?.maintenance_enabled ? 'Désactiver' : 'Activer la maintenance'}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            )}

                            {/* ── Module Compte ── */}
                            {activeModule === 'compte' && (
                                <div key="compte" className="sa-section" style={{ display: 'flex', flexDirection: 'column', gap: '20px' }}>

                                    {/* Carte profil */}
                                    <div style={{
                                        background: C.bgCard, border: `1px solid ${C.border}`,
                                        borderRadius: '14px', padding: '24px 22px',
                                    }}>
                                        <div style={{ display: 'flex', alignItems: 'center', gap: '16px', marginBottom: '22px' }}>
                                            <div style={{
                                                height: '52px', width: '52px', borderRadius: '14px',
                                                display: 'flex', alignItems: 'center', justifyContent: 'center',
                                                background: C.coralDim, border: `1px solid ${C.coralBorder}`,
                                                color: C.coralLight, fontSize: '20px', fontWeight: 600,
                                                flexShrink: 0,
                                            }}>
                                                {(authUser?.name ?? userName ?? 'A').charAt(0).toUpperCase()}
                                            </div>
                                            <div>
                                                <div style={{ display: 'flex', alignItems: 'center', gap: '10px' }}>
                                                    <span style={{ fontSize: '16px', fontWeight: 500, color: C.textPrimary }}>
                                                        {authUser?.name ?? userName ?? 'Admin'}
                                                    </span>
                                                    <span style={{
                                                        display: 'inline-flex', alignItems: 'center', borderRadius: '99px',
                                                        padding: '2px 9px', fontSize: '10px', letterSpacing: '0.06em',
                                                        fontWeight: 600, textTransform: 'uppercase',
                                                        background: C.coralDim, border: `1px solid ${C.coralBorder}`,
                                                        color: C.coralLight,
                                                    }}>
                                                        Super Admin
                                                    </span>
                                                </div>
                                                <div style={{ fontSize: '12px', color: C.textMuted, marginTop: '3px' }}>
                                                    {authUser?.email ?? '—'}
                                                </div>
                                            </div>
                                        </div>

                                        <div style={{ display: 'flex', flexDirection: 'column', gap: '14px' }}>
                                            <div>
                                                <label style={{ fontSize: '11px', fontWeight: 600, color: C.textFaint, textTransform: 'uppercase', letterSpacing: '0.09em', display: 'block', marginBottom: '6px' }}>
                                                    Rôle plateforme
                                                </label>
                                                <input
                                                    readOnly
                                                    value="Administrateur global"
                                                    className="sa-input"
                                                    style={{
                                                        width: '100%', height: '38px', padding: '0 14px',
                                                        borderRadius: '10px', fontSize: '13px',
                                                        background: C.bgInput, border: `1px solid ${C.border}`,
                                                        boxSizing: 'border-box', cursor: 'not-allowed',
                                                        opacity: 0.8,
                                                    }}
                                                />
                                            </div>
                                        </div>
                                    </div>

                                    {/* Section Sécurité */}
                                    <div style={{
                                        background: C.bgCard, border: `1px solid ${C.border}`,
                                        borderRadius: '14px', padding: '20px 22px',
                                    }}>
                                        <div style={{ marginBottom: '16px' }}>
                                            <h2 style={{ fontSize: '14px', fontWeight: 500, color: C.textPrimary, margin: 0 }}>Sécurité</h2>
                                            <p style={{ fontSize: '12px', color: C.textMuted, margin: '3px 0 0' }}>Gestion des accès et des identifiants</p>
                                        </div>

                                        <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
                                            <div>
                                                <div style={{ fontSize: '13px', fontWeight: 500, color: C.textPrimary }}>Mot de passe</div>
                                                <div style={{ fontSize: '11px', color: C.textMuted, marginTop: '3px' }}>
                                                    Modifiez votre mot de passe de connexion
                                                </div>
                                            </div>
                                            <a
                                                href="/super-admin/login"
                                                style={{
                                                    display: 'inline-flex', alignItems: 'center', gap: '6px',
                                                    height: '36px', padding: '0 16px', borderRadius: '9px', fontSize: '12px', fontWeight: 500,
                                                    background: C.coralDim, border: `1px solid ${C.coralBorder}`,
                                                    color: C.coralLight, textDecoration: 'none',
                                                    transition: 'filter 0.15s',
                                                }}
                                            >
                                                <Shield style={{ height: '13px', width: '13px' }} />
                                                Modifier le mot de passe
                                            </a>
                                        </div>
                                    </div>

                                    {/* Déconnexion */}
                                    <div style={{
                                        background: C.bgCard, border: `1px solid ${C.border}`,
                                        borderRadius: '14px', padding: '20px 22px',
                                    }}>
                                        <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
                                            <div>
                                                <div style={{ fontSize: '13px', fontWeight: 500, color: C.textPrimary }}>Session active</div>
                                                <div style={{ fontSize: '11px', color: C.textMuted, marginTop: '3px' }}>
                                                    Terminer votre session administrateur
                                                </div>
                                            </div>
                                            <button
                                                onClick={() => router.post('/logout')}
                                                className="sa-btn"
                                                style={{
                                                    display: 'flex', alignItems: 'center', gap: '8px',
                                                    height: '36px', padding: '0 16px', borderRadius: '9px', fontSize: '12px', fontWeight: 500,
                                                    background: C.redDim, border: `1px solid ${C.redBorder}`,
                                                    color: C.red, cursor: 'pointer',
                                                }}
                                            >
                                                <LogOut style={{ height: '13px', width: '13px' }} />
                                                Se déconnecter
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            )}

                        </AnimatePresence>
                    </main>
                </div>
            </div>
        </>
    );
}
