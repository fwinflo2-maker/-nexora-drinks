import { Shield, Check, Info, ChevronDown, ChevronRight } from 'lucide-react';
import React, { useState } from 'react';

const ROLES = [
    { key: 'admin', label: 'Admin', short: 'ADM', dot: 'bg-blue-500', badge: 'bg-blue-500/10 text-blue-400 ring-1 ring-blue-500/20' },
    { key: 'gerant', label: 'Gérant', short: 'GER', dot: 'bg-indigo-500', badge: 'bg-indigo-500/10 text-indigo-400 ring-1 ring-indigo-500/20' },
    { key: 'ops', label: 'OPS', short: 'OPS', dot: 'bg-cyan-500', badge: 'bg-cyan-500/10 text-cyan-400 ring-1 ring-cyan-500/20' },
    { key: 'caissier', label: 'Caissier', short: 'CAI', dot: 'bg-green-500', badge: 'bg-green-500/10 text-green-400 ring-1 ring-green-500/20' },
    { key: 'comptable', label: 'Comptable', short: 'CPT', dot: 'bg-purple-500', badge: 'bg-purple-500/10 text-purple-400 ring-1 ring-purple-500/20' },
    { key: 'magasinier', label: 'Magasinier', short: 'MAG', dot: 'bg-amber-500', badge: 'bg-amber-500/10 text-amber-400 ring-1 ring-amber-500/20' },
] as const;

type RoleKey = (typeof ROLES)[number]['key'];

const MODULE_GROUPS = [
    {
        title: 'Catalogue & Tiers',
        modules: [
            { label: 'Articles & Tarifs', roles: ['admin', 'gerant'] as RoleKey[] },
            { label: 'Familles', roles: ['admin'] as RoleKey[] },
            { label: 'Catégories', roles: ['admin'] as RoleKey[] },
            { label: 'Grilles Tarifaires', roles: ['admin'] as RoleKey[] },
            { label: 'Emballages & Consignes', roles: ['admin', 'caissier', 'magasinier'] as RoleKey[] },
            { label: 'Clients', roles: ['admin', 'caissier'] as RoleKey[] },
            { label: 'Fournisseurs', roles: ['admin', 'ops'] as RoleKey[] },
        ],
    },
    {
        title: 'Opérations',
        modules: [
            { label: 'Approvisionnements', roles: ['admin', 'gerant', 'ops'] as RoleKey[] },
            { label: 'Ventes', roles: ['admin', 'gerant', 'caissier'] as RoleKey[] },
        ],
    },
    {
        title: 'Stock',
        modules: [
            { label: 'Inventaires', roles: ['admin', 'gerant', 'ops', 'magasinier'] as RoleKey[] },
            { label: 'Pertes', roles: ['admin', 'gerant', 'ops', 'magasinier'] as RoleKey[] },
            { label: 'Mouvements de stock', roles: ['admin', 'gerant', 'magasinier'] as RoleKey[] },
            { label: 'Snapshots stock', roles: ['admin', 'gerant', 'ops', 'magasinier'] as RoleKey[] },
        ],
    },
    {
        title: 'Finance & Caisse',
        modules: [
            { label: 'Règlements Clients', roles: ['admin', 'caissier', 'comptable'] as RoleKey[] },
            { label: 'Charges', roles: ['admin', 'comptable'] as RoleKey[] },
            { label: 'Types de Charges', roles: ['admin', 'comptable'] as RoleKey[] },
            { label: 'Apports de Fonds', roles: ['admin', 'comptable'] as RoleKey[] },
            { label: 'Versements Banque', roles: ['admin', 'comptable'] as RoleKey[] },
        ],
    },
    {
        title: 'Analytique',
        modules: [
            { label: 'Rapports', roles: ['admin', 'gerant', 'comptable'] as RoleKey[] },
            { label: 'Tableau de bord', roles: ['admin', 'gerant', 'ops', 'caissier', 'comptable', 'magasinier'] as RoleKey[] },
        ],
    },
];

function RoleCell({ hasAccess, roleKey }: { hasAccess: boolean; roleKey: RoleKey }) {
    const role = ROLES.find(r => r.key === roleKey)!;

    return (
        <td className="px-3 py-2.5 text-center">
            {hasAccess ? (
                <div className="flex items-center justify-center">
                    <div className={`w-5 h-5 rounded-full ${role.dot}/15 flex items-center justify-center`}>
                        <Check className={`h-3 w-3 ${role.dot.replace('bg-', 'text-')}`} strokeWidth={2.5} />
                    </div>
                </div>
            ) : (
                <span className="text-border text-base leading-none">·</span>
            )}
        </td>
    );
}

