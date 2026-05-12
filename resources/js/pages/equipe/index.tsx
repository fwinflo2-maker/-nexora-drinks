import { useState } from 'react';
import { Head, router, useForm } from '@inertiajs/react';
import { motion, AnimatePresence } from 'framer-motion';
import {
    Users, Plus, X, Pencil, Trash2, Shield, UserCheck,
    Mail, Crown, Briefcase
} from 'lucide-react';
import * as EquipeController from '@/actions/App/Http/Controllers/EquipeController';

const fadeUp = {
    hidden: { opacity: 0, y: 16 },
    visible: { opacity: 1, y: 0, transition: { duration: 0.4, ease: [0.22, 1, 0.36, 1] } },
};

const container = {
    hidden: { opacity: 0 },
    visible: { opacity: 1, transition: { staggerChildren: 0.05 } },
};

type TeamRole = 'owner' | 'admin' | 'logisticien' | 'commercial' | 'magasinier' | 'comptable' | 'member';

interface Member {
    id: number;
    name: string;
    email: string;
    role: TeamRole;
    poste?: string | null;
    extra_roles?: string[];
    joined_at?: string;
}

interface Team {
    id: number;
    name: string;
    slug: string;
}

interface Props {
    team: Team;
    members: Member[];
}

const roleConfig: Record<TeamRole, { label: string; className: string; icon: React.ElementType }> = {
    owner: { label: 'Propriétaire', className: 'bg-purple-500/10 text-purple-500', icon: Crown },
    admin: { label: 'Administrateur', className: 'bg-blue-500/10 text-blue-500', icon: Shield },
    logisticien: { label: 'Logisticien', className: 'bg-amber-500/10 text-amber-500', icon: UserCheck },
    commercial: { label: 'Commercial', className: 'bg-emerald-500/10 text-emerald-500', icon: UserCheck },
    magasinier: { label: 'Magasinier', className: 'bg-orange-500/10 text-orange-500', icon: UserCheck },
    comptable: { label: 'Comptable', className: 'bg-indigo-500/10 text-indigo-500', icon: UserCheck },
    member: { label: 'Membre', className: 'bg-gray-500/10 text-gray-500', icon: UserCheck },
};

const availableRoles: TeamRole[] = ['admin', 'logisticien', 'commercial', 'magasinier', 'comptable', 'member'];

function getInitials(name: string): string {
    return name
        .split(' ')
        .map((n) => n[0])
        .join('')
        .toUpperCase()
        .slice(0, 2);
}

