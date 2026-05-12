import type { FormDataConvertible } from '@inertiajs/core';
import { router, usePage } from '@inertiajs/react';
import { Settings, User, Pencil, X, Check, Camera, Upload } from 'lucide-react';
import React, { useRef, useState } from 'react';
import { Button } from '@/components/ui/button';
import type { Team } from '@/types';

interface Member {
    id: number;
    name: string;
    email: string;
    role: string;
    roleLabel: string;
    is_owner: boolean;
    blocked_at: string | null;
}

interface Props {
    members: Member[];
    role: string;
}

const ROLE_COLORS: Record<string, string> = {
    owner:      'bg-yellow-500/10 text-yellow-400 ring-1 ring-yellow-500/20',
    admin:      'bg-blue-500/10 text-blue-400 ring-1 ring-blue-500/20',
    gerant:     'bg-indigo-500/10 text-indigo-400 ring-1 ring-indigo-500/20',
    ops:        'bg-cyan-500/10 text-cyan-400 ring-1 ring-cyan-500/20',
    caissier:   'bg-green-500/10 text-green-400 ring-1 ring-green-500/20',
    comptable:  'bg-purple-500/10 text-purple-400 ring-1 ring-purple-500/20',
    magasinier: 'bg-amber-500/10 text-amber-400 ring-1 ring-amber-500/20',
};

function initials(name: string): string {
    return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
}

interface EditForm { name: string; email: string }

