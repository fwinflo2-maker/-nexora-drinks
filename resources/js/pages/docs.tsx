import { Head, Link } from '@inertiajs/react';
import { motion, AnimatePresence } from 'framer-motion';
import {
    Command, Package, Truck, Activity, Users, Settings, Shield,
    BookOpen, Zap, ArrowRight, ChevronRight, Code2, Globe,
    Database, Bell, BarChart3, Lock, CheckCircle, Building2,
    Menu, X, Search,
} from 'lucide-react';
import { useState } from 'react';
import { ParticlesBackground } from '@/components/ui/particles-background';

/* ─────────────────────────────── DATA ─────────────────────────────── */

const navigation = [
    { id: 'introduction', label: 'Introduction', icon: BookOpen },
    { id: 'demarrage', label: 'Démarrage rapide', icon: Zap },
    { id: 'stock', label: 'Gestion du Stock', icon: Package },
    { id: 'logistique', label: 'Logistique & Tournées', icon: Truck },
    { id: 'finances', label: 'Aperçus Financiers', icon: Activity },
    { id: 'equipes', label: 'Équipes & Rôles', icon: Users },
    { id: 'multi-tenant', label: 'Multi-Entreprise', icon: Building2 },
    { id: 'notifications', label: 'Notifications', icon: Bell },
    { id: 'securite', label: 'Sécurité', icon: Shield },
    { id: 'api', label: 'Référence API', icon: Code2 },
    { id: 'configuration', label: 'Configuration', icon: Settings },
];

const fadeUp = {
    hidden: { opacity: 0, y: 16 },
    visible: (i = 0) => ({
        opacity: 1, y: 0,
        transition: { duration: 0.45, delay: i * 0.07 },
    }),
};

/* ─────────────────────────────── SECTIONS ─────────────────────────── */

