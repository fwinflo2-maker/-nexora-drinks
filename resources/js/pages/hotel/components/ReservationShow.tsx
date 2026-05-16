import { Link, router, useForm, usePage } from '@inertiajs/react';
import { ArrowLeft, LogIn, LogOut, XCircle, Plus, Trash2, BedDouble, User, CalendarCheck, Receipt, ReceiptText } from 'lucide-react';
import type { FormEvent } from 'react';
import React from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { StatusBadge } from './DashboardView';
import RoomServiceModal from './RoomServiceModal';
import type { Team } from '@/types';

function FolioAddForm({ reservationId }: { reservationId: number }) {
    const { currentTeam } = usePage().props;
    const team = currentTeam as Team;

    const { data, setData, post, processing, errors, reset } = useForm({
        label: '',
        amount: '',
        type: 'service',
    });

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();
        post(route('hotel.reservations.folios.store', { current_team: team.slug, reservation: reservationId }), {
            onSuccess: () => reset(),
        });
    };

    return (
        <form onSubmit={handleSubmit} className="mt-4 p-4 rounded-lg bg-muted/30 border border-border/50">
            <p className="text-xs font-semibold text-muted-foreground mb-3">Ajouter une ligne</p>
            <div className="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <div className="sm:col-span-2 space-y-1">
                    <Label className="text-xs">Libellé *</Label>
                    <Input type="text" className="h-8 text-xs" value={data.label} onChange={e => setData('label', e.target.value)} />
                    {errors.label && <p className="text-xs text-destructive">{errors.label}</p>}
                </div>
                <div className="space-y-1">
                    <Label className="text-xs">Montant *</Label>
                    <Input type="number" step="0.01" className="h-8 text-xs" value={data.amount} onChange={e => setData('amount', e.target.value)} />
                    {errors.amount && <p className="text-xs text-destructive">{errors.amount}</p>}
                </div>
                <div className="space-y-1">
                    <Label className="text-xs">Type</Label>
                    <select
                        className="w-full h-8 rounded-md border border-input bg-background px-2 text-xs focus:outline-none"
                        value={data.type}
                        onChange={e => setData('type', e.target.value)}
                    >
                        <option value="room">Chambre</option>
                        <option value="service">Service</option>
                        <option value="extra">Extra</option>
                        <option value="discount">Remise</option>
                    </select>
                </div>
            </div>
            <div className="flex justify-end mt-3">
                <Button type="submit" size="sm" disabled={processing} className="gap-1.5 h-8 text-xs">
                    <Plus className="h-3.5 w-3.5" /> Ajouter
                </Button>
            </div>
        </form>
    );
}

