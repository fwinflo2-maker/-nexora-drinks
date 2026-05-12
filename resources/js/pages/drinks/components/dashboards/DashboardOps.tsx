import { Head, usePage } from '@inertiajs/react';
import { motion } from 'framer-motion';
import {
    Package,
    AlertTriangle,
    Truck,
    Building,
    TrendingDown,
    PackageCheck,
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

export default function DashboardOps({ stats }: DashboardProps) {
    const { currentTeam } = usePage().props as any;
    const slug = currentTeam?.slug ?? '';

    return (
        <>
            <Head title="Tableau de bord OPS — Distribution Boissons" />
            <h1 className="sr-only">Tableau de bord OPS Distribution Boissons</h1>

            <motion.div
                variants={containerVariants}
                initial="hidden"
                animate="visible"
                className="flex flex-col space-y-8 p-0"
            >
                {/* ── KPI Sections ───────────────────────────── */}
                <div className="space-y-10">
                    {/* section: Approvisionnements & Flux */}
                    <div className="space-y-4">
                        <SectionTitle>Gestion des Flux & Approvisionnements</SectionTitle>
                        <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                            <KpiCard
                                icon={<Truck className="h-5 w-5" />}
                                label="Volume Appro."
                                value={formatNumber(stats.procurements_month)}
                                sub="Validés ce mois"
                                accent="text-teal-600"
                            />
                            <KpiCard
                                icon={<TrendingDown className="h-5 w-5" />}
                                label="Pertes (mois)"
                                value={formatNumber(stats.losses_month)}
                                sub="Validées ce mois"
                                accent="text-red-500"
                            />
                        </div>
                    </div>

                    {/* section: Stock & Inventaire */}
                    <div className="space-y-4">
                        <SectionTitle>Contrôle de l'Inventaire</SectionTitle>
                        <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                            <KpiCard
                                icon={<Package className="h-5 w-5" />}
                                label="Articles Catalogue"
                                value={formatNumber(stats.articles_count)}
                                accent="text-orange-500"
                            />
                            {stats.low_stock_count && stats.low_stock_count > 0 ? (
                                <KpiCard
                                    icon={<AlertTriangle className="h-5 w-5" />}
                                    label="Stock Critique"
                                    value={formatNumber(stats.low_stock_count)}
                                    sub="Articles à réapprovisionner"
                                    accent="text-amber-600"
                                    alert
                                />
                            ) : null}
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
                    <SectionTitle>Actions rapides</SectionTitle>
                    <div className="flex flex-wrap gap-2">
                        <QuickLink
                            href={route('drinks.procurements.index', { current_team: slug })}
                            label="Flux Approvisionnements"
                            icon={<Truck className="h-4 w-4" />}
                        />
                        <QuickLink
                            href={route('drinks.suppliers.index', { current_team: slug })}
                            label="Base Fournisseurs"
                            icon={<Building className="h-4 w-4" />}
                        />
                        <QuickLink
                            href={route('drinks.inventories.index', { current_team: slug })}
                            label="Inventaires"
                            icon={<PackageCheck className="h-4 w-4" />}
                        />
                        <QuickLink
                            href={route('drinks.losses.index', { current_team: slug })}
                            label="Saisie Pertes"
                            icon={<TrendingDown className="h-4 w-4" />}
                        />
                    </div>
                </div>
            </motion.div>
        </>
    );
}

DashboardOps.displayName = 'DashboardOps';
