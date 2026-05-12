import { Head, usePage } from '@inertiajs/react';
import { motion } from 'framer-motion';
import {
    Package,
    AlertTriangle,
    PackageCheck,
    TrendingDown,
    ArrowUpDown,
} from 'lucide-react';
import React from 'react';
import type { DashboardProps } from './DashboardComponents';
import {
    KpiCard,
    QuickLink,
    formatNumber,
    SectionTitle,
    containerVariants
} from './DashboardComponents';

export default function DashboardMagasinier({ stats }: DashboardProps) {
    const { currentTeam } = usePage().props as any;
    const slug = currentTeam?.slug ?? '';

    return (
        <>
            <Head title="Tableau de bord Magasinier — Distribution Boissons" />
            <h1 className="sr-only">Tableau de bord Magasinier Distribution Boissons</h1>

            <motion.div
                variants={containerVariants}
                initial="hidden"
                animate="visible"
                className="flex flex-col space-y-8 p-0"
            >
                {/* ── KPI Sections ───────────────────────────── */}
                <div className="space-y-10">
                    {/* section: État des Stocks */}
                    <div className="space-y-4">
                        <SectionTitle>État & Santé des Stocks</SectionTitle>
                        <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                            <KpiCard
                                icon={<Package className="h-5 w-5" />}
                                label="Articles Référencés"
                                value={formatNumber(stats.articles_count)}
                                accent="text-orange-500"
                            />
                            {stats.low_stock_count && stats.low_stock_count > 0 ? (
                                <KpiCard
                                    icon={<AlertTriangle className="h-5 w-5" />}
                                    label="Stock Critique"
                                    value={formatNumber(stats.low_stock_count)}
                                    sub="Articles à surveiller"
                                    accent="text-amber-600"
                                    alert
                                />
                            ) : null}
                            <KpiCard
                                icon={<TrendingDown className="h-5 w-5" />}
                                label="Coulages (mois)"
                                value={formatNumber(stats.losses_month)}
                                sub="Pertes validées"
                                accent="text-red-500"
                            />
                        </div>
                    </div>

                    {/* section: Inventaires & Mouvements */}
                    <div className="space-y-4">
                        <SectionTitle>Opérations de Magasin</SectionTitle>
                        <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                            {stats.inventories_draft && stats.inventories_draft > 0 && (
                                <KpiCard
                                    icon={<PackageCheck className="h-5 w-5" />}
                                    label="Inventaires"
                                    value={formatNumber(stats.inventories_draft)}
                                    sub="Brouillons en attente"
                                    accent="text-amber-600"
                                />
                            )}
                        </div>
                    </div>
                </div>

                {/* ── Quick Actions ────────────────────────────────────────── */}
                <div>
                    <SectionTitle>Actions magasinier</SectionTitle>
                    <div className="flex flex-wrap gap-2">
                        <QuickLink
                            href={route('drinks.inventories.create', { current_team: slug })}
                            label="Lancer un Inventaire"
                            icon={<PackageCheck className="h-4 w-4" />}
                            variant="default"
                        />
                        <QuickLink
                            href={route('drinks.losses.create', { current_team: slug })}
                            label="Déclarer une Perte"
                            icon={<TrendingDown className="h-4 w-4" />}
                        />
                        <QuickLink
                            href={route('drinks.stock-movements.index', { current_team: slug })}
                            label="Historique Mouvements"
                            icon={<ArrowUpDown className="h-4 w-4" />}
                        />
                        <QuickLink
                            href={route('drinks.packagings.index', { current_team: slug })}
                            label="Gestion Emballages"
                            icon={<Package className="h-4 w-4" />}
                        />
                    </div>
                </div>
            </motion.div>
        </>
    );
}

DashboardMagasinier.displayName = 'DashboardMagasinier';
