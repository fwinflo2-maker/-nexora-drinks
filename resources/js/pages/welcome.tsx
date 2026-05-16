import { Head, Link, usePage } from '@inertiajs/react';
import { motion } from 'framer-motion';
import {
    Package, Truck, ArrowRight, Activity, Zap, BarChart3, Users, DollarSign,
    Boxes, MapPin, Shield, PieChart, ClipboardCheck, ShoppingCart, CreditCard,
    Layout, Cpu, Globe, Lock, FileText, BedDouble, UtensilsCrossed,
    CalendarCheck, ChefHat, Coffee, Layers,
} from 'lucide-react';
import AppLogoIcon from '@/components/app-logo-icon';
import { ThemeToggle } from '@/components/theme-toggle';
import { HoverButton } from '@/components/ui/hover-button';
import { PageTransition } from '@/components/ui/page-transition';
import { ParticlesBackground } from '@/components/ui/particles-background';
import { login, register } from '@/routes';

// ─── Types ────────────────────────────────────────────────────────────────────

interface RoleCard {
    name: string;
    color: string;
    modules: { n: string; i: React.ElementType }[];
}

interface Sector {
    key: string;
    title: string;
    subtitle: string;
    icon: React.ElementType;
    iconBg: string;
    badge: string;
    badgeColor: string;
    description: string;
    roles: RoleCard[];
}

// ─── Sector Data ──────────────────────────────────────────────────────────────

const SECTORS: Sector[] = [
    {
        key: 'drinks',
        title: 'Distribution Boissons',
        subtitle: 'Brasseries, distributeurs, grossistes boissons',
        icon: Coffee,
        iconBg: 'bg-amber-50 text-amber-600',
        badge: 'Drinks',
        badgeColor: 'bg-amber-100 text-amber-700',
        description: 'Solution ERP complète pour la gestion des flux de boissons : articles consignables, approvisionnements complexes, tournées de livraison et finance intégrée.',
        roles: [
            { name: 'Admin', color: 'bg-blue-100 text-blue-700', modules: [{ n: 'Articles & Tarifs', i: Layout }, { n: 'Utilisateurs', i: Users }, { n: 'Paramètres', i: Shield }, { n: 'Audit & Logs', i: Lock }] },
            { name: 'Gérant', color: 'bg-indigo-100 text-indigo-700', modules: [{ n: 'Dashboard IA', i: Cpu }, { n: 'Reporting', i: BarChart3 }, { n: 'Validations', i: ClipboardCheck }, { n: 'Performance', i: PieChart }] },
            { name: 'OPS', color: 'bg-cyan-100 text-cyan-700', modules: [{ n: 'Approvisionnements', i: Truck }, { n: 'Fournisseurs', i: Globe }, { n: 'Inventaires', i: Boxes }, { n: 'Pertes', i: Activity }] },
            { name: 'Caissier', color: 'bg-green-100 text-green-700', modules: [{ n: 'Ventes', i: ShoppingCart }, { n: 'Consignes', i: Package }, { n: 'Règlements', i: DollarSign }, { n: 'Clôture', i: Lock }] },
            { name: 'Comptable', color: 'bg-purple-100 text-purple-700', modules: [{ n: 'Charges', i: CreditCard }, { n: 'Apports', i: DollarSign }, { n: 'Versements', i: MapPin }, { n: 'Bilans', i: FileText }] },
            { name: 'Magasinier', color: 'bg-amber-100 text-amber-700', modules: [{ n: 'Stocks', i: Boxes }, { n: 'Emballages', i: Package }, { n: 'Inventaires', i: ClipboardCheck }, { n: 'Mouvements', i: Activity }] },
        ],
    },
    {
        key: 'hotel',
        title: 'Hôtellerie',
        subtitle: 'Hôtels, résidences, établissements d\'hébergement',
        icon: BedDouble,
        iconBg: 'bg-blue-50 text-blue-600',
        badge: 'Hôtel',
        badgeColor: 'bg-blue-100 text-blue-700',
        description: 'Gestion complète de l\'établissement hôtelier : réservations, check-in/out, suivi des chambres, facturation et reporting de revenus en temps réel.',
        roles: [
            { name: 'Manager Hôtel', color: 'bg-blue-100 text-blue-700', modules: [{ n: 'Dashboard', i: BarChart3 }, { n: 'Revenus', i: DollarSign }, { n: 'Taux occupation', i: PieChart }, { n: 'Paramètres', i: Shield }] },
            { name: 'Réceptionniste', color: 'bg-sky-100 text-sky-700', modules: [{ n: 'Réservations', i: CalendarCheck }, { n: 'Check-in/out', i: BedDouble }, { n: 'Chambres', i: Layout }, { n: 'Clients', i: Users }] },
            { name: 'Housekeeping', color: 'bg-cyan-100 text-cyan-700', modules: [{ n: 'Statut chambres', i: ClipboardCheck }, { n: 'Maintenance', i: Activity }, { n: 'Planning ménage', i: FileText }, { n: 'Rapports', i: BarChart3 }] },
        ],
    },
    {
        key: 'fnb',
        title: 'Restauration & F&B',
        subtitle: 'Restaurants, cafés, hôtels-restaurants',
        icon: UtensilsCrossed,
        iconBg: 'bg-emerald-50 text-emerald-600',
        badge: 'F&B',
        badgeColor: 'bg-emerald-100 text-emerald-700',
        description: 'Pilotez votre salle de restaurant avec un écran cuisine en temps réel, gestion des tables et commandes, suivi du chiffre d\'affaires heure par heure.',
        roles: [
            { name: 'Manager F&B', color: 'bg-emerald-100 text-emerald-700', modules: [{ n: 'Dashboard', i: BarChart3 }, { n: 'CA par heure', i: DollarSign }, { n: 'Articles menu', i: Layout }, { n: 'Paramètres', i: Shield }] },
            { name: 'Serveur', color: 'bg-teal-100 text-teal-700', modules: [{ n: 'Tables', i: Users }, { n: 'Commandes', i: ShoppingCart }, { n: 'Écran cuisine', i: ChefHat }, { n: 'Additions', i: FileText }] },
            { name: 'Caissier F&B', color: 'bg-green-100 text-green-700', modules: [{ n: 'Encaissements', i: CreditCard }, { n: 'Clôture caisse', i: Lock }, { n: 'Rapports', i: PieChart }, { n: 'Historique', i: ClipboardCheck }] },
        ],
    },
];

