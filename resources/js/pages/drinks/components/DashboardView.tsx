import { Head, Link, usePage } from '@inertiajs/react';
import { motion } from 'framer-motion';
import React, { useMemo } from 'react';
import {
    BarChart3,
    Bot,
    ChevronRight,
    Clock,
    Truck,
    ShoppingCart,
    Package,
    PackageCheck,
    Wallet,
    DollarSign,
    BarChart2,
    TrendingDown,
    TrendingUp,
    AlertTriangle,
    History,
    Box
} from 'lucide-react';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';

import type { Team } from '@/types';
import * as SpecializedDashboards from './dashboards';

// ── Types ─────────────────────────────────────────────────────────────────────

type Stats = {
    sales_count: number;
    sales_total: number;
    date_range: { from: string; to: string };
    // Magasinier / OPS / Admin
    articles_count?: number;
    low_stock_count?: number;
    inventories_draft?: number;
    losses_month?: number;
    // Caissier / Admin / Gérant
    payments_total?: number;
    cash_inputs_total?: number;
    // Comptable / Admin
    expenses_total?: number;
    cash_deposits_total?: number;
    // OPS / Admin / Gérant
    procurements_month?: number;
    // Chart data
    chart_data: Array<{ name: string; total: number }>;
    expense_data: Array<{ name: string; value: number }>;
};



type Props = { stats: Stats; role: string | null };

// ── Helpers ────────────────────────────────────────────────────────────────────

function formatCurrency(val: number = 0) {
    if (val === undefined || val === null) {
return '0 FCFA';
}

    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'XAF',
        maximumFractionDigits: 0,
    }).format(val);
}

function formatNumber(val: number = 0) {
    if (val === undefined || val === null) {
return '0';
}

    return new Intl.NumberFormat('fr-FR').format(val);
}

// ── Role configuration ─────────────────────────────────────────────────────────

const ROLE_META: Record<string, { label: string; icon: React.ReactNode }> = {
    admin: { label: 'Administrateur', icon: <Box className="h-5 w-5 text-blue-400" /> },
    gerant: { label: 'Gérant', icon: <BarChart3 className="h-5 w-5 text-indigo-400" /> },
    ops: { label: 'OPS', icon: <Truck className="h-5 w-5 text-cyan-400" /> },
    caissier: { label: 'Caissier', icon: <Wallet className="h-5 w-5 text-emerald-400" /> },
    comptable: { label: 'Comptable', icon: <DollarSign className="h-5 w-5 text-violet-400" /> },
    magasinier: { label: 'Magasinier', icon: <Package className="h-5 w-5 text-amber-400" /> },
};

// ── Sub-components ─────────────────────────────────────────────────────────────

type KpiCardProps = {
    icon: React.ReactNode;
    label: string;
    value: string;
    sub?: string;
    alertText?: string;
};

function KpiCard({ icon, label, value, sub, alertText }: KpiCardProps) {
    return (
        <motion.div
            variants={{
                hidden: { opacity: 0, y: 20 },
                show: { opacity: 1, y: 0 }
            }}
            whileHover={{ y: -6, boxShadow: "0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1)" }}
            className="bg-card border border-border rounded-xl p-5 hover:border-amber-500/50 transition-all shadow-sm relative overflow-hidden group"
        >
            <div className="absolute top-0 right-0 p-2 opacity-5 group-hover:opacity-10 transition-opacity">
                <div className="w-16 h-16 bg-amber-500 rounded-full blur-2xl" />
            </div>
            <div className="flex items-center justify-between mb-3 relative z-10">
                <span className="text-xs font-medium text-muted-foreground uppercase tracking-wider">{label}</span>
                <div className="w-8 h-8 bg-muted rounded-lg flex items-center justify-center text-muted-foreground group-hover:bg-amber-500/10 group-hover:text-amber-500 transition-colors">
                    {icon}
                </div>
            </div>
            <motion.p
                initial={{ scale: 0.9 }}
                animate={{ scale: 1 }}
                className="text-2xl font-bold text-foreground tabular-nums relative z-10"
            >
                {value}
            </motion.p>
            {sub && <p className="text-xs text-muted-foreground mt-1 relative z-10">{sub}</p>}
            {alertText && (
                <div className="mt-2 flex items-center gap-1 text-xs text-amber-400 font-medium relative z-10">
                    <AlertTriangle className="h-3.5 w-3.5" />
                    {alertText}
                </div>
            )}
        </motion.div>
    );
}