const sections: Record<string, React.ReactNode> = {

    introduction: (
        <Section title="Introduction" badge="v1.0">
            <p className="lead">
                NEXORA est une plateforme ERP (Enterprise Resource Planning) de nouvelle génération,
                conçue spécifiquement pour les entreprises de distribution. Elle centralise la gestion
                du stock, la logistique, les finances et les équipes dans une interface minimaliste et performante.
            </p>

            <h3>Pourquoi NEXORA ?</h3>
            <p>
                Les solutions ERP traditionnelles sont lourdes, coûteuses et inadaptées aux réalités du
                terrain. NEXORA a été pensé pour les distributeurs qui ont besoin d'un outil rapide,
                disponible hors-ligne et accessible depuis n'importe quel appareil.
            </p>

            <FeatureGrid features={[
                { icon: Zap, title: 'Temps réel', desc: 'Données synchronisées instantanément entre tous vos postes.' },
                { icon: Globe, title: 'Hors-ligne', desc: 'Vos commerciaux restent opérationnels même sans connexion.' },
                { icon: Shield, title: 'Sécurisé', desc: 'Architecture multi-tenant avec isolation totale des données.' },
                { icon: BarChart3, title: 'Analytique', desc: 'Vos KPIs et métriques en un seul tableau de bord.' },
            ]} />

            <h3>Architecture générale</h3>
            <CodeBlock lang="txt" code={`NEXORA
├── Frontend    → React 18 + Inertia.js (SPA)
├── Backend     → Laravel 11 (API + Auth + Métier)
├── Base de données → SQLite (dev) / PostgreSQL (prod)
├── Cache       → Redis
└── Offline     → Dexie.js + Service Workers`} />
        </Section>
    ),

    demarrage: (
        <Section title="Démarrage rapide" badge="Setup">
            <p className="lead">
                Créez votre espace NEXORA en moins de 5 minutes. Aucune compétence technique requise.
            </p>

            <h3>1 — Créer votre compte entreprise</h3>
            <p>
                Rendez-vous sur <InlineCode>/register</InlineCode> et complétez les 3 étapes de l'assistant NEXA :
            </p>
            <StepList steps={[
                { num: 1, title: 'Informations personnelles', desc: 'Votre nom, prénom et fonction au sein de la société.' },
                { num: 2, title: "Données de l'entreprise", desc: "Nom commercial, type de société, numéro RC/NIU et coordonnées." },
                { num: 3, title: 'Identifiants de connexion', desc: "Adresse e-mail professionnelle et mot de passe sécurisé." },
            ]} />

            <h3>2 — Configurer vos modules</h3>
            <p>
                Après connexion, le tableau de bord NEXORA vous guide à travers la configuration initiale :
                ajout de produits, paramétrage des alertes de stock, et invitation de vos collaborateurs.
            </p>

            <h3>3 — Inviter votre équipe</h3>
            <p>
                Depuis <InlineCode>Paramètres → Équipe</InlineCode>, envoyez des invitations par e-mail.
                Chaque membre reçoit un rôle dédié (Admin, Logisticien, Commercial, Comptable).
            </p>

            <CallOut type="tip">
                Le mot de passe administrateur par défaut en environnement de démonstration est{' '}
                <InlineCode>nexora_admin2026</InlineCode>. Changez-le dès votre première connexion en production.
            </CallOut>
        </Section>
    ),

    stock: (
        <Section title="Gestion du Stock" badge="Module">
            <p className="lead">
                Le module Stock de NEXORA vous donne une visibilité complète et en temps réel sur
                vos niveaux d'inventaire, vos mouvements et vos consignations.
            </p>

            <h3>Fonctionnalités clés</h3>
            <FeatureList items={[
                'Suivi des niveaux de stock par entrepôt et par produit.',
                'Alertes intelligentes de seuil critique (paramétrable par SKU).',
                'Gestion des consignations (bouteilles, emballages consignés).',
                'Historique complet des mouvements (entrée, sortie, transfert).',
                'Prise en charge des lots et dates de péremption.',
                "Dashboard d'alertes avec indicateur de stock à risque.",
            ]} />

            <h3>Niveaux d'alerte</h3>
            <table className="doc-table">
                <thead>
                    <tr>
                        <th>Niveau</th><th>Description</th><th>Couleur</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td>Critique</td><td>Stock ≤ seuil minimal défini</td><td><Badge color="red">Rouge</Badge></td></tr>
                    <tr><td>Faible</td><td>Stock ≤ 20% du seuil optimal</td><td><Badge color="amber">Amber</Badge></td></tr>
                    <tr><td>Normal</td><td>Stock dans les limites normales</td><td><Badge color="green">Vert</Badge></td></tr>
                </tbody>
            </table>

            <h3>Configurer un seuil d'alerte</h3>
            <CodeBlock lang="json" code={`{
  "produit_id": "SKU-001",
  "nom": "Eau Minérale 1.5L",
  "seuil_critique": 50,
  "seuil_optimal": 500,
  "unite": "caisse"
}`} />
        </Section>
    ),

    logistique: (
        <Section title="Logistique & Tournées" badge="Module">
            <p className="lead">
                Optimisez vos livraisons avec le module logistique NEXORA — pensé pour le terrain,
                fonctionnel même sans connexion internet.
            </p>

            <h3>Gestion des tournées</h3>
            <p>
                Créez et assignez des tournées de livraison à vos chauffeurs. L'application mobile
                synchronise automatiquement les données à la reconnexion réseau.
            </p>
            <FeatureList items={[
                'Création et planification des tournées par zone géographique.',
                'Affectation des chauffeurs et des véhicules.',
                'Suivi en temps réel des livraisons effectuées.',
                'Signature électronique de bon de livraison.',
                'Mode hors-ligne complet via Dexie.js + Service Workers.',
                'Gestion des retours et des invendus.',
            ]} />

            <h3>Statuts de livraison</h3>
            <table className="doc-table">
                <thead><tr><th>Statut</th><th>Signification</th></tr></thead>
                <tbody>
                    <tr><td><InlineCode>planifiee</InlineCode></td><td>Tournée créée, non démarrée</td></tr>
                    <tr><td><InlineCode>en_cours</InlineCode></td><td>Chauffeur en route</td></tr>
                    <tr><td><InlineCode>livree</InlineCode></td><td>Livraison confirmée</td></tr>
                    <tr><td><InlineCode>partielle</InlineCode></td><td>Livraison partielle enregistrée</td></tr>
                    <tr><td><InlineCode>echec</InlineCode></td><td>Livraison non effectuée</td></tr>
                </tbody>
            </table>
        </Section>
    ),

    finances: (
        <Section title="Aperçus Financiers" badge="Module">
            <p className="lead">
                Comprenez vos flux de trésorerie, vos ventes et votre rentabilité en un coup d'œil
                grâce aux tableaux de bord financiers NEXORA.
            </p>

            <h3>Métriques disponibles</h3>
            <FeatureList items={[
                'Chiffre d\'affaires journalier, hebdomadaire et mensuel.',
                'Marge brute par produit et par catégorie.',
                'Suivi des créances clients et des délais de paiement.',
                'Bilan des retours et avoirs.',
                'Graphiques d\'évolution des ventes (Recharts).',
                'Export PDF et Excel des rapports financiers.',
            ]} />

            <h3>Modes de paiement supportés</h3>
            <FeatureList items={[
                'Espèces (Cash)',
                'Virement bancaire',
                'Mobile Money (MTN, Orange Money)',
                'Crédit client (avec limite de crédit paramétrable)',
            ]} />

            <CallOut type="info">
                Les rapports financiers sont accessibles uniquement aux rôles <InlineCode>Admin</InlineCode> et <InlineCode>Comptable</InlineCode>.
            </CallOut>
        </Section>
    ),

    equipes: (
        <Section title="Équipes & Rôles" badge="Accès">
            <p className="lead">
                NEXORA intègre un système de rôles granulaire pour contrôler les accès de chaque
                membre de votre organisation.
            </p>

            <h3>Rôles disponibles</h3>
            <table className="doc-table">
                <thead><tr><th>Rôle</th><th>Description</th><th>Accès</th></tr></thead>
                <tbody>
                    <tr><td><Badge color="black">Super Admin</Badge></td><td>Supervise toutes les entreprises</td><td>Total</td></tr>
                    <tr><td><Badge color="blue">Admin</Badge></td><td>Gérant / Propriétaire de l'entreprise</td><td>Complet</td></tr>
                    <tr><td><Badge color="purple">Comptable</Badge></td><td>Accès aux finances et rapports</td><td>Finances</td></tr>
                    <tr><td><Badge color="amber">Logisticien</Badge></td><td>Gestion des tournées et livraisons</td><td>Logistique</td></tr>
                    <tr><td><Badge color="green">Commercial</Badge></td><td>Prise de commande terrain</td><td>Commandes</td></tr>
                    <tr><td><Badge color="gray">Magasinier</Badge></td><td>Gestion des entrées/sorties stock</td><td>Stock</td></tr>
                </tbody>
            </table>

            <h3>Inviter un collaborateur</h3>
            <StepList steps={[
                { num: 1, title: 'Aller dans Paramètres → Équipe', desc: 'Accessible depuis le menu latéral.' },
                { num: 2, title: 'Cliquer sur "Inviter un membre"', desc: "Saisissez l'adresse e-mail et choisissez le rôle." },
                { num: 3, title: 'Validation par e-mail', desc: "Le collaborateur reçoit un lien d'invitation valide 48h." },
            ]} />
        </Section>
    ),

    'multi-tenant': (
        <Section title="Architecture Multi-Entreprise" badge="Infrastructure">
            <p className="lead">
                NEXORA est conçu comme une plateforme SaaS multi-tenant. Chaque entreprise dispose
                de son propre espace cloisonné, géré par un Super Administrateur global.
            </p>

            <h3>Modèle d'isolation</h3>
            <p>
                Les données de chaque entreprise sont isolées via un système de <InlineCode>teams</InlineCode>.
                Aucune entreprise ne peut accéder aux données d'une autre, même si elles partagent la même
                infrastructure.
            </p>

            <h3>Hiérarchie des comptes</h3>
            <CodeBlock lang="txt" code={`Super Admin (NEXORA)
└── Entreprise A (Admin A)
    ├── Équipe A (Commerciaux, Logisticiens...)
    └── Données isolées (stock, finances, tournées)
└── Entreprise B (Admin B)
    ├── Équipe B
    └── Données isolées`} />

            <h3>Types d'entreprises supportés</h3>
            <FeatureList items={[
                'Grossiste Boissons — distribution en gros vers dépôts & détaillants',
                'Dépôt / Entrepôt — stockage et redistribution régionale',
                'Distributeur Exclusif — représentation officielle de marques',
                'Brasserie / Producteur — fabrication et distribution directe',
                'Importateur / Négociant — importation de boissons étrangères',
                'Service de Livraison — livraison à domicile et aux points de vente',
                'Bar / Buvette — point de vente et consommation sur place',
                'Restaurant / Maquis — restauration avec vente de boissons',
                'Supermarché / Minimarket — commerce de détail alimentaire & boissons',
            ]} />
        </Section>
    ),

    notifications: (
        <Section title="Notifications" badge="Système">
            <p className="lead">
                Restez informé des événements critiques de votre activité grâce au système de
                notifications en temps réel de NEXORA.
            </p>

            <h3>Types de notifications</h3>
            <FeatureList items={[
                'Alerte de stock critique (seuil atteint).',
                'Nouvelle commande reçue.',
                'Livraison confirmée ou en échec.',
                'Invitation d\'équipe acceptée.',
                'Rapport financier mensuel disponible.',
                'Synchronisation hors-ligne terminée.',
            ]} />

            <h3>Canaux de diffusion</h3>
            <table className="doc-table">
                <thead><tr><th>Canal</th><th>Disponibilité</th></tr></thead>
                <tbody>
                    <tr><td>Interface NEXORA (In-app)</td><td><Badge color="green">Disponible</Badge></td></tr>
                    <tr><td>E-mail</td><td><Badge color="green">Disponible</Badge></td></tr>
                    <tr><td>SMS</td><td><Badge color="amber">Phase 2</Badge></td></tr>
                    <tr><td>WhatsApp Business</td><td><Badge color="amber">Phase 2</Badge></td></tr>
                </tbody>
            </table>
        </Section>
    ),

    securite: (
        <Section title="Sécurité" badge="Protection">
            <p className="lead">
                NEXORA applique les meilleures pratiques de sécurité pour protéger les données
                sensibles de votre entreprise.
            </p>

            <FeatureList items={[
                'Authentification sécurisée via Laravel Fortify (hachage bcrypt).',
                'Tokens de session chiffrés et rotation automatique.',
                'Isolation multi-tenant stricte (aucune fuite de données inter-entreprises).',
                'Vérification e-mail obligatoire à l\'inscription.',
                'Protection CSRF sur tous les formulaires.',
                'Politique de mots de passe : minimum 8 caractères.',
                'Journalisation des accès et des actions critiques (audit log).',
                'HTTPS obligatoire en production.',
            ]} />

            <CallOut type="warning">
                En environnement de production, assurez-vous de changer les mots de passe par défaut
                et de configurer un certificat SSL valide avant de déployer NEXORA.
            </CallOut>
        </Section>
    ),

    api: (
        <Section title="Référence API" badge="Développeurs">
            <p className="lead">
                L'API REST NEXORA permet d'intégrer vos outils tiers (CRM, e-commerce, comptabilité)
                avec votre espace de distribution.
            </p>

            <h3>Authentification API</h3>
            <p>NEXORA utilise des tokens Bearer (Laravel Sanctum).</p>
            <CodeBlock lang="bash" code={`# Obtenir un token
POST /api/auth/token
Content-Type: application/json

{
  "email": "admin@votre-entreprise.com",
  "password": "votre_mot_de_passe"
}

# Réponse
{ "token": "1|abc123xyz..." }`} />

            <h3>Endpoints principaux</h3>
            <table className="doc-table">
                <thead><tr><th>Méthode</th><th>Endpoint</th><th>Description</th></tr></thead>
                <tbody>
                    <tr><td><Badge color="green">GET</Badge></td><td><InlineCode>/api/products</InlineCode></td><td>Liste des produits</td></tr>
                    <tr><td><Badge color="blue">POST</Badge></td><td><InlineCode>/api/products</InlineCode></td><td>Créer un produit</td></tr>
                    <tr><td><Badge color="green">GET</Badge></td><td><InlineCode>/api/stock/alerts</InlineCode></td><td>Alertes de stock</td></tr>
                    <tr><td><Badge color="green">GET</Badge></td><td><InlineCode>/api/tours</InlineCode></td><td>Tournées de livraison</td></tr>
                    <tr><td><Badge color="blue">POST</Badge></td><td><InlineCode>/api/tours</InlineCode></td><td>Créer une tournée</td></tr>
                    <tr><td><Badge color="green">GET</Badge></td><td><InlineCode>/api/reports/finance</InlineCode></td><td>Rapport financier</td></tr>
                </tbody>
            </table>

            <CallOut type="info">
                L'API complète avec tous les schémas de données sera disponible en Phase 2 du projet.
            </CallOut>
        </Section>
    ),

    configuration: (
        <Section title="Configuration" badge="Admin">
            <p className="lead">
                Personnalisez NEXORA selon les besoins de votre entreprise depuis l'espace Paramètres.
            </p>

            <h3>Paramètres disponibles</h3>
            <FeatureList items={[
                'Informations de l\'entreprise (nom, logo, adresse, RC/NIU).',
                'Devise et formats de date locaux.',
                'Seuils d\'alerte de stock personnalisables par produit.',
                'Modèles d\'e-mail (invitations, rapports).',
                'Gestion des entrepôts et des zones de livraison.',
                'Intégrations tierces (API clés, Webhooks).',
                'Préférences de notification par rôle et par événement.',
            ]} />

            <h3>Variables d'environnement clés</h3>
            <CodeBlock lang="bash" code={`APP_NAME=NEXORA
APP_LOCALE=fr
APP_URL=https://votre-domaine.com
DB_CONNECTION=pgsql
DB_DATABASE=nexora_prod
MAIL_MAILER=smtp
MAIL_HOST=smtp.votre-fournisseur.com`} />
        </Section>
    ),
};