// ─── Animation Variants ───────────────────────────────────────────────────────

const containerVariants = {
    hidden: { opacity: 0 },
    visible: { opacity: 1, transition: { staggerChildren: 0.1, delayChildren: 0.2 } },
};

const itemVariants = {
    hidden: { opacity: 0, y: 20 },
    visible: { opacity: 1, y: 0, transition: { duration: 0.5 } },
};

// ─── Sector Section ───────────────────────────────────────────────────────────

function SectorSection({ sector, index }: { sector: Sector; index: number }) {
    const Icon = sector.icon;

    return (
        <motion.div
            initial={{ opacity: 0, y: 24 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.5, delay: index * 0.12 }}
            className="space-y-5"
        >
            {/* Sector header card */}
            <div className="rounded-2xl border border-border bg-secondary/30 p-6">
                <div className="flex items-start gap-5">
                    <div className={`flex h-14 w-14 shrink-0 items-center justify-center rounded-xl ${sector.iconBg}`}>
                        <Icon className="h-7 w-7" />
                    </div>
                    <div className="min-w-0">
                        <div className="flex items-center gap-2 mb-1">
                            <span className={`text-xs font-semibold px-2.5 py-0.5 rounded-full ${sector.badgeColor}`}>
                                {sector.badge}
                            </span>
                        </div>
                        <h3 className="text-xl font-bold">{sector.title}</h3>
                        <p className="text-xs text-muted-foreground mt-0.5">{sector.subtitle}</p>
                        <p className="mt-3 text-sm leading-relaxed text-muted-foreground">{sector.description}</p>
                    </div>
                </div>
            </div>

            {/* Roles grid */}
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                {sector.roles.map((role, idx) => (
                    <div key={idx} className="rounded-xl border border-border bg-background p-4">
                        <span className={`inline-block ${role.color} px-2.5 py-0.5 rounded-full text-xs font-semibold mb-3`}>
                            {role.name}
                        </span>
                        <div className="flex flex-wrap gap-1.5">
                            {role.modules.map((mod, midx) => {
                                const ModIcon = mod.i;
                                return (
                                    <span key={midx} className="flex items-center gap-1 text-[11px] bg-secondary/80 text-foreground px-2 py-1 rounded-lg border border-border/50">
                                        <ModIcon className="h-3 w-3 text-muted-foreground" />
                                        {mod.n}
                                    </span>
                                );
                            })}
                        </div>
                    </div>
                ))}
            </div>
        </motion.div>
    );
}

