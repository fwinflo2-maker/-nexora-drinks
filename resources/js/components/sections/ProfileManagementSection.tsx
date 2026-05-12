import { motion } from 'framer-motion';
import { Users, Plus, Edit, Trash2, Copy, ChevronRight, Zap } from 'lucide-react';
import { useState } from 'react';
import { HoverButton } from '@/components/ui/hover-button';

export function ProfileManagementSection({ teamSlug, userRole }: { teamSlug: string; userRole: string }) {
    const [isOpen, setIsOpen] = useState(false);
    const [profiles, setProfiles] = useState<any[]>([]);
    const [loading, setLoading] = useState(false);

    const loadProfiles = async () => {
        setLoading(true);

        try {
            const response = await fetch(`/api/v1/custom-profiles`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Team-Slug': teamSlug,
                }
            });
            const result = await response.json();
            setProfiles(result.data || []);
        } catch (error) {
            console.error('Erreur chargement profils:', error);
        } finally {
            setLoading(false);
        }
    };

    const toggleSection = () => {
        if (!isOpen) {
            loadProfiles();
        }

        setIsOpen(!isOpen);
    };

    const handleCreateProfile = () => {
        // Ouvrir formulaire de création
        window.location.href = `/${teamSlug}/admin/profiles/create`;
    };

    return (
        <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            className="rounded-2xl border-2 bg-gradient-to-br from-purple-50 to-blue-50 dark:from-purple-950/30 dark:to-blue-950/30 border-purple-300 dark:border-purple-700 p-6 shadow-lg"
        >
            <div 
                onClick={toggleSection}
                className="flex items-center justify-between cursor-pointer group"
            >
                <div className="flex items-center gap-4">
                    <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-purple-200 to-blue-200 dark:from-purple-800 dark:to-blue-800 group-hover:scale-110 transition-transform">
                        <Users className="h-6 w-6 text-purple-700 dark:text-purple-200" />
                    </div>
                    <div>
                        <h3 className="text-lg font-bold text-purple-950 dark:text-purple-100">
                            🎯 Gestion des Profils
                        </h3>
                        <p className="text-sm text-purple-700 dark:text-purple-300">
                            Créez et adaptez les rôles à votre organisation
                        </p>
                    </div>
                </div>
                <ChevronRight className={`h-5 w-5 text-purple-600 dark:text-purple-400 transition-transform ${isOpen ? 'rotate-90' : ''}`} />
            </div>

            {isOpen && (
                <motion.div
                    initial={{ opacity: 0, height: 0 }}
                    animate={{ opacity: 1, height: 'auto' }}
                    exit={{ opacity: 0, height: 0 }}
                    className="mt-6 border-t border-purple-200 dark:border-purple-700 pt-6 space-y-4"
                >
                    {/* Liste des profils */}
                    {loading ? (
                        <div className="text-center py-8 text-purple-600">Chargement...</div>
                    ) : profiles.length === 0 ? (
                        <div className="text-center py-8">
                            <p className="text-sm text-muted-foreground mb-4">Aucun profil personnalisé créé</p>
                            <HoverButton
                                variant="secondary"
                                onClick={handleCreateProfile}
                                className="inline-flex items-center gap-2"
                            >
                                <Plus className="h-4 w-4" />
                                Créer un profil
                            </HoverButton>
                        </div>
                    ) : (
                        <div className="space-y-3">
                            {profiles.map((profile) => (
                                <motion.div
                                    key={profile.id}
                                    initial={{ opacity: 0, x: -10 }}
                                    animate={{ opacity: 1, x: 0 }}
                                    className="flex items-center justify-between p-4 rounded-lg bg-white dark:bg-slate-800 border border-purple-200/50 dark:border-purple-700/50 hover:shadow-md transition-shadow"
                                >
                                    <div className="flex-1">
                                        <h4 className="font-semibold text-foreground">{profile.name}</h4>
                                        <p className="text-xs text-muted-foreground mt-1">
                                            {profile.description || 'Pas de description'}
                                        </p>
                                        <div className="flex gap-2 mt-2">
                                            {profile.permissions && Object.keys(profile.permissions).slice(0, 3).map(module => (
                                                <span key={module} className="text-xs bg-purple-100 dark:bg-purple-900/50 text-purple-800 dark:text-purple-200 px-2 py-1 rounded-full">
                                                    {module}
                                                </span>
                                            ))}
                                            {profile.permissions && Object.keys(profile.permissions).length > 3 && (
                                                <span className="text-xs text-muted-foreground px-2 py-1">
                                                    +{Object.keys(profile.permissions).length - 3} more
                                                </span>
                                            )}
                                        </div>
                                    </div>
                                    <div className="flex items-center gap-2 ml-4">
                                        <button className="p-2 hover:bg-purple-100 dark:hover:bg-purple-900/30 rounded-lg text-purple-600 dark:text-purple-400 transition-colors" title="Dupliquer">
                                            <Copy className="h-4 w-4" />
                                        </button>
                                        <button className="p-2 hover:bg-blue-100 dark:hover:bg-blue-900/30 rounded-lg text-blue-600 dark:text-blue-400 transition-colors" title="Éditer">
                                            <Edit className="h-4 w-4" />
                                        </button>
                                        <button className="p-2 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-lg text-red-600 dark:text-red-400 transition-colors" title="Supprimer">
                                            <Trash2 className="h-4 w-4" />
                                        </button>
                                    </div>
                                </motion.div>
                            ))}
                        </div>
                    )}

                    {/* Bouton créer profil */}
                    <div className="pt-4 border-t border-purple-200 dark:border-purple-700">
                        <HoverButton
                            onClick={handleCreateProfile}
                            className="w-full flex items-center justify-center gap-2"
                        >
                            <Plus className="h-4 w-4" />
                            Créer un nouveau profil
                        </HoverButton>
                    </div>

                    {/* Info encadre */}
                    <div className="rounded-lg bg-purple-100/50 dark:bg-purple-900/30 border border-purple-200 dark:border-purple-700/50 p-4 mt-4">
                        <div className="flex gap-3">
                            <Zap className="h-5 w-5 text-purple-600 dark:text-purple-400 flex-shrink-0 mt-0.5" />
                            <div className="text-sm text-purple-900 dark:text-purple-100">
                                <p className="font-semibold">💡 Conseil</p>
                                <p className="mt-1 opacity-90">
                                    Adaptez les profils à votre structure métier. Chaque profil peut avoir des permissions granulaires (lecture, création, modification, suppression) par module.
                                </p>
                            </div>
                        </div>
                    </div>
                </motion.div>
            )}
        </motion.div>
    );
}