/* ─────────────────────────────── SUB-COMPONENTS ────────────────────── */

function Section({ title, badge, children }: { title: string; badge: string; children: React.ReactNode }) {
    return (
        <motion.article
            key={title}
            initial={{ opacity: 0, y: 12 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.35 }}
            className="doc-article"
        >
            <div className="flex items-center gap-3 mb-6">
                <span className="inline-flex items-center rounded-full bg-foreground/8 border border-border px-2.5 py-0.5 text-xs font-medium text-muted-foreground">
                    {badge}
                </span>
            </div>
            <h2 className="text-3xl font-bold tracking-tight mb-6">{title}</h2>
            <div className="doc-content">{children}</div>
        </motion.article>
    );
}

function FeatureGrid({ features }: { features: { icon: React.ElementType; title: string; desc: string }[] }) {
    return (
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 my-6">
            {features.map(({ icon: Icon, title, desc }) => (
                <div key={title} className="rounded-xl border border-border bg-secondary/30 p-4 flex gap-3">
                    <div className="shrink-0 flex h-9 w-9 items-center justify-center rounded-lg bg-foreground/8 border border-border">
                        <Icon className="h-4 w-4 text-foreground/70" />
                    </div>
                    <div>
                        <p className="font-semibold text-sm">{title}</p>
                        <p className="text-xs text-muted-foreground mt-0.5 leading-relaxed">{desc}</p>
                    </div>
                </div>
            ))}
        </div>
    );
}

