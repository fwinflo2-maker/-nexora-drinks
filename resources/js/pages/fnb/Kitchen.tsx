import { Head, router } from '@inertiajs/react';
import { motion, AnimatePresence } from 'framer-motion';
import { ChefHat, CheckCircle2, Clock } from 'lucide-react';
import { useState } from 'react';

// ─── Types ───────────────────────────────────────────────────────────────────

interface OrderItem {
  id: number;
  name: string | null;
  quantity: number;
  status: string;
  notes: string | null;
}

interface KitchenOrder {
  id: number;
  reference: string;
  table_name: string | null;
  status: string;
  items: OrderItem[];
}

interface Props {
  orders: KitchenOrder[];
}

// ─── Item Status Badge ────────────────────────────────────────────────────────

function ItemBadge({ status }: { status: string }) {
  const map: Record<string, string> = {
    pending: 'bg-amber-100 text-amber-700',
    preparing: 'bg-blue-100 text-blue-700',
    served: 'bg-emerald-100 text-emerald-700',
  };
  const labels: Record<string, string> = {
    pending: 'En attente', preparing: 'En prép.', served: 'Servi',
  };

  return (
    <span className={`text-[10px] font-medium px-1.5 py-0.5 rounded-full ${map[status] ?? 'bg-slate-100 text-slate-500'}`}>
      {labels[status] ?? status}
    </span>
  );
}

// ─── Order Card ───────────────────────────────────────────────────────────────

function OrderCard({ order }: { order: KitchenOrder }) {
  const [loading, setLoading] = useState(false);

  const markReady = () => {
    setLoading(true);
    router.patch(`/fnb/orders/${order.id}/status`, { status: 'ready' }, {
      onFinish: () => setLoading(false),
      preserveScroll: true,
    });
  };

  const allServed = order.items.every((i) => i.status === 'served');

  return (
    <motion.div
      layout
      initial={{ opacity: 0, scale: 0.97 }}
      animate={{ opacity: 1, scale: 1 }}
      exit={{ opacity: 0, scale: 0.95 }}
      className={`rounded-2xl border p-4 shadow-sm bg-white ${order.status === 'preparing' ? 'border-blue-200' : 'border-slate-200'}`}
    >
      {/* Header */}
      <div className="flex items-center justify-between mb-3">
        <div>
          <p className="font-mono text-xs text-slate-400">{order.reference}</p>
          <p className="font-semibold text-slate-800 text-sm mt-0.5">{order.table_name ?? 'Sans table'}</p>
        </div>
        <span className={`text-xs font-medium px-2 py-0.5 rounded-full ${order.status === 'preparing' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700'}`}>
          {order.status === 'preparing' ? 'En prép.' : 'Reçue'}
        </span>
      </div>

      {/* Items */}
      <div className="space-y-2 mb-4">
        {order.items.map((item) => (
          <div key={item.id} className="flex items-start justify-between gap-2">
            <div className="flex items-center gap-2 min-w-0">
              <span className="shrink-0 h-5 w-5 rounded-full bg-slate-100 text-slate-600 text-xs flex items-center justify-center font-medium">
                {item.quantity}
              </span>
              <div className="min-w-0">
                <p className="text-sm text-slate-800 truncate">{item.name ?? '—'}</p>
                {item.notes && <p className="text-[11px] text-slate-400">{item.notes}</p>}
              </div>
            </div>
            <ItemBadge status={item.status} />
          </div>
        ))}
      </div>

      {/* Action */}
      {!allServed && (
        <button
          type="button"
          onClick={markReady}
          disabled={loading}
          className="w-full h-9 rounded-xl flex items-center justify-center gap-2 text-sm font-medium bg-slate-800 text-white hover:bg-slate-700 disabled:opacity-50 transition-colors"
        >
          <CheckCircle2 className="h-4 w-4" />
          {loading ? 'En cours…' : 'Marquer prête'}
        </button>
      )}
    </motion.div>
  );
}

// ─── Main ─────────────────────────────────────────────────────────────────────

export default function Kitchen({ orders }: Props) {
  const sent = orders.filter((o) => o.status === 'sent');
  const preparing = orders.filter((o) => o.status === 'preparing');

  return (
    <>
      <Head title="Écran Cuisine" />

      <div className="min-h-screen bg-slate-900 p-6">
        {/* Header */}
        <div className="flex items-center gap-3 mb-6">
          <div className="h-10 w-10 rounded-xl bg-slate-700 flex items-center justify-center">
            <ChefHat className="h-5 w-5 text-white" />
          </div>
          <div>
            <h1 className="text-lg font-semibold text-white">Écran Cuisine</h1>
            <p className="text-sm text-slate-400">
              {orders.length} commande{orders.length !== 1 ? 's' : ''} active{orders.length !== 1 ? 's' : ''}
            </p>
          </div>
          <div className="ml-auto flex items-center gap-2 text-sm text-slate-400">
            <Clock className="h-4 w-4" />
            <span>Temps réel</span>
          </div>
        </div>

        {/* Columns */}
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
          {/* Received */}
          <div>
            <div className="flex items-center gap-2 mb-3">
              <span className="h-2 w-2 rounded-full bg-amber-400" />
              <h2 className="text-sm font-medium text-slate-300">Reçues ({sent.length})</h2>
            </div>
            <div className="space-y-3">
              <AnimatePresence>
                {sent.length === 0 ? (
                  <p className="text-slate-600 text-sm text-center py-8">Aucune commande reçue</p>
                ) : (
                  sent.map((o) => <OrderCard key={o.id} order={o} />)
                )}
              </AnimatePresence>
            </div>
          </div>

          {/* Preparing */}
          <div>
            <div className="flex items-center gap-2 mb-3">
              <span className="h-2 w-2 rounded-full bg-blue-400" />
              <h2 className="text-sm font-medium text-slate-300">En préparation ({preparing.length})</h2>
            </div>
            <div className="space-y-3">
              <AnimatePresence>
                {preparing.length === 0 ? (
                  <p className="text-slate-600 text-sm text-center py-8">Aucune commande en cours</p>
                ) : (
                  preparing.map((o) => <OrderCard key={o.id} order={o} />)
                )}
              </AnimatePresence>
            </div>
          </div>
        </div>
      </div>
    </>
  );
}

Kitchen.layout = (page: React.ReactNode) => <>{page}</>;
