import { Head, Link, usePage } from '@inertiajs/react';
import { motion } from 'framer-motion';
import {
    Package, Truck, ArrowRight, Activity, Zap, BarChart3, Users, DollarSign, Boxes, MapPin,
    Shield, PieChart, ClipboardCheck, ShoppingCart, CreditCard, Layout,
    Cpu, Globe, Lock, FileText,
} from 'lucide-react';
import AppLogoIcon from '@/components/app-logo-icon';
import { ThemeToggle } from '@/components/theme-toggle';
import { HoverButton } from '@/components/ui/hover-button';
import { PageTransition } from '@/components/ui/page-transition';
import { ParticlesBackground } from '@/components/ui/particles-background';
import { login, register } from '@/routes';

export default function Welcome({
    canRegister = true,
}: {
    canRegister?: boolean;
}) {
    const { auth, currentTeam } = usePage().props;
    const dashboardUrl = auth?.user?.nexora_role === 'super_admin'
        ? '/super-admin/dashboard'
        : (currentTeam ? `/${currentTeam.slug}/dashboard` : '/dashboard');

    const sector = {
        title: 'Distribution Boissons',
        subtitle: 'Brasseries, distributeurs, grossistes boissons',
        icon: Package,
        description: 'Solution ERP complète pilotée par IA pour la gestion des flux de boissons : articles consignables, approvisionnements complexes, tournées de livraison et finance intégrée.',
        roles: [
            { name: 'Admin', color: 'bg-blue-100 text-blue-700', modules: [{ n: 'Articles & Tarifs', i: Layout }, { n: 'Utilisateurs', i: Users }, { n: 'Paramètres', i: Shield }, { n: 'Audit & Logs', i: Lock }] },
            { name: 'Gérant', color: 'bg-indigo-100 text-indigo-700', modules: [{ n: 'Dashboard IA', i: Cpu }, { n: 'Reporting Avancé', i: BarChart3 }, { n: 'Validations', i: ClipboardCheck }, { n: 'Performance', i: PieChart }] },
            { name: 'OPS', color: 'bg-cyan-100 text-cyan-700', modules: [{ n: 'Approvisionnements', i: Truck }, { n: 'Fournisseurs', i: Globe }, { n: 'Inventaires', i: Boxes }, { n: 'Pertes', i: Activity }] },
            { name: 'Caissier', color: 'bg-green-100 text-green-700', modules: [{ n: 'Ventes', i: ShoppingCart }, { n: 'Consignes', i: Package }, { n: 'Règlements', i: DollarSign }, { n: 'Clôture', i: Lock }] },
            { name: 'Comptable', color: 'bg-purple-100 text-purple-700', modules: [{ n: 'Charges', i: CreditCard }, { n: 'Apports', i: DollarSign }, { n: 'Versements', i: MapPin }, { n: 'Bilans', i: FileText }] },
            { name: 'Magasinier', color: 'bg-amber-100 text-amber-700', modules: [{ n: 'Stocks', i: Boxes }, { n: 'Emballages', i: Package }, { n: 'Inventaires', i: ClipboardCheck }, { n: 'Mouvements', i: Activity }] },
        ]
    };

    const containerVariants = {
        hidden: { opacity: 0 },
        visible: {
            opacity: 1,
            transition: { staggerChildren: 0.1, delayChildren: 0.2 },
        },
    };

    const itemVariants = {
        hidden: { opacity: 0, y: 20 },
        visible: { opacity: 1, y: 0, transition: { duration: 0.5 } },
    };

    return (
        <>
            <ParticlesBackground />

            <PageTransition className="relative z-10 min-h-screen text-foreground selection:bg-foreground selection:text-background font-sans antialiased">
                <Head title="NEXORA — Distribution Boissons">
                    <link rel="preconnect" href="https://fonts.bunny.net" />
                    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />
                </Head>

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
                        {auth.user ? (
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

                <main className="relative z-10 flex min-h-screen flex-col items-center justify-center px-6 pt-32 pb-16 lg:px-8">
                    <motion.div
                        variants={containerVariants}
                        initial="hidden"
                        animate="visible"
                        className="w-full max-w-5xl flex flex-col items-center text-center"
                    >
                        <motion.div variants={itemVariants} className="mb-6 inline-flex items-center rounded-full border border-border bg-secondary/50 px-3 py-1 text-xs font-medium backdrop-blur-sm">
                            <Zap className="h-3.5 w-3.5 mr-2 fill-foreground/20" />
                            NEXORA Drinks — L'ERP complet pour la distribution de boissons
                        </motion.div>

                        <motion.h1 variants={itemVariants} className="max-w-4xl text-5xl font-bold tracking-tighter sm:text-7xl lg:text-8xl">
                            Boostez votre distribution <br className="hidden sm:block" />
                            <span className="text-muted-foreground">Spécial boissons.</span>
                        </motion.h1>

                        <motion.p variants={itemVariants} className="mt-8 max-w-2xl text-lg text-muted-foreground sm:text-xl leading-relaxed">
                            NEXORA, c’est la plateforme qui centralise vos commandes, stocks, consignes et finances, le tout propulsé par l’IA.
                        </motion.p>

                        <motion.div variants={itemVariants} className="mt-10 flex flex-col sm:flex-row items-center gap-4">
                            <Link
                                href={auth.user ? dashboardUrl : register.url()}
                                className="h-12 rounded-full px-8 text-base shadow-sm bg-foreground text-background inline-flex items-center justify-center font-medium hover:bg-foreground/90 transition-all active:scale-95"
                            >
                                {auth.user ? 'Accéder au tableau de bord' : 'Commencer'}
                                <ArrowRight className="ml-2 h-4 w-4" />
                            </Link>
                        </motion.div>
                    </motion.div>

                    {/* Main Content */}
                    <div className="mt-32 w-full max-w-6xl">
                        <div className="space-y-8">
                            <div className="rounded-2xl border border-border bg-secondary/30 p-8">
                                <div className="flex items-start gap-6">
                                    <div className="flex h-16 w-16 items-center justify-center rounded-full bg-foreground/10">
                                        <Package className="h-8 w-8" />
                                    </div>
                                    <div>
                                        <h3 className="text-2xl font-bold">{sector.title}</h3>
                                        <p className="mt-2 text-sm text-muted-foreground">{sector.subtitle}</p>
                                        <p className="mt-4 text-base leading-relaxed">{sector.description}</p>
                                    </div>
                                </div>
                            </div>

                            {/* Roles Grid */}
                            <div>
                                <h4 className="text-xl font-semibold mb-4 tracking-tight flex items-center gap-2">
                                    <Users className="h-5 w-5 text-primary" />
                                    Rôles & Permissions Métier
                                </h4>
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    {sector.roles.map((role, idx) => (
                                        <motion.div
                                            key={idx}
                                            initial={{ opacity: 0, x: -20 }}
                                            animate={{ opacity: 1, x: 0 }}
                                            transition={{ delay: idx * 0.1 }}
                                            className="rounded-xl border border-border bg-background p-5"
                                        >
                                            <div className={`inline-block ${role.color} px-3 py-1 rounded-full text-xs font-semibold mb-3`}>
                                                {role.name}
                                            </div>
                                            <div className="flex flex-wrap gap-2">
                                                {role.modules.map((module: any, midx) => {
                                                    const ModIcon = module.i;
                                                    return (
                                                        <span key={midx} className="flex items-center gap-1.5 text-[11px] bg-secondary/80 text-foreground px-2.5 py-1.5 rounded-lg border border-border/50 transition-all hover:bg-secondary">
                                                            <ModIcon className="h-3 w-3 text-muted-foreground" />
                                                            {module.n}
                                                        </span>
                                                    );
                                                })}
                                            </div>
                                        </motion.div>
                                    ))}
                                </div>
                            </div>

                            {/* Transversal Capabilities */}
                            <div className="mt-20 pt-12 border-t border-border/50">
                                <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div className="rounded-xl border border-border bg-background p-6">
                                        <div className="mb-4 flex h-10 w-10 items-center justify-center rounded-lg bg-secondary">
                                            <DollarSign className="h-5 w-5" />
                                        </div>
                                        <h4 className="font-semibold">Gestion Financière</h4>
                                        <p className="mt-2 text-sm text-muted-foreground">Facturation, règlements et gestion des consignes en temps réel.</p>
                                    </div>
                                    <div className="rounded-xl border border-border bg-background p-6">
                                        <div className="mb-4 flex h-10 w-10 items-center justify-center rounded-lg bg-secondary">
                                            <Truck className="h-5 w-5" />
                                        </div>
                                        <h4 className="font-semibold">Optimisation Logistique</h4>
                                        <p className="mt-2 text-sm text-muted-foreground">Suivi des approvisionnements et des tournées de livraison.</p>
                                    </div>
                                    <div className="rounded-xl border border-border bg-background p-6">
                                        <div className="mb-4 flex h-10 w-10 items-center justify-center rounded-lg bg-secondary">
                                            <BarChart3 className="h-5 w-5" />
                                        </div>
                                        <h4 className="font-semibold">Insights IA</h4>
                                        <p className="mt-2 text-sm text-muted-foreground">Analyse prédictive des ventes et rapports de performance par secteur.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>

                <footer className="border-t border-border py-12 text-center">
                    <p className="text-sm text-muted-foreground">
                        &copy; {new Date().getFullYear()} NEXORA Drinks. Conçu pour l'Afrique centrale.
                    </p>
                </footer>
            </PageTransition>
        </>
    );
}
