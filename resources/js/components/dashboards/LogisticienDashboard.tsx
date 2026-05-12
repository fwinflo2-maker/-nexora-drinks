import { usePage, Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { Truck, Clock, CheckCircle, XCircle, AlertTriangle, Map, User, Fuel, Package } from 'lucide-react';

const item = { hidden: { opacity: 0, y: 16 }, visible: { opacity: 1, y: 0, transition: { duration: 0.4 } } };
const container = { hidden: { opacity: 0 }, visible: { opacity: 1, transition: { staggerChildren: 0.07 } } };

/* ─── Gauge ─── */
function Gauge({ value, max, color, label }: { value: number; max: number; color: string; label: string }) {
    const pct = max > 0 ? value / max : 0;
    const r = 36; const cx = 44; const cy = 44;
    const circumference = Math.PI * r;
    const offset = circumference * (1 - pct);

    return (
        <div className="flex flex-col items-center gap-2">
            <svg width="88" height="56" viewBox="0 0 88 56">
                <path d={`M 8 44 A ${r} ${r} 0 0 1 ${cx * 2 - 8} 44`} fill="none" stroke="hsl(var(--border))" strokeWidth="8" strokeLinecap="round" />
                <path d={`M 8 44 A ${r} ${r} 0 0 1 ${cx * 2 - 8} 44`} fill="none" stroke={color} strokeWidth="8" strokeLinecap="round"
                    strokeDasharray={circumference} strokeDashoffset={offset}
                    style={{ strokeDashoffset: circumference, transition: 'stroke-dashoffset 1s cubic-bezier(0.16,1,0.3,1) 0.5s' }}
                    ref={el => {
 if (el) {
setTimeout(() => {
 el.style.strokeDashoffset = `${offset}`; 
}, 100);
} 
}}
                />
                <text x="44" y="42" textAnchor="middle" fill="hsl(var(--foreground))" fontSize="16" fontWeight="700">{value}</text>
            </svg>
            <span className="text-[10px] text-muted-foreground text-center leading-tight">{label}</span>
        </div>
    );
}

/* ─── Status maps ─── */
const routeStatusStyle: Record<string, string> = {
    planned:     "bg-secondary/50 text-muted-foreground border-border",
    in_progress: "bg-blue-500/10 text-blue-400 border-blue-500/20",
    completed:   "bg-emerald-500/10 text-emerald-400 border-emerald-500/20",
};

const routeStatusLabel: Record<string, string> = {
    planned:     "Planifiée",
    in_progress: "En cours",
    completed:   "Terminée",
};

const driverColors = ["hsl(142,71%,40%)", "hsl(217,91%,55%)", "hsl(262,83%,58%)", "hsl(38,92%,50%)", "hsl(0,84%,60%)"];

export default function LogisticienDashboard({ userName }: { userName: string }) {
    const { logisticsKpis, routes: serverRoutes, drivers: serverDrivers, teamName, currentTeam } = usePage().props as any;
    const slug = currentTeam?.slug ?? '';

    const today = new Date().toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long' });

    const routes: {
        id: number;
        name: string;
        date: string;
        status: string;
        stops_count: number;
        delivered_count: number;
        progress_pct: number;
        driver_name: string;
        vehicle: string;
    }[] = serverRoutes ?? [];

    const drivers: {
        id: number;
        name: string;
        deliveries_today: number;
        deliveries_done: number;
    }[] = serverDrivers ?? [];

    /* ─── KPIs ─── */
    const delaysCount = logisticsKpis?.delays_count ?? 0;
    const kpis = [
        { title: "Tournées Actives",      value: logisticsKpis?.active_routes ?? 0,       icon: Map,           color: "bg-blue-500/10 text-blue-400"      },
        { title: "Livraisons Restantes",  value: logisticsKpis?.deliveries_remaining ?? 0, icon: Package,       color: "bg-amber-500/10 text-amber-400"    },
        { title: "Livrées Aujourd'hui",   value: logisticsKpis?.deliveries_done ?? 0,      icon: CheckCircle,   color: "bg-emerald-500/10 text-emerald-400" },
        { title: "Retards",               value: delaysCount,                               icon: AlertTriangle, color: delaysCount > 0 ? "bg-rose-500/10 text-rose-400" : "bg-secondary/50 text-muted-foreground" },
    ];

    return (
        <div className="relative z-50 flex flex-1 flex-col gap-6 p-6 lg:p-8 w-full">

            {/* Header */}
            <motion.div initial={{ opacity: 0, y: -8 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.4 }}
                className="flex items-start justify-between gap-4">
                <div>
                    <h1 className="text-2xl font-bold tracking-tight text-foreground">Logistique & Tournées</h1>
                    <p className="text-sm text-muted-foreground mt-1 capitalize">{today} · {teamName ?? userName}</p>
                </div>
                {slug && (
                    <Link
                        href={`/${slug}/tournees`}
                        className="flex items-center gap-2 px-4 py-2 rounded-lg bg-primary text-primary-foreground text-sm font-semibold hover:bg-primary/90 transition-colors shrink-0"
                    >
                        Nouvelle tournée +
                    </Link>
                )}
            </motion.div>

            {/* KPIs */}
            <motion.div variants={container} initial="hidden" animate="visible"
                className="grid grid-cols-2 xl:grid-cols-4 gap-4">
                {kpis.map((k, i) => {
                    const Icon = k.icon;

                    return (
                        <motion.div key={i} variants={item} className="rounded-2xl border border-border bg-card p-5 flex flex-col gap-3 hover:shadow-lg hover:border-primary/20 transition-all duration-300">
                            <div className="flex items-center justify-between">
                                <span className="text-xs font-semibold text-muted-foreground uppercase tracking-wider">{k.title}</span>
                                <div className={`p-2 rounded-xl ${k.color}`}><Icon className="h-4 w-4" /></div>
                            </div>
                            <div>
                                <div className="text-3xl font-bold tracking-tight text-foreground">{k.value}</div>
                            </div>
                        </motion.div>
                    );
                })}
            </motion.div>

            {/* Progress Gauges */}
            {drivers.length > 0 && (
                <motion.div variants={item} initial="hidden" animate="visible" transition={{ delay: 0.3 }}
                    className="rounded-2xl border border-border bg-card p-5">
                    <h2 className="text-sm font-semibold text-foreground mb-5">Avancement des tournées en temps réel</h2>
                    <div className="flex items-center justify-around gap-4 flex-wrap">
                        {drivers.map((d, i) => (
                            <div key={d.id} className="flex flex-col items-center gap-1">
                                <Gauge
                                    value={d.deliveries_done}
                                    max={d.deliveries_today}
                                    color={driverColors[i % driverColors.length]}
                                    label={d.name}
                                />
                                <span className="text-[9px] text-muted-foreground">{d.deliveries_done}/{d.deliveries_today} liv.</span>
                            </div>
                        ))}
                    </div>
                </motion.div>
            )}

            {/* Routes list */}
            <motion.div variants={item} initial="hidden" animate="visible" transition={{ delay: 0.4 }}
                className="rounded-2xl border border-border bg-card overflow-hidden">
                <div className="px-5 py-4 border-b border-border flex items-center justify-between">
                    <div>
                        <h2 className="text-sm font-semibold text-foreground">Planning des tournées — Aujourd'hui</h2>
                        <p className="text-xs text-muted-foreground mt-0.5">Avancement par tournée</p>
                    </div>
                    {slug && (
                        <Link href={`/${slug}/tournees`} className="text-xs text-primary hover:underline font-medium">
                            Voir toutes les tournées →
                        </Link>
                    )}
                </div>
                {routes.length === 0 ? (
                    <div className="flex flex-col items-center justify-center py-12 gap-3 text-muted-foreground">
                        <Map className="h-8 w-8 opacity-40" />
                        <span className="text-sm">Aucune tournée aujourd'hui</span>
                    </div>
                ) : (
                    <div className="divide-y divide-border">
                        {routes.map((route) => {
                            const style = routeStatusStyle[route.status] ?? routeStatusStyle.planned;
                            const label = routeStatusLabel[route.status] ?? route.status;
                            const pct = Math.min(100, Math.max(0, route.progress_pct ?? 0));

                            return (
                                <div key={route.id} className="px-5 py-4 hover:bg-secondary/20 transition-colors">
                                    <div className="flex items-start justify-between gap-4 mb-2">
                                        <div>
                                            <div className="font-medium text-sm text-foreground">{route.name}</div>
                                            <div className="text-xs text-muted-foreground mt-0.5">
                                                {route.driver_name && <span>{route.driver_name}</span>}
                                                {route.vehicle && <span className="ml-2 font-mono">{route.vehicle}</span>}
                                            </div>
                                        </div>
                                        <div className="flex items-center gap-3 shrink-0">
                                            <span className="text-xs text-muted-foreground">{route.delivered_count}/{route.stops_count} arrêts</span>
                                            <span className={`inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold border ${style}`}>
                                                {route.status === 'completed' ? <CheckCircle className="h-3 w-3" /> : route.status === 'in_progress' ? <Truck className="h-3 w-3" /> : <Clock className="h-3 w-3" />}
                                                {label}
                                            </span>
                                        </div>
                                    </div>
                                    <div className="w-full h-1.5 rounded-full bg-secondary overflow-hidden">
                                        <div className="h-full rounded-full bg-primary transition-all duration-700" style={{ width: `${pct}%` }} />
                                    </div>
                                    <div className="text-[10px] text-muted-foreground mt-1">{pct}% complété</div>
                                </div>
                            );
                        })}
                    </div>
                )}
            </motion.div>

            {/* Chauffeurs table */}
            <motion.div variants={item} initial="hidden" animate="visible" transition={{ delay: 0.5 }}
                className="rounded-2xl border border-border bg-card overflow-hidden">
                <div className="px-5 py-4 border-b border-border flex items-center justify-between">
                    <h2 className="text-sm font-semibold text-foreground">Chauffeurs — Statut en temps réel</h2>
                    {slug && (
                        <Link href={`/${slug}/tournees`} className="text-xs text-primary hover:underline font-medium">
                            Nouvelle tournée +
                        </Link>
                    )}
                </div>
                {drivers.length === 0 ? (
                    <div className="flex flex-col items-center justify-center py-12 gap-3 text-muted-foreground">
                        <User className="h-8 w-8 opacity-40" />
                        <span className="text-sm">Aucun chauffeur assigné</span>
                    </div>
                ) : (
                    <div className="overflow-x-auto">
                        <table className="w-full text-sm">
                            <thead>
                                <tr className="border-b border-border bg-secondary/30">
                                    {["Chauffeur", "Livraisons du jour", "Terminées", "Progression"].map(h => (
                                        <th key={h} className="text-left px-5 py-3 text-xs font-semibold text-muted-foreground tracking-wide">{h}</th>
                                    ))}
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-border">
                                {drivers.map((d) => {
                                    const pct = d.deliveries_today > 0
                                        ? Math.round((d.deliveries_done / d.deliveries_today) * 100)
                                        : 0;

                                    return (
                                        <tr key={d.id} className="hover:bg-secondary/20 transition-colors">
                                            <td className="px-5 py-3.5">
                                                <div className="flex items-center gap-2">
                                                    <div className="h-7 w-7 rounded-full bg-primary/10 flex items-center justify-center text-[10px] font-bold text-primary">
                                                        {d.name.split(' ').map((n: string) => n[0]).join('')}
                                                    </div>
                                                    <span className="font-medium text-foreground">{d.name}</span>
                                                </div>
                                            </td>
                                            <td className="px-5 py-3.5 font-semibold text-foreground">{d.deliveries_today}</td>
                                            <td className="px-5 py-3.5 font-semibold text-foreground">{d.deliveries_done}</td>
                                            <td className="px-5 py-3.5">
                                                <div className="flex items-center gap-2">
                                                    <div className="w-24 h-1.5 rounded-full bg-secondary overflow-hidden">
                                                        <div className="h-full rounded-full bg-primary transition-all duration-700" style={{ width: `${pct}%` }} />
                                                    </div>
                                                    <span className="text-xs text-muted-foreground">{pct}%</span>
                                                </div>
                                            </td>
                                        </tr>
                                    );
                                })}
                            </tbody>
                        </table>
                    </div>
                )}
            </motion.div>

        </div>
    );
}