export default function ProfileSettingsView({ members, role }: Props) {
    const { currentTeam, auth } = usePage().props as any;
    const team = currentTeam as Team & { logo_url?: string | null };
    const slug = team.slug;
    const currentUser = auth?.user;

    const isAdmin = ['admin', 'owner', 'gerant'].includes(role.toLowerCase());

    const [editing, setEditing] = useState<number | null>(null);
    const [editForm, setEditForm] = useState<EditForm>({ name: '', email: '' });
    const [editErrors, setEditErrors] = useState<Partial<EditForm>>({});
    const [processing, setProcessing] = useState(false);

    // Avatar upload state
    const avatarInputRef = useRef<HTMLInputElement>(null);
    const [avatarPreview, setAvatarPreview] = useState<string | null>(null);
    const [avatarFile, setAvatarFile] = useState<File | null>(null);
    const [avatarUploading, setAvatarUploading] = useState(false);

    const handleAvatarChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0];

        if (!file) {
return;
}

        setAvatarFile(file);
        setAvatarPreview(URL.createObjectURL(file));
    };

    const saveAvatar = () => {
        if (!avatarFile) {
return;
}

        setAvatarUploading(true);
        const formData = new FormData();
        formData.append('_method', 'PATCH');
        formData.append('name', currentUser.name);
        formData.append('email', currentUser.email);
        formData.append('avatar', avatarFile);
        router.post(route('profile.update'), formData, {
            forceFormData: true,
            onSuccess: () => {
 setAvatarPreview(null); setAvatarFile(null); 
},
            onFinish: () => setAvatarUploading(false),
            preserveScroll: true,
        });
    };

    const startEdit = (member: Member) => {
        setEditing(member.id);
        setEditForm({ name: member.name, email: member.email });
        setEditErrors({});
    };

    const cancelEdit = () => {
        setEditing(null);
        setEditErrors({});
    };

    const saveProfile = (memberId: number) => {
        setProcessing(true);
        setEditErrors({});
        router.patch(
            route('drinks.membres.update-profile', { current_team: slug, user: memberId }),
            editForm as unknown as Record<string, FormDataConvertible>,
            {
                onSuccess: () => setEditing(null),
                onError: (errors) => setEditErrors(errors as Partial<EditForm>),
                onFinish: () => setProcessing(false),
                preserveScroll: true,
            }
        );
    };

    return (
        <div className="p-6 space-y-6">
            {/* ── Mon profil ───────────────────────────────── */}
            <div className="rounded-xl border border-amber-500/20 bg-amber-500/5 p-4 space-y-3">
                <div className="flex items-center gap-2 mb-1">
                    <Camera className="h-4 w-4 text-amber-500" />
                    <h2 className="text-sm font-semibold text-foreground">Ma photo de profil</h2>
                </div>
                <div className="flex items-center gap-4">
                    {/* Avatar display */}
                    <div className="relative flex-shrink-0">
                        {(avatarPreview || currentUser?.avatar_url) ? (
                            <img
                                src={avatarPreview ?? currentUser?.avatar_url}
                                alt={currentUser?.name}
                                className="w-16 h-16 rounded-full object-cover border-2 border-amber-500/40"
                            />
                        ) : (
                            <div className="w-16 h-16 rounded-full bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center text-white font-bold text-xl">
                                {initials(currentUser?.name ?? 'U')}
                            </div>
                        )}
                        <button
                            onClick={() => avatarInputRef.current?.click()}
                            className="absolute -bottom-1 -right-1 w-6 h-6 bg-amber-500 hover:bg-amber-400 rounded-full flex items-center justify-center shadow-md transition-colors"
                            title="Changer la photo"
                        >
                            <Pencil className="h-3 w-3 text-white" />
                        </button>
                    </div>
                    <div className="flex-1">
                        <p className="text-sm font-medium text-foreground">{currentUser?.name}</p>
                        <p className="text-xs text-muted-foreground">{currentUser?.email}</p>
                        <input
                            ref={avatarInputRef}
                            type="file"
                            accept="image/*"
                            className="hidden"
                            onChange={handleAvatarChange}
                        />
                        {avatarFile ? (
                            <div className="flex items-center gap-2 mt-2">
                                <Button size="sm" onClick={saveAvatar} disabled={avatarUploading} className="h-7 text-xs gap-1.5">
                                    <Upload className="h-3 w-3" />
                                    {avatarUploading ? 'Envoi...' : 'Enregistrer'}
                                </Button>
                                <Button size="sm" variant="ghost" onClick={() => {
 setAvatarFile(null); setAvatarPreview(null); 
}} className="h-7 text-xs">
                                    <X className="h-3 w-3" />
                                </Button>
                            </div>
                        ) : (
                            <button onClick={() => avatarInputRef.current?.click()} className="mt-1.5 text-xs text-amber-500 hover:underline">
                                Changer la photo…
                            </button>
                        )}
                    </div>
                </div>
            </div>

            {/* ── Header liste membres ─────────────────────── */}
            <div className="flex items-center gap-2">
                <Settings className="h-4 w-4 text-amber-500" />
                <h2 className="text-base font-semibold text-foreground">Paramètres des profils</h2>
                <span className="ml-auto text-xs text-muted-foreground">{members.length} profil{members.length > 1 ? 's' : ''}</span>
            </div>
            <p className="text-xs text-muted-foreground -mt-4">
                Modifiez le nom et l'adresse e-mail de chaque profil collaborateur.
            </p>

            <div className="space-y-3">
                {members.map(member => (
                    <div
                        key={member.id}
                        className="rounded-xl border border-border bg-card overflow-hidden"
                    >
                        {editing === member.id ? (
                            /* Edit mode */
                            <div className="p-4 space-y-3">
                                <div className="flex items-center gap-3 mb-3">
                                    <div className="w-9 h-9 rounded-full bg-muted flex items-center justify-center flex-shrink-0">
                                        <span className="text-xs font-medium text-foreground">{initials(editForm.name || member.name)}</span>
                                    </div>
                                    <span className={`inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ${ROLE_COLORS[member.role] ?? 'bg-muted text-muted-foreground'}`}>
                                        {member.roleLabel}
                                    </span>
                                </div>
                                <div className="grid grid-cols-2 gap-3">
                                    <div>
                                        <label className="text-xs text-muted-foreground mb-1 block">Nom complet</label>
                                        <input
                                            type="text"
                                            value={editForm.name}
                                            onChange={e => setEditForm(f => ({ ...f, name: e.target.value }))}
                                            className="w-full h-9 rounded-lg border border-border bg-background px-3 text-sm text-foreground focus:outline-none focus:ring-1 focus:ring-amber-500"
                                        />
                                        {editErrors.name && <p className="text-xs text-red-400 mt-1">{editErrors.name}</p>}
                                    </div>
                                    <div>
                                        <label className="text-xs text-muted-foreground mb-1 block">Adresse e-mail</label>
                                        <input
                                            type="email"
                                            value={editForm.email}
                                            onChange={e => setEditForm(f => ({ ...f, email: e.target.value }))}
                                            className="w-full h-9 rounded-lg border border-border bg-background px-3 text-sm text-foreground focus:outline-none focus:ring-1 focus:ring-amber-500"
                                        />
                                        {editErrors.email && <p className="text-xs text-red-400 mt-1">{editErrors.email}</p>}
                                    </div>
                                </div>
                                <div className="flex justify-end gap-2">
                                    <Button variant="ghost" size="sm" onClick={cancelEdit}>
                                        <X className="h-3.5 w-3.5 mr-1" /> Annuler
                                    </Button>
                                    <Button size="sm" onClick={() => saveProfile(member.id)} disabled={processing}>
                                        <Check className="h-3.5 w-3.5 mr-1" />
                                        {processing ? 'Enregistrement...' : 'Enregistrer'}
                                    </Button>
                                </div>
                            </div>
                        ) : (
                            /* View mode */
                            <div className="px-4 py-3 flex items-center gap-3">
                                <div className="w-9 h-9 rounded-full bg-muted flex items-center justify-center flex-shrink-0">
                                    <span className="text-xs font-medium text-foreground">{initials(member.name)}</span>
                                </div>
                                <div className="flex-1 min-w-0">
                                    <div className="flex items-center gap-2">
                                        <p className="text-sm font-medium text-foreground truncate">{member.name}</p>
                                        {member.blocked_at && (
                                            <span className="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-red-500/10 text-red-400">Bloqué</span>
                                        )}
                                    </div>
                                    <p className="text-xs text-muted-foreground truncate">{member.email}</p>
                                </div>
                                <span className={`inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium flex-shrink-0 ${ROLE_COLORS[member.role] ?? 'bg-muted text-muted-foreground'}`}>
                                    {member.roleLabel}
                                </span>
                                {!member.is_owner && (
                                    <button
                                        onClick={() => startEdit(member)}
                                        className="p-1.5 rounded-lg text-muted-foreground hover:text-amber-400 hover:bg-amber-500/10 transition-colors flex-shrink-0"
                                        title="Modifier ce profil"
                                    >
                                        <Pencil className="h-3.5 w-3.5" />
                                    </button>
                                )}
                                {member.is_owner && (
                                    <div className="w-8 flex-shrink-0" />
                                )}
                            </div>
                        )}
                    </div>
                ))}
            </div>

            <div className="rounded-lg bg-muted/40 border border-border px-4 py-3 text-xs text-muted-foreground flex items-start gap-2">
                <User className="h-3.5 w-3.5 mt-0.5 flex-shrink-0 text-amber-500" />
                <span>
                    La modification de l'email change l'identifiant de connexion. Le propriétaire ne peut pas être modifié depuis cette interface.
                </span>
            </div>

            {/* ── Identité visuelle (Admin only) ─────────── */}
            {isAdmin && (
                <BrandingSection team={team} slug={slug} />
            )}
        </div>
    );
}

