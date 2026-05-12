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
    Settings,
    Users,
} from 'lucide-react';
import React from 'react';
import type { DashboardProps } from './DashboardComponents';
import {
    KpiCard,
    QuickLink,
    formatCurrency,
    formatNumber,
    DashboardHeader,
    SectionTitle,
    containerVariants
} from './DashboardComponents';

export default function DashboardAdmin({ stats }: DashboardProps) {
    const { currentTeam } = usePage().props as any;
    const slug = currentTeam?.slug ?? '';

    return (
        <>
            <Head title="Tableau de bord Admin — Distribution Boissons" />
            <h1 className="sr-only">Tableau de bord Admin Distribution Boissons</h1>

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
                        <div className="grid gap-6 grid-cols-1 md:grid-cols-2">
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
                        </div>
                    </div>

                    {/* section: Finance & Trésorerie */}
                    <div className="space-y-4">
                        <SectionTitle>Gestion Financière & Trésorerie</SectionTitle>
                        <div className="grid gap-6 grid-cols-1 md:grid-cols-2 2xl:grid-cols-4">
                            <KpiCard
                                icon={<DollarSign className="h-5 w-5" />}
                                label="Total des Charges"
                                value={formatCurrency(stats.expenses_total)}
                                sub="Dépenses opérationnelles"
                                accent="text-rose-500"
                            />
                            <KpiCard
                                icon={<BarChart2 className="h-5 w-5" />}
                                label="Versements Banque"
                                value={formatCurrency(stats.cash_deposits_total)}
                                sub="Fonds déposés"
                                accent="text-violet-600"
                            />
                            <KpiCard
                                icon={<Wallet className="h-5 w-5" />}
                                label="Règlements Clients"
                                value={formatCurrency(stats.payments_total)}
                                sub="Encaissements reçus"
                                accent="text-indigo-600"
                            />
                            <KpiCard
                                icon={<Receipt className="h-5 w-5" />}
                                label="Apports de Fonds"
                                value={formatCurrency(stats.cash_inputs_total)}
                                sub="Injections de capital"
                                accent="text-teal-600"
                            />
                        </div>
                    </div>

                    {/* section: Logistique & Stock */}
                    <div className="space-y-4">
                        <SectionTitle>Logistique & Inventaire</SectionTitle>
                        <div className="grid gap-6 grid-cols-1 md:grid-cols-2 xl:grid-cols-4">
                            <KpiCard
                                icon={<Package className="h-5 w-5" />}
                                label="Articles en Catalogue"
                                value={formatNumber(stats.articles_count)}
                                accent="text-orange-500"
                            />
                            {stats.low_stock_count && stats.low_stock_count > 0 ? (
                                <KpiCard
                                    icon={<AlertTriangle className="h-5 w-5" />}
                                    label="Alerte Stock Faible"
                                    value={formatNumber(stats.low_stock_count)}
                                    sub="Articles sous le seuil critique"
                                    accent="text-amber-600"
                                    alert
                                />
                            ) : null}
                            <KpiCard
                                icon={<TrendingDown className="h-5 w-5" />}
                                label="Pertes & Coulages"
                                value={formatNumber(stats.losses_month)}
                                sub="Pertes déclarées"
                                accent="text-red-500"
                            />
                            <KpiCard
                                icon={<Truck className="h-5 w-5" />}
                                label="Flux Appro."
                                value={formatNumber(stats.procurements_month)}
                                sub="Réceptions ce mois"
                                accent="text-cyan-600"
                            />
                        </div>
                    </div>
                </div>

                {/* ── Quick Actions (Admin — Tous les modules) ─────────────── */}
                <div>
                    <SectionTitle>Actions rapides</SectionTitle>
                    <div className="grid gap-6 grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
                        <QuickLink
                            href={route('drinks.articles.index', { current_team: slug })}
                            label="Articles"
                            description="Gérer le catalogue"
                            icon={<Package className="h-5 w-5" />}
                        />
                        <QuickLink
                            href={route('drinks.sales.create', { current_team: slug })}
                            label="Nouvelle vente"
                            description="Enregistrer une transaction"
                            icon={<ShoppingCart className="h-5 w-5" />}
                        />
                        <QuickLink
                            href={route('drinks.procurements.create', { current_team: slug })}
                            label="Approvisionnement"
                            description="Réceptionner du stock"
                            icon={<Truck className="h-5 w-5" />}
                        />
                        <QuickLink
                            href={route('drinks.inventories.create', { current_team: slug })}
                            label="Inventaire"
                            description="Ajuster les quantités"
                            icon={<PackageCheck className="h-5 w-5" />}
                        />
                        <QuickLink
                            href={route('drinks.payments.create', { current_team: slug })}
                            label="Règlement"
                            description="Encaisser un paiement"
                            icon={<Wallet className="h-5 w-5" />}
                        />
                        <QuickLink
                            href={route('drinks.expenses.create', { current_team: slug })}
                            label="Nouvelle charge"
                            description="Enregistrer une dépense"
                            icon={<DollarSign className="h-5 w-5" />}
                        />
                        <QuickLink
                            href={route('drinks.cash-deposits.create', { current_team: slug })}
                            label="Versement banque"
                            description="Dépôt de fonds"
                            icon={<BarChart2 className="h-5 w-5" />}
                        />
                        <QuickLink
                            href={route('drinks.settings.index', { current_team: slug })}
                            label="Paramètres"
                            description="Configuration système"
                            icon={<Settings className="h-5 w-5" />}
                        />
                    </div>
                </div>
            </motion.div>
        </>
    );
}

DashboardAdmin.displayName = 'DashboardAdmin';