function FeatureList({ items }: { items: string[] }) {
    return (
        <ul className="my-4 space-y-2">
            {items.map((item, i) => (
                <li key={i} className="flex gap-2 text-sm text-foreground">
                    <CheckCircle className="h-4 w-4 text-foreground/40 shrink-0 mt-0.5" />
                    <span>{item}</span>
                </li>
            ))}
        </ul>
    );
}

function StepList({ steps }: { steps: { num: number; title: string; desc: string }[] }) {
    return (
        <ol className="my-4 space-y-4 relative pl-6 border-l border-border">
            {steps.map(({ num, title, desc }) => (
                <li key={num} className="relative">
                    <span className="absolute -left-[1.65rem] flex h-7 w-7 items-center justify-center rounded-full bg-foreground text-background text-xs font-bold">
                        {num}
                    </span>
                    <p className="font-semibold text-sm">{title}</p>
                    <p className="text-xs text-muted-foreground mt-0.5">{desc}</p>
                </li>
            ))}
        </ol>
    );
}

function CodeBlock({ code, lang }: { code: string; lang: string }) {
    return (
        <div className="my-4 rounded-xl border border-border bg-foreground/[0.03] overflow-hidden">
            <div className="flex items-center justify-between px-4 py-2 border-b border-border bg-foreground/[0.02]">
                <span className="text-xs text-muted-foreground font-mono">{lang}</span>
                <Code2 className="h-3.5 w-3.5 text-muted-foreground" />
            </div>
            <pre className="p-4 text-xs font-mono text-foreground/80 overflow-x-auto leading-relaxed whitespace-pre">
                {code}
            </pre>
        </div>
    );
}

