import { motion } from 'framer-motion';
import { Plus, Edit2, Trash2, Copy, Filter, Search, Shield, Lock } from 'lucide-react';
import { useState } from 'react';

const item = { hidden: { opacity: 0, y: 16 }, visible: { opacity: 1, y: 0, transition: { duration: 0.4 } } };

export default function AdminProfiles({ teamKpis }: { teamKpis?: any }) {
    const [showCreateForm, setShowCreateForm] = useState(false);
    const [selectedProfile, setSelectedProfile] = useState<string | null>(null);
    const [filterActive, setFilterActive] = useState<'all' | 'active' | 'inactive'>('all');
    const [searchQuery, setSearchQuery] = useState('');

    // Sample data - in production, these would come from API
    const [profiles, setProfiles] = useState<Array<{
        id: string;
        name: string;
        description: string;
        isActive: boolean;
        usersCount: number;
        permissions: Record<string, string[]>;
        createdAt: string;
    }>>([
        {
            id: 'role_admin',
            name: 'Administrateur',
            description: 'Accès complet à tous les modules',
            isActive: true,
            usersCount: 1,
            permissions: {
                stock: ['create', 'read', 'update', 'delete'],
                orders: ['create', 'read', 'update', 'delete'],
                delivery: ['create', 'read', 'update', 'delete'],
                finance: ['read'],
                users: ['create', 'read', 'update', 'delete'],
            },
            createdAt: '2025-01-15',
        },
        {
            id: 'role_manager',
            name: 'Responsable Secteur',
            description: 'Gestion zone + livraisons',
            isActive: true,
            usersCount: 3,
            permissions: {
                stock: ['read', 'update'],
                orders: ['read', 'update'],
                delivery: ['create', 'read', 'update'],
                finance: ['read'],
            },
            createdAt: '2025-02-01',
        },
        {
            id: 'role_warehouse',
            name: 'Magasinier',
            description: 'Gestion du stock',
            isActive: true,
            usersCount: 2,
            permissions: {
                stock: ['create', 'read', 'update'],
                orders: ['read'],
            },
            createdAt: '2025-02-10',
        },
    ]);

    const filteredProfiles = profiles.filter(p => {
        const matchesActive = filterActive === 'all' || (filterActive === 'active' ? p.isActive : !p.isActive);
        const matchesSearch = p.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
                            p.description.toLowerCase().includes(searchQuery.toLowerCase());

        return matchesActive && matchesSearch;
    });

    const modules = [
        { id: 'stock', name: 'Gestion du Stock', color: 'bg-blue-500/10 text-blue-400' },
        { id: 'orders', name: 'Commandes', color: 'bg-violet-500/10 text-violet-400' },
        { id: 'delivery', name: 'Livraisons', color: 'bg-emerald-500/10 text-emerald-400' },
        { id: 'finance', name: 'Finance', color: 'bg-amber-500/10 text-amber-400' },
        { id: 'users', name: 'Utilisateurs', color: 'bg-rose-500/10 text-rose-400' },
    ];

    return (
        <div className="flex flex-col gap-6">
            {/* Header */}
            <motion.div initial={{ opacity: 0, y: -8 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.4 }}>
                <div className="flex items-center justify-between">
                    <div>
                        <h2 className="text-2xl font-bold tracking-tight text-foreground">
                            Gestion des profils
                        </h2>
                        <p className="text-sm text-muted-foreground mt-1">
                            Créez et gérez des rôles personnalisés pour votre équipe
                        </p>
                    </div>
                    <button
                        onClick={() => setShowCreateForm(true)}
                        className="flex items-center gap-2 px-4 py-2 bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 transition-colors font-medium text-sm"
                    >
                        <Plus className="h-4 w-4" />
                        Créer un profil
                    </button>
                </div>
            </motion.div>

            {/* Filters */}
            <motion.div variants={item} initial="hidden" animate="visible" className="flex items-center gap-3">
                <div className="relative flex-1">
                    <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                    <input
                        type="text"
                        placeholder="Rechercher un profil..."
                        value={searchQuery}
                        onChange={(e) => setSearchQuery(e.target.value)}
                        className="w-full pl-10 pr-4 py-2 rounded-lg border border-border bg-card text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary"
                    />
                </div>
                <div className="flex items-center gap-1 p-1 rounded-lg border border-border bg-card">
                    {(['all', 'active', 'inactive'] as const).map(status => (
                        <button
                            key={status}
                            onClick={() => setFilterActive(status)}
                            className={`px-3 py-1.5 rounded text-xs font-medium transition-all ${
                                filterActive === status
                                    ? 'bg-primary text-primary-foreground'
                                    : 'text-muted-foreground hover:text-foreground'
                            }`}
                        >
                            {status === 'all' ? 'Tous' : status === 'active' ? 'Actifs' : 'Inactifs'}
                        </button>
                    ))}
                </div>
            </motion.div>

            {/* Profiles Grid */}
            <motion.div
                variants={{ hidden: { opacity: 0 }, visible: { opacity: 1, transition: { staggerChildren: 0.08 } } }}
                initial="hidden"
                animate="visible"
                className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4"
            >
                {filteredProfiles.map(profile => (
                    <motion.div
                        key={profile.id}
                        variants={item}
                        onClick={() => setSelectedProfile(profile.id)}
                        className={`p-5 rounded-2xl border-2 transition-all cursor-pointer group ${
                            selectedProfile === profile.id
                                ? 'border-primary bg-primary/5'
                                : 'border-border bg-card hover:border-primary/30 hover:shadow-lg'
                        }`}
                    >
                        {/* Header */}
                        <div className="flex items-start justify-between mb-3">
                            <div>
                                <div className="flex items-center gap-2">
                                    <Shield className="h-4 w-4 text-primary" />
                                    <h3 className="font-bold text-foreground">{profile.name}</h3>
                                </div>
                                <p className="text-xs text-muted-foreground mt-1">{profile.description}</p>
                            </div>
                            <div className={`h-2 w-2 rounded-full ${profile.isActive ? 'bg-emerald-500' : 'bg-muted-foreground'}`} />
                        </div>

                        {/* Permissions */}
                        <div className="mb-4 pb-4 border-b border-border">
                            <p className="text-[11px] font-semibold text-muted-foreground mb-2 uppercase tracking-wider">Modules</p>
                            <div className="flex flex-wrap gap-1.5">
                                {Object.entries(profile.permissions).map(([moduleId, actions]) => {
                                    const module = modules.find(m => m.id === moduleId);

                                    return (
                                        <div key={moduleId} className={`px-2.5 py-1 rounded text-[10px] font-semibold ${module?.color}`}>
                                            {module?.name}
                                        </div>
                                    );
                                })}
                            </div>
                        </div>

                        {/* Stats */}
                        <div className="mb-4 pb-4 border-b border-border flex items-center justify-between">
                            <div>
                                <p className="text-[11px] text-muted-foreground">Utilisateurs assignés</p>
                                <p className="text-sm font-bold text-foreground">{profile.usersCount}</p>
                            </div>
                            <div>
                                <p className="text-[11px] text-muted-foreground">Créé le</p>
                                <p className="text-sm font-semibold text-foreground">{profile.createdAt}</p>
                            </div>
                        </div>

                        {/* Actions */}
                        <div className="flex items-center gap-2">
                            <button className="flex-1 flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-primary hover:bg-primary/10 transition-colors">
                                <Edit2 className="h-3.5 w-3.5" />
                                Éditer
                            </button>
                            <button className="p-1.5 rounded-lg text-muted-foreground hover:bg-secondary transition-colors">
                                <Copy className="h-4 w-4" />
                            </button>
                            {profile.usersCount === 0 && (
                                <button className="p-1.5 rounded-lg text-muted-foreground hover:text-rose-400 hover:bg-rose-500/10 transition-colors">
                                    <Trash2 className="h-4 w-4" />
                                </button>
                            )}
                        </div>
                    </motion.div>
                ))}
            </motion.div>

            {/* Empty State */}
            {filteredProfiles.length === 0 && (
                <motion.div
                    variants={item}
                    initial="hidden"
                    animate="visible"
                    className="text-center py-12 rounded-2xl border-2 border-dashed border-border"
                >
                    <Lock className="h-12 w-12 text-muted-foreground mx-auto mb-3 opacity-50" />
                    <h3 className="text-lg font-semibold text-foreground mb-1">Aucun profil trouvé</h3>
                    <p className="text-sm text-muted-foreground mb-4">
                        Créez votre premier profil personnalisé
                    </p>
                    <button
                        onClick={() => setShowCreateForm(true)}
                        className="inline-flex items-center gap-2 px-4 py-2 bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 transition-colors font-medium text-sm"
                    >
                        <Plus className="h-4 w-4" />
                        Créer un profil
                    </button>
                </motion.div>
            )}

            {/* Create Form Modal */}
            {showCreateForm && (
                <motion.div
                    initial={{ opacity: 0 }}
                    animate={{ opacity: 1 }}
                    exit={{ opacity: 0 }}
                    className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
                    onClick={() => setShowCreateForm(false)}
                >
                    <motion.div
                        onClick={(e) => e.stopPropagation()}
                        initial={{ scale: 0.95, opacity: 0 }}
                        animate={{ scale: 1, opacity: 1 }}
                        exit={{ scale: 0.95, opacity: 0 }}
                        className="bg-card border border-border rounded-2xl p-6 max-w-md w-full shadow-xl"
                    >
                        <h3 className="text-lg font-bold text-foreground mb-4">Créer un nouveau profil</h3>
                        <div className="space-y-4">
                            <div>
                                <label className="text-sm font-medium text-foreground block mb-1.5">Nom du profil</label>
                                <input
                                    type="text"
                                    placeholder="Ex: Chef de Zone"
                                    className="w-full px-3 py-2 rounded-lg border border-border bg-card text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary"
                                />
                            </div>
                            <div>
                                <label className="text-sm font-medium text-foreground block mb-1.5">Description</label>
                                <textarea
                                    placeholder="Description du rôle..."
                                    rows={3}
                                    className="w-full px-3 py-2 rounded-lg border border-border bg-card text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary"
                                />
                            </div>
                            <div>
                                <label className="text-sm font-medium text-foreground block mb-2">Modules</label>
                                <div className="space-y-2">
                                    {modules.map(module => (
                                        <label key={module.id} className="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" className="rounded border-border" />
                                            <span className="text-sm text-foreground">{module.name}</span>
                                        </label>
                                    ))}
                                </div>
                            </div>
                        </div>
                        <div className="flex gap-3 mt-6">
                            <button
                                onClick={() => setShowCreateForm(false)}
                                className="flex-1 px-4 py-2 rounded-lg border border-border text-foreground hover:bg-secondary transition-colors font-medium text-sm"
                            >
                                Annuler
                            </button>
                            <button
                                onClick={() => setShowCreateForm(false)}
                                className="flex-1 px-4 py-2 rounded-lg bg-primary text-primary-foreground hover:bg-primary/90 transition-colors font-medium text-sm"
                            >
                                Créer
                            </button>
                        </div>
                    </motion.div>
                </motion.div>
            )}
        </div>
    );
}