export default function EquipeIndex({ team, members }: Props) {
    const [isInviteModalOpen, setIsInviteModalOpen] = useState(false);
    const [editingMember, setEditingMember] = useState<Member | null>(null);
    const [memberToDelete, setMemberToDelete] = useState<Member | null>(null);

    const inviteForm = useForm({
        email: '',
        role: 'member' as TeamRole,
        poste: '',
        extra_roles: [] as string[],
    });

    const editForm = useForm({
        role: 'member' as TeamRole,
        poste: '',
        extra_roles: [] as string[],
    });

    const handleInvite = (e: React.FormEvent) => {
        e.preventDefault();
        inviteForm.post(EquipeController.store({ current_team: team.slug }).url, {
            onSuccess: () => {
                setIsInviteModalOpen(false);
                inviteForm.reset();
            },
        });
    };

    const handleEditSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        if (!editingMember) return;
        editForm.patch(
            EquipeController.update({
                current_team: team.slug,
                membership: editingMember.id,
            }).url,
            {
                onSuccess: () => {
                    setEditingMember(null);
                    editForm.reset();
                },
            },
        );
    };

    const handleDelete = () => {
        if (!memberToDelete) return;
        router.delete(
            EquipeController.destroy({
                current_team: team.slug,
                membership: memberToDelete.id,
            }).url,
            {
                onSuccess: () => setMemberToDelete(null),
            },
        );
    };

    const openEdit = (member: Member) => {
        setEditingMember(member);
        editForm.setData({
            role: member.role,
            poste: member.poste ?? '',
            extra_roles: member.extra_roles ?? [],
        });
    };

    return (
        <div className="flex flex-col gap-6 p-6 lg:p-8 w-full min-h-screen bg-background text-foreground">
            <Head title="Equipe — NEXORA" />

            {/* Header */}
            <motion.div
                initial={{ opacity: 0, y: -8 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.4 }}
                className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4"
            >
                <div>
                    <div className="flex items-center gap-2 mb-1">
                        <Users className="h-5 w-5 text-primary" />
                        <span className="text-xs font-semibold text-primary uppercase tracking-wider">
                            Gestion Equipe
                        </span>
                    </div>
                    <h1 className="text-2xl font-bold tracking-tight">Membres de l'equipe</h1>
                    <p className="text-sm text-muted-foreground mt-1">
                        {members.length} membre{members.length > 1 ? 's' : ''} dans {team.name}
                    </p>
                </div>
                <button
                    onClick={() => setIsInviteModalOpen(true)}
                    className="inline-flex items-center gap-2 rounded-lg bg-primary text-primary-foreground px-4 py-2.5 text-sm font-semibold hover:brightness-110 transition-all shadow-lg shadow-primary/20"
                >
                    <Plus className="h-4 w-4" />
                    Inviter un membre
                </button>
            </motion.div>

            {/* Grille membres */}
            <motion.div
                variants={container}
                initial="hidden"
                animate="visible"
                className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4"
            >
                {members.map((member) => {
                    const config = roleConfig[member.role] ?? roleConfig.member;
                    const Icon = config.icon;

                    return (
                        <motion.div
                            key={member.id}
                            variants={fadeUp}
                            className="rounded-2xl border border-border bg-card p-5 flex items-start gap-4 hover:shadow-md transition-shadow"
                        >
                            <div className="h-11 w-11 rounded-xl bg-primary/10 text-primary flex items-center justify-center font-bold text-sm shrink-0">
                                {getInitials(member.name)}
                            </div>
                            <div className="flex-1 min-w-0">
                                <div className="flex items-start justify-between gap-2">
                                    <div className="min-w-0">
                                        <p className="font-semibold text-foreground truncate">{member.name}</p>
                                        <div className="flex items-center gap-1 mt-0.5">
                                            <Mail className="h-3 w-3 text-muted-foreground shrink-0" />
                                            <p className="text-xs text-muted-foreground truncate">{member.email}</p>
                                        </div>
                                    </div>
                                    {member.role !== 'owner' && (
                                        <div className="flex items-center gap-1 shrink-0">
                                            <button
                                                onClick={() => openEdit(member)}
                                                className="p-1.5 rounded-lg text-muted-foreground hover:bg-secondary hover:text-foreground transition-colors"
                                                title="Modifier le role"
                                            >
                                                <Pencil className="h-3.5 w-3.5" />
                                            </button>
                                            <button
                                                onClick={() => setMemberToDelete(member)}
                                                className="p-1.5 rounded-lg text-muted-foreground hover:bg-rose-500/10 hover:text-rose-500 transition-colors"
                                                title="Retirer de l'equipe"
                                            >
                                                <Trash2 className="h-3.5 w-3.5" />
                                            </button>
                                        </div>
                                    )}
                                </div>
                                <div className="mt-3">
                                    <span
                                        className={`inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium ${config.className}`}
                                    >
                                        <Icon className="h-3 w-3" />
                                        {config.label}
                                    </span>
                                    {member.poste && (
                                        <div className="flex items-center gap-1 mt-1.5">
                                            <Briefcase className="h-3 w-3 text-muted-foreground shrink-0" />
                                            <span className="text-xs text-muted-foreground">{member.poste}</span>
                                        </div>
                                    )}
                                    {member.extra_roles && member.extra_roles.length > 0 && (
                                        <div className="flex flex-wrap gap-1 mt-1.5">
                                            {member.extra_roles.map((r) => (
                                                <span key={r} className="text-xs bg-secondary/80 text-foreground px-2 py-0.5 rounded-full">
                                                    +{roleConfig[r as TeamRole]?.label ?? r}
                                                </span>
                                            ))}
                                        </div>
                                    )}
                                </div>
                            </div>
                        </motion.div>
                    );
                })}
            </motion.div>

            {members.length === 0 && (
                <div className="flex flex-col items-center justify-center py-16 text-muted-foreground">
                    <Users className="h-12 w-12 mb-4 opacity-20" />
                    <p className="text-sm">Aucun membre dans cette equipe.</p>
                </div>
            )}

            {/* Modal invitation */}
            <AnimatePresence>
                {isInviteModalOpen && (
                    <>
                        <motion.div
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                            exit={{ opacity: 0 }}
                            className="fixed inset-0 z-50 bg-background/80 backdrop-blur-sm"
                            onClick={() => setIsInviteModalOpen(false)}
                        />
                        <motion.div
                            initial={{ opacity: 0, scale: 0.95, y: 20 }}
                            animate={{ opacity: 1, scale: 1, y: 0 }}
                            exit={{ opacity: 0, scale: 0.95, y: 20 }}
                            className="fixed z-50 left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md p-6 rounded-2xl border border-border bg-card shadow-2xl"
                        >
                            <div className="flex items-center justify-between mb-5">
                                <h2 className="text-lg font-bold text-foreground">Inviter un membre</h2>
                                <button
                                    onClick={() => setIsInviteModalOpen(false)}
                                    className="p-1 rounded-md text-muted-foreground hover:bg-secondary transition-colors"
                                >
                                    <X className="h-5 w-5" />
                                </button>
                            </div>

                            <form onSubmit={handleInvite} className="flex flex-col gap-4">
                                <div className="grid gap-1.5">
                                    <label className="text-sm font-medium">Adresse email *</label>
                                    <input
                                        type="email"
                                        required
                                        value={inviteForm.data.email}
                                        onChange={(e) => inviteForm.setData('email', e.target.value)}
                                        className="h-10 w-full rounded-lg border border-input bg-background px-3 text-sm focus:outline-none focus:ring-1 focus:ring-primary"
                                        placeholder="email@exemple.com"
                                    />
                                    {inviteForm.errors.email && (
                                        <span className="text-xs text-rose-500">{inviteForm.errors.email}</span>
                                    )}
                                </div>

                                <div className="grid gap-1.5">
                                    <label className="text-sm font-medium">Role *</label>
                                    <select
                                        value={inviteForm.data.role}
                                        onChange={(e) => inviteForm.setData('role', e.target.value as TeamRole)}
                                        className="h-10 w-full rounded-lg border border-input bg-background px-3 text-sm focus:outline-none focus:ring-1 focus:ring-primary"
                                    >
                                        {availableRoles.map((role) => (
                                            <option key={role} value={role}>
                                                {roleConfig[role].label}
                                            </option>
                                        ))}
                                    </select>
                                    {inviteForm.errors.role && (
                                        <span className="text-xs text-rose-500">{inviteForm.errors.role}</span>
                                    )}
                                </div>

                                <div className="grid gap-1.5">
                                    <label className="text-sm font-medium">Poste <span className="text-muted-foreground text-xs">(optionnel)</span></label>
                                    <input
                                        type="text"
                                        value={inviteForm.data.poste}
                                        onChange={(e) => inviteForm.setData('poste', e.target.value)}
                                        className="h-10 w-full rounded-lg border border-input bg-background px-3 text-sm focus:outline-none focus:ring-1 focus:ring-primary"
                                        placeholder="Ex: Chef de dépôt, Livreur Nord..."
                                    />
                                </div>

                                <div className="grid gap-1.5">
                                    <label className="text-sm font-medium">Rôles supplémentaires <span className="text-muted-foreground text-xs">(hybride, optionnel)</span></label>
                                    <div className="flex flex-wrap gap-3">
                                        {(['admin', 'logisticien', 'commercial', 'magasinier', 'comptable'] as const).map((r) => (
                                            <label key={r} className="flex items-center gap-1.5 cursor-pointer select-none">
                                                <input
                                                    type="checkbox"
                                                    checked={inviteForm.data.extra_roles.includes(r)}
                                                    onChange={(e) => {
                                                        const current = inviteForm.data.extra_roles;
                                                        inviteForm.setData('extra_roles', e.target.checked ? [...current, r] : current.filter((x) => x !== r));
                                                    }}
                                                    className="rounded border-border"
                                                />
                                                <span className="text-xs text-foreground">{roleConfig[r]?.label ?? r}</span>
                                            </label>
                                        ))}
                                    </div>
                                </div>

                                <div className="mt-2 flex justify-end gap-3">
                                    <button
                                        type="button"
                                        onClick={() => setIsInviteModalOpen(false)}
                                        className="h-10 px-4 rounded-lg border border-border text-sm font-medium hover:bg-secondary transition-colors"
                                    >
                                        Annuler
                                    </button>
                                    <button
                                        type="submit"
                                        disabled={inviteForm.processing}
                                        className="h-10 px-6 rounded-lg bg-primary text-primary-foreground text-sm font-bold hover:brightness-110 transition-all disabled:opacity-50"
                                    >
                                        {inviteForm.processing ? 'Envoi...' : "Envoyer l'invitation"}
                                    </button>
                                </div>
                            </form>
                        </motion.div>
                    </>
                )}
            </AnimatePresence>

            {/* Modal edition role */}
            <AnimatePresence>
                {editingMember && (
                    <>
                        <motion.div
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                            exit={{ opacity: 0 }}
                            className="fixed inset-0 z-50 bg-background/80 backdrop-blur-sm"
                            onClick={() => setEditingMember(null)}
                        />
                        <motion.div
                            initial={{ opacity: 0, scale: 0.95, y: 20 }}
                            animate={{ opacity: 1, scale: 1, y: 0 }}
                            exit={{ opacity: 0, scale: 0.95, y: 20 }}
                            className="fixed z-50 left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md p-6 rounded-2xl border border-border bg-card shadow-2xl"
                        >
                            <div className="flex items-center justify-between mb-5">
                                <h2 className="text-lg font-bold text-foreground">Modifier le role</h2>
                                <button
                                    onClick={() => setEditingMember(null)}
                                    className="p-1 rounded-md text-muted-foreground hover:bg-secondary transition-colors"
                                >
                                    <X className="h-5 w-5" />
                                </button>
                            </div>
                            <p className="text-sm text-muted-foreground mb-4">
                                Role de <span className="font-semibold text-foreground">{editingMember.name}</span>
                            </p>

                            <form onSubmit={handleEditSubmit} className="flex flex-col gap-4">
                                <div className="grid gap-1.5">
                                    <label className="text-sm font-medium">Nouveau role *</label>
                                    <select
                                        value={editForm.data.role}
                                        onChange={(e) => editForm.setData('role', e.target.value as TeamRole)}
                                        className="h-10 w-full rounded-lg border border-input bg-background px-3 text-sm focus:outline-none focus:ring-1 focus:ring-primary"
                                    >
                                        {availableRoles.map((role) => (
                                            <option key={role} value={role}>
                                                {roleConfig[role].label}
                                            </option>
                                        ))}
                                    </select>
                                </div>

                                <div className="grid gap-1.5">
                                    <label className="text-sm font-medium">Poste <span className="text-muted-foreground text-xs">(optionnel)</span></label>
                                    <input
                                        type="text"
                                        value={editForm.data.poste}
                                        onChange={(e) => editForm.setData('poste', e.target.value)}
                                        className="h-10 w-full rounded-lg border border-input bg-background px-3 text-sm focus:outline-none focus:ring-1 focus:ring-primary"
                                        placeholder="Ex: Chef de dépôt..."
                                    />
                                </div>

                                <div className="grid gap-1.5">
                                    <label className="text-sm font-medium">Rôles supplémentaires <span className="text-muted-foreground text-xs">(hybride)</span></label>
                                    <div className="flex flex-wrap gap-3">
                                        {(['admin', 'logisticien', 'commercial', 'magasinier', 'comptable'] as const).map((r) => (
                                            <label key={r} className="flex items-center gap-1.5 cursor-pointer select-none">
                                                <input
                                                    type="checkbox"
                                                    checked={editForm.data.extra_roles.includes(r)}
                                                    onChange={(e) => {
                                                        const current = editForm.data.extra_roles;
                                                        editForm.setData('extra_roles', e.target.checked ? [...current, r] : current.filter((x) => x !== r));
                                                    }}
                                                    className="rounded border-border"
                                                />
                                                <span className="text-xs text-foreground">{roleConfig[r]?.label ?? r}</span>
                                            </label>
                                        ))}
                                    </div>
                                </div>

                                <div className="mt-2 flex justify-end gap-3">
                                    <button
                                        type="button"
                                        onClick={() => setEditingMember(null)}
                                        className="h-10 px-4 rounded-lg border border-border text-sm font-medium hover:bg-secondary transition-colors"
                                    >
                                        Annuler
                                    </button>
                                    <button
                                        type="submit"
                                        disabled={editForm.processing}
                                        className="h-10 px-6 rounded-lg bg-primary text-primary-foreground text-sm font-bold hover:brightness-110 transition-all disabled:opacity-50"
                                    >
                                        {editForm.processing ? 'Modification...' : 'Enregistrer'}
                                    </button>
                                </div>
                            </form>
                        </motion.div>
                    </>
                )}
            </AnimatePresence>

            {/* Modal suppression */}
            <AnimatePresence>
                {memberToDelete && (
                    <>
                        <motion.div
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                            exit={{ opacity: 0 }}
                            className="fixed inset-0 z-50 bg-background/80 backdrop-blur-sm"
                            onClick={() => setMemberToDelete(null)}
                        />
                        <motion.div
                            initial={{ opacity: 0, scale: 0.95, y: 20 }}
                            animate={{ opacity: 1, scale: 1, y: 0 }}
                            exit={{ opacity: 0, scale: 0.95, y: 20 }}
                            className="fixed z-50 left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-sm p-6 rounded-2xl border border-border bg-card shadow-2xl"
                        >
                            <div className="flex items-center gap-3 mb-4">
                                <div className="p-2 rounded-xl bg-rose-500/10 text-rose-500">
                                    <Trash2 className="h-5 w-5" />
                                </div>
                                <h2 className="text-lg font-bold text-foreground">Retirer le membre</h2>
                            </div>
                            <p className="text-sm text-muted-foreground mb-6">
                                Voulez-vous retirer{' '}
                                <span className="font-semibold text-foreground">{memberToDelete.name}</span> de
                                l'equipe ? Cette action est irreversible.
                            </p>
                            <div className="flex justify-end gap-3">
                                <button
                                    onClick={() => setMemberToDelete(null)}
                                    className="h-10 px-4 rounded-lg border border-border text-sm font-medium hover:bg-secondary transition-colors"
                                >
                                    Annuler
                                </button>
                                <button
                                    onClick={handleDelete}
                                    className="h-10 px-6 rounded-lg bg-rose-500 text-white text-sm font-bold hover:bg-rose-600 transition-colors"
                                >
                                    Retirer
                                </button>
                            </div>
                        </motion.div>
                    </>
                )}
            </AnimatePresence>
        </div>
    );
}

EquipeIndex.layout = (props: { currentTeam?: { slug: string } | null }) => ({
    breadcrumbs: [
        {
            title: 'Équipe',
            href: props.currentTeam ? `/${props.currentTeam.slug}/equipe` : '/',
        },
    ],
});
