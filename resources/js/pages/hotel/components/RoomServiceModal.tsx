import { useForm, usePage } from '@inertiajs/react';
import { Minus, Plus, ShoppingBag, Utensils, X } from 'lucide-react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import type { Team } from '@/types';

interface Category {
    id: number;
    name: string;
}

interface MenuItem {
    id: number;
    name: string;
    price: number;
    category?: Category;
}

interface CartItem {
    menu_item_id: number;
    name: string;
    price: number;
    quantity: number;
}

interface Props {
    reservation: { id: number; room?: { number: string } };
    menuItems: MenuItem[];
}

const fmt = (n: number) => n.toLocaleString('fr-FR') + ' FCFA';

export default function RoomServiceModal({ reservation, menuItems }: Props) {
    const { currentTeam } = usePage().props as any;
    const team = currentTeam as Team;
    const [open, setOpen] = useState(false);
    const [cart, setCart] = useState<CartItem[]>([]);

    const { post, processing, reset } = useForm({});

    const grouped = menuItems.reduce<Record<string, MenuItem[]>>((acc, item) => {
        const cat = item.category?.name ?? 'Divers';
        if (!acc[cat]) acc[cat] = [];
        acc[cat].push(item);
        return acc;
    }, {});

    const cartTotal = cart.reduce((sum, i) => sum + i.price * i.quantity, 0);

    const addItem = (item: MenuItem) => {
        setCart(prev => {
            const existing = prev.find(c => c.menu_item_id === item.id);
            if (existing) {
                return prev.map(c =>
                    c.menu_item_id === item.id ? { ...c, quantity: c.quantity + 1 } : c
                );
            }
            return [...prev, { menu_item_id: item.id, name: item.name, price: item.price, quantity: 1 }];
        });
    };

    const removeItem = (id: number) => {
        setCart(prev => {
            const existing = prev.find(c => c.menu_item_id === id);
            if (!existing) return prev;
            if (existing.quantity === 1) return prev.filter(c => c.menu_item_id !== id);
            return prev.map(c => c.menu_item_id === id ? { ...c, quantity: c.quantity - 1 } : c);
        });
    };

    const getQty = (id: number) => cart.find(c => c.menu_item_id === id)?.quantity ?? 0;

    const handleSubmit = () => {
        if (cart.length === 0) return;
        post(
            route('fnb.room-service.store', { current_team: team.slug }),
            {
                data: {
                    reservation_id: reservation.id,
                    items: cart.map(c => ({ menu_item_id: c.menu_item_id, quantity: c.quantity })),
                },
                onSuccess: () => {
                    setCart([]);
                    setOpen(false);
                    reset();
                },
            } as any
        );
    };

    if (menuItems.length === 0) return null;

    return (
        <Dialog open={open} onOpenChange={setOpen}>
            <DialogTrigger asChild>
                <Button size="sm" variant="outline" className="gap-1.5 text-xs h-8">
                    <Utensils className="h-3.5 w-3.5" /> Room Service
                </Button>
            </DialogTrigger>
            <DialogContent className="max-w-lg max-h-[90vh] flex flex-col p-0">
                <DialogHeader className="px-5 pt-5 pb-4 border-b border-border flex-shrink-0">
                    <DialogTitle className="text-sm font-semibold flex items-center gap-2">
                        <Utensils className="h-4 w-4 text-blue-500" />
                        Room Service — Chambre {reservation.room?.number ?? '—'}
                    </DialogTitle>
                </DialogHeader>

                <div className="flex flex-col flex-1 overflow-hidden">
                    <div className="flex-1 overflow-y-auto px-5 py-4 space-y-5">
                        {Object.entries(grouped).map(([category, items]) => (
                            <div key={category}>
                                <p className="text-[10px] font-bold uppercase tracking-widest text-muted-foreground mb-2">{category}</p>
                                <div className="space-y-2">
                                    {items.map(item => {
                                        const qty = getQty(item.id);
                                        return (
                                            <div key={item.id} className="flex items-center justify-between py-2 border-b border-border/40 last:border-0">
                                                <div className="flex-1 min-w-0">
                                                    <p className="text-sm font-medium text-foreground truncate">{item.name}</p>
                                                    <p className="text-xs text-muted-foreground">{fmt(item.price)}</p>
                                                </div>
                                                <div className="flex items-center gap-2 ml-3">
                                                    {qty > 0 ? (
                                                        <>
                                                            <button
                                                                onClick={() => removeItem(item.id)}
                                                                className="w-7 h-7 rounded-full border border-border flex items-center justify-center hover:bg-muted transition-colors"
                                                            >
                                                                <Minus className="h-3 w-3" />
                                                            </button>
                                                            <span className="w-5 text-center text-sm font-semibold">{qty}</span>
                                                        </>
                                                    ) : (
                                                        <span className="w-16" />
                                                    )}
                                                    <button
                                                        onClick={() => addItem(item)}
                                                        className="w-7 h-7 rounded-full bg-blue-600 text-white flex items-center justify-center hover:bg-blue-500 transition-colors"
                                                    >
                                                        <Plus className="h-3 w-3" />
                                                    </button>
                                                </div>
                                            </div>
                                        );
                                    })}
                                </div>
                            </div>
                        ))}
                    </div>

                    {cart.length > 0 && (
                        <div className="px-5 py-4 border-t border-border bg-muted/30 flex-shrink-0 space-y-3">
                            <div className="space-y-1.5 max-h-28 overflow-y-auto">
                                {cart.map(c => (
                                    <div key={c.menu_item_id} className="flex items-center justify-between text-xs">
                                        <span className="text-foreground">{c.quantity}× {c.name}</span>
                                        <div className="flex items-center gap-2">
                                            <span className="text-muted-foreground">{fmt(c.price * c.quantity)}</span>
                                            <button onClick={() => setCart(prev => prev.filter(i => i.menu_item_id !== c.menu_item_id))}>
                                                <X className="h-3 w-3 text-muted-foreground hover:text-destructive" />
                                            </button>
                                        </div>
                                    </div>
                                ))}
                            </div>
                            <div className="flex items-center justify-between">
                                <span className="text-sm font-semibold">Total : {fmt(cartTotal)}</span>
                                <Button size="sm" className="gap-1.5 h-8 text-xs" onClick={handleSubmit} disabled={processing}>
                                    <ShoppingBag className="h-3.5 w-3.5" />
                                    Commander
                                </Button>
                            </div>
                        </div>
                    )}
                </div>
            </DialogContent>
        </Dialog>
    );
}
