import { motion } from 'framer-motion';
import { BarChart3, Settings, Bot } from 'lucide-react';
import { useState } from 'react';
import AdminAgent from '@/components/dashboards/tabs/AdminAgent';
import AdminOverview from '@/components/dashboards/tabs/AdminOverview';
import AdminProfiles from '@/components/dashboards/tabs/AdminProfiles';

export default function AdminDashboard({ userName, teamName, teamKpis, categories, warehouses, initialSection }: { userName: string, teamName?: string, teamKpis?: any, categories?: any[], warehouses?: any[], initialSection?: string }) {
    const [activeTab, setActiveTab] = useState<'overview' | 'profiles' | 'agent'>((initialSection as any) ?? 'overview');

    const tabs = [
        { id: 'overview', label: 'Vue d\'ensemble', icon: BarChart3, badge: null },
        { id: 'profiles', label: 'Gestion Profils', icon: Settings, badge: null },
        { id: 'agent', label: 'Agent IA', icon: Bot, badge: null },
    ] as const;

    return (
        <div className="relative z-50 flex flex-1 flex-col gap-6 p-6 lg:p-8 w-full">

            {/* Header */}
            <motion.div initial={{ opacity: 0, y: -8 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.4 }}>
                <div className="flex items-start justify-between">
                    <div>
                        <h1 className="text-2xl font-bold tracking-tight text-foreground">
                            Bonjour, {userName} 👋
                        </h1>
                        <p className="text-sm text-muted-foreground mt-1 capitalize">Tableau de bord administrateur de {teamName ? teamName : "votre réseau"}</p>
                    </div>
                    <div className="flex items-center gap-2 rounded-xl border border-border bg-card px-4 py-2 text-sm">
                        <div className="h-2 w-2 rounded-full bg-emerald-400 animate-pulse" />
                        <span className="text-muted-foreground">Système opérationnel</span>
                    </div>
                </div>
            </motion.div>

            {/* Tabs Navigation */}
            <motion.div initial={{ opacity: 0, y: -4 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.4, delay: 0.1 }}
                className="flex items-center gap-1 rounded-xl border border-border bg-card p-1">
                {tabs.map(tab => {
                    const Icon = tab.icon;
                    const isActive = activeTab === tab.id;
                    const buttonClass = isActive
                        ? 'bg-primary text-primary-foreground shadow-sm'
                        : 'text-muted-foreground hover:text-foreground hover:bg-secondary/50';

                    return (
                        <button
                            key={tab.id}
                            onClick={() => setActiveTab(tab.id)}
                            className={`flex items-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 font-medium text-sm relative group ${buttonClass}`}
                        >
                            <Icon className="h-4 w-4" />
                            <span>{tab.label}</span>
                            {tab.badge && (
                                <span className="ml-1.5 inline-flex h-5 w-5 items-center justify-center rounded-full bg-rose-500/10 text-rose-400 text-[10px] font-bold">
                                    {tab.badge}
                                </span>
                            )}
                        </button>
                    );
                })}
            </motion.div>

            {/* Tab Content */}
            <motion.div
                key={activeTab}
                initial={{ opacity: 0, y: 8 }}
                animate={{ opacity: 1, y: 0 }}
                exit={{ opacity: 0, y: -8 }}
                transition={{ duration: 0.3 }}
            >
                {activeTab === 'overview' && (
                    <AdminOverview userName={userName} teamName={teamName} teamKpis={teamKpis} categories={categories} warehouses={warehouses} />
                )}
                {activeTab === 'profiles' && (
                    <AdminProfiles teamKpis={teamKpis} />
                )}
                {activeTab === 'agent' && (
                    <AdminAgent userName={userName} teamName={teamName} />
                )}
            </motion.div>
        </div>
    );
}
