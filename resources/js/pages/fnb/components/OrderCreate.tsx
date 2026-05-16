import { router } from '@inertiajs/react';
import { motion, AnimatePresence } from 'framer-motion';
import { Plus, Minus, Trash2, ShoppingBag, ChevronRight } from 'lucide-react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Textarea } from '@/components/ui/textarea';
import { Label } from '@/components/ui/label';
import { usePage } from '@inertiajs/react';
import type { Team } from '@/types';

interface FnBTable {
    id: number;
    name: string;
    capacity: number;
}

interface MenuItem {
    id: number;
    name: string;
    price: number;
    description: string | null;
}

interface Category {
    id: number;
    name: string;
    color: string | null;
    icon: string | null;
    menu_items: MenuItem[];
}

interface Props {
    tables: FnBTable[];
    categories: Category[];
}

interface CartItem {
    menu_item_id: number;
    name: string;
    price: number;
    quantity: number;
    notes: string;
}

export default function OrderCreate({ tables, categories }: Props) {
    const { currentTeam } = usePage().props as any;
    const team = currentTeam as Team & { slug?: string };
    const slug = team?.slug;

    const [tableId, setTableId] = useState('');
    const [notes, setNotes] = useState('');
    const [cart, setCart] = useState<CartItem[]>([]);
    const [activeCategory, setActiveCategory] = useState(categories[0]?.id ?? 0);
    const [processing, setProcessing] = useState(false);
    const [editNoteId, setEditNoteId] = useState<number | null>(null);

    const currentCategory = categories.find(c => c.id === activeCategory);

    const addItem = (item: MenuItem) => {
        setCart(prev => {
            const existing = prev.find(c => c.menu_item_id === item.id);
            if (existing) {
                return prev.map(c => c.menu_item_id === item.id ? { ...c, quantity: c.quantity + 1 } : c);
            }
            return [...prev, { menu_item_id: item.id, name: item.name, price: item.price, quantity: 1, notes: '' }];
        });
    };

    const updateQty = (menuItemId: number, delta: number) => {
        setCart(prev => {
            const updated = prev.map(c => c.menu_item_id === menuItemId ? { ...c, quantity: Math.max(0, c.quantity + delta) } : c);
            return updated.filter(c => c.quantity > 0);
        });
    };

    const setItemNote = (menuItemId: number, note: string) => {
        setCart(prev => prev.map(c => c.menu_item_id === menuItemId ? { ...c, notes: note } : c));
    };

    const total = cart.reduce((sum, c) => sum + c.price * c.quantity, 0);
    const itemCount = cart.reduce((sum, c) => sum + c.quantity, 0);

    const cartQty = (menuItemId: number) => cart.find(c => c.menu_item_id === menuItemId)?.quantity ?? 0;

    const submit = () => {
        if (!tableId || cart.length === 0) return;
        setProcessing(true);
        router.post(
            route('fnb.orders.store', { current_team: slug }),
            {
                table_id: parseInt(tableId),
                notes: notes || null,
                items: cart.map(c => ({
                    menu_item_id: c.menu_item_id,
                    quantity: c.quantity,
                    notes: c.notes || null,
                })),
            },
            { onFinish: () => setProcessing(false) }
        );
    };

    return (
        <div className="flex flex-col lg:flex-row gap-4 h-full">
            {/* Left: Menu */}
            <div className="flex-1 flex flex-col gap-4 min-w-0">
                {/* Table select */}
                <div className="bg-card border border-border rounded-xl p-4">
                    <Label htmlFor="table-select" className="text-xs font-semibold uppercase tracking-wider text-muted-foreground mb-2 block">
                        Table
                    </Label>
                    <select
                        id="table-select"
                        value={tableId}
                        onChange={e => setTableId(e.target.value)}
                        className="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm"
                    >
                        <option value="">— Sélectionner une table —</option>
                        {tables.map(t => (
                            <option key={t.id} value={String(t.id)}>{t.name} ({t.capacity} couverts)</option>
                        ))}
                    </select>
                </div>

                {/* Category tabs */}
                <div className="flex gap-1 overflow-x-auto pb-1">
                    {categories.map(c => (
                        <button
                            key={c.id}
                            onClick={() => setActiveCategory(c.id)}
                            className={`flex-shrink-0 flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors ${
                                activeCategory === c.id
                                    ? 'bg-emerald-600 text-white'
                                    : 'bg-muted text-muted-foreground hover:text-foreground'
                            }`}
                        >
                            {c.icon && <span>{c.icon}</span>}
                            {c.name}
                        </button>
                    ))}
                </div>

                {/* Menu items grid */}
                <div className="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    <AnimatePresence mode="popLayout">
                        {currentCategory?.menu_items.map(item => {
                            const qty = cartQty(item.id);
                            return (
                                <motion.button
                                    key={item.id}
                                    layout
                                    initial={{ opacity: 0, scale: 0.97 }}
                                    animate={{ opacity: 1, scale: 1 }}
                                    exit={{ opacity: 0, scale: 0.95 }}
                                    onClick={() => addItem(item)}
                                    className={`relative bg-card border rounded-xl p-4 text-left transition-all hover:shadow-md active:scale-[0.98] ${
                                        qty > 0
                                            ? 'border-emerald-400 shadow-sm shadow-emerald-100'
                                            : 'border-border hover:border-emerald-200'
                                    }`}
                                >
                                    {qty > 0 && (
                                        <span className="absolute top-2 right-2 h-5 w-5 rounded-full bg-emerald-600 text-white text-[10px] font-bold flex items-center justify-center">
                                            {qty}
                                        </span>
                                    )}
                                    <p className="text-sm font-medium text-foreground pr-6">{item.name}</p>
                                    {item.description && (
                                        <p className="text-[11px] text-muted-foreground mt-0.5 line-clamp-2">{item.description}</p>
                                    )}
                                    <p className="text-sm font-semibold text-emerald-600 mt-2">
                                        {Number(item.price).toLocaleString()}
                                    </p>
                                </motion.button>
                            );
                        })}
                    </AnimatePresence>
                    {(currentCategory?.menu_items.length ?? 0) === 0 && (
                        <p className="col-span-full text-center py-8 text-muted-foreground text-sm">
                            Aucun article dans cette catégorie.
                        </p>
                    )}
                </div>
            </div>

            {/* Right: Cart */}
            <div className="w-full lg:w-80 flex-shrink-0 flex flex-col gap-4">
                <div className="bg-card border border-border rounded-xl overflow-hidden flex flex-col">
                    <div className="px-4 py-3 border-b border-border flex items-center gap-2">
                        <ShoppingBag className="h-4 w-4 text-muted-foreground" />
                        <span className="text-sm font-semibold text-foreground">Commande</span>
                        {itemCount > 0 && (
                            <span className="ml-auto text-xs bg-emerald-600 text-white rounded-full px-2 py-0.5">
                                {itemCount}
                            </span>
                        )}
                    </div>

                    <div className="flex-1 overflow-y-auto max-h-[360px]">
                        {cart.length === 0 ? (
                            <p className="text-xs text-muted-foreground text-center py-8">
                                Sélectionnez des articles
                            </p>
                        ) : (
                            <div className="divide-y divide-border">
                                {cart.map(item => (
                                    <div key={item.menu_item_id} className="px-4 py-3">
                                        <div className="flex items-start justify-between gap-2">
                                            <div className="flex-1 min-w-0">
                                                <p className="text-sm font-medium text-foreground truncate">{item.name}</p>
                                                <p className="text-xs text-muted-foreground">
                                                    {Number(item.price).toLocaleString()} × {item.quantity} = {(item.price * item.quantity).toLocaleString()}
                                                </p>
                                            </div>
                                            <div className="flex items-center gap-1 flex-shrink-0">
                                                <button
                                                    onClick={() => updateQty(item.menu_item_id, -1)}
                                                    className="h-5 w-5 rounded-md bg-muted flex items-center justify-center hover:bg-muted/80"
                                                >
                                                    <Minus className="h-3 w-3" />
                                                </button>
                                                <span className="text-xs font-medium w-5 text-center">{item.quantity}</span>
                                                <button
                                                    onClick={() => updateQty(item.menu_item_id, 1)}
                                                    className="h-5 w-5 rounded-md bg-muted flex items-center justify-center hover:bg-muted/80"
                                                >
                                                    <Plus className="h-3 w-3" />
                                                </button>
                                            </div>
                                        </div>
                                        <button
                                            onClick={() => setEditNoteId(editNoteId === item.menu_item_id ? null : item.menu_item_id)}
                                            className="text-[11px] text-muted-foreground hover:text-foreground mt-1"
                                        >
                                            {item.notes || '+ Note...'}
                                        </button>
                                        {editNoteId === item.menu_item_id && (
                                            <input
                                                autoFocus
                                                className="mt-1 w-full text-xs border border-input rounded-md px-2 py-1 bg-transparent"
                                                value={item.notes}
                                                onChange={e => setItemNote(item.menu_item_id, e.target.value)}
                                                onBlur={() => setEditNoteId(null)}
                                                placeholder="Note pour cet article..."
                                            />
                                        )}
                                    </div>
                                ))}
                            </div>
                        )}
                    </div>

                    {cart.length > 0 && (
                        <div className="border-t border-border px-4 py-3 space-y-3">
                            <div className="flex justify-between text-sm font-semibold text-foreground">
                                <span>Total</span>
                                <span>{total.toLocaleString()}</span>
                            </div>
                            <Textarea
                                placeholder="Notes pour la commande..."
                                value={notes}
                                onChange={e => setNotes(e.target.value)}
                                rows={2}
                                className="text-xs"
                            />
                            <Button
                                className="w-full"
                                disabled={processing || !tableId || cart.length === 0}
                                onClick={submit}
                            >
                                Créer la commande
                                <ChevronRight className="h-4 w-4 ml-1" />
                            </Button>
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
}
