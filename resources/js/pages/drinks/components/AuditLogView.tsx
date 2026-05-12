import { Search, History, User, Activity, Box } from 'lucide-react';
import React, { useState } from 'react';

interface Log {
    id: number;
    module: string;
    action: string;
    description: string;
    created_at: string;
    user?: { name: string };
    metadata?: any;
}

interface Props {
    logs: Log[];
}

export default function AuditLogView({ logs = [] }: Props) {
    const [search, setSearch] = useState('');

    const filteredLogs = logs.filter(log => 
        log.description.toLowerCase().includes(search.toLowerCase()) ||
        log.module.toLowerCase().includes(search.toLowerCase()) ||
        log.user?.name.toLowerCase().includes(search.toLowerCase())
    );

    return (
        <div className="p-6 space-y-6">
            <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 className="text-xl font-bold text-foreground flex items-center gap-2">
                        <History className="h-5 w-5 text-amber-500" />
                        Audit & Logs
                    </h1>
                    <p className="text-sm text-muted-foreground">Historique complet des actions effectuées sur la plateforme.</p>
                </div>

                <div className="relative w-full md:w-64">
                    <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                    <input
                        type="text"
                        placeholder="Rechercher une action..."
                        value={search}
                        onChange={e => setSearch(e.target.value)}
                        className="w-full pl-9 pr-4 h-10 bg-card border border-border rounded-xl text-sm focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all"
                    />
                </div>
            </div>

            <div className="bg-card border border-border rounded-2xl overflow-hidden shadow-sm">
                <div className="overflow-x-auto">
                    <table className="w-full text-left border-collapse">
                        <thead>
                            <tr className="bg-muted/30 border-b border-border">
                                <th className="px-4 py-3 text-[10px] font-bold uppercase tracking-wider text-muted-foreground">Date & Heure</th>
                                <th className="px-4 py-3 text-[10px] font-bold uppercase tracking-wider text-muted-foreground">Utilisateur</th>
                                <th className="px-4 py-3 text-[10px] font-bold uppercase tracking-wider text-muted-foreground">Module</th>
                                <th className="px-4 py-3 text-[10px] font-bold uppercase tracking-wider text-muted-foreground">Action</th>
                                <th className="px-4 py-3 text-[10px] font-bold uppercase tracking-wider text-muted-foreground">Description</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-border">
                            {filteredLogs.map((log) => (
                                <tr key={log.id} className="hover:bg-muted/10 transition-colors">
                                    <td className="px-4 py-3 whitespace-nowrap">
                                        <div className="text-xs font-medium text-foreground text-capitalize">
                                            {new Date(log.created_at).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' })}
                                        </div>
                                        <div className="text-[10px] text-muted-foreground">
                                            {new Date(log.created_at).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit', second: '2-digit' })}
                                        </div>
                                    </td>
                                    <td className="px-4 py-3">
                                        <div className="flex items-center gap-2">
                                            <div className="h-7 w-7 rounded-full bg-amber-500/10 flex items-center justify-center text-[10px] font-bold text-amber-600">
                                                <User className="h-3.5 w-3.5" />
                                            </div>
                                            <span className="text-xs font-medium text-foreground">{log.user?.name || 'Système'}</span>
                                        </div>
                                    </td>
                                    <td className="px-4 py-3">
                                        <span className="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-blue-500/10 text-blue-600 text-[10px] font-bold uppercase">
                                            <Box className="h-3 w-3" />
                                            {log.module}
                                        </span>
                                    </td>
                                    <td className="px-4 py-3">
                                        <span className={`inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[10px] font-bold uppercase ${
                                            log.action.includes('delete') || log.action.includes('remove') 
                                                ? 'bg-red-500/10 text-red-600'
                                                : log.action.includes('create') || log.action.includes('add')
                                                    ? 'bg-emerald-500/10 text-emerald-600'
                                                    : 'bg-orange-500/10 text-orange-600'
                                        }`}>
                                            <Activity className="h-3 w-3" />
                                            {log.action}
                                        </span>
                                    </td>
                                    <td className="px-4 py-3">
                                        <p className="text-xs text-muted-foreground line-clamp-1" title={log.description}>
                                            {log.description}
                                        </p>
                                    </td>
                                </tr>
                            ))}
                            {filteredLogs.length === 0 && (
                                <tr>
                                    <td colSpan={5} className="px-4 py-12 text-center text-sm text-muted-foreground italic">
                                        Aucun log d'activité trouvé.
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    );
}