type QuickLinkProps = { href: string; label: string; icon: React.ReactNode };
function QuickLink({ href, label, icon }: QuickLinkProps) {
    return (
        <Link
            href={href}
            className="flex items-center gap-3 px-4 py-3 bg-card border border-border hover:border-amber-500/50 hover:bg-amber-500/5 rounded-xl text-sm font-medium text-muted-foreground hover:text-foreground transition-all duration-300 group shadow-sm hover:shadow-md"
        >
            <motion.div
                whileHover={{ rotate: 360, scale: 1.2 }}
                transition={{ duration: 0.5 }}
                className="text-muted-foreground group-hover:text-amber-500 transition-colors"
            >
                {icon}
            </motion.div>
            {label}
        </Link>
    );
}

// ── Operational Widgets ──────────────────────────────────────────────────────

function ActivityFeed({ sales = [] }: { sales: any[] }) {
    return (
        <div className="bg-white border border-slate-100 rounded-[2.5rem] p-8 shadow-sm hover:shadow-xl hover:shadow-slate-200/40 transition-all duration-500 overflow-hidden relative group h-full">
             <div className="absolute top-0 right-0 w-64 h-64 bg-blue-500/5 rounded-full blur-3xl -mr-32 -mt-32" />
             
             <div className="flex items-center justify-between mb-8 relative z-10">
                <div>
                    <h4 className="text-sm font-black flex items-center gap-2 uppercase tracking-wider text-slate-400">
                        <History className="h-4 w-4 text-blue-500" />
                        Dernières Ventes
                    </h4>
                    <p className="text-lg font-black text-slate-800 mt-1">Flux d'activité récent</p>
                </div>
                <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-50 text-slate-400 group-hover:bg-blue-500 group-hover:text-white transition-all duration-300">
                    <TrendingUp className="h-5 w-5" />
                </div>
            </div>

            <div className="space-y-4 relative z-10">
                {sales.length === 0 ? (
                    <div className="py-12 text-center">
                        <p className="text-sm text-slate-400 italic">Aucune vente récente enregistrée</p>
                    </div>
                ) : (
                    sales.map((sale: any, i: number) => (
                        <motion.div 
                            key={sale.id}
                            initial={{ opacity: 0, y: 10 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ delay: i * 0.1 }}
                            className="flex items-center justify-between p-4 rounded-2xl bg-slate-50/50 border border-slate-100/50 hover:bg-white hover:border-blue-200 hover:shadow-md transition-all duration-300 group/item"
                        >
                            <div className="flex items-center gap-4">
                                <div className="h-10 w-10 rounded-xl bg-white border border-slate-100 flex items-center justify-center text-blue-500 shadow-sm group-hover/item:bg-blue-500 group-hover/item:text-white transition-all">
                                    <ShoppingCart className="h-5 w-5" />
                                </div>
                                <div>
                                    <p className="text-xs font-black text-slate-800 uppercase tracking-tight">{sale.client?.name || 'Client de passage'}</p>
                                    <p className="text-[10px] text-slate-400 font-bold">{new Date(sale.document_date).toLocaleDateString()}</p>
                                </div>
                            </div>
                            <div className="text-right">
                                <p className="text-sm font-black text-slate-900">{new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XAF', maximumFractionDigits: 0 }).format(sale.total_ttc)}</p>
                                <span className="text-[9px] px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-600 font-black uppercase tracking-widest">Validé</span>
                            </div>
                        </motion.div>
                    ))
                )}
            </div>
            
            <Link 
                href={sales.length > 0 ? route('drinks.sales.index', { current_team: sales[0].team_id }) : '#'} 
                className="mt-8 flex items-center justify-center gap-2 text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-blue-600 transition-colors group/link"
            >
                Voir tout l'historique
                <ChevronRight className="h-3 w-3 group-hover/link:translate-x-1 transition-transform" />
            </Link>
        </div>
    );
}

function StockAlertWidget({ articles = [] }: { articles: any[] }) {
    return (
        <div className="bg-white border border-slate-100 rounded-[2.5rem] p-8 shadow-sm hover:shadow-xl hover:shadow-rose-200/20 transition-all duration-500 overflow-hidden relative group h-full">
            <div className="absolute bottom-0 left-0 w-64 h-64 bg-rose-500/5 rounded-full blur-3xl -ml-32 -mb-32" />

            <div className="flex items-center justify-between mb-8 relative z-10">
                <div>
                    <h4 className="text-sm font-black flex items-center gap-2 uppercase tracking-wider text-slate-400">
                        <Package className="h-4 w-4 text-rose-500" />
                        Alerte Ruptures
                    </h4>
                    <p className="text-lg font-black text-slate-800 mt-1">Stocks à surveiller</p>
                </div>
                <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-50 text-slate-400 group-hover:bg-rose-500 group-hover:text-white transition-all duration-300">
                    <AlertTriangle className="h-5 w-5" />
                </div>
            </div>

            <div className="space-y-4 relative z-10">
                {articles.length === 0 ? (
                    <div className="py-12 text-center">
                        <p className="text-sm text-slate-400 italic">Tous les stocks sont optimaux</p>
                    </div>
                ) : (
                    articles.map((article: any, i: number) => (
                        <div 
                            key={article.id}
                            className="flex items-center justify-between p-4 rounded-2xl bg-rose-50/30 border border-rose-100/50 hover:bg-white transition-all duration-300"
                        >
                            <div className="flex items-center gap-4">
                                <div className="h-10 w-10 rounded-xl bg-white border border-rose-100 flex items-center justify-center text-rose-500 shadow-sm">
                                    <Box className="h-5 w-5" />
                                </div>
                                <div>
                                    <p className="text-xs font-black text-slate-800 uppercase tracking-tight truncate max-w-[120px]">{article.name}</p>
                                    <p className="text-[10px] text-rose-600 font-bold uppercase tracking-widest">{article.sku}</p>
                                </div>
                            </div>
                            <div className="text-right">
                                <p className="text-sm font-black text-rose-600">{article.stock_qty}</p>
                                <p className="text-[9px] text-slate-400 font-bold uppercase">Restants</p>
                            </div>
                        </div>
                    ))
                )}
            </div>

            <Link 
                href={articles.length > 0 ? route('drinks.articles.index', { current_team: articles[0].team_id }) : '#'} 
                className="mt-8 flex items-center justify-center gap-2 text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-rose-600 transition-colors group/link"
            >
                Gérer le catalogue
                <ChevronRight className="h-3 w-3 group-hover/link:translate-x-1 transition-transform" />
            </Link>
        </div>
    );
}

function SectionTitle({ children }: { children: React.ReactNode }) {
    return (
        <h3 className="text-[10px] font-bold text-muted-foreground uppercase tracking-[0.2em] flex items-center gap-2 mb-6 px-2">
            <div className="h-1.5 w-1.5 rounded-full bg-amber-500" />
            {children}
        </h3>
    );
}

// ── Main Component ─────────────────────────────────────────────────────────────

export default function DrinksDashboard({ stats, role }: Props) {
    const { currentTeam, auth } = usePage().props as any;
    const team = currentTeam as Team & { logo_url?: string | null };

    if (!stats || !stats.date_range) {
        return (
            <div className="flex flex-col items-center justify-center h-64 space-y-4">
                <Box className="h-12 w-12 text-amber-500 animate-bounce" />
                <p className="text-sm text-muted-foreground font-medium italic">Préparation de votre cockpit Nexora...</p>
            </div>
        );
    }

    const slug = team?.slug;
    const t = (name: string) => route(name, { current_team: slug });

    const roleKey = role?.toLowerCase() ?? 'member';
    const meta = ROLE_META[roleKey] ?? ROLE_META['admin'];

    const dateRange = stats.date_range ? `${stats.date_range.from} → ${stats.date_range.to}` : '';
    const userName: string = auth?.user?.name ?? 'Utilisateur';

    // Greeting based on time of day
    const hour = new Date().getHours();
    const greeting = hour < 12 ? 'Bonjour' : hour < 18 ? 'Bon après-midi' : 'Bonsoir';

    return (
        <>
            <Head title="Cockpit Opérationnel | Nexora Drinks" />
            <h1 className="sr-only">Tableau de bord Distribution Boissons</h1>

            <div className="flex flex-1 flex-col space-y-8 pt-6 px-8 pb-12 bg-slate-50/30 min-h-full">
                {/* ── Header Hero Section ──────────────────────────────────────────────────── */}
                <div className="relative overflow-hidden bg-white border border-slate-100 rounded-[2.5rem] p-10 shadow-sm group">
                    {/* Abstract background decorations */}
                    <div className="absolute top-0 right-0 -mr-24 -mt-24 w-80 h-80 bg-amber-500/5 rounded-full blur-[100px] group-hover:bg-amber-500/10 transition-all duration-1000" />
                    <div className="absolute bottom-0 left-0 -ml-24 -mb-24 w-64 h-64 bg-indigo-500/5 rounded-full blur-[80px] group-hover:bg-indigo-500/10 transition-all duration-1000" />
                    
                    <div className="flex flex-col md:flex-row md:items-center gap-8 relative z-10">
                        {/* Team Identity */}
                        <div className="flex items-center gap-6">
                            {team?.logo_url ? (
                                <div className="h-24 w-24 rounded-[2rem] border-4 border-white shadow-2xl overflow-hidden bg-white p-2 group-hover:scale-105 transition-transform duration-500">
                                    <img src={team.logo_url} alt={team?.name} className="h-full w-full object-contain" />
                                </div>
                            ) : (
                                <div className="h-24 w-24 bg-gradient-to-br from-amber-400 to-orange-600 rounded-[2rem] flex items-center justify-center shadow-2xl shadow-amber-500/20 group-hover:scale-105 transition-transform duration-500 ring-8 ring-amber-50">
                                    <span className="text-white font-black text-4xl">{team?.name?.[0] ?? '?'}</span>
                                </div>
                            )}
                            
                            <div className="flex flex-col">
                                <div className="flex items-center gap-3 mb-2">
                                    <div className="flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-900 text-[9px] font-black text-white uppercase tracking-[0.2em]">
                                        <div className="h-1.5 w-1.5 rounded-full bg-amber-500" />
                                        {team?.name}
                                    </div>
                                    <div className="px-3 py-1 rounded-full bg-amber-100 text-[9px] font-black text-amber-700 uppercase tracking-[0.2em]">
                                        {meta.label}
                                    </div>
                                </div>
                                <h2 className="text-4xl font-black text-slate-900 tracking-tight sm:text-5xl leading-tight">
                                    {greeting}, <span className="text-transparent bg-clip-text bg-gradient-to-r from-amber-500 to-orange-600">{userName.split(' ')[0]}</span>
                                </h2>
                                <p className="text-sm text-slate-400 font-bold flex items-center gap-2 mt-2 opacity-80 uppercase tracking-widest">
                                    <Clock className="h-4 w-4 text-amber-500" />
                                    Cockpit Nexora • <span className="text-slate-900">{dateRange}</span>
                                </p>
                            </div>
                        </div>
                        
                        <div className="md:ml-auto flex items-center gap-8 bg-slate-50/50 p-6 rounded-[2rem] border border-slate-100/50 backdrop-blur-sm">
                            <div className="hidden lg:flex flex-col items-end">
                                <span className="text-sm font-black text-slate-900">{userName}</span>
                                <div className="flex items-center gap-2 text-[10px] text-emerald-600 font-black uppercase tracking-[0.2em] mt-1.5">
                                    <div className="h-2 w-2 rounded-full bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.5)] animate-pulse" />
                                    Terminal Actif
                                </div>
                            </div>
                            <div className="relative">
                                <Avatar className="h-20 w-20 rounded-[1.8rem] border-4 border-white shadow-2xl ring-1 ring-slate-200 overflow-hidden group-hover:rotate-3 transition-transform duration-500">
                                    <AvatarImage src={auth?.user?.avatar_url} alt={userName} className="object-cover" />
                                    <AvatarFallback className="bg-gradient-to-br from-slate-800 to-slate-950 text-white font-black text-xl">
                                        {userName.split(' ').map((n: any) => n[0]).join('').toUpperCase().slice(0, 2)}
                                    </AvatarFallback>
                                </Avatar>
                                <div className="absolute -bottom-1 -right-1 h-6 w-6 bg-emerald-500 border-4 border-white rounded-full shadow-lg" />
                            </div>
                        </div>
                    </div>
                </div>

                <div className="grid grid-cols-1 xl:grid-cols-4 gap-6">
                    {/* ── MAIN CONTENT AREA (3/4 on large screens) ────────── */}
                    <div className="xl:col-span-3 space-y-6">
                        {/* ── Specialized Content (KPIs & Role Actions) ────────────────── */}
                        <div className="w-full">
                            {roleKey === 'admin' && <SpecializedDashboards.DashboardAdmin stats={stats} />}
                            {roleKey === 'gerant' && <SpecializedDashboards.DashboardGerant stats={stats} />}
                            {roleKey === 'ops' && <SpecializedDashboards.DashboardOps stats={stats} />}
                            {roleKey === 'caissier' && <SpecializedDashboards.DashboardCaissier stats={stats} />}
                            {roleKey === 'comptable' && <SpecializedDashboards.DashboardComptable stats={stats} />}
                            {roleKey === 'magasinier' && <SpecializedDashboards.DashboardMagasinier stats={stats} />}
                            
                            {!['admin', 'gerant', 'ops', 'caissier', 'comptable', 'magasinier'].includes(roleKey) && (
                                <div className="p-12 text-center bg-card border border-dashed rounded-3xl">
                                    <Bot className="h-12 w-12 text-muted-foreground/30 mx-auto mb-4" />
                                    <h3 className="text-lg font-bold">Bienvenue sur Nexora</h3>
                                    <p className="text-sm text-muted-foreground">Votre rôle ({roleKey}) n'a pas encore de dashboard spécifique configuré.</p>
                                </div>
                            )}
                        </div>

                        {/* ── Real-time Activity & Stock Alerts (Admin/Gerant) ────────── */}
                        {(roleKey === 'admin' || roleKey === 'gerant') && (
                            <div className="grid gap-8 lg:grid-cols-2 pt-4">
                                <ActivityFeed sales={stats.recent_sales} />
                                <StockAlertWidget articles={stats.low_stock_articles} />
                            </div>
                        )}


                    </div>

                    {/* ── SIDEBAR / UTILITY AREA (1/4 on large screens) ──────────── */}
                    <div className="xl:col-span-1 space-y-6">
                        {/* Guide Assistant - Promoted to top of sidebar */}
                        <div className="space-y-6">
                            <h3 className="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] flex items-center gap-3 px-2">
                                <Bot className="h-4 w-4 text-amber-500" />
                                Assistant Intelligence
                                <div className="h-px w-full bg-slate-100" />
                            </h3>
                            <div className="rounded-[2.5rem] border border-slate-100 bg-white p-8 shadow-sm flex flex-col relative overflow-hidden group hover:shadow-xl hover:shadow-amber-500/5 transition-all duration-500">
                                {/* Decorative elements */}
                                <div className="absolute -right-8 -top-8 w-32 h-32 bg-amber-500/10 rounded-full blur-3xl group-hover:bg-amber-500/20 transition-all duration-700" />
                                <div className="absolute -left-8 -bottom-8 w-24 h-24 bg-indigo-500/5 rounded-full blur-2xl group-hover:bg-indigo-500/10 transition-all duration-700" />
                                
                                <div className="flex items-center gap-4 mb-8 relative z-10">
                                    <div className="w-14 h-14 rounded-2xl bg-gradient-to-br from-amber-400 to-orange-500 shadow-lg shadow-amber-500/30 flex items-center justify-center text-white ring-4 ring-amber-50">
                                        <Bot className="h-7 w-7 animate-pulse" />
                                    </div>
                                    <div>
                                        <h4 className="text-base font-black text-slate-800 tracking-tight">Conseils IA</h4>
                                        <div className="flex items-center gap-1.5">
                                            <div className="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse" />
                                            <p className="text-[9px] text-emerald-600 font-black uppercase tracking-widest">Analyse en direct</p>
                                        </div>
                                    </div>
                                </div>
                                <div className="space-y-5 relative z-10">
                                    {[
                                        { text: "Optimisez vos <strong>réapprovisionnements</strong> basés sur les alertes de stock critique." },
                                        { text: "La <strong>répartition des charges</strong> montre une hausse des frais logistiques ce mois." },
                                        { text: "Utilisez le <strong>rapport de brouillard</strong> pour valider la cohérence de caisse." }
                                    ].map((item, i) => (
                                        <motion.div 
                                            key={i} 
                                            initial={{ opacity: 0, x: 20 }}
                                            animate={{ opacity: 1, x: 0 }}
                                            transition={{ delay: 0.3 + (i * 0.1) }}
                                            className="flex gap-4 group/item"
                                        >
                                            <div className="h-2 w-2 rounded-full bg-amber-500 mt-2 flex-shrink-0 shadow-[0_0_10px_rgba(245,158,11,0.6)] group-hover/item:scale-150 transition-transform" />
                                            <p className="text-[11px] text-slate-500 leading-relaxed font-medium" dangerouslySetInnerHTML={{ __html: item.text }} />
                                        </motion.div>
                                    ))}
                                </div>
                                <Button className="mt-8 w-full py-7 rounded-2xl bg-slate-900 hover:bg-black text-white font-black text-[10px] uppercase tracking-[0.2em] shadow-xl shadow-slate-200 group transition-all duration-300">
                                    <Bot className="mr-2 h-4 w-4 group-hover:rotate-12 transition-transform" />
                                    Consulter l'IA Nexora
                                </Button>
                            </div>
                        </div>

                        {/* Quick Insight Card (Optional placeholder for more visual elements) */}
                        <div className="rounded-3xl border border-indigo-500/10 bg-indigo-500/5 p-6 relative overflow-hidden hidden xl:block">
                            <div className="absolute -left-10 -bottom-10 w-32 h-32 bg-indigo-500/10 rounded-full blur-3xl" />
                            <div className="relative z-10">
                                <p className="text-[10px] font-bold text-indigo-600 uppercase tracking-widest mb-2">Statut Opérationnel</p>
                                <p className="text-xs text-indigo-900/70 leading-relaxed mb-4">
                                    Tous les services de distribution sont opérationnels. Vos données sont synchronisées en temps réel.
                                </p>
                                <div className="flex items-center gap-2">
                                    <div className="h-2 w-2 rounded-full bg-emerald-500 animate-pulse" />
                                    <span className="text-[10px] font-bold text-emerald-600 uppercase tracking-widest">Connecté</span>
                                </div>
                            </div>
                        </div>
                </div>
                </div>
            </div>
        </>
    );
}
