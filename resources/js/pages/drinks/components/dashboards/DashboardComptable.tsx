import { Head, usePage } from '@inertiajs/react';
import { motion } from 'framer-motion';
import {
    DollarSign,
    BarChart2,
    Receipt,
    ShoppingCart,
    TrendingUp,
    Wallet,
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

export default function DashboardComptable({ stats }: DashboardProps) {
    const { currentTeam } = usePage().props as any;
    const slug = currentTeam?.slug ?? '';

    return (
        <>
            <Head title="Tableau de bord Comptable — Distribution Boissons" />
            <h1 className="sr-only">Tableau de bord Comptable Distribution Boissons</h1>

            <motion.div
                variants={containerVariants}
                initial="hidden"
                animate="visible"
                className="flex flex-col space-y-8 p-0"
            >
                {/* ── KPI Sections ───────────────────────────── */}
                <div className="space-y-10">
                    {/* section: Finance & Dépenses */}
                    <div className="space-y-4">
                        <SectionTitle>Gestion des Charges & Versements</SectionTitle>
                        <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
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
                                sub="Fonds sécurisés"
                                accent="text-violet-600"
                            />
                            <KpiCard
                                icon={<Receipt className="h-5 w-5" />}
                                label="Apports de Fonds"
                                value={formatCurrency(stats.cash_inputs_total)}
                                sub="Trésorerie entrante"
                                accent="text-teal-600"
                            />
                            <KpiCard
                                icon={<Wallet className="h-5 w-5" />}
                                label="Règlements Clients"
                                value={formatCurrency(stats.payments_total)}
                                sub="Suivi des créances"
                                accent="text-indigo-600"
                            />
                        </div>
                    </div>

                    {/* section: Analyse de Vente */}
                    <div className="space-y-4">
                        <SectionTitle>Récapitulatif des Ventes</SectionTitle>
                        <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                            <KpiCard
                                icon={<ShoppingCart className="h-5 w-5" />}
                                label="Volume de Ventes"
                                value={formatNumber(stats.sales_count)}
                                accent="text-blue-600"
                            />
                            <KpiCard
                                icon={<TrendingUp className="h-5 w-5" />}
                                label="Chiffre d'Affaires"
                                value={formatCurrency(stats.sales_total)}
                                accent="text-emerald-600"
                            />
                        </div>
                    </div>
                </div>

                {/* ── Quick Actions ────────────────────────────────────────── */}
                <div>
                    <SectionTitle>Actions comptables</SectionTitle>
                    <div className="flex flex-wrap gap-2">
                        <QuickLink
                            href={route('drinks.expenses.index', { current_team: slug })}
                            label="Gestion des Charges"
                            icon={<DollarSign className="h-4 w-4" />}
                        />
                        <QuickLink
                            href={route('drinks.expenses.create', { current_team: slug })}
                            label="Nouvelle Charge"
                            icon={<DollarSign className="h-4 w-4" />}
                        />
                        <QuickLink
                            href={route('drinks.cash-deposits.index', { current_team: slug })}
                            label="Suivi Versements"
                            icon={<BarChart2 className="h-4 w-4" />}
                        />
                        <QuickLink
                            href={route('drinks.cash-deposits.create', { current_team: slug })}
                            label="Nouveau Versement"
                            icon={<BarChart2 className="h-4 w-4" />}
                        />
                        <QuickLink
                            href={route('drinks.reports.brouillard', { current_team: slug })}
                            label="Rapports Financiers"
                            icon={<BarChart2 className="h-4 w-4" />}
                            variant="default"
                        />
                    </div>
                </div>
            </motion.div>
        </>
    );
}

DashboardComptable.displayName = 'DashboardComptable';