function InlineCode({ children }: { children: React.ReactNode }) {
    return (
        <code className="px-1.5 py-0.5 rounded-md bg-foreground/8 border border-border text-xs font-mono">
            {children}
        </code>
    );
}

function CallOut({ type, children }: { type: 'tip' | 'warning' | 'info'; children: React.ReactNode }) {
    const styles = {
        tip: 'bg-green-50 border-green-200 text-green-800',
        warning: 'bg-amber-50 border-amber-200 text-amber-800',
        info: 'bg-blue-50 border-blue-200 text-blue-800',
    };
    const labels = { tip: '💡 Conseil', warning: '⚠️ Attention', info: 'ℹ️ Information' };

    return (
        <div className={`my-4 rounded-xl border px-4 py-3 text-sm leading-relaxed ${styles[type]}`}>
            <span className="font-semibold block mb-1">{labels[type]}</span>
            {children}
        </div>
    );
}

type BadgeColor = 'green' | 'red' | 'amber' | 'blue' | 'purple' | 'gray' | 'black';

function Badge({ color, children }: { color: BadgeColor; children: React.ReactNode }) {
    const styles: Record<BadgeColor, string> = {
        green: 'bg-green-100 text-green-700',
        red: 'bg-red-100 text-red-700',
        amber: 'bg-amber-100 text-amber-700',
        blue: 'bg-blue-100 text-blue-700',
        purple: 'bg-purple-100 text-purple-700',
        gray: 'bg-gray-100 text-gray-600',
        black: 'bg-foreground text-background',
    };

    return (
        <span className={`inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium ${styles[color]}`}>
            {children}
        </span>
    );
}

