import { usePage } from '@inertiajs/react';
import { ArrowLeft, User } from 'lucide-react';
import React from 'react';
import { Button } from '@/components/ui/button';
import type { Team } from '@/types';

export default function ClientShow({ client }: { client: any }) {
    return (
        <div className="p-6 max-w-3xl">
            <Button variant="ghost" className="mb-6 -ml-2 h-8 text-muted-foreground gap-1.5 text-sm" onClick={() => window.history.back()}>
                <ArrowLeft className="h-3.5 w-3.5" /> Retour
            </Button>
            
            <div className="rounded-xl border border-border bg-card/50 p-5">
                <h3 className="text-sm font-semibold text-foreground mb-4 flex items-center gap-2">
                    <User className="h-4 w-4 text-amber-500" />
                    Détails du Client: {client.name}
                </h3>
                
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <p className="text-xs text-muted-foreground">Code</p>
                        <p className="font-medium">{client.code}</p>
                    </div>
                    <div>
                        <p className="text-xs text-muted-foreground">Téléphone</p>
                        <p className="font-medium">{client.phone || '-'}</p>
                    </div>
                    <div>
                        <p className="text-xs text-muted-foreground">Email</p>
                        <p className="font-medium">{client.email || '-'}</p>
                    </div>
                    <div>
                        <p className="text-xs text-muted-foreground">Adresse</p>
                        <p className="font-medium">{client.address || '-'}</p>
                    </div>
                    <div>
                        <p className="text-xs text-muted-foreground">Statut</p>
                        <p className="font-medium">
                            <span className={`inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium ${client.is_active ? 'bg-emerald-500/10 text-emerald-500' : 'bg-red-500/10 text-red-500'}`}>
                                {client.is_active ? 'Actif' : 'Inactif'}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    );
}
