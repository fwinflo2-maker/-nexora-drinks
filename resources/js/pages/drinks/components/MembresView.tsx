import type { FormDataConvertible } from '@inertiajs/core';
import { router, usePage } from '@inertiajs/react';
import { motion, AnimatePresence } from 'framer-motion';
import { UserCircle, Shield, Trash2, Lock, Unlock, KeyRound, UserPlus, X, Mail, Fingerprint, Activity } from 'lucide-react';
import React, { useState } from 'react';
import { Button } from '@/components/ui/button';
import {
    Select, SelectContent, SelectItem, SelectTrigger, SelectValue,
} from '@/components/ui/select';
import type { Team } from '@/types';

interface Member {
    id: number;
    name: string;
    email: string;
    role: string;
    roleLabel: string;
    is_owner: boolean;
    is_admin: boolean;
    blocked_at: string | null;
}

interface Role { value: string; label: string }

interface Props {
    members: Member[];
    roles: Role[];
}

const ROLE_COLORS: Record<string, string> = {
    owner:      'bg-yellow-500/10 text-yellow-500 border-yellow-500/20',
    admin:      'bg-blue-500/10 text-blue-500 border-blue-500/20',
    gerant:     'bg-indigo-500/10 text-indigo-500 border-indigo-500/20',
    ops:        'bg-cyan-500/10 text-cyan-500 border-cyan-500/20',
    caissier:   'bg-emerald-500/10 text-emerald-500 border-emerald-500/20',
    comptable:  'bg-violet-500/10 text-violet-500 border-violet-500/20',
    magasinier: 'bg-amber-500/10 text-amber-500 border-amber-500/20',
};

function initials(name: string): string {
    return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
}

interface CreateForm { name: string; email: string; password: string; role: string }

