/**
 * Composants réutilisables pour les dashboards drinks
 * KpiCard, QuickLink, et autres composants partagés par les différents rôles
 */
import { Head, Link, usePage } from '@inertiajs/react';
import React from 'react';
import { motion } from 'framer-motion';
import { Button } from '@/components/ui/button';
import { ChevronRight, ArrowUpRight, AlertCircle } from 'lucide-react';
import type { Team } from '@/types';

// ── Types ─────────────────────────────────────────────────────────────────────

export type KpiCardProps = {
    icon: React.ReactNode;
    label: string;
    value: string;
    sub?: string;
    accent?: string;
    alert?: boolean;
    trend?: { value: string; positive: boolean };
};

export type QuickLinkProps = {
    href: string;
    label: string;
    icon: React.ReactNode;
    variant?: 'default' | 'outline' | 'ghost';
    description?: string;
};

export type DashboardProps = {
    stats: Record<string, any>;
};

// ── Formatting helpers ────────────────────────────────────────────────────────

export function formatCurrency(val: number | undefined) {
    if (val === undefined || val === null) return '0 FCFA';
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'XAF',
        maximumFractionDigits: 0,
    }).format(val);
}

export function formatNumber(val: number | undefined) {
    if (val === undefined || val === null) return '0';
    return new Intl.NumberFormat('fr-FR').format(val);
}

// ── KpiCard ───────────────────────────────────────────────────────────────────

export function KpiCard({
    icon,
    label,
    value,
    sub,
    accent = 'text-primary',
    alert = false,
    trend
}: KpiCardProps) {
    return (
        <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            whileHover={{ y: -4 }}
            className={`relative group overflow-hidden rounded-[2.5rem] border bg-white p-8 shadow-sm transition-all duration-300 hover:shadow-xl hover:shadow-slate-200/50 ${alert ? 'border-amber-200 bg-amber-50/30' : 'border-slate-100'}`}
        >
            {/* Background Accent Gradient */}
            <div className={`absolute -right-8 -top-8 w-40 h-40 rounded-full blur-3xl opacity-0 group-hover:opacity-10 transition-opacity duration-500 bg-current ${accent}`} />
            
            <div className="flex flex-col h-full relative z-10">
                <div className="flex items-start justify-between mb-6">
                    <div className={`flex h-14 w-14 items-center justify-center rounded-2xl transition-transform duration-500 group-hover:scale-110 group-hover:rotate-3 shadow-sm ${alert ? 'bg-amber-100 text-amber-600' : 'bg-slate-50 text-slate-600 group-hover:bg-white group-hover:text-amber-500'}`}>
                        {icon}
                    </div>
                    {trend && (
                        <div className={`flex items-center gap-1 px-3 py-1.5 rounded-full text-[11px] font-black uppercase tracking-wider ${trend.positive ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600'}`}>
                            {trend.positive ? '+' : '-'}{trend.value}
                            <ArrowUpRight className={`h-3 w-3 ${!trend.positive && 'rotate-90'}`} />
                        </div>
                    )}
                </div>

                <div className="space-y-2">
                    <p className="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">{label}</p>
                    <div className="flex items-baseline gap-2">
                        <p className={`text-3xl xl:text-4xl font-black tracking-tighter ${accent}`}>{value}</p>
                    </div>
                    {sub && (
                        <p className="text-xs text-slate-500 font-bold flex items-center gap-2 opacity-80 mt-2">
                            {alert && <AlertCircle className="h-3.5 w-3.5 text-amber-500" />}
                            {sub}
                        </p>
                    )}
                </div>
            </div>
            
            {/* Interactive indicator */}
            <div className="absolute bottom-4 right-4 opacity-0 group-hover:opacity-100 transition-all duration-300 translate-x-4 group-hover:translate-x-0">
                <ChevronRight className="h-5 w-5 text-slate-300" />
            </div>
        </motion.div>
    );
}

// ── QuickLink ─────────────────────────────────────────────────────────────────

export function QuickLink({ href, label, icon, variant = 'outline', description }: QuickLinkProps) {
    return (
        <Button 
            asChild 
            variant="ghost" 
            className="group relative h-full w-full p-0 rounded-[2rem] overflow-hidden hover:bg-transparent"
        >
            <Link href={href} className="flex items-center gap-4 px-8 py-5 bg-white border border-slate-100 shadow-sm hover:border-amber-500/30 hover:shadow-xl hover:shadow-amber-500/5 transition-all duration-500 w-full h-full">
                <div className="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-slate-50 text-slate-500 group-hover:bg-amber-500 group-hover:text-white transition-all duration-300 shadow-inner">
                    {icon}
                </div>
                <div className="flex flex-col items-start flex-1">
                    <span className="text-sm font-black text-slate-700 group-hover:text-amber-600 transition-colors uppercase tracking-tight">{label}</span>
                    {description && <span className="text-[10px] text-slate-400 font-bold mt-0.5">{description}</span>}
                </div>
                <ChevronRight className="h-5 w-5 text-slate-300 group-hover:text-amber-500 group-hover:translate-x-1 transition-all" />
            </Link>
        </Button>
    );
}

// ── Role metadata ─────────────────────────────────────────────────────────────

export const ROLE_META: Record<
    string,
    { label: string; badge: string; color: string; gradient: string }
> = {
    admin: {
        label: 'Administrateur',
        badge: 'bg-blue-100 text-blue-700',
        color: 'text-blue-600',
        gradient: 'from-blue-500/10 to-indigo-500/5',
    },
    gerant: {
        label: 'Gérant',
        badge: 'bg-indigo-100 text-indigo-700',
        color: 'text-indigo-600',
        gradient: 'from-indigo-500/10 to-violet-500/5',
    },
    ops: {
        label: 'OPS',
        badge: 'bg-cyan-100 text-cyan-700',
        color: 'text-cyan-600',
        gradient: 'from-cyan-500/10 to-teal-500/5',
    },
    caissier: {
        label: 'Caissier',
        badge: 'bg-green-100 text-green-700',
        color: 'text-green-600',
        gradient: 'from-green-500/10 to-emerald-500/5',
    },
    comptable: {
        label: 'Comptable',
        badge: 'bg-purple-100 text-purple-700',
        color: 'text-violet-600',
        gradient: 'from-purple-500/10 to-violet-500/5',
    },
    magasinier: {
        label: 'Magasinier',
        badge: 'bg-amber-100 text-amber-700',
        color: 'text-amber-600',
        gradient: 'from-amber-500/10 to-orange-500/5',
    },
};

// ── Dashboard header (Deprecated in favor of shell header, kept for compatibility) ──────────────────

export function DashboardHeader({ role, stats }: { role: string | null; stats: Record<string, any> }) {
    return null; // The shell header in DashboardView is now used
}

// ── Section title helper ──────────────────────────────────────────────────────

export function SectionTitle({ children }: { children: string }) {
    return (
        <div className="flex items-center gap-4 mb-6">
            <h3 className="text-xs font-black text-slate-400 uppercase tracking-[0.2em] whitespace-nowrap">
                {children}
            </h3>
            <div className="h-px w-full bg-gradient-to-r from-slate-100 to-transparent" />
        </div>
    );
}

// ── Container variants for animations ─────────────────────────────────────────

export const containerVariants = {
    hidden: { opacity: 0 },
    visible: { opacity: 1, transition: { staggerChildren: 0.1, delayChildren: 0.2 } },
};
