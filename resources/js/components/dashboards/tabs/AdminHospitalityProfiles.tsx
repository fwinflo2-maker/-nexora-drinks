import { motion } from 'framer-motion';
import {
    Plus, Search, MoreVertical, Edit2, Trash2, UserX,
} from 'lucide-react';
import { useState } from 'react';

const container = { hidden: { opacity: 0 }, visible: { opacity: 1, transition: { staggerChildren: 0.05 } } };
const item = { hidden: { opacity: 0, y: 12 }, visible: { opacity: 1, y: 0, transition: { duration: 0.3 } } };

interface HospitalityProfile {
    id: number;
    name: string;
    email: string;
    role: 'admin' | 'receptionniste' | 'barman' | 'comptable';
    status: 'active' | 'inactive';
    joinDate: string;
}

export default function AdminHospitalityProfiles({ teamKpis }: { teamKpis?: any }) {
    const [searchTerm, setSearchTerm] = useState('');
    const [selectedRole, setSelectedRole] = useState<string | null>(null);

    const profiles: HospitalityProfile[] = [
        { id: 1, name: 'Jean Ekobe', email: 'j.ekobe@nexora.cm', role: 'admin', status: 'active', joinDate: '2024-01-15' },
        { id: 2, name: 'Marie Fotso', email: 'm.fotso@nexora.cm', role: 'receptionniste', status: 'active', joinDate: '2024-02-10' },
        { id: 3, name: 'Alain Djoufack', email: 'a.djoufack@nexora.cm', role: 'barman', status: 'active', joinDate: '2024-01-20' },
        { id: 4, name: 'Sophie Etaé', email: 's.etae@nexora.cm', role: 'receptionniste', status: 'active', joinDate: '2024-03-01' },
        { id: 5, name: 'Pierre Ngouem', email: 'p.ngouem@nexora.cm', role: 'barman', status: 'active', joinDate: '2024-02-15' },
        { id: 6, name: 'Francine Belinga', email: 'f.belinga@nexora.cm', role: 'comptable', status: 'inactive', joinDate: '2024-01-10' },
    ];

    const roleConfig: Record<HospitalityProfile['role'], { label: string; color: string }> = {
        admin:          { label: 'Admin',           color: 'bg-purple-500/10 text-purple-400' },
        receptionniste: { label: 'Réceptionniste',  color: 'bg-indigo-500/10 text-indigo-400' },
        barman:         { label: 'Barman',           color: 'bg-orange-500/10 text-orange-400' },
        comptable:      { label: 'Comptable',        color: 'bg-pink-500/10 text-pink-400' },
    };

    const filtered = profiles.filter(p => {
        const matches = p.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
            p.email.toLowerCase().includes(searchTerm.toLowerCase());
        const roleMatches = !selectedRole || p.role === selectedRole;
        return matches && roleMatches;
    });

    return (
        <motion.div variants={container} initial="hidden" animate="visible" className="space-y-6">

            {/* Header with Add Button */}
            <motion.div variants={item} className="flex items-center justify-between">
                <div>
                    <h2 className="text-lg font-semibold text-foreground">Gestion des Profils</h2>
                    <p className="text-sm text-muted-foreground mt-1">{profiles.length} utilisateurs dans votre équipe</p>
                </div>
                <button className="flex items-center gap-2 px-4 py-2 rounded-lg bg-primary text-primary-foreground font-semibold hover:bg-primary/90 transition-colors">
                    <Plus className="h-4 w-4" />
                    Ajouter Profil
                </button>
            </motion.div>

            {/* Filters */}
            <motion.div variants={item} className="flex gap-4 items-center flex-wrap">
                <div className="flex-1 min-w-56 relative">
                    <Search className="absolute left-3 top-3 h-4 w-4 text-muted-foreground" />
                    <input
                        type="text"
                        placeholder="Chercher par nom ou email..."
                        className="w-full pl-10 pr-4 py-2 rounded-lg border border-border bg-secondary focus:outline-none focus:ring-2 focus:ring-primary"
                        value={searchTerm}
                        onChange={e => setSearchTerm(e.target.value)}
                    />
                </div>
                <select
                    className="px-4 py-2 rounded-lg border border-border bg-secondary text-foreground focus:outline-none focus:ring-2 focus:ring-primary"
                    value={selectedRole || ''}
                    onChange={e => setSelectedRole(e.target.value || null)}
                >
                    <option value="">Tous les rôles</option>
                    {Object.entries(roleConfig).map(([key, val]) => (
                        <option key={key} value={key}>{val.label}</option>
                    ))}
                </select>
            </motion.div>

            {/* Profiles Grid */}
            <motion.div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                {filtered.map(profile => (
                    <motion.div key={profile.id} variants={item} className="rounded-lg border border-border bg-card p-4 hover:shadow-lg hover:border-primary/20 transition-all duration-300 group">
                        <div className="flex items-start justify-between mb-3">
                            <div className="flex-1">
                                <div className="font-semibold text-foreground">{profile.name}</div>
                                <div className="text-xs text-muted-foreground mt-0.5">{profile.email}</div>
                            </div>
                            <div className="relative">
                                <button className="p-1.5 rounded hover:bg-secondary opacity-0 group-hover:opacity-100 transition-opacity">
                                    <MoreVertical className="h-4 w-4 text-muted-foreground" />
                                </button>
                            </div>
                        </div>

                        <div className="flex items-center gap-2 mb-3">
                            <span className={`text-xs font-semibold px-2.5 py-1 rounded-lg ${roleConfig[profile.role].color}`}>
                                {roleConfig[profile.role].label}
                            </span>
                            <span className={`text-xs font-semibold px-2.5 py-1 rounded-lg ${profile.status === 'active' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-rose-500/10 text-rose-400'}`}>
                                {profile.status === 'active' ? 'Actif' : 'Inactif'}
                            </span>
                        </div>

                        <div className="text-xs text-muted-foreground mb-3">
                            Ajouté le {new Date(profile.joinDate).toLocaleDateString('fr-FR')}
                        </div>

                        <div className="flex gap-2 pt-3 border-t border-border">
                            <button className="flex-1 flex items-center justify-center gap-1 px-3 py-1.5 rounded text-xs font-semibold hover:bg-secondary transition-colors text-foreground">
                                <Edit2 className="h-3.5 w-3.5" />
                                Éditer
                            </button>
                            <button className="flex-1 flex items-center justify-center gap-1 px-3 py-1.5 rounded text-xs font-semibold hover:bg-rose-500/10 transition-colors text-rose-400">
                                <Trash2 className="h-3.5 w-3.5" />
                                Supprimer
                            </button>
                        </div>
                    </motion.div>
                ))}
            </motion.div>

            {filtered.length === 0 && (
                <motion.div variants={item} className="text-center py-12 rounded-lg border-2 border-dashed border-border">
                    <UserX className="h-12 w-12 text-muted-foreground mx-auto mb-3 opacity-50" />
                    <p className="text-muted-foreground">Aucun profil ne correspond à votre recherche</p>
                </motion.div>
            )}

        </motion.div>
    );
}
