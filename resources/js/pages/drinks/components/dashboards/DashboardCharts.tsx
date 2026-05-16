import React from 'react';
import {
    AreaChart,
    Area,
    XAxis,
    YAxis,
    CartesianGrid,
    Tooltip,
    ResponsiveContainer,
    PieChart,
    Pie,
    Cell,
    Legend,
} from 'recharts';
import { SectionTitle, formatCurrency } from './DashboardComponents';

// ── Types ─────────────────────────────────────────────────────────────────────

type ChartPoint = { name: string; total: number };
type ExpensePoint = { name: string; value: number };

// ── Palette ───────────────────────────────────────────────────────────────────

const EXPENSE_COLORS = [
    '#f59e0b', '#3b82f6', '#10b981', '#ef4444',
    '#8b5cf6', '#06b6d4', '#f97316', '#ec4899',
];

// ── Tooltip personnalisé ──────────────────────────────────────────────────────

function SalesTooltip({ active, payload, label }: any) {
    if (!active || !payload?.length) return null;
    return (
        <div className="bg-white border border-slate-100 rounded-2xl px-4 py-3 shadow-xl shadow-slate-200/50 text-xs">
            <p className="font-black text-slate-400 uppercase tracking-widest mb-1">{label}</p>
            <p className="font-black text-emerald-600 text-sm">{formatCurrency(payload[0]?.value)}</p>
        </div>
    );
}

function ExpenseTooltip({ active, payload }: any) {
    if (!active || !payload?.length) return null;
    return (
        <div className="bg-white border border-slate-100 rounded-2xl px-4 py-3 shadow-xl shadow-slate-200/50 text-xs">
            <p className="font-black text-slate-500">{payload[0]?.name}</p>
            <p className="font-black text-rose-500 text-sm">{formatCurrency(payload[0]?.value)}</p>
        </div>
    );
}

// ── Composant Tendance des Ventes ─────────────────────────────────────────────

export function SalesTrendChart({ data }: { data: ChartPoint[] }) {
    if (!data || data.length === 0) {
        return (
            <div className="bg-white border border-slate-100 rounded-[2.5rem] p-8 shadow-sm">
                <SectionTitle>Tendance des Ventes — 15 derniers jours</SectionTitle>
                <div className="h-48 flex items-center justify-center">
                    <p className="text-xs text-slate-400 italic font-bold">Aucune donnée disponible</p>
                </div>
            </div>
        );
    }

    return (
        <div className="bg-white border border-slate-100 rounded-[2.5rem] p-8 shadow-sm hover:shadow-xl hover:shadow-emerald-500/5 transition-all duration-500">
            <SectionTitle>Tendance des Ventes — 15 derniers jours</SectionTitle>
            <div className="h-56">
                <ResponsiveContainer width="100%" height="100%">
                    <AreaChart data={data} margin={{ top: 4, right: 4, left: 4, bottom: 0 }}>
                        <defs>
                            <linearGradient id="salesGradient" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="5%" stopColor="#10b981" stopOpacity={0.15} />
                                <stop offset="95%" stopColor="#10b981" stopOpacity={0} />
                            </linearGradient>
                        </defs>
                        <CartesianGrid strokeDasharray="3 3" stroke="#f1f5f9" vertical={false} />
                        <XAxis
                            dataKey="name"
                            tick={{ fontSize: 10, fill: '#94a3b8', fontWeight: 700 }}
                            axisLine={false}
                            tickLine={false}
                            interval="preserveStartEnd"
                        />
                        <YAxis
                            tick={{ fontSize: 10, fill: '#94a3b8', fontWeight: 700 }}
                            axisLine={false}
                            tickLine={false}
                            tickFormatter={(v) => (v >= 1000 ? `${(v / 1000).toFixed(0)}k` : v)}
                            width={40}
                        />
                        <Tooltip content={<SalesTooltip />} cursor={{ stroke: '#e2e8f0', strokeWidth: 1 }} />
                        <Area
                            type="monotone"
                            dataKey="total"
                            stroke="#10b981"
                            strokeWidth={2.5}
                            fill="url(#salesGradient)"
                            dot={false}
                            activeDot={{ r: 5, fill: '#10b981', strokeWidth: 0 }}
                        />
                    </AreaChart>
                </ResponsiveContainer>
            </div>
        </div>
    );
}

// ── Composant Répartition des Dépenses ────────────────────────────────────────

export function ExpenseBreakdownChart({ data }: { data: ExpensePoint[] }) {
    if (!data || data.length === 0) {
        return (
            <div className="bg-white border border-slate-100 rounded-[2.5rem] p-8 shadow-sm">
                <SectionTitle>Répartition des Dépenses</SectionTitle>
                <div className="h-48 flex items-center justify-center">
                    <p className="text-xs text-slate-400 italic font-bold">Aucune dépense ce mois</p>
                </div>
            </div>
        );
    }

    const total = data.reduce((sum, d) => sum + d.value, 0);

    return (
        <div className="bg-white border border-slate-100 rounded-[2.5rem] p-8 shadow-sm hover:shadow-xl hover:shadow-rose-500/5 transition-all duration-500">
            <SectionTitle>Répartition des Dépenses</SectionTitle>
            <div className="flex items-center gap-6">
                <div className="h-48 w-48 shrink-0">
                    <ResponsiveContainer width="100%" height="100%">
                        <PieChart>
                            <Pie
                                data={data}
                                cx="50%"
                                cy="50%"
                                innerRadius={52}
                                outerRadius={72}
                                paddingAngle={3}
                                dataKey="value"
                                strokeWidth={0}
                            >
                                {data.map((_, index) => (
                                    <Cell key={index} fill={EXPENSE_COLORS[index % EXPENSE_COLORS.length]} />
                                ))}
                            </Pie>
                            <Tooltip content={<ExpenseTooltip />} />
                        </PieChart>
                    </ResponsiveContainer>
                </div>
                <div className="flex-1 space-y-2 min-w-0">
                    {data.slice(0, 6).map((item, index) => (
                        <div key={index} className="flex items-center gap-2 min-w-0">
                            <div
                                className="h-2 w-2 shrink-0 rounded-full"
                                style={{ backgroundColor: EXPENSE_COLORS[index % EXPENSE_COLORS.length] }}
                            />
                            <span className="text-[10px] font-bold text-slate-500 truncate flex-1">{item.name}</span>
                            <span className="text-[10px] font-black text-slate-700 shrink-0">
                                {total > 0 ? Math.round((item.value / total) * 100) : 0}%
                            </span>
                        </div>
                    ))}
                    {data.length > 6 && (
                        <p className="text-[9px] text-slate-400 font-bold mt-1">+{data.length - 6} autres catégories</p>
                    )}
                </div>
            </div>
        </div>
    );
}
