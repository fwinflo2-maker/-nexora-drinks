import { Head, usePage } from '@inertiajs/react';
import { motion } from 'framer-motion';
import {
    ShoppingCart,
    TrendingUp,
    Wallet,
    Receipt,
    Package,
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

export default function DashboardCaissier({ stats }: DashboardProps) {
    const { currentTeam } = usePage().props as any;
    const slug = currentTeam?.slug ?? '';

    return (
        <>
            <Head title="Tableau de bord Caissier — Distribution Boissons" />
            <h1 className="sr-only">Tableau de bord Caissier Distribution Boissons</h1>

            <motion.div
                variants={containerVariants}
                initial="hidden"
                animate="visible"
                className="flex flex-col space-y-8 p-0"
            >
                {/* ── KPI Sections ───────────────────────────── */}
                <div className="space-y-10">
                    {/* section: Activité de Caisse */}
                    <div className="space-y-4">
                        <SectionTitle>Activité de Vente & Encaissements</SectionTitle>
                        <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                            <KpiCard
                                icon={<ShoppingCart className="h-5 w-5" />}
                                label="Ventes (mois)"
                                value={formatNumber(stats.sales_count)}
                                sub="Transactions validées"
                                accent="text-blue-600"
                            />
                            <KpiCard
                                icon={<TrendingUp className="h-5 w-5" />}
                                label="Chiffre d'Affaires"
                                value={formatCurrency(stats.sales_total)}
                                sub="Total TTC généré"
                                accent="text-emerald-600"
                            />
                            <KpiCard
                                icon={<Wallet className="h-5 w-5" />}
                                label="Règlements reçus"
                                value={formatCurrency(stats.payments_total)}
                                sub="Encaissements clients"
                                accent="text-indigo-600"
                            />
                            <KpiCard
                                icon={<Receipt className="h-5 w-5" />}
                                label="Apports de fonds"
                                value={formatCurrency(stats.cash_inputs_total)}
                                sub="Fonds de caisse reçus"
                                accent="text-teal-600"
                            />
                        </div>
                    </div>
                </div>

                {/* ── Quick Actions ────────────────────────────────────────── */}
                <div>
                    <SectionTitle>Actions rapides de caisse</SectionTitle>
                    <div className="flex flex-wrap gap-2">
                        <QuickLink
                            href={route('drinks.sales.create', { current_team: slug })}
                            label="Nouvelle Vente"
                            icon={<ShoppingCart className="h-4 w-4" />}
                            variant="default"
                        />
                        <QuickLink
                            href={route('drinks.payments.create', { current_team: slug })}
                            label="Encaisser un client"
                            icon={<Wallet className="h-4 w-4" />}
                        />
                        <QuickLink
                            href={route('drinks.sales.index', { current_team: slug })}
                            label="Historique des ventes"
                            icon={<ShoppingCart className="h-4 w-4" />}
                        />
                        <QuickLink
                            href={route('drinks.cash-inputs.index', { current_team: slug })}
                            label="Suivi des apports"
                            icon={<Receipt className="h-4 w-4" />}
                        />
                    </div>
                </div>
            </motion.div>
        </>
    );
}

DashboardCaissier.displayName = 'DashboardCaissier';