function BrandingSection({ team, slug }: { team: any, slug: string }) {
    const [logoFile, setLogoFile] = useState<File | null>(null);
    const [logoPreview, setLogoPreview] = useState<string | null>(null);
    const [phone, setPhone] = useState(team.settings_json?.phone || '');
    const [address, setAddress] = useState(team.settings_json?.address || '');
    const [rccm, setRccm] = useState(team.settings_json?.rccm || '');
    const [processing, setProcessing] = useState(false);
    const logoInputRef = useRef<HTMLInputElement>(null);

    const handleLogoChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0];

        if (!file) {
return;
}

        setLogoFile(file);
        setLogoPreview(URL.createObjectURL(file));
    };

    const saveBranding = () => {
        setProcessing(true);
        const formData = new FormData();

        if (logoFile) {
formData.append('logo', logoFile);
}

        formData.append('phone', phone);
        formData.append('address', address);
        formData.append('rccm', rccm);

        router.post(route('drinks.settings.update-branding', { current_team: slug }), formData, {
            forceFormData: true,
            onSuccess: () => {
 setLogoFile(null); setLogoPreview(null); 
},
            onFinish: () => setProcessing(false),
            preserveScroll: true,
        });
    };

    return (
        <div className="rounded-xl border border-border bg-card p-4 space-y-4">
            <div className="flex items-center gap-2 mb-1">
                <Camera className="h-4 w-4 text-amber-500" />
                <h2 className="text-sm font-semibold text-foreground">Identité visuelle & Entête PDF</h2>
            </div>

            <div className="flex items-start gap-6">
                {/* Logo Section */}
                <div className="space-y-2">
                    <p className="text-[10px] uppercase font-bold text-muted-foreground tracking-wider">Logo de l'établissement</p>
                    <div className="relative group w-24 h-24 rounded-lg border-2 border-dashed border-border bg-muted/30 flex items-center justify-center overflow-hidden transition-colors hover:border-amber-500/50">
                        {(logoPreview || team.logo_url) ? (
                            <img src={logoPreview ?? team.logo_url} className="w-full h-full object-contain p-1" />
                        ) : (
                            <div className="text-center p-2">
                                <Upload className="h-5 w-5 mx-auto text-muted-foreground mb-1" />
                                <span className="text-[9px] text-muted-foreground">PNG/JPG max 2MB</span>
                            </div>
                        )}
                        <button
                            onClick={() => logoInputRef.current?.click()}
                            className="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center"
                        >
                            <Pencil className="h-5 w-5 text-white" />
                        </button>
                    </div>
                    <input ref={logoInputRef} type="file" className="hidden" accept="image/*" onChange={handleLogoChange} />
                </div>

                {/* Info Section */}
                <div className="flex-1 grid grid-cols-2 gap-3">
                    <div className="col-span-1">
                        <label className="text-[10px] uppercase font-bold text-muted-foreground tracking-wider mb-1 block">Téléphone</label>
                        <input
                            type="text"
                            value={phone}
                            onChange={e => setPhone(e.target.value)}
                            placeholder="+243 ..."
                            className="w-full h-9 rounded-lg border border-border bg-background px-3 text-sm focus:ring-1 focus:ring-amber-500"
                        />
                    </div>
                    <div className="col-span-1">
                        <label className="text-[10px] uppercase font-bold text-muted-foreground tracking-wider mb-1 block">RCCM / ID Nat</label>
                        <input
                            type="text"
                            value={rccm}
                            onChange={e => setRccm(e.target.value)}
                            placeholder="CD/GOM/..."
                            className="w-full h-9 rounded-lg border border-border bg-background px-3 text-sm focus:ring-1 focus:ring-amber-500"
                        />
                    </div>
                    <div className="col-span-2">
                        <label className="text-[10px] uppercase font-bold text-muted-foreground tracking-wider mb-1 block">Adresse physique</label>
                        <input
                            type="text"
                            value={address}
                            onChange={e => setAddress(e.target.value)}
                            placeholder="N°, Avenue, Quartier, Ville..."
                            className="w-full h-9 rounded-lg border border-border bg-background px-3 text-sm focus:ring-1 focus:ring-amber-500"
                        />
                    </div>
                </div>
            </div>

            <div className="flex justify-end pt-2 border-t border-border">
                <Button size="sm" onClick={saveBranding} disabled={processing} className="h-8">
                    <Check className="h-3.5 w-3.5 mr-1.5" />
                    {processing ? 'Mise à jour...' : 'Sauvegarder l\'identité'}
                </Button>
            </div>
        </div>
    );
}

