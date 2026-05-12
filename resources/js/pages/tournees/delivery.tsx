import { Head, Link, useForm } from '@inertiajs/react';
import { motion } from 'framer-motion';
import {
    ArrowLeft, ChevronLeft, ChevronRight, User, MapPin, Phone,
    Package, ShoppingCart, CheckCircle2, Clock, XCircle, AlertCircle,
    ExternalLink,
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
    product_name: string | null;
    product_sku: string | null;
    quantity: number;
    unit_price: number;
    discount_pct: number;
    line_total: number;
}

interface DeliveryClient {
    id: number;
    name: string;
    phone: string | null;
    phone2: string | null;
    address: string | null;
    gps_lat: number | null;
    gps_lng: number | null;
    zone: string | null;
    client_type: string;
}

interface DeliveryOrder {
    id: number;
    order_number: string;
    subtotal: number;
    discount_amount: number;
    total: number;
    notes: string | null;
    items: OrderItem[];
}

interface DeliveryDetail {
    id: number;
    sequence_number: number;
    status: DeliveryStatus;
    delivered_at: string | null;
    notes: string | null;
    client: DeliveryClient | null;
    order: DeliveryOrder | null;
}

interface Navigation {
    prev_id: number | null;
    next_id: number | null;
    total: number;
    current_position: number;
}

interface Props {
    team: { id: number; name: string; slug: string };
    route: { id: number; name: string; date: string; status: RouteStatus };
    delivery: DeliveryDetail;
    navigation: Navigation;
}

/* ─── Helpers ───────────────────────────────────────────────────────────────── */

function formatDate(d: string) {
    return new Intl.DateTimeFormat('fr-FR', { day: '2-digit', month: 'long', year: 'numeric' }).format(new Date(d));
}

function formatCurrency(v: number) {
    return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XAF', maximumFractionDigits: 0 }).format(v);
}

const deliveryStatusConfig: Record<DeliveryStatus, { label: string; icon: React.ElementType; className: string }> = {
    pending:   { label: 'En attente', icon: Clock,        className: 'bg-secondary text-muted-foreground' },
    delivered: { label: 'Livré',      icon: CheckCircle2, className: 'bg-emerald-500/10 text-emerald-500' },
    partial:   { label: 'Partielle',  icon: AlertCircle,  className: 'bg-amber-500/10 text-amber-500' },
    failed:    { label: 'Échoué',     icon: XCircle,      className: 'bg-rose-500/10 text-rose-500' },
};

const clientTypeConfig: Record<string, { label: string; className: string }> = {
    detail:    { label: 'Détaillant',    className: 'bg-blue-500/10 text-blue-500' },
    grossiste: { label: 'Grossiste',     className: 'bg-purple-500/10 text-purple-500' },
    bar:       { label: 'Bar / Buvette', className: 'bg-amber-500/10 text-amber-500' },
    resto:     { label: 'Restaurant',    className: 'bg-emerald-500/10 text-emerald-500' },
};

/* ─── Page principale ───────────────────────────────────────────────────────── */

type FormItem = { id: number; delivered_qty: number };