export default function RolesAccessView() {
    const [collapsed, setCollapsed] = useState<Record<string, boolean>>({});

    const toggleGroup = (title: string) => {
        setCollapsed(prev => ({ ...prev, [title]: !prev[title] }));
    };

    const totalModules = MODULE_GROUPS.reduce((acc, g) => acc + g.modules.length, 0);

    return (
        <div className="p-6 space-y-6">
            {/* Header */}
            <div className="flex items-start justify-between">
                <div>
                    <div className="flex items-center gap-2 mb-1">
                        <Shield className="h-4 w-4 text-amber-500" />
                        <h2 className="text-base font-semibold text-foreground">Rôles et accès modulaires</h2>
                    </div>
                    <p className="text-xs text-muted-foreground">
                        {ROLES.length} rôles · {totalModules} modules · Distribution Boissons
                    </p>
                </div>
                <div className="flex items-center gap-1.5 rounded-lg bg-amber-500/5 border border-amber-500/15 px-3 py-1.5">
                    <Info className="h-3 w-3 text-amber-500 flex-shrink-0" />
                    <span className="text-xs text-amber-600 dark:text-amber-400">
                        Les admins ont accès à tous les modules
                    </span>
                </div>
            </div>

            {/* Role legend */}
            <div className="flex flex-wrap gap-2">
                {ROLES.map(role => (
                    <span key={role.key} className={`inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium ${role.badge}`}>
                        <span className={`w-1.5 h-1.5 rounded-full ${role.dot}`} />
                        {role.label}
                    </span>
                ))}
            </div>

            {/* Permission matrix */}
            <div className="rounded-xl border border-border overflow-hidden bg-card">
                <div className="overflow-x-auto">
                    <table className="w-full text-sm min-w-[640px]">
                        <thead>
                            <tr className="border-b border-border bg-muted/30">
                                <th className="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-muted-foreground w-48">
                                    Module
                                </th>
                                {ROLES.map(role => (
                                    <th key={role.key} className="px-3 py-3 text-center min-w-[72px]">
                                        <div className="flex flex-col items-center gap-1">
                                            <span className={`w-2 h-2 rounded-full ${role.dot}`} />
                                            <span className="text-[10px] font-semibold uppercase tracking-wider text-muted-foreground">
                                                {role.short}
                                            </span>
                                        </div>
                                    </th>
                                ))}
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-border/40">
                            {MODULE_GROUPS.map(group => (
                                <React.Fragment key={group.title}>
                                    {/* Group header */}
                                    <tr
                                        className="bg-muted/20 cursor-pointer hover:bg-muted/30 transition-colors"
                                        onClick={() => toggleGroup(group.title)}
                                    >
                                        <td
                                            colSpan={ROLES.length + 1}
                                            className="px-4 py-2"
                                        >
                                            <div className="flex items-center gap-2">
                                                {collapsed[group.title]
                                                    ? <ChevronRight className="h-3 w-3 text-muted-foreground" />
                                                    : <ChevronDown className="h-3 w-3 text-muted-foreground" />
                                                }
                                                <span className="text-[10px] font-bold uppercase tracking-widest text-muted-foreground">
                                                    {group.title}
                                                </span>
                                                <span className="text-[10px] text-muted-foreground/60">
                                                    ({group.modules.length})
                                                </span>
                                            </div>
                                        </td>
                                    </tr>

                                    {/* Module rows */}
                                    {!collapsed[group.title] && group.modules.map(mod => (
                                        <tr key={mod.label} className="hover:bg-muted/20 transition-colors group">
                                            <td className="px-4 py-2.5 text-sm text-foreground font-medium pl-8">
                                                {mod.label}
                                            </td>
                                            {ROLES.map(role => (
                                                <RoleCell
                                                    key={role.key}
                                                    hasAccess={mod.roles.includes(role.key)}
                                                    roleKey={role.key}
                                                />
                                            ))}
                                        </tr>
                                    ))}
                                </React.Fragment>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>

            {/* Coverage stats */}
            <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                {ROLES.map(role => {
                    const accessCount = MODULE_GROUPS.reduce(
                        (acc, g) => acc + g.modules.filter(m => m.roles.includes(role.key)).length,
                        0
                    );
                    const pct = Math.round((accessCount / totalModules) * 100);

                    return (
                        <div key={role.key} className="rounded-lg border border-border bg-card/50 p-3">
                            <div className="flex items-center gap-1.5 mb-2">
                                <span className={`w-2 h-2 rounded-full ${role.dot}`} />
                                <span className="text-xs font-medium text-foreground">{role.label}</span>
                            </div>
                            <div className="flex items-end justify-between mb-1.5">
                                <span className="text-lg font-bold text-foreground">{accessCount}</span>
                                <span className="text-[10px] text-muted-foreground">{pct}%</span>
                            </div>
                            <div className="h-1 bg-muted rounded-full overflow-hidden">
                                <div
                                    className={`h-full ${role.dot} rounded-full transition-all`}
                                    style={{ width: `${pct}%` }}
                                />
                            </div>
                            <p className="text-[10px] text-muted-foreground mt-1">modules accessibles</p>
                        </div>
                    );
                })}
            </div>
        </div>
    );
}