// ─── Main Component ───────────────────────────────────────────────────────────

export default function Welcome({ canRegister = true }: { canRegister?: boolean }) {
    const { auth, currentTeam } = usePage().props;
    const dashboardUrl = (auth as any)?.user?.nexora_role === 'super_admin'
        ? '/super-admin/dashboard'
        : (currentTeam ? `/${(currentTeam as any).slug}/dashboard` : '/dashboard');

    return (
        <>
            <ParticlesBackground />

            <PageTransition className="relative z-10 min-h-screen text-foreground selection:bg-foreground selection:text-background font-sans antialiased">
                <Head title="NEXORA — ERP Distribution, Hôtellerie & Restauration">
                    <link rel="preconnect" href="https://fonts.bunny.net" />
                    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />
                </Head>

                {/* ── Header ── */}
                <header className="fixed inset-x-0 top-0 z-50 flex h-16 items-center justify-between px-6 lg:px-12 bg-background/80 backdrop-blur-md border-b border-border/40 transition-colors">
                    <motion.div
                        initial={{ opacity: 0, x: -20 }}
                        animate={{ opacity: 1, x: 0 }}
                        transition={{ duration: 0.5 }}
                        className="flex items-center gap-3"
                    >
                        <div className="flex h-8 w-8 items-center justify-center rounded-md bg-foreground text-background">
                            <AppLogoIcon className="h-4 w-4" />
                        </div>
                        <span className="text-lg font-semibold tracking-tight">NEXORA</span>
                    </motion.div>

                    <motion.nav
                        initial={{ opacity: 0, x: 20 }}
                        animate={{ opacity: 1, x: 0 }}
                        transition={{ duration: 0.5 }}
                        className="flex items-center gap-3"
                    >
                        <ThemeToggle />
                        <Link href="/docs" className="hidden sm:inline-flex text-sm font-medium text-muted-foreground hover:text-foreground transition-colors mr-2">
                            Documentation
                        </Link>
                        {(auth as any).user ? (
                            <Link href={dashboardUrl}>
                                <HoverButton variant="primary" className="h-9 rounded-full px-5">
                                    Accéder au tableau de bord
                                </HoverButton>
                            </Link>
                        ) : (
                            <>
                                <Link href={login.url()} className="text-sm font-medium text-muted-foreground hover:text-foreground transition-colors">
                                    Connexion
                                </Link>
                                {canRegister && (
                                    <Link href={register.url()} className="h-9 rounded-full px-5 bg-foreground text-background inline-flex items-center justify-center text-sm font-medium hover:bg-foreground/90 transition-all active:scale-95">
                                        Créer un compte
                                    </Link>
                                )}
                            </>
                        )}
                    </motion.nav>
                </header>

                {/* ── Hero ── */}
                <main className="relative z-10 flex min-h-screen flex-col items-center justify-center px-6 pt-32 pb-16 lg:px-8">
                    <motion.div
                        variants={containerVariants}
                        initial="hidden"
                        animate="visible"
                        className="w-full max-w-5xl flex flex-col items-center text-center"
                    >
                        <motion.div variants={itemVariants} className="mb-6 inline-flex items-center rounded-full border border-border bg-secondary/50 px-3 py-1 text-xs font-medium backdrop-blur-sm">
                            <Zap className="h-3.5 w-3.5 mr-2 fill-foreground/20" />
                            NEXORA ERP — Distribution, Hôtellerie & Restauration
                        </motion.div>

                        <motion.h1 variants={itemVariants} className="max-w-4xl text-5xl font-bold tracking-tighter sm:text-7xl lg:text-8xl">
                            Un ERP pour <br className="hidden sm:block" />
                            <span className="text-muted-foreground">chaque métier.</span>
                        </motion.h1>

                        <motion.p variants={itemVariants} className="mt-8 max-w-2xl text-lg text-muted-foreground sm:text-xl leading-relaxed">
                            NEXORA centralise vos opérations — stocks, commandes, réservations, tables et finances — dans une seule plateforme modulaire pilotée par l'IA.
                        </motion.p>

                        <motion.div variants={itemVariants} className="mt-10 flex flex-col sm:flex-row items-center gap-4">
                            <Link
                                href={(auth as any).user ? dashboardUrl : register.url()}
                                className="h-12 rounded-full px-8 text-base shadow-sm bg-foreground text-background inline-flex items-center justify-center font-medium hover:bg-foreground/90 transition-all active:scale-95"
                            >
                                {(auth as any).user ? 'Accéder au tableau de bord' : 'Commencer gratuitement'}
                                <ArrowRight className="ml-2 h-4 w-4" />
                            </Link>
                        </motion.div>

                        {/* Module pills */}
                        <motion.div variants={itemVariants} className="mt-8 flex flex-wrap justify-center gap-2">
                            {[
                                { label: 'Distribution Boissons', icon: Coffee, color: 'bg-amber-50 text-amber-700 border-amber-200' },
                                { label: 'Hôtellerie', icon: BedDouble, color: 'bg-blue-50 text-blue-700 border-blue-200' },
                                { label: 'Restauration F&B', icon: UtensilsCrossed, color: 'bg-emerald-50 text-emerald-700 border-emerald-200' },
                            ].map(({ label, icon: Icon, color }) => (
                                <span key={label} className={`inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-full border ${color}`}>
                                    <Icon className="h-3 w-3" />
                                    {label}
                                </span>
                            ))}
                        </motion.div>
                    </motion.div>

                    {/* ── Sectors ── */}
                    <div className="mt-32 w-full max-w-6xl space-y-16">

                        {/* Section header */}
                        <div className="flex items-center gap-3">
                            <Layers className="h-5 w-5 text-muted-foreground" />
                            <h2 className="text-2xl font-bold tracking-tight">Modules disponibles</h2>
                        </div>

                        {SECTORS.map((sector, idx) => (
                            <SectorSection key={sector.key} sector={sector} index={idx} />
                        ))}

                        {/* ── Transversal capabilities ── */}
                        <div className="pt-12 border-t border-border/50">
                            <h2 className="text-2xl font-bold tracking-tight mb-6 flex items-center gap-3">
                                <Cpu className="h-5 w-5 text-muted-foreground" />
                                Capacités transversales
                            </h2>
                            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div className="rounded-xl border border-border bg-background p-6">
                                    <div className="mb-4 flex h-10 w-10 items-center justify-center rounded-lg bg-secondary">
                                        <DollarSign className="h-5 w-5" />
                                    </div>
                                    <h4 className="font-semibold">Gestion Financière</h4>
                                    <p className="mt-2 text-sm text-muted-foreground">Facturation, règlements, dépôts de caisse et bilans financiers par module.</p>
                                </div>
                                <div className="rounded-xl border border-border bg-background p-6">
                                    <div className="mb-4 flex h-10 w-10 items-center justify-center rounded-lg bg-secondary">
                                        <Shield className="h-5 w-5" />
                                    </div>
                                    <h4 className="font-semibold">Sécurité & Rôles</h4>
                                    <p className="mt-2 text-sm text-muted-foreground">Permissions fines par rôle et par module. Audit log complet de toutes les actions.</p>
                                </div>
                                <div className="rounded-xl border border-border bg-background p-6">
                                    <div className="mb-4 flex h-10 w-10 items-center justify-center rounded-lg bg-secondary">
                                        <BarChart3 className="h-5 w-5" />
                                    </div>
                                    <h4 className="font-semibold">Insights IA</h4>
                                    <p className="mt-2 text-sm text-muted-foreground">Analyse prédictive des ventes, revenus hôteliers et performance F&B par heure.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>

                <footer className="border-t border-border py-12 text-center">
                    <p className="text-sm text-muted-foreground">
                        &copy; {new Date().getFullYear()} NEXORA. Conçu pour l'Afrique centrale.
                    </p>
                </footer>
            </PageTransition>
        </>
    );
}