export default function TourneesDelivery({ team, route, delivery, navigation }: Props) {
    const statusCfg = deliveryStatusConfig[delivery.status];
    const StatusIcon = statusCfg.icon;

    const clientTypeCfg = delivery.client
        ? (clientTypeConfig[delivery.client.client_type] ?? { label: delivery.client.client_type, className: 'bg-secondary text-muted-foreground' })
        : null;

    const mapsUrl = delivery.client?.gps_lat && delivery.client?.gps_lng
        ? `https://www.google.com/maps?q=${delivery.client.gps_lat},${delivery.client.gps_lng}`
        : delivery.client?.address
            ? `https://www.google.com/maps/search/${encodeURIComponent(delivery.client.address)}`
            : null;

    const { data, setData, patch, processing, errors } = useForm<{
        status: DeliveryStatus;
        notes: string;
        items: FormItem[];
    }>({
        status: delivery.status === 'pending' ? 'delivered' : delivery.status,
        notes: delivery.notes ?? '',
        items: delivery.order?.items.map(i => ({ id: i.id, delivered_qty: i.quantity })) ?? [],
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        patch(TourneeController.updateDelivery({
            current_team: team.slug,
            deliveryRoute: route.id,
            delivery: delivery.id,
        }).url);
    };

    const updateItemQty = (itemId: number, qty: number) => {
        setData('items', data.items.map(i =>
            i.id === itemId ? { ...i, delivered_qty: qty } : i
        ));
    };

    const statusOptions: { value: DeliveryStatus; label: string; icon: React.ElementType }[] = [
        { value: 'delivered', label: 'Livré',    icon: CheckCircle2 },
        { value: 'partial',   label: 'Partielle', icon: AlertCircle },
        { value: 'failed',    label: 'Échoué',    icon: XCircle },
    ];

    return (
        <div className="flex flex-col gap-6 p-6 lg:p-8 w-full min-h-screen bg-background text-foreground">
            <Head title={`Stop ${delivery.sequence_number} — ${delivery.client?.name ?? 'Livraison'}`} />

            {/* Banner impression */}
            <div className="hidden print:block mb-4 text-center text-xs text-muted-foreground">
                {team.name} — Imprimé le {new Date().toLocaleDateString('fr-FR')}
            </div>

            {/* Barre de navigation */}
            <div className="flex flex-col gap-3 print:hidden">
                <Link
                    href={TourneeController.show({
                        current_team: team.slug,
                        deliveryRoute: route.id,
                    }).url}
                    className="inline-flex items-center gap-1.5 text-sm text-muted-foreground hover:text-foreground transition-colors w-fit"
                >
                    <ArrowLeft className="h-4 w-4" />
                    Retour à la feuille de route
                </Link>

                <div className="flex items-center justify-between">
                    <div>
                        {navigation.prev_id ? (
                            <Link
                                href={TourneeController.showDelivery({
                                    current_team: team.slug,
                                    deliveryRoute: route.id,
                                    delivery: navigation.prev_id,
                                }).url}
                                className="inline-flex items-center gap-1 text-sm font-medium text-muted-foreground hover:text-foreground transition-colors px-3 py-1.5 rounded-lg border border-border hover:bg-secondary"
                            >
                                <ChevronLeft className="h-4 w-4" />
                                Stop précédent
                            </Link>
                        ) : (
                            <span className="inline-flex items-center gap-1 text-sm text-muted-foreground/40 px-3 py-1.5">
                                <ChevronLeft className="h-4 w-4" />
                                Stop précédent
                            </span>
                        )}
                    </div>

                    <span className="text-sm font-semibold text-foreground">
                        Stop {navigation.current_position} / {navigation.total}
                    </span>

                    <div>
                        {navigation.next_id ? (
                            <Link
                                href={TourneeController.showDelivery({
                                    current_team: team.slug,
                                    deliveryRoute: route.id,
                                    delivery: navigation.next_id,
                                }).url}
                                className="inline-flex items-center gap-1 text-sm font-medium text-muted-foreground hover:text-foreground transition-colors px-3 py-1.5 rounded-lg border border-border hover:bg-secondary"
                            >
                                Stop suivant
                                <ChevronRight className="h-4 w-4" />
                            </Link>
                        ) : (
                            <span className="inline-flex items-center gap-1 text-sm text-muted-foreground/40 px-3 py-1.5">
                                Stop suivant
                                <ChevronRight className="h-4 w-4" />
                            </span>
                        )}
                    </div>
                </div>
            </div>

            {/* Header card */}
            <motion.div
                initial={{ opacity: 0, y: -8 }}
                animate={{ opacity: 1, y: 0 }}
                className="rounded-2xl border border-border bg-card p-6"
            >
                <div className="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <div className="flex flex-wrap items-center gap-2 mb-2">
                            <span className="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-primary/10 text-primary">
                                Stop {delivery.sequence_number}
                            </span>
                            <span className={`inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium ${statusCfg.className}`}>
                                <StatusIcon className="h-3 w-3" />
                                {statusCfg.label}
                            </span>
                        </div>
                        <h1 className="text-2xl font-bold tracking-tight text-foreground">
                            {delivery.client?.name ?? 'Client inconnu'}
                        </h1>
                        <p className="text-sm text-muted-foreground mt-1">
                            Route : <span className="font-medium text-foreground">{route.name}</span>
                            {' — '}
                            {formatDate(route.date)}
                        </p>
                    </div>
                </div>
            </motion.div>

            {/* Section Client */}
            {delivery.client && (
                <motion.div
                    custom={0}
                    variants={fadeUp}
                    initial="hidden"
                    animate="visible"
                    className="rounded-2xl border border-border bg-card p-6"
                >
                    <div className="flex items-center gap-2 mb-4">
                        <User className="h-4 w-4 text-primary" />
                        <h2 className="font-semibold text-foreground">Informations client</h2>
                    </div>

                    <div className="space-y-3">
                        {/* Nom + type */}
                        <div className="flex flex-wrap items-center gap-2">
                            <span className="font-medium text-foreground">{delivery.client.name}</span>
                            {clientTypeCfg && (
                                <span className={`inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ${clientTypeCfg.className}`}>
                                    {clientTypeCfg.label}
                                </span>
                            )}
                            {delivery.client.zone && (
                                <span className="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-secondary text-muted-foreground">
                                    <MapPin className="h-3 w-3" />
                                    {delivery.client.zone}
                                </span>
                            )}
                        </div>

                        {/* Adresse */}
                        {(delivery.client.address || mapsUrl) && (
                            <div className="flex items-start gap-2 text-sm text-muted-foreground">
                                <MapPin className="h-4 w-4 mt-0.5 shrink-0" />
                                <div className="flex items-center gap-2">
                                    <span>{delivery.client.address}</span>
                                    {mapsUrl && (
                                        <a
                                            href={mapsUrl}
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            className="inline-flex items-center gap-1 text-primary hover:underline text-xs font-medium"
                                        >
                                            <ExternalLink className="h-3 w-3" />
                                            Maps
                                        </a>
                                    )}
                                </div>
                            </div>
                        )}

                        {/* Téléphones */}
                        {delivery.client.phone && (
                            <div className="flex items-center gap-2 text-sm">
                                <Phone className="h-4 w-4 text-muted-foreground shrink-0" />
                                <a
                                    href={`tel:${delivery.client.phone}`}
                                    className="text-foreground hover:text-primary transition-colors font-medium"
                                >
                                    {delivery.client.phone}
                                </a>
                            </div>
                        )}
                        {delivery.client.phone2 && (
                            <div className="flex items-center gap-2 text-sm">
                                <Phone className="h-4 w-4 text-muted-foreground shrink-0" />
                                <a
                                    href={`tel:${delivery.client.phone2}`}
                                    className="text-foreground hover:text-primary transition-colors font-medium"
                                >
                                    {delivery.client.phone2}
                                </a>
                            </div>
                        )}
                    </div>
                </motion.div>
            )}

            {/* Section Commande */}
            {delivery.order && (
                <motion.div
                    custom={1}
                    variants={fadeUp}
                    initial="hidden"
                    animate="visible"
                    className="rounded-2xl border border-border bg-card p-6"
                >
                    <div className="flex flex-wrap items-center justify-between gap-3 mb-4">
                        <div className="flex items-center gap-2">
                            <ShoppingCart className="h-4 w-4 text-primary" />
                            <h2 className="font-semibold text-foreground">Articles à livrer</h2>
                        </div>
                        <div className="flex items-center gap-3">
                            <span className="font-mono text-xs text-muted-foreground bg-secondary px-2 py-0.5 rounded">
                                {delivery.order.order_number}
                            </span>
                            <span className="font-bold text-foreground">
                                {formatCurrency(delivery.order.total)}
                            </span>
                        </div>
                    </div>

                    <table className="w-full text-sm">
                        <thead>
                            <tr className="text-xs text-muted-foreground border-b border-border">
                                <th className="text-left pb-2 font-medium">Produit</th>
                                <th className="text-left pb-2 font-medium hidden sm:table-cell">SKU</th>
                                <th className="text-right pb-2 font-medium">Qté</th>
                                <th className="text-right pb-2 font-medium hidden sm:table-cell">P.U.</th>
                                <th className="text-right pb-2 font-medium">Total</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-border">
                            {delivery.order.items.map(item => (
                                <tr key={item.id} className="text-foreground">
                                    <td className="py-2 pr-3">{item.product_name}</td>
                                    <td className="py-2 pr-3 text-muted-foreground font-mono text-xs hidden sm:table-cell">{item.product_sku}</td>
                                    <td className="py-2 text-right font-medium">{item.quantity}</td>
                                    <td className="py-2 text-right text-muted-foreground hidden sm:table-cell">{formatCurrency(item.unit_price)}</td>
                                    <td className="py-2 text-right font-semibold">{formatCurrency(item.line_total)}</td>
                                </tr>
                            ))}
                        </tbody>
                        <tfoot>
                            <tr className="border-t border-border font-bold">
                                <td colSpan={2} className="pt-2 text-right text-muted-foreground text-xs hidden sm:table-cell">Total commande</td>
                                <td colSpan={3} className="pt-2 text-right text-muted-foreground text-xs sm:hidden">Total commande</td>
                                <td colSpan={2} className="pt-2 text-right hidden sm:table-cell">{formatCurrency(delivery.order.total)}</td>
                                <td className="pt-2 text-right sm:hidden">{formatCurrency(delivery.order.total)}</td>
                            </tr>
                        </tfoot>
                    </table>

                    {delivery.order.notes && (
                        <p className="mt-3 text-xs text-muted-foreground italic border-t border-border pt-3">
                            Note : {delivery.order.notes}
                        </p>
                    )}
                </motion.div>
            )}

            {/* Formulaire de mise à jour */}
            <motion.div
                custom={2}
                variants={fadeUp}
                initial="hidden"
                animate="visible"
                className="rounded-2xl border border-border bg-card p-6 print:hidden"
            >
                <div className="flex items-center gap-2 mb-4">
                    <Package className="h-4 w-4 text-primary" />
                    <h2 className="font-semibold text-foreground">Mise à jour de la livraison</h2>
                </div>

                <form onSubmit={handleSubmit} className="space-y-5">
                    {/* Radio groupe statut */}
                    <div>
                        <label className="block text-sm font-medium text-foreground mb-2">Statut</label>
                        <div className="flex flex-wrap gap-3">
                            {statusOptions.map(({ value, label, icon: Icon }) => (
                                <label
                                    key={value}
                                    className={`flex items-center gap-2 px-4 py-2.5 rounded-xl border cursor-pointer transition-all ${
                                        data.status === value
                                            ? 'border-primary bg-primary/5 text-primary font-medium'
                                            : 'border-border text-muted-foreground hover:border-foreground/30 hover:text-foreground'
                                    }`}
                                >
                                    <input
                                        type="radio"
                                        name="status"
                                        value={value}
                                        checked={data.status === value}
                                        onChange={() => setData('status', value)}
                                        className="sr-only"
                                    />
                                    <Icon className="h-4 w-4" />
                                    {label}
                                </label>
                            ))}
                        </div>
                        {errors.status && (
                            <p className="mt-1 text-xs text-rose-500">{errors.status}</p>
                        )}
                    </div>

                    {/* Table quantités livrées (si partielle) */}
                    {data.status === 'partial' && delivery.order && delivery.order.items.length > 0 && (
                        <div>
                            <label className="block text-sm font-medium text-foreground mb-2">
                                Quantités livrées
                            </label>
                            <div className="rounded-xl border border-border overflow-hidden">
                                <table className="w-full text-sm">
                                    <thead>
                                        <tr className="bg-secondary/50 text-xs text-muted-foreground">
                                            <th className="text-left px-4 py-2.5 font-medium">Produit</th>
                                            <th className="text-right px-4 py-2.5 font-medium">Qté commandée</th>
                                            <th className="text-right px-4 py-2.5 font-medium">Qté livrée</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-border">
                                        {delivery.order.items.map(item => {
                                            const formItem = data.items.find(i => i.id === item.id);
                                            return (
                                                <tr key={item.id} className="bg-card">
                                                    <td className="px-4 py-2.5 text-foreground">{item.product_name}</td>
                                                    <td className="px-4 py-2.5 text-right text-muted-foreground">{item.quantity}</td>
                                                    <td className="px-4 py-2.5 text-right">
                                                        <input
                                                            type="number"
                                                            min={0}
                                                            max={item.quantity}
                                                            value={formItem?.delivered_qty ?? item.quantity}
                                                            onChange={e => updateItemQty(item.id, parseInt(e.target.value, 10) || 0)}
                                                            className="w-20 rounded-lg border border-border bg-background px-2 py-1 text-right text-sm focus:outline-none focus:ring-2 focus:ring-primary/50"
                                                        />
                                                    </td>
                                                </tr>
                                            );
                                        })}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    )}

                    {/* Notes */}
                    <div>
                        <label htmlFor="notes" className="block text-sm font-medium text-foreground mb-2">
                            Notes
                        </label>
                        <textarea
                            id="notes"
                            rows={3}
                            value={data.notes}
                            onChange={e => setData('notes', e.target.value)}
                            placeholder="Observations, problèmes rencontrés..."
                            className="w-full rounded-xl border border-border bg-background px-4 py-3 text-sm text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/50 resize-none"
                        />
                        {errors.notes && (
                            <p className="mt-1 text-xs text-rose-500">{errors.notes}</p>
                        )}
                    </div>

                    {/* Bouton submit */}
                    <div className="flex justify-end">
                        <button
                            type="submit"
                            disabled={processing}
                            className="inline-flex items-center gap-2 rounded-xl bg-primary px-6 py-2.5 text-sm font-semibold text-primary-foreground hover:bg-primary/90 transition-all duration-200 active:scale-95 disabled:opacity-60 disabled:cursor-not-allowed disabled:active:scale-100"
                        >
                            {processing ? (
                                <>
                                    <span className="h-4 w-4 rounded-full border-2 border-primary-foreground/30 border-t-primary-foreground animate-spin" />
                                    Enregistrement...
                                </>
                            ) : (
                                <>
                                    <CheckCircle2 className="h-4 w-4" />
                                    Enregistrer
                                </>
                            )}
                        </button>
                    </div>
                </form>
            </motion.div>
        </div>
    );
}

TourneesDelivery.layout = (props: {
    currentTeam?: { slug: string } | null;
    route?: { name: string; id: number };
    delivery?: { sequence_number: number };
}) => ({
    breadcrumbs: [
        {
            title: 'Tournées',
            href: props.currentTeam ? `/${props.currentTeam.slug}/tournees` : '/',
        },
        {
            title: props.route?.name ?? 'Feuille de route',
            href: props.currentTeam && props.route ? `/${props.currentTeam.slug}/tournees/${props.route.id}` : '#',
        },
        {
            title: `Stop ${props.delivery?.sequence_number ?? ''}`,
            href: '#',
        },
    ],
});
