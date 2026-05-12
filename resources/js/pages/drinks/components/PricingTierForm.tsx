import { useForm, usePage } from '@inertiajs/react';
import { Save, ArrowLeft, Edit } from 'lucide-react';
import type { FormEvent } from 'react';
import React from 'react';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { Team } from '@/types';

export default function PricingTierForm({ _action, pricingTier }: { _action: string, pricingTier?: any }) {
    const { currentTeam } = usePage().props;
    const team = currentTeam as Team;
    const item = pricingTier;

    const { data, setData, post, put, processing, errors } = useForm({
        name: item?.name ?? '',
        discount_rate: item?.discount_rate ?? 0,
        is_active: item?.is_active ?? true
    });

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();

        if (_action === 'edit' && item?.id) {
            put(route('drinks.pricing-tiers.update', { current_team: team.slug, pricingTier: item.id }));
        } else {
            post(route('drinks.pricing-tiers.store', { current_team: team.slug }));
        }
    };

    return (
        <div className="p-6 max-w-2xl">
            <Button variant="ghost" className="mb-6 -ml-2 h-8 text-muted-foreground gap-1.5 text-sm" onClick={() => window.history.back()}>
                <ArrowLeft className="h-3.5 w-3.5" /> Retour
            </Button>
            <form onSubmit={handleSubmit} className="space-y-5 rounded-xl border border-border bg-card/50 p-5">
                <h3 className="text-sm font-semibold text-foreground mb-4 flex items-center gap-2">
                    <Edit className="h-3.5 w-3.5 text-amber-500" />
                    {_action === 'edit' ? 'Modifier' : 'Nouveau'}
                </h3>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    
                        <div className="space-y-1.5">
                            <Label htmlFor="name" className="text-xs">Nom</Label>
                            <Input
                                id="name"
                                type="text"
                                
                                className="h-9 text-sm"
                                value={data.name}
                                onChange={e => setData('name', e.target.value)}
                            />
                            {errors.name && <p className="text-xs text-destructive">{errors.name}</p>}
                        </div>
                        <div className="space-y-1.5">
                            <Label htmlFor="discount_rate" className="text-xs">Taux de remise (%)</Label>
                            <Input
                                id="discount_rate"
                                type="number"
                                step="any"
                                className="h-9 text-sm"
                                value={data.discount_rate}
                                onChange={e => setData('discount_rate', Number(e.target.value))}
                            />
                            {errors.discount_rate && <p className="text-xs text-destructive">{errors.discount_rate}</p>}
                        </div>
                        <label className="flex items-center gap-2 cursor-pointer pt-4">
                            <Checkbox checked={data.is_active} onCheckedChange={v => setData('is_active', !!v)} />
                            <span className="text-sm font-medium">Actif</span>
                        </label>
                </div>
                <div className="flex justify-end pt-4 border-t border-border/50">
                    <Button type="submit" disabled={processing} className="gap-2 shadow-sm">
                        <Save className="h-4 w-4" /> Enregistrer
                    </Button>
                </div>
            </form>
        </div>
    );
}
