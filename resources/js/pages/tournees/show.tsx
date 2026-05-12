import { useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import {
    ArrowLeft, Truck, Calendar, User, Car, CheckCircle2,
    Clock, XCircle, AlertCircle, MapPin, Phone, Package,
    ChevronDown, ChevronUp, Printer, Navigation,
} from 'lucide-react';
import * as TourneeController from '@/actions/App/Http/Controllers/TourneeController';

const fadeUp = {
    hidden: { opacity: 0, y: 12 },
    visible: (i: number) => ({
        opacity: 1, y: 0,
        transition: { duration: 0.35, delay: i * 0.05, ease: [0.22, 1, 0.36, 1] },
    }),
};

/* ─── Types ─────────────────────────────────────────────────────────────────── */

type DeliveryStatus = 'pending' | 'delivered' | 'partial' | 'failed';
type RouteStatus    = 'planned' | 'in_progress' | 'completed' | 'cancelled';

interface OrderItem {
    id: number;
    product_name: string;
    product_sku: string;
    quantity: number;
    unit_price: number;
    discount_pct: number;
    line_total: number;
}

interface DeliveryOrder {
    id: number;
    order_number: string;
    status: string;
    delivery_date: string | null;
    subtotal: number;
    discount_amount: number;
    total: number;
    notes: string | null;
    items: OrderItem[];
}

interface DeliveryClient {
    id: number;
    name: string;
    address: string | null;
    phone: string | null;
    phone2: string | null;
    zone: string | null;
    gps_lat: number | null;
    gps_lng: number | null;
    client_type: string;
}

interface Delivery {
    id: number;
    sequence_number: number;
    status: DeliveryStatus;
    delivered_at: string | null;
    notes: string | null;
    client: DeliveryClient;
    order: DeliveryOrder;
}

interface RouteDriver { id: number; name: string }
interface RouteVehicle { id: number; name: string; plate: string | null }

interface Route {
    id: number;
    name: string;
    date: string;
    status: RouteStatus;
    departure_time: string | null;
    arrival_time: string | null;
    total_distance_km: number | null;
    driver: RouteDriver | null;
    vehicle: RouteVehicle | null;
}

interface Stats {
    total: number;
    delivered: number;
    pending: number;
    failed: number;
}

interface Props {
    team: { id: number; name: string; slug: string };
    route: Route;
    deliveries: Delivery[];
    stats: Stats;
}

/* ─── Helpers ───────────────────────────────────────────────────────────────── */

function formatDate(d: string) {
    return new Intl.DateTimeFormat('fr-FR', { day: '2-digit', month: 'long', year: 'numeric' }).format(new Date(d));
}

function formatCurrency(v: number) {
    return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XAF', maximumFractionDigits: 0 }).format(v);
}

const routeStatusConfig: Record<RouteStatus, { label: string; className: string }> = {
    planned:     { label: 'Planifiée',  className: 'bg-blue-500/10 text-blue-500' },
    in_progress: { label: 'En cours',   className: 'bg-amber-500/10 text-amber-500' },
    completed:   { label: 'Terminée',   className: 'bg-emerald-500/10 text-emerald-500' },
    cancelled:   { label: 'Annulée',    className: 'bg-rose-500/10 text-rose-500' },
};

const deliveryStatusConfig: Record<DeliveryStatus, { label: string; icon: React.ElementType; className: string }> = {
    pending:   { label: 'En attente', icon: Clock,        className: 'bg-secondary text-muted-foreground' },
    delivered: { label: 'Livré',      icon: CheckCircle2, className: 'bg-emerald-500/10 text-emerald-500' },
    partial:   { label: 'Partielle',  icon: AlertCircle,  className: 'bg-amber-500/10 text-amber-500' },
    failed:    { label: 'Échoué',     icon: XCircle,      className: 'bg-rose-500/10 text-rose-500' },
};

/* ─── Stop Card ─────────────────────────────────────────────────────────────── */

function StopCard({ delivery, index, teamSlug, routeId }: {
    delivery: Delivery;
    index: number;
    teamSlug: string;
    routeId: number;
}) {
    const [expanded, setExpanded] = useState(false);
    const statusCfg = deliveryStatusConfig[delivery.status];
    const StatusIcon = statusCfg.icon;

    return (
        <motion.div
            custom={index}
            variants={fadeUp}
            initial="hidden"
            animate="visible"
            className="rounded-2xl border border-border bg-card overflow-hidden print:break-inside-avoid"
        >
            {/* En-tête du stop */}
            <div className="flex items-start gap-4 p-5">
                {/* Numéro de stop */}
                <div className="flex shrink-0 h-10 w-10 items-center justify-center rounded-xl bg-primary/10 text-primary font-bold text-sm">
                    {delivery.sequence_number}
                </div>

                <div className="flex-1 min-w-0">
                    {/* Nom client + statut */}
                    <div className="flex flex-wrap items-center gap-2 mb-1">
                        <span className="font-semibold text-foreground text-base">
                            {delivery.client.name}
                        </span>
                        <span className={`inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium ${statusCfg.className}`}>
                            <StatusIcon className="h-3 w-3" />
                            {statusCfg.label}
                        </span>
                        {delivery.client.zone && (
                            <span className="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-secondary text-muted-foreground">
                                <MapPin className="h-3 w-3" />
                                {delivery.client.zone}
                            </span>
                        )}
                    </div>

                    {/* Infos client */}
                    <div className="flex flex-wrap gap-x-4 gap-y-1 text-sm text-muted-foreground">
                        {delivery.client.address && (
                            <span className="flex items-center gap-1">
                                <Navigation className="h-3.5 w-3.5 shrink-0" />
                                {delivery.client.address}
                            </span>
                        )}
                        {delivery.client.phone && (
                            <a href={`tel:${delivery.client.phone}`} className="flex items-center gap-1 hover:text-foreground transition-colors">
                                <Phone className="h-3.5 w-3.5 shrink-0" />
                                {delivery.client.phone}
                            </a>
                        )}
                        {delivery.client.phone2 && (
                            <a href={`tel:${delivery.client.phone2}`} className="flex items-center gap-1 hover:text-foreground transition-colors">
                                <Phone className="h-3.5 w-3.5 shrink-0" />
                                {delivery.client.phone2}
                            </a>
                        )}
                    </div>

                    {/* Résumé commande */}
                    {delivery.order && (
                        <div className="flex flex-wrap items-center gap-3 mt-2">
                            <span className="text-xs font-mono text-muted-foreground bg-secondary px-2 py-0.5 rounded">
                                {delivery.order.order_number}
                            </span>
                            <span className="text-sm font-bold text-foreground">
                                {formatCurrency(delivery.order.total)}
                            </span>
                            {delivery.order.discount_amount > 0 && (
                                <span className="text-xs text-muted-foreground">
                                    (remise {formatCurrency(delivery.order.discount_amount)})
                                </span>
                            )}
                        </div>
                    )}
                </div>

                {/* Actions */}
                <div className="flex items-center gap-2 shrink-0">
                    {delivery.order && (
                        <button
                            onClick={() => setExpanded(v => !v)}
                            className="flex items-center gap-1 text-xs text-muted-foreground hover:text-foreground transition-colors px-2 py-1 rounded-lg border border-border print:hidden"
                        >
                            <Package className="h-3.5 w-3.5" />
                            {delivery.order.items.length} art.
                            {expanded ? <ChevronUp className="h-3.5 w-3.5" /> : <ChevronDown className="h-3.5 w-3.5" />}
                        </button>
                    )}
                    <Link
                        href={TourneeController.showDelivery({
                            current_team: teamSlug,
                            deliveryRoute: routeId,
                            delivery: delivery.id,
                        }).url}
                        className="h-8 px-3 rounded-lg border border-border text-xs font-medium hover:bg-secondary transition-colors inline-flex items-center print:hidden"
                    >
                        Détail
                    </Link>
                </div>
            </div>

            {/* Lignes de commande (expandable) */}
            {delivery.order && (
                <div className={`border-t border-border px-5 pb-4 ${expanded ? '' : 'hidden print:block'}`}>
                    <table className="w-full text-sm mt-3">
                        <thead>
                            <tr className="text-xs text-muted-foreground border-b border-border">
                                <th className="text-left pb-2 font-medium">Produit</th>
                                <th className="text-left pb-2 font-medium">SKU</th>
                                <th className="text-right pb-2 font-medium">Qté</th>
                                <th className="text-right pb-2 font-medium">P.U.</th>
                                <th className="text-right pb-2 font-medium">Total</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-border">
                            {delivery.order.items.map(item => (
                                <tr key={item.id} className="text-foreground">
                                    <td className="py-2 pr-3">{item.product_name}</td>
                                    <td className="py-2 pr-3 text-muted-foreground font-mono text-xs">{item.product_sku}</td>
                                    <td className="py-2 text-right font-medium">{item.quantity}</td>
                                    <td className="py-2 text-right text-muted-foreground">{formatCurrency(item.unit_price)}</td>
                                    <td className="py-2 text-right font-semibold">{formatCurrency(item.line_total)}</td>
                                </tr>
                            ))}
                        </tbody>
                        <tfoot>
                            <tr className="border-t border-border font-bold">
                                <td colSpan={4} className="pt-2 text-right text-muted-foreground text-xs">Total commande</td>
                                <td className="pt-2 text-right">{formatCurrency(delivery.order.total)}</td>
                            </tr>
                        </tfoot>
                    </table>
                    {delivery.order.notes && (
                        <p className="mt-2 text-xs text-muted-foreground italic">Note : {delivery.order.notes}</p>
                    )}
                </div>
            )}
        </motion.div>
    );
}

/* ─── Page principale ───────────────────────────────────────────────────────── */

export default function TourneesShow({ team, route, deliveries, stats }: Props) {
    const routeCfg = routeStatusConfig[route.status] ?? routeStatusConfig.planned;
    const progress = stats.total > 0 ? Math.round((stats.delivered / stats.total) * 100) : 0;

    return (
        <div className="flex flex-col gap-6 p-6 lg:p-8 w-full min-h-screen bg-background text-foreground">
            <Head title={`Feuille de route — ${route.name}`} />

            {/* Banner impression */}
            <div className="hidden print:block mb-4 text-center text-xs text-muted-foreground">
                {team.name} — Imprimé le {new Date().toLocaleDateString('fr-FR')}
            </div>

            {/* Navigation */}
            <div className="flex items-center justify-between print:hidden">
                <Link
                    href={`/${team.slug}/tournees`}
                    className="inline-flex items-center gap-1.5 text-sm text-muted-foreground hover:text-foreground transition-colors"
                >
                    <ArrowLeft className="h-4 w-4" />
                    Retour aux tournées
                </Link>
                <button
                    onClick={() => window.print()}
                    className="inline-flex items-center gap-2 rounded-lg border border-border px-3 py-1.5 text-sm font-medium hover:bg-secondary transition-colors"
                >
                    <Printer className="h-4 w-4" />
                    Imprimer
                </button>
            </div>

            {/* Header tournée */}
            <motion.div
                initial={{ opacity: 0, y: -8 }}
                animate={{ opacity: 1, y: 0 }}
                className="rounded-2xl border border-border bg-card p-6"
            >
                <div className="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <div className="flex items-center gap-2 mb-1">
                            <Truck className="h-5 w-5 text-primary" />
                            <span className="text-xs font-semibold text-primary uppercase tracking-wider">
                                Feuille de route
                            </span>
                        </div>
                        <h1 className="text-2xl font-bold tracking-tight text-foreground">{route.name}</h1>
                        <div className="flex flex-wrap items-center gap-4 mt-2 text-sm text-muted-foreground">
                            <span className="flex items-center gap-1.5">
                                <Calendar className="h-4 w-4" />
                                {formatDate(route.date)}
                            </span>
                            {route.driver && (
                                <span className="flex items-center gap-1.5">
                                    <User className="h-4 w-4" />
                                    {route.driver.name}
                                </span>
                            )}
                            {route.vehicle && (
                                <span className="flex items-center gap-1.5">
                                    <Car className="h-4 w-4" />
                                    {route.vehicle.name}
                                    {route.vehicle.plate && (
                                        <span className="font-mono text-xs">({route.vehicle.plate})</span>
                                    )}
                                </span>
                            )}
                            {route.total_distance_km && (
                                <span className="flex items-center gap-1.5">
                                    <Navigation className="h-4 w-4" />
                                    {route.total_distance_km} km
                                </span>
                            )}
                        </div>
                    </div>
                    <span className={`inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${routeCfg.className}`}>
                        {routeCfg.label}
                    </span>
                </div>

                {/* Barre de progression */}
                {stats.total > 0 && (
                    <div className="mt-4">
                        <div className="flex items-center justify-between text-xs text-muted-foreground mb-1.5">
                            <span>{stats.delivered} / {stats.total} livraisons effectuées</span>
                            <span className="font-semibold text-foreground">{progress}%</span>
                        </div>
                        <div className="h-2 w-full bg-secondary rounded-full overflow-hidden">
                            <div
                                className="h-full bg-primary rounded-full transition-all duration-500"
                                style={{ width: `${progress}%` }}
                            />
                        </div>
                    </div>
                )}
            </motion.div>

            {/* KPIs */}
            <div className="grid grid-cols-2 md:grid-cols-4 gap-4 print:hidden">
                {[
                    { label: 'Total', value: stats.total,     icon: Package,     color: 'blue' },
                    { label: 'Livrés', value: stats.delivered, icon: CheckCircle2, color: 'emerald' },
                    { label: 'En attente', value: stats.pending, icon: Clock,       color: 'amber' },
                    { label: 'Échoués', value: stats.failed,   icon: XCircle,     color: 'rose' },
                ].map(({ label, value, icon: Icon, color }, i) => (
                    <motion.div
                        key={label}
                        custom={i}
                        variants={fadeUp}
                        initial="hidden"
                        animate="visible"
                        className="rounded-2xl border border-border bg-card p-4 flex items-center gap-3"
                    >
                        <div className={`p-2 rounded-xl bg-${color}-500/10 text-${color}-500`}>
                            <Icon className="h-4 w-4" />
                        </div>
                        <div>
                            <p className="text-2xl font-bold text-foreground">{value}</p>
                            <p className="text-xs text-muted-foreground">{label}</p>
                        </div>
                    </motion.div>
                ))}
            </div>

            {/* Liste des stops */}
            <div className="space-y-3">
                {deliveries.length === 0 ? (
                    <div className="flex flex-col items-center justify-center py-16 rounded-2xl border border-border bg-card text-muted-foreground">
                        <Package className="h-12 w-12 mb-3 opacity-20" />
                        <p className="text-sm">Aucune livraison dans cette tournée.</p>
                    </div>
                ) : (
                    deliveries.map((delivery, i) => (
                        <StopCard
                            key={delivery.id}
                            delivery={delivery}
                            index={i}
                            teamSlug={team.slug}
                            routeId={route.id}
                        />
                    ))
                )}
            </div>
        </div>
    );
}

TourneesShow.layout = (props: { currentTeam?: { slug: string } | null; route?: { name: string; id: number } }) => ({
    breadcrumbs: [
        {
            title: 'Tournées',
            href: props.currentTeam ? `/${props.currentTeam.slug}/tournees` : '/',
        },
        {
            title: props.route?.name ?? 'Détail',
            href: '#',
        },
    ],
});