export default function ReservationShow({ reservation, fnb_menu_items = [] }: { reservation: any; fnb_menu_items?: any[] }) {
    const { currentTeam } = usePage().props;
    const team = currentTeam as Team;
    const item = reservation ?? {};
    const folios = item.folios ?? [];
    const slug = team.slug;

    const canCheckIn = item.status === 'confirmed';
    const canCheckOut = item.status === 'checked_in';
    const canCancel = item.status === 'pending' || item.status === 'confirmed';

    const handleCheckIn = () => {
        if (!confirm('Effectuer le check-in ?')) return;
        router.post(route('hotel.reservations.check-in', { current_team: slug, reservation: item.id }));
    };

    const handleCheckOut = () => {
        if (!confirm('Effectuer le check-out ?')) return;
        router.post(route('hotel.reservations.check-out', { current_team: slug, reservation: item.id }));
    };

    const handleCancel = () => {
        if (!confirm('Annuler cette réservation ?')) return;
        router.post(route('hotel.reservations.cancel', { current_team: slug, reservation: item.id }));
    };

    const handleDeleteFolio = (folioId: number) => {
        if (!confirm('Supprimer cette ligne ?')) return;
        router.delete(route('hotel.reservations.folios.destroy', { current_team: slug, reservation: item.id, folio: folioId }));
    };

    const FOLIO_TYPES: Record<string, string> = { room: 'Chambre', service: 'Service', extra: 'Extra', discount: 'Remise' };
    const folioTotal = folios.reduce((sum: number, f: any) => {
        return f.type === 'discount' ? sum - Number(f.amount) : sum + Number(f.amount);
    }, 0);

    return (
        <div className="p-6 max-w-3xl space-y-4">
            <div className="flex items-center justify-between">
                <Button variant="ghost" className="-ml-2 h-8 text-muted-foreground gap-1.5 text-sm" onClick={() => window.history.back()}>
                    <ArrowLeft className="h-3.5 w-3.5" /> Retour
                </Button>
                <div className="flex flex-wrap gap-2">
                    {canCheckIn && (
                        <Button onClick={handleCheckIn} size="sm" className="gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs">
                            <LogIn className="h-3.5 w-3.5" /> Check-in
                        </Button>
                    )}
                    {canCheckOut && (
                        <>
                            <RoomServiceModal reservation={item} menuItems={fnb_menu_items} />
                            <Link href={route('hotel.reservations.fnb-orders', { current_team: slug, reservation: item.id })}>
                                <Button size="sm" variant="outline" className="gap-1.5 text-xs h-8">
                                    <ReceiptText className="h-3.5 w-3.5" /> F&B
                                </Button>
                            </Link>
                            <Link href={route('hotel.reservations.folio', { current_team: slug, reservation: item.id })}>
                                <Button size="sm" variant="outline" className="gap-1.5 text-xs h-8">
                                    <Receipt className="h-3.5 w-3.5" /> Folio
                                </Button>
                            </Link>
                            <Button onClick={handleCheckOut} size="sm" className="gap-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs">
                                <LogOut className="h-3.5 w-3.5" /> Check-out
                            </Button>
                        </>
                    )}
                    {canCancel && (
                        <Button onClick={handleCancel} variant="outline" size="sm" className="gap-1.5 text-red-500 border-red-200 hover:bg-red-50 text-xs">
                            <XCircle className="h-3.5 w-3.5" /> Annuler
                        </Button>
                    )}
                </div>
            </div>

            <div className="rounded-xl border border-border bg-card p-5">
                <div className="flex items-center justify-between mb-4">
                    <div>
                        <p className="text-xs text-muted-foreground mb-1">Référence</p>
                        <p className="font-mono font-bold text-foreground text-lg">{item.reference}</p>
                    </div>
                    <StatusBadge status={item.status} />
                </div>

                <div className="grid grid-cols-2 sm:grid-cols-3 gap-4 text-sm">
                    <div className="flex items-start gap-2">
                        <BedDouble className="h-4 w-4 text-blue-500 mt-0.5 flex-shrink-0" />
                        <div>
                            <p className="text-xs text-muted-foreground">Chambre</p>
                            <p className="font-medium text-foreground">{item.room?.number ?? '—'}</p>
                            <p className="text-xs text-muted-foreground">{item.room?.room_type?.name ?? ''}</p>
                        </div>
                    </div>
                    <div className="flex items-start gap-2">
                        <User className="h-4 w-4 text-blue-500 mt-0.5 flex-shrink-0" />
                        <div>
                            <p className="text-xs text-muted-foreground">Client</p>
                            <Link
                                href={route('hotel.guests.show', { current_team: slug, guest: item.guest?.id })}
                                className="font-medium text-foreground hover:text-blue-500 transition-colors"
                            >
                                {item.guest?.name ?? '—'}
                            </Link>
                        </div>
                    </div>
                    <div className="flex items-start gap-2">
                        <CalendarCheck className="h-4 w-4 text-blue-500 mt-0.5 flex-shrink-0" />
                        <div>
                            <p className="text-xs text-muted-foreground">Séjour</p>
                            <p className="font-medium text-foreground">{item.check_in ?? '—'} → {item.check_out ?? '—'}</p>
                            <p className="text-xs text-muted-foreground">{item.nights ?? 0} nuit(s)</p>
                        </div>
                    </div>
                </div>

                {item.notes && (
                    <div className="mt-4 pt-4 border-t border-border/50">
                        <p className="text-xs text-muted-foreground mb-1">Notes</p>
                        <p className="text-sm text-foreground">{item.notes}</p>
                    </div>
                )}
            </div>

            <div className="rounded-xl border border-border bg-card overflow-hidden">
                <div className="px-5 py-4 border-b border-border flex items-center gap-2">
                    <Receipt className="h-4 w-4 text-blue-500" />
                    <h3 className="text-sm font-semibold text-foreground">Folio</h3>
                    <span className="ml-auto text-sm font-bold text-foreground">
                        Total : {folioTotal.toLocaleString()}
                    </span>
                </div>

                {folios.length === 0 ? (
                    <p className="py-6 text-center text-sm text-muted-foreground">Aucune ligne de folio.</p>
                ) : (
                    <div className="overflow-x-auto">
                        <table className="w-full text-sm">
                            <thead className="bg-muted/50 border-b border-border">
                                <tr>
                                    <th className="h-9 px-4 text-left font-medium text-muted-foreground">Libellé</th>
                                    <th className="h-9 px-4 text-left font-medium text-muted-foreground">Type</th>
                                    <th className="h-9 px-4 text-right font-medium text-muted-foreground">Montant</th>
                                    <th className="h-9 px-4 text-right font-medium text-muted-foreground"></th>
                                </tr>
                            </thead>
                            <tbody>
                                {folios.map((f: any) => (
                                    <tr key={f.id} className="border-b border-border/50 hover:bg-muted/20">
                                        <td className="p-3 text-foreground">{f.label}</td>
                                        <td className="p-3 text-muted-foreground">{FOLIO_TYPES[f.type] ?? f.type}</td>
                                        <td className={`p-3 text-right font-medium ${f.type === 'discount' ? 'text-red-500' : 'text-foreground'}`}>
                                            {f.type === 'discount' ? '-' : ''}{Number(f.amount).toLocaleString()}
                                        </td>
                                        <td className="p-3 text-right">
                                            <button
                                                onClick={() => handleDeleteFolio(f.id)}
                                                className="inline-flex items-center justify-center rounded h-7 w-7 hover:bg-red-50 transition-colors"
                                            >
                                                <Trash2 className="h-3.5 w-3.5 text-red-400" />
                                            </button>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                )}

                {(canCheckIn || canCheckOut || item.status === 'checked_in') && (
                    <div className="p-4 border-t border-border/50">
                        <FolioAddForm reservationId={item.id} />
                    </div>
                )}
            </div>

            <div className="rounded-xl border border-border bg-card p-5">
                <p className="text-xs text-muted-foreground mb-1">Montant réservation</p>
                <p className="text-2xl font-bold text-foreground">{item.total_price?.toLocaleString() ?? '—'}</p>
            </div>
        </div>
    );
}
