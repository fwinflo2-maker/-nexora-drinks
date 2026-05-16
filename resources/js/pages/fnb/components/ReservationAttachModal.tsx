import { router, usePage } from '@inertiajs/react';
import { AnimatePresence, motion } from 'framer-motion';
import { BedDouble, Loader2, Search, X } from 'lucide-react';
import { useCallback, useEffect, useRef, useState } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import type { Team } from '@/types';

interface ReservationResult {
    id: number;
    reference: string;
    check_in: string;
    check_out: string;
    room: { number: string; type: string };
    guest: { name: string; phone: string | null };
    pending_fnb_total: number;
}

interface Props {
    orderId: number;
    onClose: () => void;
}

export default function ReservationAttachModal({ orderId, onClose }: Props) {
    const { currentTeam } = usePage().props as any;
    const team = currentTeam as Team;

    const [roomNumber, setRoomNumber] = useState('');
    const [loading, setLoading] = useState(false);
    const [result, setResult] = useState<ReservationResult | null>(null);
    const [notFound, setNotFound] = useState(false);
    const [attaching, setAttaching] = useState(false);
    const debounceRef = useRef<ReturnType<typeof setTimeout> | null>(null);

    const search = useCallback((value: string) => {
        if (value.trim().length < 1) {
            setResult(null);
            setNotFound(false);
            return;
        }
        setLoading(true);
        setNotFound(false);
        fetch(route('fnb.room-search', { current_team: team.slug }) + `?room_number=${encodeURIComponent(value)}`, {
            headers: { Accept: 'application/json', 'X-Inertia': 'true' },
        })
            .then(r => r.json())
            .then(data => {
                setLoading(false);
                if (data.reservation) {
                    setResult(data.reservation);
                } else {
                    setResult(null);
                    setNotFound(true);
                }
            })
            .catch(() => {
                setLoading(false);
                setResult(null);
            });
    }, [team.slug]);

    useEffect(() => {
        if (debounceRef.current) clearTimeout(debounceRef.current);
        debounceRef.current = setTimeout(() => search(roomNumber), 400);
        return () => { if (debounceRef.current) clearTimeout(debounceRef.current); };
    }, [roomNumber, search]);

    const handleAttach = () => {
        if (!result) return;
        setAttaching(true);
        router.patch(
            route('fnb.orders.attach-reservation', { current_team: team.slug, order: orderId }),
            { reservation_id: result.id },
            { onFinish: () => { setAttaching(false); onClose(); } }
        );
    };

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm" onClick={onClose}>
            <motion.div
                initial={{ opacity: 0, scale: 0.96 }}
                animate={{ opacity: 1, scale: 1 }}
                exit={{ opacity: 0, scale: 0.96 }}
                transition={{ duration: 0.18 }}
                onClick={e => e.stopPropagation()}
                className="relative w-full max-w-md bg-background rounded-xl shadow-2xl border border-border p-5"
            >
                <div className="flex items-center justify-between mb-4">
                    <div className="flex items-center gap-2">
                        <BedDouble className="h-4 w-4 text-blue-500" />
                        <h2 className="font-semibold text-sm">Rattacher à une chambre</h2>
                    </div>
                    <button onClick={onClose} className="text-muted-foreground hover:text-foreground">
                        <X className="h-4 w-4" />
                    </button>
                </div>

                <div className="relative">
                    <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                    <Input
                        type="text"
                        placeholder="Numéro de chambre..."
                        value={roomNumber}
                        onChange={e => setRoomNumber(e.target.value)}
                        className="pl-9 h-9 text-sm"
                        autoFocus
                    />
                    {loading && <Loader2 className="absolute right-3 top-1/2 -translate-y-1/2 h-4 w-4 animate-spin text-muted-foreground" />}
                </div>

                <AnimatePresence>
                    {result && (
                        <motion.div
                            initial={{ opacity: 0, y: 8 }}
                            animate={{ opacity: 1, y: 0 }}
                            exit={{ opacity: 0 }}
                            className="mt-4 rounded-lg bg-muted/40 border border-border p-4 space-y-2"
                        >
                            <div className="flex items-center justify-between">
                                <span className="text-xs font-medium text-blue-500">{result.reference}</span>
                                <span className="text-xs text-muted-foreground">Chambre {result.room.number} — {result.room.type}</span>
                            </div>
                            <p className="text-sm font-medium">{result.guest.name}</p>
                            {result.guest.phone && <p className="text-xs text-muted-foreground">{result.guest.phone}</p>}
                            <p className="text-xs text-muted-foreground">{result.check_in} → {result.check_out}</p>
                            {result.pending_fnb_total > 0 && (
                                <p className="text-xs text-amber-500">Consommations en cours : {result.pending_fnb_total.toLocaleString('fr-FR')} FCFA</p>
                            )}
                            <Button size="sm" className="w-full h-8 text-xs mt-2 gap-1.5" onClick={handleAttach} disabled={attaching}>
                                {attaching ? <Loader2 className="h-3.5 w-3.5 animate-spin" /> : <BedDouble className="h-3.5 w-3.5" />}
                                Rattacher à {result.reference}
                            </Button>
                        </motion.div>
                    )}
                    {notFound && (
                        <motion.p initial={{ opacity: 0 }} animate={{ opacity: 1 }} className="mt-4 text-xs text-center text-muted-foreground">
                            Aucune réservation en cours pour la chambre « {roomNumber} »
                        </motion.p>
                    )}
                </AnimatePresence>
            </motion.div>
        </div>
    );
}