/* ─────────────────────────────── MAIN PAGE ─────────────────────────── */

export default function Docs() {
    const [active, setActive] = useState('introduction');
    const [sidebarOpen, setSidebarOpen] = useState(false);
    const [search, setSearch] = useState('');

    const filtered = navigation.filter(n =>
        n.label.toLowerCase().includes(search.toLowerCase())
    );

    return (
        <>
            <Head title="Documentation — NEXORA" />
            <ParticlesBackground />

            <div className="relative z-10 min-h-screen flex flex-col bg-background/95">

                {/* ── Top Navbar ── */}
                <header className="sticky top-0 z-40 flex h-14 items-center justify-between border-b border-border bg-background/90 backdrop-blur-md px-4 lg:px-8">
                    <div className="flex items-center gap-3">
                        <button
                            className="lg:hidden p-1.5 rounded-md hover:bg-secondary transition-colors"
                            onClick={() => setSidebarOpen(v => !v)}
                        >
                            {sidebarOpen ? <X className="h-5 w-5" /> : <Menu className="h-5 w-5" />}
                        </button>
                        <Link href="/" className="flex items-center gap-2.5">
                            <div className="flex h-7 w-7 items-center justify-center rounded-md bg-foreground text-background">
                                <Command className="h-3.5 w-3.5" />
                            </div>
                            <span className="font-bold text-base tracking-tight">NEXORA</span>
                        </Link>
                        <ChevronRight className="h-4 w-4 text-muted-foreground" />
                        <span className="text-sm text-muted-foreground">Documentation</span>
                    </div>
                    <div className="flex items-center gap-3">
                        <Link
                            href="/login"
                            className="text-sm font-medium text-muted-foreground hover:text-foreground transition-colors"
                        >
                            Connexion
                        </Link>
                        <Link
                            href="/register"
                            className="inline-flex h-8 items-center rounded-full bg-foreground text-background px-4 text-xs font-semibold hover:opacity-85 transition-opacity"
                        >
                            Créer un compte <ArrowRight className="ml-1.5 h-3 w-3" />
                        </Link>
                    </div>
                </header>

                <div className="flex flex-1 relative">

                    {/* ── Sidebar Mobile Overlay ── */}
                    <AnimatePresence>
                        {sidebarOpen && (
                            <motion.div
                                initial={{ opacity: 0 }}
                                animate={{ opacity: 1 }}
                                exit={{ opacity: 0 }}
                                className="fixed inset-0 z-30 bg-black/20 lg:hidden"
                                onClick={() => setSidebarOpen(false)}
                            />
                        )}
                    </AnimatePresence>

                    {/* ── Sidebar ── */}
                    <aside className={`
                        fixed lg:sticky top-14 z-30 lg:z-auto
                        h-[calc(100vh-3.5rem)] w-64 shrink-0
                        border-r border-border bg-background/98 backdrop-blur-sm
                        overflow-y-auto flex flex-col gap-1 p-4
                        transition-transform duration-300 ease-out
                        ${sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'}
                    `}>
                        {/* Search */}
                        <div className="relative mb-3">
                            <Search className="absolute left-2.5 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-muted-foreground" />
                            <input
                                type="text"
                                value={search}
                                onChange={e => setSearch(e.target.value)}
                                placeholder="Rechercher..."
                                className="w-full h-8 pl-8 pr-3 rounded-lg border border-border bg-secondary/50 text-sm placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-foreground/10 transition-all"
                            />
                        </div>

                        {filtered.map(({ id, label, icon: Icon }) => (
                            <button
                                key={id}
                                onClick={() => {
 setActive(id); setSidebarOpen(false); 
}}
                                className={`
                                    w-full flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm font-medium text-left
                                    transition-all duration-150
                                    ${active === id
                                        ? 'bg-foreground text-background'
                                        : 'text-muted-foreground hover:text-foreground hover:bg-secondary/60'
                                    }
                                `}
                            >
                                <Icon className="h-4 w-4 shrink-0" />
                                {label}
                            </button>
                        ))}
                    </aside>

                    {/* ── Main Content ── */}
                    <main className="flex-1 min-w-0 px-6 py-10 lg:px-16 xl:px-24 max-w-4xl">
                        <AnimatePresence mode="wait">
                            {sections[active] ?? (
                                <p className="text-muted-foreground text-sm">Section introuvable.</p>
                            )}
                        </AnimatePresence>
                    </main>
                </div>

                {/* ── Footer ── */}
                <footer className="border-t border-border py-6 text-center text-xs text-muted-foreground">
                    © {new Date().getFullYear()} NEXORA · Documentation v1.0
                </footer>
            </div>

            {/* ── Inline styles for doc content ── */}
            <style>{`
                .doc-article {}
                .doc-content p { margin-bottom: 1rem; font-size: 0.9375rem; line-height: 1.75; color: hsl(var(--muted-foreground)); }
                .doc-content p.lead { font-size: 1.05rem; color: hsl(var(--foreground)); border-left: 3px solid hsl(var(--border)); padding-left: 1rem; }
                .doc-content h3 { font-size: 1.125rem; font-weight: 700; margin-top: 2rem; margin-bottom: 0.5rem; color: hsl(var(--foreground)); }
                .doc-table { width: 100%; border-collapse: collapse; margin: 1rem 0; font-size: 0.875rem; }
                .doc-table th { text-align: left; font-weight: 600; padding: 0.5rem 0.75rem; background: hsl(var(--secondary)); border-bottom: 1px solid hsl(var(--border)); }
                .doc-table td { padding: 0.5rem 0.75rem; border-bottom: 1px solid hsl(var(--border)/0.5); color: hsl(var(--muted-foreground)); }
                .doc-table tr:last-child td { border-bottom: none; }
            `}</style>
        </>
    );
}

Docs.layout = undefined;
