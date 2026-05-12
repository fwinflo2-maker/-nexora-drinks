import { Head, usePage } from '@inertiajs/react';
import { motion } from 'framer-motion';
import {
    Package,
    ShoppingCart,
    TrendingUp,
    Wallet,
    Receipt,
    DollarSign,
    BarChart2,
    AlertTriangle,
    PackageCheck,
    TrendingDown,
    Truck,
} from 'lucide-react';
import React from 'react';
import type { DashboardProps } from './DashboardComponents';
import {
    KpiCard,
    QuickLink,
    formatCurrency,
    formatNumber,
    SectionTitle,
    containerVariants
} from './DashboardComponents';

export default function DashboardGerant({ stats }: DashboardProps) {
    const { currentTeam } = usePage().props as any;
    const slug = currentTeam?.slug ?? '';

    return (
        <>
            <Head title="Tableau de bord Gérant — Distribution Boissons" />
            <h1 className="sr-only">Tableau de bord Gérant Distribution Boissons</h1>

            <motion.div
                variants={containerVariants}
                initial="hidden"
                animate="visible"
                className="flex flex-col space-y-8 p-0"
            >
                {/* ── KPI Sections ───────────────────────────── */}
                <div className="space-y-10">
                    {/* section: Commercial & Ventes */}
                    <div className="space-y-4">
                        <SectionTitle>Performance Commerciale</SectionTitle>
                        <div className="grid gap-6 grid-cols-1 md:grid-cols-2 xl:grid-cols-3">
                            <KpiCard
                                icon={<ShoppingCart className="h-5 w-5" />}
                                label="Volume de Ventes"
                                value={formatNumber(stats.sales_count)}
                                sub="Transactions validées ce mois"
                                accent="text-blue-600"
                                trend={{ value: '12%', positive: true }}
                            />
                            <KpiCard
                                icon={<TrendingUp className="h-5 w-5" />}
                                label="Chiffre d'Affaires"
                                value={formatCurrency(stats.sales_total)}
                                sub="Total TTC encaissé"
                                accent="text-emerald-600"
                                trend={{ value: '8.4%', positive: true }}
                            />
                            <KpiCard
                                icon={<Truck className="h-5 w-5" />}
                                label="Approvisionnements"
                                value={formatNumber(stats.procurements_month)}
                                sub="Reçus ce mois"
                                accent="text-teal-600"
                            />
                        </div>
                    </div>

                    {/* section: Finance & Stock */}
                    <div className="space-y-4">
                        <SectionTitle>Gestion Opérationnelle</SectionTitle>
                        <div className="grid gap-6 grid-cols-1 md:grid-cols-2 2xl:grid-cols-4">
                            <KpiCard
                                icon={<DollarSign className="h-5 w-5" />}
                                label="Total des Charges"
                                value={formatCurrency(stats.expenses_total)}
                                sub="Dépenses opérationnelles"
                                accent="text-rose-500"
                            />
                             <KpiCard
                                icon={<Receipt className="h-5 w-5" />}
                                label="Apports de Fonds"
                                value={formatCurrency(stats.cash_inputs_total)}
                                sub="Trésorerie injectée"
                                accent="text-teal-600"
                            />
                            {stats.low_stock_count && stats.low_stock_count > 0 ? (
                                <KpiCard
                                    icon={<AlertTriangle className="h-5 w-5" />}
                                    label="Alerte Stock"
                                    value={formatNumber(stats.low_stock_count)}
                                    sub="Articles critiques"
                                    accent="text-amber-600"
                                    alert
                                />
                            ) : null}
                            {stats.inventories_draft && stats.inventories_draft > 0 && (
                                <KpiCard
                                    icon={<PackageCheck className="h-5 w-5" />}
                                    label="Inventaires en cours"
                                    value={formatNumber(stats.inventories_draft)}
                                    sub="Brouillons à valider"
                                    accent="text-blue-600"
                                />
                            )}
                        </div>
                    </div>
                </div>

                {/* ── Quick Actions ────────────────────────────────────────── */}
                <div>
                    <SectionTitle>Actions rapides</SectionTitle>
                    <div className="grid gap-6 grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
                        <QuickLink
                            href={route('drinks.procurements.index', { current_team: slug })}
                            label="Approvisionnements"
                            description="Liste des réceptions"
                            icon={<Truck className="h-5 w-5" />}
                        />
                        <QuickLink
                            href={route('drinks.procurements.create', { current_team: slug })}
                            label="Nouvel appro."
                            description="Réceptionner du stock"
                            icon={<Truck className="h-5 w-5" />}
                        />
                        <QuickLink
                            href={route('drinks.sales.create', { current_team: slug })}
                            label="Nouvelle vente"
                            description="Enregistrer une transaction"
                            icon={<ShoppingCart className="h-5 w-5" />}
                        />
                        <QuickLink
                            href={route('drinks.inventories.index', { current_team: slug })}
                            label="Inventaires"
                            description="Gérer les stocks"
                            icon={<PackageCheck className="h-5 w-5" />}
                        />
                        <QuickLink
                            href={route('drinks.reports.brouillard', { current_team: slug })}
                            label="Rapports"
                            description="Analyses & Statistiques"
                            icon={<BarChart2 className="h-5 w-5" />}
                        />
                    </div>
                </div>
            </motion.div>
        </>
    );
}

DashboardGerant.displayName = 'DashboardGerant';
