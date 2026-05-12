import { motion } from 'framer-motion';
import { AlertTriangle, Activity, Settings, Lock, Trash2, Zap, Database, Shield, ChevronDown } from 'lucide-react';
import { useState } from 'react';
import { HoverButton } from '@/components/ui/hover-button';

export function GodmodeControlPanel() {
    const [expanded, setExpanded] = useState<string | null>(null);
    const [systemHealth, setSystemHealth] = useState<any>(null);
    const [auditLogs, setAuditLogs] = useState<any[]>([]);
    const [loading, setLoading] = useState(false);

    const loadGodmodeData = async () => {
        setLoading(true);

        try {
            const response = await fetch(`/api/v1/godmode/dashboard`, {
                headers: { 'Accept': 'application/json' }
            });
            const result = await response.json();
            setSystemHealth(result.data);
        } catch (error) {
            console.error('Erreur chargement godmode:', error);
        } finally {
            setLoading(false);
        }
    };

    const loadAuditLogs = async () => {
        try {
            const response = await fetch(`/api/v1/godmode/audit-logs`, {
                headers: { 'Accept': 'application/json' }
            });
            const result = await response.json();
            setAuditLogs(result.data || []);
        } catch (error) {
            console.error('Erreur chargement audit logs:', error);
        }
    };

    const toggleSection = (section: string) => {
        if (section === expanded) {
            setExpanded(null);
        } else {
            setExpanded(section);

            if (section === 'health') {
                loadGodmodeData();
            }

            if (section === 'audit') {
                loadAuditLogs();
            }
        }
    };

    return (
        <div className="space-y-6">
            <div className="rounded-2xl border-2 border-red-500 bg-gradient-to-br from-red-50 to-pink-50 dark:from-red-950/20 dark:to-pink-950/20 p-8">
                <div className="flex items-start gap-4">
                    <div className="flex h-14 w-14 items-center justify-center rounded-xl bg-red-200 dark:bg-red-900/50">
                        <Shield className="h-8 w-8 text-red-700 dark:text-red-300" />
                    </div>
                    <div>
                        <h2 className="text-2xl font-bold text-red-950 dark:text-red-100">
                            🔓 GODMODE ACTIVÉ
                        </h2>
                        <p className="mt-2 text-red-800 dark:text-red-200">
                            Accès super-administrateur complet. Toutes les actions sont enregistrées et tracées.
                        </p>
                        <div className="mt-4 p-4 rounded-lg bg-red-100/50 dark:bg-red-900/30 border border-red-200 dark:border-red-700/50 text-sm text-red-900 dark:text-red-100">
                            ⚠️ <strong>Attention:</strong> Ces opérations sont irréversibles et affecteront tous les utilisateurs et tenants.
                        </div>
                    </div>
                </div>
            </div>

            {/* Contrôles système */}
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                {/* Santé système */}
                <motion.button
                    onClick={() => toggleSection('health')}
                    whileHover={{ scale: 1.02 }}
                    whileTap={{ scale: 0.98 }}
                    className="text-left p-6 rounded-xl border-2 border-blue-300 dark:border-blue-700 bg-gradient-to-br from-blue-50 to-cyan-50 dark:from-blue-950/20 dark:to-cyan-950/20 hover:shadow-lg transition-shadow"
                >
                    <div className="flex items-center justify-between">
                        <div className="flex items-center gap-3">
                            <Activity className="h-6 w-6 text-blue-600 dark:text-blue-400" />
                            <div>
                                <h3 className="font-bold text-foreground">Santé système</h3>
                                <p className="text-sm text-muted-foreground">État de tous les services</p>
                            </div>
                        </div>
                        <ChevronDown className={`h-5 w-5 transition-transform ${expanded === 'health' ? 'rotate-180' : ''}`} />
                    </div>
                </motion.button>

                {/* Maintenance */}
                <motion.button
                    onClick={() => toggleSection('maintenance')}
                    whileHover={{ scale: 1.02 }}
                    whileTap={{ scale: 0.98 }}
                    className="text-left p-6 rounded-xl border-2 border-yellow-300 dark:border-yellow-700 bg-gradient-to-br from-yellow-50 to-amber-50 dark:from-yellow-950/20 dark:to-amber-950/20 hover:shadow-lg transition-shadow"
                >
                    <div className="flex items-center justify-between">
                        <div className="flex items-center gap-3">
                            <Zap className="h-6 w-6 text-yellow-600 dark:text-yellow-400" />
                            <div>
                                <h3 className="font-bold text-foreground">Mode maintenance</h3>
                                <p className="text-sm text-muted-foreground">Fermer la plateforme aux utilisateurs</p>
                            </div>
                        </div>
                        <ChevronDown className={`h-5 w-5 transition-transform ${expanded === 'maintenance' ? 'rotate-180' : ''}`} />
                    </div>
                </motion.button>

                {/* Requêtes SQL */}
                <motion.button
                    onClick={() => toggleSection('sql')}
                    whileHover={{ scale: 1.02 }}
                    whileTap={{ scale: 0.98 }}
                    className="text-left p-6 rounded-xl border-2 border-purple-300 dark:border-purple-700 bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-950/20 dark:to-pink-950/20 hover:shadow-lg transition-shadow"
                >
                    <div className="flex items-center justify-between">
                        <div className="flex items-center gap-3">
                            <Database className="h-6 w-6 text-purple-600 dark:text-purple-400" />
                            <div>
                                <h3 className="font-bold text-foreground">Exécution SQL</h3>
                                <p className="text-sm text-muted-foreground">Requête directe à la base de données</p>
                            </div>
                        </div>
                        <ChevronDown className={`h-5 w-5 transition-transform ${expanded === 'sql' ? 'rotate-180' : ''}`} />
                    </div>
                </motion.button>

                {/* Audit trail */}
                <motion.button
                    onClick={() => toggleSection('audit')}
                    whileHover={{ scale: 1.02 }}
                    whileTap={{ scale: 0.98 }}
                    className="text-left p-6 rounded-xl border-2 border-green-300 dark:border-green-700 bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-950/20 dark:to-emerald-950/20 hover:shadow-lg transition-shadow"
                >
                    <div className="flex items-center justify-between">
                        <div className="flex items-center gap-3">
                            <Shield className="h-6 w-6 text-green-600 dark:text-green-400" />
                            <div>
                                <h3 className="font-bold text-foreground">Audit trail</h3>
                                <p className="text-sm text-muted-foreground">Historique de toutes les actions</p>
                            </div>
                        </div>
                        <ChevronDown className={`h-5 w-5 transition-transform ${expanded === 'audit' ? 'rotate-180' : ''}`} />
                    </div>
                </motion.button>
            </div>

            {/* Contenu des sections */}
            {expanded === 'health' && systemHealth && (
                <motion.div
                    initial={{ opacity: 0, y: 10 }}
                    animate={{ opacity: 1, y: 0 }}
                    className="rounded-xl border border-border bg-card p-6 space-y-4"
                >
                    <h3 className="font-semibold text-lg">État du système</h3>
                    <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                        {systemHealth.system_health && Object.entries(systemHealth.system_health).map(([key, value]: [string, any]) => (
                            <div key={key} className="p-4 rounded-lg bg-secondary/50 border border-border">
                                <p className="text-xs font-semibold text-muted-foreground uppercase">{key}</p>
                                <p className={`text-sm font-bold mt-1 ${value.status === 'healthy' ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400'}`}>
                                    {value.status}
                                </p>
                            </div>
                        ))}
                    </div>
                </motion.div>
            )}

            {expanded === 'audit' && (
                <motion.div
                    initial={{ opacity: 0, y: 10 }}
                    animate={{ opacity: 1, y: 0 }}
                    className="rounded-xl border border-border bg-card p-6 space-y-4"
                >
                    <h3 className="font-semibold text-lg">Audit trail</h3>
                    <div className="overflow-x-auto">
                        <table className="w-full text-sm">
                            <thead className="border-b border-border">
                                <tr>
                                    <th className="text-left py-2 px-2 font-semibold">Super Admin</th>
                                    <th className="text-left py-2 px-2 font-semibold">Action</th>
                                    <th className="text-left py-2 px-2 font-semibold">Tenant</th>
                                    <th className="text-left py-2 px-2 font-semibold">Date</th>
                                </tr>
                            </thead>
                            <tbody className="space-y-2">
                                {auditLogs.map((log) => (
                                    <tr key={log.id} className="border-b border-border/50 hover:bg-secondary/50 transition-colors">
                                        <td className="py-2 px-2 text-xs">{log.superAdmin?.email || 'Unknown'}</td>
                                        <td className="py-2 px-2 text-xs font-mono text-amber-600 dark:text-amber-400">{log.action}</td>
                                        <td className="py-2 px-2 text-xs">{log.targetTeam?.name || 'N/A'}</td>
                                        <td className="py-2 px-2 text-xs text-muted-foreground">{new Date(log.created_at).toLocaleDateString()}</td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </motion.div>
            )}

            {/* Section SQL */}
            {expanded === 'sql' && (
                <motion.div
                    initial={{ opacity: 0, y: 10 }}
                    animate={{ opacity: 1, y: 0 }}
                    className="rounded-xl border-2 border-red-300 dark:border-red-700 bg-card p-6 space-y-4"
                >
                    <div className="flex items-start gap-3 p-4 rounded-lg bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-700/50">
                        <AlertTriangle className="h-5 w-5 text-red-600 dark:text-red-400 flex-shrink-0 mt-0.5" />
                        <p className="text-sm text-red-800 dark:text-red-200">
                            <strong>ATTENTION:</strong> Cette fonctionnalité exécute du SQL brut. Une mauvaise requête peut corrompre la base de données.
                        </p>
                    </div>
                    <textarea
                        placeholder="SELECT * FROM teams LIMIT 10;"
                        className="w-full h-32 p-4 rounded-lg border border-border bg-secondary font-mono text-xs focus:outline-none focus:ring-2 focus:ring-red-500"
                    />
                    <HoverButton variant="destructive" className="w-full">
                        <Database className="h-4 w-4 mr-2" />
                        Exécuter requête (Nécessite confirmation)
                    </HoverButton>
                </motion.div>
            )}
        </div>
    );
}