export default function MembresView({ members, roles }: Props) {
    const { props } = usePage();
    const team = (props.currentTeam as Team) || {};
    const slug = team.slug || window.location.pathname.split('/')[1] || '';

    const [showCreate, setShowCreate] = useState(false);
    const [createForm, setCreateForm] = useState<CreateForm>({ name: '', email: '', password: '', role: roles[0]?.value ?? '' });
    const [createErrors, setCreateErrors] = useState<Partial<Record<keyof CreateForm, string>>>({});
    const [passwordModal, setPasswordModal] = useState<{ id: number; name: string } | null>(null);
    const [editProfileModal, setEditProfileModal] = useState<{ id: number; name: string; email: string } | null>(null);
    const [newPassword, setNewPassword] = useState('');
    const [passwordError, setPasswordError] = useState('');
    const [processing, setProcessing] = useState(false);

    const handleBlock = (member: Member) => {
        const action = member.blocked_at ? 'Débloquer' : 'Bloquer';
        if (!confirm(`${action} le profil de ${member.name} ?`)) return;

        const url = member.blocked_at 
            ? `/${slug}/drinks/membres/${member.id}/unblock`
            : `/${slug}/drinks/membres/${member.id}/block`;

        router.post(url, {}, { preserveScroll: true });
    };

    const handleCreate = () => {
        setProcessing(true);
        setCreateErrors({});
        router.post(`/${slug}/drinks/membres/store`, createForm as any, {
            onSuccess: () => {
                setShowCreate(false);
                setCreateForm({ name: '', email: '', password: '', role: roles[0]?.value ?? '' });
            },
            onError: (errors) => setCreateErrors(errors as any),
            onFinish: () => setProcessing(false),
            preserveScroll: true,
        });
    };

    const handlePasswordSave = () => {
        if (!passwordModal) return;
        if (newPassword.length < 8) {
            setPasswordError('Minimum 8 caractères.');
            return;
        }

        router.post(`/${slug}/drinks/membres/${passwordModal.id}/password`, { password: newPassword }, {
            onSuccess: () => {
                setPasswordModal(null);
                setNewPassword('');
                setPasswordError('');
            },
            onError: (err: any) => setPasswordError(err.password || 'Erreur lors de la mise à jour.'),
            preserveScroll: true,
        });
    };

    const handleEditProfileSave = () => {
        if (!editProfileModal) return;
        router.post(`/${slug}/drinks/membres/${editProfileModal.id}/profile`, {
            name: editProfileModal.name,
            email: editProfileModal.email
        }, {
            onSuccess: () => setEditProfileModal(null),
            preserveScroll: true,
        });
    };

    const handleRemove = (id: number, name: string) => {
        if (!confirm(`Retirer ${name} de l'équipe ?`)) return;
        router.post(`/${slug}/drinks/membres/${id}/remove`, {}, { preserveScroll: true });
    };

    return (
        <div className="p-6 space-y-8 max-w-7xl mx-auto">
            {/* Header Section */}
            <div className="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-card/50 backdrop-blur-md border border-border p-6 rounded-2xl shadow-sm">
                <div className="flex items-center gap-4">
                    <div className="w-12 h-12 bg-amber-500/10 rounded-xl flex items-center justify-center text-amber-500 shadow-inner">
                        <UserCircle className="h-7 w-7" />
                    </div>
                    <div>
                        <h2 className="text-xl font-bold text-foreground tracking-tight">Gestion de l'Équipe</h2>
                        <p className="text-sm text-muted-foreground">Pilotez les accès et rôles de vos collaborateurs Nexora.</p>
                    </div>
                </div>
                <div className="flex items-center gap-3">
                    <div className="hidden sm:flex flex-col items-end px-4 border-r border-border">
                        <span className="text-xs font-bold text-foreground">{members.length}</span>
                        <span className="text-[10px] text-muted-foreground uppercase tracking-widest">Membres</span>
                    </div>
                    <Button onClick={() => setShowCreate(true)} className="gap-2 bg-primary hover:bg-primary/90 text-primary-foreground shadow-lg shadow-primary/20 transition-all active:scale-95">
                        <UserPlus className="h-4 w-4" />
                        Ajouter un collaborateur
                    </Button>
                </div>
            </div>

            {/* Members Grid/Table */}
            <div className="grid gap-4">
                <AnimatePresence mode="popLayout">
                    {members.map((member, index) => (
                        <motion.div
                            key={member.id}
                            initial={{ opacity: 0, y: 20 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ delay: index * 0.05 }}
                            className={`group relative bg-card border border-border rounded-2xl p-5 hover:border-amber-500/30 hover:shadow-xl hover:shadow-amber-500/[0.02] transition-all duration-300 ${member.blocked_at ? 'opacity-70 bg-muted/30' : ''}`}
                        >
                            <div className="flex flex-col lg:flex-row lg:items-center gap-6">
                                {/* User Info */}
                                <div className="flex items-center gap-4 lg:w-1/3">
                                    <div className="relative">
                                        <div className="w-12 h-12 rounded-xl bg-gradient-to-br from-muted to-muted/50 flex items-center justify-center border border-border group-hover:scale-105 transition-transform duration-300">
                                            <span className="text-base font-bold text-foreground/80">{initials(member.name)}</span>
                                        </div>
                                        {!member.blocked_at && (
                                            <div className="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full bg-emerald-500 border-2 border-card shadow-sm" />
                                        )}
                                    </div>
                                    <div className="min-w-0">
                                        <h3 className="font-bold text-foreground text-sm truncate">{member.name}</h3>
                                        <div className="flex items-center gap-1.5 text-[11px] text-muted-foreground">
                                            <Mail className="h-3 w-3" />
                                            <span className="truncate">{member.email}</span>
                                        </div>
                                    </div>
                                </div>

                                {/* Status & Role */}
                                <div className="lg:w-1/3 flex items-center gap-3">
                                    <span className={`px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border ${ROLE_COLORS[member.role] || 'bg-muted text-muted-foreground border-border'}`}>
                                        {member.roleLabel}
                                    </span>
                                    {member.is_owner && (
                                        <span className="p-1 bg-amber-500/10 text-amber-500 rounded-lg" title="Propriétaire">
                                            <Shield className="h-3.5 w-3.5" />
                                        </span>
                                    )}
                                    {(member.is_admin || member.is_owner) ? (
                                        <span className="text-[10px] text-muted-foreground flex items-center gap-1.5 italic">
                                            <Lock className="h-3 w-3" /> Protégé
                                        </span>
                                    ) : (
                                        <span className="text-[10px] text-muted-foreground flex items-center gap-1.5">
                                            <Activity className="h-3 w-3 text-emerald-500" /> Actif
                                        </span>
                                    )}
                                </div>

                                {/* Actions */}
                                <div className="flex items-center justify-end gap-1 lg:w-1/3 ml-auto">
                                    {!member.is_owner && !member.is_admin && (
                                        <>
                                            <Button 
                                                variant="ghost" 
                                                size="sm" 
                                                onClick={() => setEditProfileModal({ id: member.id, name: member.name, email: member.email })}
                                                className="h-8 w-8 p-0 hover:bg-amber-500/10 hover:text-amber-500"
                                                title="Modifier le profil"
                                            >
                                                <Fingerprint className="h-4 w-4" />
                                            </Button>
                                            <Button 
                                                variant="ghost" 
                                                size="sm" 
                                                onClick={() => setPasswordModal({ id: member.id, name: member.name })}
                                                className="h-8 w-8 p-0 hover:bg-amber-500/10 hover:text-amber-500"
                                                title="Changer mot de passe"
                                            >
                                                <KeyRound className="h-4 w-4" />
                                            </Button>
                                            <Button 
                                                variant="ghost" 
                                                size="sm" 
                                                onClick={() => handleBlock(member)}
                                                className={`h-8 w-8 p-0 ${member.blocked_at ? 'text-red-500 hover:bg-red-500/10' : 'hover:bg-orange-500/10 hover:text-orange-500'}`}
                                                title={member.blocked_at ? "Débloquer" : "Bloquer"}
                                            >
                                                {member.blocked_at ? <Unlock className="h-4 w-4" /> : <Lock className="h-4 w-4" />}
                                            </Button>
                                            <Button 
                                                variant="ghost" 
                                                size="sm" 
                                                onClick={() => handleRemove(member.id, member.name)}
                                                className="h-8 w-8 p-0 text-muted-foreground hover:bg-red-500/10 hover:text-red-500"
                                                title="Retirer de l'équipe"
                                            >
                                                <Trash2 className="h-4 w-4" />
                                            </Button>
                                        </>
                                    )}
                                </div>
                            </div>
                        </motion.div>
                    ))}
                </AnimatePresence>
            </div>

            {/* Modals & Overlays */}
            <AnimatePresence>
                {showCreate && (
                    <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
                        <motion.div initial={{ opacity: 0 }} animate={{ opacity: 1 }} exit={{ opacity: 0 }} onClick={() => setShowCreate(false)} className="absolute inset-0 bg-background/80 backdrop-blur-sm" />
                        <motion.div initial={{ scale: 0.95, opacity: 0 }} animate={{ scale: 1, opacity: 1 }} exit={{ scale: 0.95, opacity: 0 }} className="relative w-full max-w-md bg-card border border-border p-8 rounded-3xl shadow-2xl">
                            <div className="flex items-center justify-between mb-8">
                                <h3 className="text-xl font-bold text-foreground">Nouveau Collaborateur</h3>
                                <Button variant="ghost" size="sm" onClick={() => setShowCreate(false)} className="h-8 w-8 p-0 rounded-full hover:bg-muted">
                                    <X className="h-4 w-4" />
                                </Button>
                            </div>

                            <div className="space-y-5">
                                <div className="space-y-1.5">
                                    <label className="text-xs font-bold text-muted-foreground uppercase tracking-widest ml-1">Nom Complet</label>
                                    <input 
                                        type="text" 
                                        value={createForm.name} 
                                        onChange={e => setCreateForm(f => ({ ...f, name: e.target.value }))}
                                        className="w-full h-11 bg-muted/30 border-border rounded-xl px-4 text-sm focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all"
                                        placeholder="Jean Dupont"
                                    />
                                    {createErrors.name && <p className="text-[10px] text-red-500 ml-1 font-medium">{createErrors.name}</p>}
                                </div>
                                <div className="space-y-1.5">
                                    <label className="text-xs font-bold text-muted-foreground uppercase tracking-widest ml-1">Adresse Email</label>
                                    <input 
                                        type="email" 
                                        value={createForm.email} 
                                        onChange={e => setCreateForm(f => ({ ...f, email: e.target.value }))}
                                        className="w-full h-11 bg-muted/30 border-border rounded-xl px-4 text-sm focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all"
                                        placeholder="jean@entreprise.com"
                                    />
                                    {createErrors.email && <p className="text-[10px] text-red-500 ml-1 font-medium">{createErrors.email}</p>}
                                </div>
                                <div className="grid grid-cols-2 gap-4">
                                    <div className="space-y-1.5">
                                        <label className="text-xs font-bold text-muted-foreground uppercase tracking-widest ml-1">Mot de passe</label>
                                        <input 
                                            type="password" 
                                            value={createForm.password} 
                                            onChange={e => setCreateForm(f => ({ ...f, password: e.target.value }))}
                                            className="w-full h-11 bg-muted/30 border-border rounded-xl px-4 text-sm focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all"
                                            placeholder="••••••••"
                                        />
                                        {createErrors.password && <p className="text-[10px] text-red-500 ml-1 font-medium">{createErrors.password}</p>}
                                    </div>
                                    <div className="space-y-1.5">
                                        <label className="text-xs font-bold text-muted-foreground uppercase tracking-widest ml-1">Rôle initial</label>
                                        <Select value={createForm.role} onValueChange={(val) => setCreateForm(f => ({ ...f, role: val }))}>
                                            <SelectTrigger className="h-11 bg-muted/30 border-border rounded-xl text-sm">
                                                <SelectValue />
                                            </SelectTrigger>
                                            <SelectContent>
                                                {roles.map(r => <SelectItem key={r.value} value={r.value}>{r.label}</SelectItem>)}
                                            </SelectContent>
                                        </Select>
                                    </div>
                                </div>

                                {/* Role Description Helper */}
                                <motion.div 
                                    key={createForm.role}
                                    initial={{ opacity: 0, height: 0 }}
                                    animate={{ opacity: 1, height: 'auto' }}
                                    className="p-3 bg-amber-500/5 border border-amber-500/10 rounded-xl"
                                >
                                    <p className="text-[10px] font-bold text-amber-600 uppercase tracking-widest mb-1 flex items-center gap-1.5">
                                        <Shield className="h-3 w-3" /> Périmètre du rôle
                                    </p>
                                    <p className="text-[11px] text-muted-foreground leading-relaxed">
                                        {createForm.role === 'gerant' && "Pilotage complet : stocks, ventes, finance et rapports. Peut tout valider."}
                                        {createForm.role === 'ops' && "Opérations : approvisionnements, fournisseurs, inventaires et mouvements de stock."}
                                        {createForm.role === 'magasinier' && "Terrain : gestion physique du stock, emballages, inventaires et pertes."}
                                        {createForm.role === 'caissier' && "Ventes : encaissements, gestion des clients et suivi des règlements."}
                                        {createForm.role === 'comptable' && "Finance : gestion des charges, apports, versements banque et brouillard."}
                                        {createForm.role === 'admin' && "Administration : contrôle total, y compris la gestion de l'équipe."}
                                    </p>
                                </motion.div>
                            </div>

                            <Button onClick={handleCreate} disabled={processing} className="w-full h-11 mt-8 bg-primary hover:bg-primary/90 text-primary-foreground font-bold shadow-lg shadow-primary/20">
                                {processing ? 'Création en cours...' : 'Confirmer la création'}
                            </Button>
                        </motion.div>
                    </div>
                )}

                {passwordModal && (
                    <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
                        <motion.div initial={{ opacity: 0 }} animate={{ opacity: 1 }} exit={{ opacity: 0 }} onClick={() => setPasswordModal(null)} className="absolute inset-0 bg-background/80 backdrop-blur-sm" />
                        <motion.div initial={{ scale: 0.95, opacity: 0 }} animate={{ scale: 1, opacity: 1 }} exit={{ scale: 0.95, opacity: 0 }} className="relative w-full max-w-sm bg-card border border-border p-8 rounded-3xl shadow-2xl">
                            <div className="flex items-center gap-3 mb-6">
                                <div className="w-10 h-10 bg-amber-500/10 rounded-xl flex items-center justify-center text-amber-500">
                                    <KeyRound className="h-5 w-5" />
                                </div>
                                <div>
                                    <h3 className="font-bold text-foreground">Mot de passe</h3>
                                    <p className="text-xs text-muted-foreground truncate max-w-[200px]">Pour {passwordModal.name}</p>
                                </div>
                            </div>

                            <div className="space-y-4">
                                <div className="space-y-1.5">
                                    <label className="text-[10px] font-bold text-muted-foreground uppercase tracking-[0.2em] ml-1">Nouveau mot de passe</label>
                                    <input 
                                        type="password" 
                                        value={newPassword} 
                                        onChange={e => setNewPassword(e.target.value)}
                                        className="w-full h-11 bg-muted/30 border-border rounded-xl px-4 text-sm focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all"
                                        placeholder="••••••••"
                                        autoFocus
                                    />
                                    {passwordError && <p className="text-[10px] text-red-500 ml-1 font-medium">{passwordError}</p>}
                                </div>
                            </div>

                            <div className="flex gap-3 mt-8">
                                <Button variant="ghost" onClick={() => setPasswordModal(null)} className="flex-1 h-11 rounded-xl">Annuler</Button>
                                <Button onClick={handlePasswordSave} disabled={processing} className="flex-1 h-11 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-xl shadow-lg shadow-amber-500/20">
                                    Mettre à jour
                                </Button>
                            </div>
                        </motion.div>
                    </div>
                )}
                {editProfileModal && (
                    <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
                        <motion.div initial={{ opacity: 0 }} animate={{ opacity: 1 }} exit={{ opacity: 0 }} onClick={() => setEditProfileModal(null)} className="absolute inset-0 bg-background/80 backdrop-blur-sm" />
                        <motion.div initial={{ scale: 0.95, opacity: 0 }} animate={{ scale: 1, opacity: 1 }} exit={{ scale: 0.95, opacity: 0 }} className="relative w-full max-w-sm bg-card border border-border p-8 rounded-3xl shadow-2xl">
                            <div className="flex items-center gap-3 mb-6">
                                <div className="w-10 h-10 bg-amber-500/10 rounded-xl flex items-center justify-center text-amber-500">
                                    <Fingerprint className="h-5 w-5" />
                                </div>
                                <h3 className="font-bold text-foreground">Modifier le profil</h3>
                            </div>

                            <div className="space-y-4">
                                <div className="space-y-1.5">
                                    <label className="text-[10px] font-bold text-muted-foreground uppercase tracking-[0.2em] ml-1">Nom complet</label>
                                    <input 
                                        type="text" 
                                        value={editProfileModal.name} 
                                        onChange={e => setEditProfileModal(f => f ? { ...f, name: e.target.value } : null)}
                                        className="w-full h-11 bg-muted/30 border-border rounded-xl px-4 text-sm focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all"
                                        placeholder="Nom"
                                    />
                                </div>
                                <div className="space-y-1.5">
                                    <label className="text-[10px] font-bold text-muted-foreground uppercase tracking-[0.2em] ml-1">Adresse Email</label>
                                    <input 
                                        type="email" 
                                        value={editProfileModal.email} 
                                        onChange={e => setEditProfileModal(f => f ? { ...f, email: e.target.value } : null)}
                                        className="w-full h-11 bg-muted/30 border-border rounded-xl px-4 text-sm focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all"
                                        placeholder="Email"
                                    />
                                </div>
                            </div>

                            <div className="flex gap-3 mt-8">
                                <Button variant="ghost" onClick={() => setEditProfileModal(null)} className="flex-1 h-11 rounded-xl">Annuler</Button>
                                <Button onClick={handleEditProfileSave} disabled={processing} className="flex-1 h-11 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-xl shadow-lg shadow-amber-500/20">
                                    Enregistrer
                                </Button>
                            </div>
                        </motion.div>
                    </div>
                )}
            </AnimatePresence>
        </div>
    );
}
