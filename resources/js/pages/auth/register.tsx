import { Head, Link, router } from '@inertiajs/react';
import { useState, useRef, useEffect } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import {
  ArrowRight, Bot, User, CheckCircle2, Sparkles,
  Package, Truck, Boxes, CreditCard, MapPin, Users,
  Building2, Eye, EyeOff, Coffee, Zap, Globe, Map, Coins,
  BedDouble, UtensilsCrossed, CalendarCheck, Layout, ChefHat, DollarSign, ShoppingCart,
} from 'lucide-react';

import AppLogoIcon from '@/components/app-logo-icon';
import { PageTransition } from '@/components/ui/page-transition';
import { ParticlesBackground } from '@/components/ui/particles-background';

// ─── Types ──────────────────────────────────────────────────────────────────

interface Message {
  id: string;
  sender: 'nexa' | 'user';
  text: React.ReactNode;
}

type CompanyType = 'boissons' | 'hotel' | 'fnb' | 'hotel_fnb';

interface SectorConfig {
  label: string;
  icon: React.ReactNode;
  modules: { id: string; name: string; description: string; priority: string; icon: React.ReactNode }[];
  roles: { role: string; label: string }[];
  defaultCategories: string[];
}

const BOISSONS_CONFIG: SectorConfig = {
  label: 'Distribution Boissons',
  icon: <Coffee className="h-3.5 w-3.5" />,
  modules: [
    { id: 'stock', name: 'Gestion du Stock', priority: 'core', description: 'Entrepôts, lots, niveaux de stock en temps réel', icon: <Boxes className="h-3.5 w-3.5" /> },
    { id: 'commandes', name: 'Commandes & Livraisons', priority: 'core', description: 'Tournées, chauffeurs, expéditions multicanaux', icon: <Truck className="h-3.5 w-3.5" /> },
    { id: 'consignes', name: 'Casiers & Consignes', priority: 'core', description: 'Suivi des emballages consignables par client', icon: <Package className="h-3.5 w-3.5" /> },
    { id: 'clients', name: 'Portefeuille Clients', priority: 'core', description: 'Clients B2B/B2C, tarification sur mesure', icon: <Users className="h-3.5 w-3.5" /> },
    { id: 'finance', name: 'Finance & Caisse', priority: 'recommended', description: 'Paiements, facturation et sessions de caisse', icon: <CreditCard className="h-3.5 w-3.5" /> },
  ],
  roles: [
    { role: 'admin', label: 'Administrateur' },
    { role: 'manager', label: 'Manager Opérations' },
    { role: 'commercial', label: 'Commercial' },
    { role: 'livreur', label: 'Livreur' },
    { role: 'magasinier', label: 'Magasinier' },
  ],
  defaultCategories: ['Bières', 'Sodas', 'Eaux', 'Vins & Spiritueux', 'Jus de fruits'],
};

const HOTEL_CONFIG: SectorConfig = {
  label: 'Hôtellerie',
  icon: <BedDouble className="h-3.5 w-3.5" />,
  modules: [
    { id: 'reservations', name: 'Réservations', priority: 'core', description: 'Gestion des arrivées, départs et tarifs chambre', icon: <CalendarCheck className="h-3.5 w-3.5" /> },
    { id: 'chambres', name: 'Gestion des Chambres', priority: 'core', description: 'Statut des chambres, ménage et maintenance', icon: <Layout className="h-3.5 w-3.5" /> },
    { id: 'facturation', name: 'Facturation Hôtel', priority: 'recommended', description: 'Notes de séjour, acomptes et reporting de revenu', icon: <CreditCard className="h-3.5 w-3.5" /> },
    { id: 'clients', name: 'Clients & Groupes', priority: 'recommended', description: 'Fichiers clients, entreprises et contrats séminaires', icon: <Users className="h-3.5 w-3.5" /> },
  ],
  roles: [
    { role: 'admin', label: 'Administrateur' },
    { role: 'manager_hotel', label: 'Manager Hôtel' },
    { role: 'receptionniste', label: 'Réceptionniste' },
    { role: 'housekeeping', label: 'Housekeeping' },
  ],
  defaultCategories: ['Chambres', 'Tarifs', 'Services', 'Clients'],
};

const FNB_CONFIG: SectorConfig = {
  label: 'Restauration F&B',
  icon: <UtensilsCrossed className="h-3.5 w-3.5" />,
  modules: [
    { id: 'menus', name: 'Menus & Articles', priority: 'core', description: 'Cartes, catégories, prix et variantes', icon: <Package className="h-3.5 w-3.5" /> },
    { id: 'salle', name: 'Service en Salle', priority: 'core', description: 'Gestion des tables, commandes et tickets', icon: <ShoppingCart className="h-3.5 w-3.5" /> },
    { id: 'cuisine', name: 'Écran Cuisine', priority: 'recommended', description: 'Flux de préparation en temps réel pour les plats', icon: <ChefHat className="h-3.5 w-3.5" /> },
    { id: 'caisses', name: 'Caisse & Paiements', priority: 'recommended', description: 'Encaissements, clôtures et notes de frais', icon: <DollarSign className="h-3.5 w-3.5" /> },
  ],
  roles: [
    { role: 'admin', label: 'Administrateur' },
    { role: 'manager_fnb', label: 'Manager F&B' },
    { role: 'serveur', label: 'Serveur' },
    { role: 'caissier_fnb', label: 'Caissier F&B' },
  ],
  defaultCategories: ['Plats', 'Boissons', 'Menus', 'Tables'],
};

const HOTEL_FNB_CONFIG: SectorConfig = {
  label: 'Hôtel + F&B liés',
  icon: <Sparkles className="h-3.5 w-3.5" />,
  modules: [
    ...HOTEL_CONFIG.modules,
    ...FNB_CONFIG.modules,
  ],
  roles: [
    ...HOTEL_CONFIG.roles,
    ...FNB_CONFIG.roles.filter(role => role.role !== 'admin'),
  ],
  defaultCategories: [...HOTEL_CONFIG.defaultCategories, ...FNB_CONFIG.defaultCategories],
};

const SECTOR_CONFIGS: Record<CompanyType, SectorConfig> = {
  boissons: BOISSONS_CONFIG,
  hotel: HOTEL_CONFIG,
  fnb: FNB_CONFIG,
  hotel_fnb: HOTEL_FNB_CONFIG,
};

// ─── Config Preview ───────────────────────────────────────────────────────────

function ConfigPreview({ config }: { config: SectorConfig }) {
  return (
    <motion.div
      initial={{ opacity: 0, y: 10 }}
      animate={{ opacity: 1, y: 0 }}
      transition={{ duration: 0.5, ease: [0.16, 1, 0.3, 1] }}
      className="mt-4 space-y-5 text-sm"
    >
      <div className="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border border-slate-300 bg-slate-100 text-xs font-medium text-slate-700 shadow-sm">
        {config.icon}
        <span>Configuration: {config.label}</span>
      </div>

      <div>
        <p className="text-[10px] font-semibold uppercase tracking-widest text-slate-500 mb-2.5">Modules Opérationnels</p>
        <div className="space-y-2">
          {config.modules.map(mod => (
            <div key={mod.id} className="flex items-start gap-3 py-2.5 px-3 rounded-xl bg-slate-100/80 border border-slate-200 hover:border-slate-300 hover:shadow-sm transition-all duration-300">
              <div className="mt-0.5 text-slate-400 shrink-0">{mod.icon}</div>
              <div className="min-w-0">
                <p className="text-xs font-medium text-slate-900 mb-0.5">{mod.name}</p>
                <p className="text-[11px] text-slate-500 leading-relaxed">{mod.description}</p>
              </div>
            </div>
          ))}
        </div>
      </div>

      <div className="flex items-start gap-2.5 p-3 rounded-xl bg-slate-50 border border-slate-100">
        <Zap className="h-3.5 w-3.5 text-slate-400 mt-0.5 shrink-0" />
        <p className="text-[11px] text-slate-600 leading-relaxed">
          Le système déploiera automatiquement les structures de données (Casiers, Lots, Tournées) optimisées pour votre activité.
        </p>
      </div>
    </motion.div>
  );
}

// ─── Main Register Component ──────────────────────────────────────────────────

export default function Register() {

  // Step 0: Select business type (module)
  const [messages, setMessages] = useState<Message[]>([
    {
      id: '1',
      sender: 'nexa',
      text: (
        <div className="space-y-3">
          <p className="text-slate-900 font-medium">Bienvenue sur NEXORA ✦</p>
          <p className="text-slate-500 text-[13px]">Pour commencer, sélectionnez le type d'entreprise qui correspond le mieux à votre activité. Ce choix déterminera les modules et fonctionnalités adaptés à votre secteur.</p>
        </div>
      )
    }
  ]);

  const [step, setStep] = useState(0);
  const [isTyping, setIsTyping] = useState(false);
  const [companyData, setCompanyData] = useState({
    name: '', country: '', region: '', city: '', currency: '',
    companyType: '' as CompanyType | '',
    selectedModules: [] as string[],
    estimatedRooms: '' as string,
    estimatedTables: '' as string,
    phone: '', email: '', password: '',
  });

  const fieldLabels: Record<string, string> = {
    name: 'nom de l’entreprise',
    country: 'pays',
    region: 'région',
    city: 'ville',
    currency: 'monnaie',
    phone: 'numéro de téléphone',
    email: 'email administrateur',
  };

  const chatEndRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    chatEndRef.current?.scrollIntoView({ behavior: 'smooth' });
  }, [messages, isTyping]);

  const addNexaMessage = (text: React.ReactNode, delay = 800) => {
    setIsTyping(true);
    setTimeout(() => {
      setMessages(prev => [...prev, { id: Date.now().toString(), sender: 'nexa', text }]);
      setIsTyping(false);
    }, delay);
  };

  const addUserMessage = (text: string) => {
    setMessages(prev => [...prev, { id: Date.now().toString(), sender: 'user', text }]);
  };

  const getCsrfToken = () => {
    return (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement | null)?.content || '';
  };

  // ─── AI Intent Handling ──────────────────────────────────────────────────

  const fetchNexaAI = async (userInput: string, currentStep: number, currentData: typeof companyData, intent = 'standard'): Promise<string | null> => {
    try {
      const res = await fetch('/nexa-chat', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': getCsrfToken(),
        },
        body: JSON.stringify({
          step: currentStep,
          intent,
          input: userInput,
          companyData: currentData,
          sectorKey: currentData.companyType || 'boissons',
        }),
      });
      const data = await res.json();
      return data?.reply || null;
    } catch (err: unknown) {
      console.error('NEXA chat error', err);
      return null;
    }
  };

  const isCorrectionRequest = (value: string) => {
    return /\b(corrig|modifier|changer|recommen|repren|rectif|erreur|corrige)\b/i.test(value);
  };

  const guessCorrectionField = (value: string): string | null => {
    if (/\b(nom|entreprise|soci[eé]t[eé]|raison sociale)\b/i.test(value)) return 'name';
    if (/\b(pays|nation)\b/i.test(value)) return 'country';
    if (/\b(r[eé]gion|province|d[eé]partement)\b/i.test(value)) return 'region';
    if (/\b(ville|localisation|adresse)\b/i.test(value)) return 'city';
    if (/\b(monnaie|devise|currency|franc|euro|dollar)\b/i.test(value)) return 'currency';
    if (/\b(t[eé]l[eé]phone|portable|contact)\b/i.test(value)) return 'phone';
    if (/\b(email|courriel|mail)\b/i.test(value)) return 'email';
    return null;
  };

  const handleCorrectionRequest = async (value: string) => {
    addUserMessage(value);
    const field = guessCorrectionField(value);

    if (field) {
      // step 5 = modules, so phone is now step 6, email step 7
      const stepMap: Record<string, number> = { name: 0, country: 1, region: 2, city: 3, currency: 4, phone: 6, email: 7 };
      setStep(stepMap[field]);
      addNexaMessage(`Entendu. Indiquez la nouvelle valeur pour le ${fieldLabels[field]}.`, 400);
      return;
    }

    const aiReply = await fetchNexaAI(value, step, companyData, 'correction');
    addNexaMessage(aiReply || "Je peux corriger : nom, pays, région, ville, monnaie, téléphone, ou email.", 400);
  };

  const handleReviewEdit = (field: string) => {
    const stepMap: Record<string, number> = { name: 0, country: 1, region: 2, city: 3, currency: 4, phone: 6, email: 7 };
    setStep(stepMap[field]);
    addNexaMessage(`Très bien. Quelle est la nouvelle valeur pour le ${fieldLabels[field]} ?`, 400);
  };

  // ─── Step Handlers ────────────────────────────────────────────────────────

  // Step 0: Select business type
  const handleStep0 = async (companyType: CompanyType) => {
    const modules = companyType === 'hotel_fnb'
      ? ['hotel', 'fnb']
      : companyType === 'hotel'
      ? ['hotel']
      : companyType === 'fnb'
      ? ['fnb']
      : [];
    const allModules = Array.from(new Set(['drinks', ...modules]));
    setCompanyData(prev => ({ ...prev, companyType, selectedModules: allModules }));
    addUserMessage(SECTOR_CONFIGS[companyType].label);
    setStep(1);
    addNexaMessage(
      <div className="space-y-3">
        <p className="text-slate-900 font-medium">Type sélectionné : <span className="text-slate-950 font-bold">{SECTOR_CONFIGS[companyType].label}</span></p>
        {companyType === 'hotel_fnb' && (
          <p className="text-[12px] text-emerald-600 flex items-center gap-1.5">
            <Sparkles className="h-3 w-3" />
            Mode 3 — Hôtel + F&B liés : gestion synchronisée des chambres, réservations, commandes et cuisine pour une expérience intégrée.
          </p>
        )}
        <p className="text-slate-500 text-[13px]">Quel est le nom de votre entreprise ?</p>
      </div>,
      800
    );
  };

  // Step 1: Company name -> Country
  const handleStep1 = async (value: string) => {
    addUserMessage(value);
    const updated = { ...companyData, name: value };
    setCompanyData(updated);
    setStep(2);
    addNexaMessage(`Dans quel pays votre entreprise est-elle située ?`, 600);
  };

  const handleStep2 = async (value: string) => { // Region -> City
    addUserMessage(value);
    const updated = { ...companyData, region: value };
    setCompanyData(updated);
    setStep(3);
    addNexaMessage(`Parfait. Et dans quelle ville précisément ?`, 600);
  };

  const handleStep3 = async (value: string) => { // City -> Currency
    addUserMessage(value);
    const updated = { ...companyData, city: value };
    setCompanyData(updated);
    setStep(4);
    addNexaMessage(`D'accord. Quelle monnaie sera utilisée pour vos opérations ? (ex: XOF, EUR, USD)`, 600);
  };

  const handleStep4 = async (value: string) => { // Currency -> Phone
    addUserMessage(value);
    const updated = { ...companyData, currency: value.toUpperCase() };
    setCompanyData(updated);
    setStep(5);
    addNexaMessage(`Devise ${value.toUpperCase()} configurée. Quel est le numéro de téléphone de l'entreprise ? (Format : +228 90 00 00 00)`, 600);
  };

  // Step 5 is now phone input
  // (removed module selection here)

  const validatePhone = (phone: string) => {
    const phoneRegex = /^\+?[0-9\s-]{8,20}$/;
    return phoneRegex.test(phone);
  };

  const handleStep6 = async (value: string) => { // Phone -> Email/Pwd
    if (!validatePhone(value)) {
      addNexaMessage(`Le format du numéro semble incorrect. Veuillez utiliser un format valide (ex: +228 90 00 00 00).`, 400);
      return;
    }
    addUserMessage(value);
    const updated = { ...companyData, phone: value };
    setCompanyData(updated);
    setStep(6);
    addNexaMessage(`C'est noté. Pour finaliser, veuillez définir l'email et le mot de passe du compte administrateur.`, 600);
  };

  const handleStep7 = async (value: { email: string; password: string }) => { // Email/Pwd -> OTP
    addUserMessage(value.email + ' · ••••••••');
    const updated = { ...companyData, email: value.email, password: value.password };
    setCompanyData(updated);
    setIsTyping(true);

    try {
      const response = await fetch('/send-otp', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': getCsrfToken(),
        },
        body: JSON.stringify({ email: value.email }),
      });

      if (!response.ok) {
        const data = await response.json();
        throw new Error(data.message || "Erreur lors de l'envoi du code");
      }

      setIsTyping(false);
      setStep(7);
      addNexaMessage(
        <div className="space-y-2">
          <p>Pour sécuriser votre compte, un code a été envoyé à <strong className="font-medium text-slate-900">{value.email}</strong>.</p>
          <p className="text-slate-500">Veuillez saisir le code à 6 chiffres ci-dessous.</p>
        </div>,
        400
      );
    } catch (err: unknown) {
      setIsTyping(false);
      const msg = err instanceof Error ? err.message : "Erreur d'envoi du code. Veuillez réessayer.";
      addNexaMessage(<span>{msg}</span>, 400);
    }
  };

  const handleStep8 = async (value: string) => { // OTP -> Review
    addUserMessage(value);
    setIsTyping(true);

    try {
      const response = await fetch('/verify-otp', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': getCsrfToken(),
        },
        body: JSON.stringify({ email: companyData.email, otp: value }),
      });

      if (!response.ok) {
        const data = await response.json();
        throw new Error(data.message || 'Code invalide ou expiré');
      }

      setIsTyping(false);
      setStep(8);

      const activeModules = companyData.selectedModules.length ? companyData.selectedModules : ['drinks'];

      addNexaMessage(
        <div className="space-y-4">
          <p className="flex items-center gap-2"><CheckCircle2 className="h-4 w-4 text-emerald-500" /> Adresse vérifiée avec succès.</p>

          <div className="p-4 rounded-xl border border-slate-100 bg-slate-50/80 text-xs text-slate-600 space-y-2">
            <p className="text-slate-400 font-semibold tracking-wide uppercase mb-3">Synthèse du Déploiement</p>
            <p className="flex justify-between"><span>Entreprise:</span> <strong className="text-slate-900">{companyData.name}</strong></p>
            <p className="flex justify-between"><span>Localisation:</span> <strong className="text-slate-900">{companyData.city}, {companyData.region}, {companyData.country}</strong></p>
            <p className="flex justify-between"><span>Devise:</span> <strong className="text-slate-900">{companyData.currency}</strong></p>
            <p className="flex justify-between"><span>Secteur:</span> <strong className="text-slate-900">{SECTOR_CONFIGS[companyData.companyType || 'boissons'].label}</strong></p>
            <p className="flex justify-between"><span>Modules actifs:</span> <strong className="text-slate-900">{activeModules.length}</strong></p>
            {activeModules.includes('hotel') && (
              <p className="flex justify-between items-center">
                <span className="flex items-center gap-1"><BedDouble className="h-3 w-3 text-slate-400" /> Hôtellerie:</span>
                <strong className="text-slate-900">{companyData.estimatedRooms ? companyData.estimatedRooms + ' chambres' : 'Activé'}</strong>
              </p>
            )}
            {activeModules.includes('fnb') && (
              <p className="flex justify-between items-center">
                <span className="flex items-center gap-1"><UtensilsCrossed className="h-3 w-3 text-slate-400" /> Restauration:</span>
                <strong className="text-slate-900">{companyData.estimatedTables ? companyData.estimatedTables + ' tables' : 'Activé'}</strong>
              </p>
            )}
            {activeModules.includes('hotel') && activeModules.includes('fnb') && (
              <p className="flex items-center gap-1.5 text-emerald-600 pt-1">
                <Sparkles className="h-3 w-3" />
                <span>Mode liaison Hôtel + Restaurant activé</span>
              </p>
            )}
          </div>

          <p className="text-slate-500">L'environnement est prêt. Vous pouvez valider la création de l'espace.</p>
        </div>,
        600
      );
    } catch (err: unknown) {
      setIsTyping(false);
      const msg = err instanceof Error ? err.message : 'Veuillez vérifier et réessayer.';
      addNexaMessage(`Code invalide — ${msg}`, 400);
    }
  };

  const handleInputSubmit = async (value: any) => {
    if (typeof value === 'string' && isCorrectionRequest(value)) {
      await handleCorrectionRequest(value);
      return;
    }
    const handlers: Record<number, (v: any) => Promise<void>> = {
      0: handleStep0, 1: handleStep1, 2: handleStep2,
      3: handleStep3, 4: handleStep4, 5: handleStep6,
      6: handleStep7, 7: handleStep8, 8: undefined,
    };
    await handlers[step]?.(value);
  };

  const submitRegistration = () => {
    setIsTyping(true);
    const modules = Array.from(new Set(['drinks', ...companyData.selectedModules]));
    const hasHotel = modules.includes('hotel');
    const hasFnB = modules.includes('fnb');

    const selectedType = companyData.companyType || (hasHotel && hasFnB ? 'hotel_fnb' : hasHotel ? 'hotel' : hasFnB ? 'fnb' : 'boissons');
    const selectedConfig = SECTOR_CONFIGS[selectedType];

    const payload = {
      name: 'Admin ' + companyData.name,
      company_name: companyData.name,
      company_type: selectedType,
      ville: companyData.city,
      region: companyData.region,
      pays: companyData.country,
      currency: companyData.currency,
      telephone: companyData.phone,
      email: companyData.email,
      password: companyData.password,
      password_confirmation: companyData.password,
      warehouses: '1',
      modules,
      estimated_rooms: companyData.estimatedRooms || null,
      estimated_tables: companyData.estimatedTables || null,
      default_categories: selectedConfig.defaultCategories,
      roles: selectedConfig.roles.map(r => r.role),
      plan: 'pro',
      sector_config: selectedType,
    };

    router.post('/register', payload, {
      onError: (errors) => {
        setIsTyping(false);
        const firstError = Object.values(errors)[0];
        addNexaMessage(
          <div className="text-red-500 bg-red-50/50 p-3 rounded-lg border border-red-100">
            <p className="font-bold flex items-center gap-2">
              <Bot className="h-4 w-4" />
              Un problème est survenu
            </p>
            <p className="mt-1 text-xs opacity-90">{firstError as string}</p>
          </div>,
          400
        );
      },
      onSuccess: () => {
        setIsTyping(false);
        setStep(9);
        addNexaMessage(
          <div className="space-y-2">
            <p className="text-slate-900 font-medium">✨ Initialisation terminée</p>
            <p className="text-slate-500">Votre plateforme NEXORA est configurée avec succès.</p>
            {(hasHotel || hasFnB) && (
              <p className="text-[12px] text-emerald-600 flex items-center gap-1.5">
                <Sparkles className="h-3 w-3" />
                {hasHotel && hasFnB
                  ? 'Espace Hôtel + Restaurant prêt — mode liaison activé.'
                  : hasHotel
                  ? 'Module Hôtellerie activé.'
                  : 'Module Restauration F&B activé.'}
              </p>
            )}
            <p className="text-slate-400 text-[12px]">Redirection en cours...</p>
          </div>,
          200
        );
      }
    });
  };

  // ─── Input Renderer ────────────────────────────────────────────────────────

  const renderInputArea = () => {
    if (step === 0) {
      // Business type selection UI
      return (
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
          {(['boissons', 'hotel', 'fnb', 'hotel_fnb'] as CompanyType[]).map(type => (
            <button
              key={type}
              type="button"
              onClick={() => handleInputSubmit(type)}
              className="flex flex-col items-center gap-2 p-4 rounded-xl border border-slate-200 bg-white hover:bg-emerald-50 hover:border-emerald-400 transition-all shadow-sm group"
            >
              <span className="text-2xl">{SECTOR_CONFIGS[type].icon}</span>
              <span className="font-semibold text-slate-800 text-sm group-hover:text-emerald-700">{SECTOR_CONFIGS[type].label}</span>
              {type === 'hotel_fnb' && (
                <span className="text-[11px] text-emerald-600 font-medium mt-1">Mode 3 — Hôtel + F&B liés</span>
              )}
            </button>
          ))}
        </div>
      );
    }
    if (step === 1) {
      return <SingleInput onSubmit={handleInputSubmit} placeholder="Nom de la société..." />;
    }
    if (step === 2) {
      return <SingleInput onSubmit={handleInputSubmit} placeholder="Pays..." />;
    }
    if (step === 3) {
      return <SingleInput onSubmit={handleInputSubmit} placeholder="Région..." />;
    }
    if (step === 4) {
      return <SingleInput onSubmit={handleInputSubmit} placeholder="Ville..." />;
    }
    if (step === 5) {
      return <SingleInput onSubmit={handleInputSubmit} placeholder="Téléphone (ex: +228 90 00 00 00)..." />;
    }
    if (step === 6) {
      return <DoubleInput onSubmit={handleInputSubmit} ph1="Email professionnel" ph2="Mot de passe sécurisé" k1="email" k2="password" type2="password" />;
    }
    if (step === 7) {
      return <OTPInput onSubmit={handleInputSubmit} />;
    }
    if (step === 8) {
      return (
        <motion.div initial={{ opacity: 0, y: 10 }} animate={{ opacity: 1, y: 0 }} className="space-y-4">
          <div className="flex flex-wrap gap-2 items-center text-xs text-slate-500 mb-2">
            <span>Ajuster :</span>
            {(['name', 'country', 'region', 'city', 'currency', 'phone', 'email'] as const).map(field => (
              <button
                key={field}
                type="button"
                onClick={() => handleReviewEdit(field)}
                className="px-2.5 py-1 rounded-md border border-slate-300 bg-slate-100 hover:bg-slate-200 transition-colors text-slate-700 shadow-sm"
              >
                {fieldLabels[field]}
              </button>
            ))}
          </div>
          <button
            onClick={submitRegistration}
            className="w-full h-12 rounded-xl text-sm font-medium tracking-wide flex items-center justify-center gap-2 bg-slate-800 text-white hover:bg-slate-700 transition-colors shadow-md"
          >
            <Sparkles className="h-4 w-4" />
            Déployer l'espace de travail
          </button>
        </motion.div>
      );
    }
    if (step === 9) {
      return (
        <motion.div initial={{ opacity: 0 }} animate={{ opacity: 1 }}>
          <Link href="/login" className="w-full h-12 rounded-xl border border-slate-300 bg-slate-100 text-slate-700 text-sm font-medium flex items-center justify-center hover:bg-slate-200 hover:text-slate-900 transition-all shadow-sm">
            Accéder à l'espace →
          </Link>
        </motion.div>
      );
    }
    return null;
  };

  const totalSteps = 9;
  const progress = Math.min((step / totalSteps) * 100, 100);

  const stepLabels = [
    { label: 'Secteur', icon: <Boxes className="h-3 w-3" /> },
    { label: 'Entreprise', icon: <Building2 className="h-3 w-3" /> },
    { label: 'Pays', icon: <Globe className="h-3 w-3" /> },
    { label: 'Région', icon: <Map className="h-3 w-3" /> },
    { label: 'Ville', icon: <MapPin className="h-3 w-3" /> },
    { label: 'Contact', icon: <Users className="h-3 w-3" /> },
    { label: 'Administrateur', icon: <User className="h-3 w-3" /> },
    { label: 'Vérification', icon: <CheckCircle2 className="h-3 w-3" /> },
    { label: 'Finalisation', icon: <Sparkles className="h-3 w-3" /> },
  ];

  return (
    <>
      <Head title="Créer un espace — NEXORA" />

      <style>{`
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=JetBrains+Mono:wght@400;500&display=swap');

        .nexora-clean * { font-family: 'Inter', sans-serif; }
        .nexora-clean .mono { font-family: 'JetBrains Mono', monospace; }

        .nexora-clean ::-webkit-scrollbar { width: 4px; }
        .nexora-clean ::-webkit-scrollbar-track { background: transparent; }
        .nexora-clean ::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.1); border-radius: 4px; }

        .clean-glass {
          background: rgba(248, 250, 252, 0.85);
          backdrop-filter: blur(24px);
          -webkit-backdrop-filter: blur(24px);
          border: 1px solid rgba(0, 0, 0, 0.04);
          box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.08), 0 0 0 1px rgba(0,0,0,0.02);
        }

        .clean-input {
          background: rgba(241, 245, 249, 0.6);
          border: 1px solid #cbd5e1;
          color: #0f172a;
          transition: all 0.2s ease;
          font-size: 13px;
        }
        .clean-input::placeholder { color: #94a3b8; }
        .clean-input:focus {
          outline: none;
          border-color: #94a3b8;
          background: #f8fafc;
          box-shadow: 0 0 0 3px rgba(226, 232, 240, 0.5);
        }

        .step-line::after {
          content: '';
          position: absolute;
          left: 9px;
          top: 22px;
          bottom: -8px;
          width: 1px;
          background: #e2e8f0;
        }
        .step-line:last-child::after { display: none; }

        @keyframes pulse-soft {
          0%, 100% { opacity: 0.4; transform: scale(1); }
          50% { opacity: 1; transform: scale(1.1); }
        }
        .dot-typing { animation: pulse-soft 1.4s ease-in-out infinite; }

        @keyframes gradient-pan {
          0% { background-position: 0% 50%; }
          50% { background-position: 100% 50%; }
          100% { background-position: 0% 50%; }
        }
        .animated-bg {
          background: linear-gradient(-45deg, #e2e8f0, #cbd5e1, #f1f5f9, #e2e8f0);
          background-size: 400% 400%;
          animation: gradient-pan 15s ease infinite;
        }
      `}</style>

      <PageTransition className="nexora-clean relative z-10 min-h-screen flex items-center justify-center p-4 animated-bg overflow-hidden">
        <div className="absolute inset-0 z-0 opacity-40 mix-blend-multiply pointer-events-none">
          <ParticlesBackground />
        </div>

        <motion.div
          initial={{ opacity: 0, y: 30, scale: 0.98 }}
          animate={{ opacity: 1, y: 0, scale: 1 }}
          transition={{ duration: 0.8, ease: [0.16, 1, 0.3, 1] }}
          className="relative z-10 w-full max-w-[800px] h-[580px] flex rounded-3xl overflow-hidden clean-glass shadow-2xl shadow-slate-200/50"
        >
          {/* ── Left Sidebar ── */}
          <div className="hidden md:flex w-[260px] shrink-0 flex-col p-8 border-r border-slate-100 bg-white/40 backdrop-blur-md relative overflow-hidden">

            <div className="absolute top-0 left-0 w-full h-full pointer-events-none">
              <div className="absolute -top-20 -left-20 w-64 h-64 bg-slate-200/50 blur-[60px] rounded-full mix-blend-multiply" />
            </div>

            {/* Logo */}
            <Link href="/" className="flex items-center gap-3 no-underline z-10 group">
              <div className="flex h-8 w-8 items-center justify-center rounded-lg border border-slate-300 bg-slate-50 shadow-sm transition-all group-hover:border-slate-400">
                <AppLogoIcon className="h-4 w-4 text-slate-800" />
              </div>
              <span className="text-slate-900 text-sm font-semibold tracking-wider">NEXORA</span>
            </Link>

            <div className="mt-8 mb-6 h-px bg-slate-200/60" />

            {/* Steps Tracker */}
            <div className="z-10 flex-1 overflow-y-auto">
              {stepLabels.map((s, i) => {
                const isDone = i < step;
                const isCurrent = i === step;

                return (
                  <div key={i} className="step-line relative flex items-start gap-3.5 pb-4">
                    <div className="shrink-0 relative flex items-center justify-center h-5 w-5 mt-0.5">
                      {isDone ? (
                        <motion.div
                          initial={{ scale: 0.8 }}
                          animate={{ scale: 1 }}
                          className="h-4 w-4 rounded-full bg-slate-900 text-white flex items-center justify-center shadow-sm"
                        >
                          <CheckCircle2 className="h-2.5 w-2.5" />
                        </motion.div>
                      ) : isCurrent ? (
                        <motion.div
                          animate={{ scale: [1, 1.1, 1] }}
                          transition={{ repeat: Infinity, duration: 2 }}
                          className="h-4 w-4 rounded-full border-2 border-slate-900 flex items-center justify-center bg-slate-100"
                        >
                          <div className="h-1.5 w-1.5 bg-slate-900 rounded-full dot-typing" />
                        </motion.div>
                      ) : (
                        <div className="h-4 w-4 rounded-full border border-slate-300 bg-slate-100/50" />
                      )}
                    </div>
                    <span className={`text-xs pt-0.5 transition-all duration-500 font-medium ${
                      isCurrent ? 'text-slate-900 translate-x-1' :
                      isDone ? 'text-slate-400' :
                      'text-slate-400'
                    }`}>
                      {s.label}
                    </span>
                  </div>
                );
              })}
            </div>

            {/* Bottom Progress */}
            <div className="mt-auto z-10 space-y-3">
              <div className="flex justify-between items-center text-[10px] font-medium uppercase tracking-widest text-slate-400">
                <span>Progression</span>
                <span className="mono text-slate-500">{Math.round(progress)}%</span>
              </div>
              <div className="h-1 rounded-full bg-slate-200 overflow-hidden">
                <motion.div
                  className="h-full bg-slate-900 rounded-full"
                  initial={{ width: 0 }}
                  animate={{ width: `${progress}%` }}
                  transition={{ duration: 0.8, ease: "easeOut" }}
                />
              </div>
            </div>
          </div>

          {/* ── Right Chat Interface ── */}
          <div className="flex-1 flex flex-col relative min-w-0 bg-slate-50/60 backdrop-blur-sm">

            {/* Header */}
            <div className="h-16 flex items-center justify-between px-8 shrink-0 border-b border-slate-200/60 bg-transparent">
              <div className="flex items-center gap-3">
                <div className="relative">
                  <div className="h-8 w-8 rounded-full flex items-center justify-center bg-slate-100 border border-slate-300 shadow-sm">
                    <Sparkles className="h-3.5 w-3.5 text-slate-600" />
                  </div>
                  <span className="absolute bottom-0 right-0 h-2.5 w-2.5 rounded-full bg-emerald-500 border-2 border-slate-50" />
                </div>
                <div>
                  <div className="text-xs font-semibold text-slate-900 tracking-wide">NEXA AI</div>
                  <div className="text-[10px] text-slate-500 mt-0.5 font-medium">Assistant de Déploiement</div>
                </div>
              </div>
              <Link href="/login" className="text-[11px] text-slate-400 hover:text-slate-600 font-medium transition-colors tracking-wide">
                Déjà inscrit →
              </Link>
            </div>

            {/* Chat Area */}
            <div className="flex-1 overflow-y-auto px-8 py-6 space-y-6">
              <AnimatePresence>
                {messages.map((m) => (
                  <motion.div
                    key={m.id}
                    initial={{ opacity: 0, y: 15, scale: 0.95 }}
                    animate={{ opacity: 1, y: 0, scale: 1 }}
                    transition={{ type: "spring", stiffness: 260, damping: 20 }}
                    className={`flex ${m.sender === 'user' ? 'justify-end' : 'justify-start'}`}
                  >
                    <div className={`flex gap-3 max-w-[85%] ${m.sender === 'user' ? 'flex-row-reverse' : 'flex-row'}`}>

                      <div className={`shrink-0 h-7 w-7 rounded-full flex items-center justify-center mt-auto border ${
                        m.sender === 'user'
                          ? 'bg-slate-200 border-slate-300 text-slate-600'
                          : 'bg-slate-100 border-slate-300 text-slate-700 shadow-sm'
                      }`}>
                        {m.sender === 'user' ? <User className="h-3.5 w-3.5" /> : <Bot className="h-3.5 w-3.5" />}
                      </div>

                      <div className={`px-4 py-3 rounded-2xl text-[13px] leading-relaxed shadow-sm border ${
                        m.sender === 'user'
                          ? 'rounded-br-sm bg-slate-800 text-slate-100 border-slate-700'
                          : 'rounded-bl-sm bg-slate-100 text-slate-700 border-slate-300/80'
                      }`}>
                        {m.text}
                      </div>

                    </div>
                  </motion.div>
                ))}

                {isTyping && (
                  <motion.div
                    initial={{ opacity: 0, y: 5 }}
                    animate={{ opacity: 1, y: 0 }}
                    exit={{ opacity: 0, scale: 0.95 }}
                    className="flex justify-start"
                  >
                    <div className="flex gap-3">
                      <div className="h-7 w-7 rounded-full flex items-center justify-center bg-slate-100 border border-slate-300 shadow-sm mt-auto">
                        <Bot className="h-3.5 w-3.5 text-slate-500" />
                      </div>
                      <motion.div
                        layout
                        className="px-5 py-4 rounded-2xl rounded-bl-sm bg-slate-100 border border-slate-300/80 shadow-sm flex items-center gap-1.5"
                      >
                        {[0, 150, 300].map((delay, i) => (
                          <motion.span
                            key={i}
                            animate={{ y: [0, -4, 0] }}
                            transition={{ repeat: Infinity, duration: 0.6, delay: delay / 1000 }}
                            className="w-1.5 h-1.5 rounded-full bg-slate-400"
                          />
                        ))}
                      </motion.div>
                    </div>
                  </motion.div>
                )}
              </AnimatePresence>
              <div ref={chatEndRef} className="h-2" />
            </div>

            {/* Input Area */}
            <div className="px-8 py-5 shrink-0 border-t border-slate-100 bg-slate-50/50">
              <AnimatePresence mode="wait">
                <motion.div
                  key={step}
                  initial={{ opacity: 0, y: 10 }}
                  animate={{ opacity: 1, y: 0 }}
                  exit={{ opacity: 0, y: -10 }}
                  transition={{ duration: 0.3, ease: [0.16, 1, 0.3, 1] }}
                >
                  {renderInputArea()}
                </motion.div>
              </AnimatePresence>
            </div>
          </div>

        </motion.div>
      </PageTransition>
    </>
  );
}

// ─── Input Components ─────────────────────────────────────────────────────────

function SingleInput({ onSubmit, placeholder }: { onSubmit: (v: string) => void; placeholder: string }) {
  const [val, setVal] = useState('');

  return (
    <form onSubmit={e => { e.preventDefault(); if (val.trim()) { onSubmit(val.trim()); setVal(''); } }} className="relative flex items-center">
      <input
        autoFocus value={val} onChange={e => setVal(e.target.value)} placeholder={placeholder}
        className="clean-input w-full h-11 pl-4 pr-12 rounded-xl"
      />
      <button type="submit" disabled={!val.trim()}
        className="absolute right-2 h-7 w-7 rounded-lg flex items-center justify-center bg-slate-800 text-white shadow-sm transition-all disabled:opacity-30 disabled:bg-slate-300 disabled:text-slate-500 disabled:shadow-none hover:bg-slate-700">
        <ArrowRight className="h-3.5 w-3.5" />
      </button>
    </form>
  );
}

function DoubleInput({ onSubmit, ph1, ph2, k1, k2, type2 = 'text' }: {
  onSubmit: (v: Record<string, string>) => void; ph1: string; ph2: string; k1: string; k2: string; type2?: string
}) {
  const [v1, setV1] = useState('');
  const [v2, setV2] = useState('');
  const [showPassword, setShowPassword] = useState(false);
  const valid = v1.trim() && v2.trim();
  const isPassword = type2 === 'password';

  return (
    <form onSubmit={e => { e.preventDefault(); if (valid) onSubmit({ [k1]: v1, [k2]: v2 }); }} className="flex gap-3">
      <input autoFocus value={v1} onChange={e => setV1(e.target.value)} placeholder={ph1} className="clean-input flex-1 h-11 px-4 rounded-xl" />
      <div className="flex-1 relative">
        <input
          type={isPassword && !showPassword ? 'password' : 'text'}
          value={v2} onChange={e => setV2(e.target.value)} placeholder={ph2}
          className="clean-input w-full h-11 pl-4 pr-10 rounded-xl"
        />
        {isPassword && (
          <button type="button" onClick={() => setShowPassword(p => !p)}
            className="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors" tabIndex={-1}>
            {showPassword ? <EyeOff className="h-4 w-4" /> : <Eye className="h-4 w-4" />}
          </button>
        )}
      </div>
      <button type="submit" disabled={!valid}
        className="shrink-0 h-11 w-11 rounded-xl flex items-center justify-center bg-slate-800 text-white shadow-sm transition-all disabled:opacity-30 disabled:bg-slate-300 disabled:text-slate-500 disabled:shadow-none hover:bg-slate-700">
        <ArrowRight className="h-4 w-4" />
      </button>
    </form>
  );
}

function OTPInput({ onSubmit }: { onSubmit: (v: string) => void }) {
  const [digits, setDigits] = useState(['', '', '', '', '', '']);
  const refs = Array.from({ length: 6 }, () => useRef<HTMLInputElement>(null));

  const handleChange = (i: number, val: string) => {
    if (!/^\d?$/.test(val)) return;
    const next = [...digits];
    next[i] = val;
    setDigits(next);
    if (val && i < 5) refs[i + 1].current?.focus();
    if (next.every(d => d) && next.join('').length === 6) onSubmit(next.join(''));
  };

  const handleKeyDown = (i: number, e: React.KeyboardEvent) => {
    if (e.key === 'Backspace' && !digits[i] && i > 0) refs[i - 1].current?.focus();
  };

  return (
    <div className="flex gap-3 justify-center">
      {digits.map((d, i) => (
        <input
          key={i} ref={refs[i]} value={d} maxLength={1}
          onChange={e => handleChange(i, e.target.value)}
          onKeyDown={e => handleKeyDown(i, e)}
          autoFocus={i === 0}
          className="clean-input mono w-12 h-14 text-center text-lg font-medium rounded-xl focus:border-slate-400 focus:bg-white"
        />
      ))}
    </div>
  );
}

// ─── Module Select ────────────────────────────────────────────────────────────

function ModuleSelect({ onSubmit }: { onSubmit: (payload: { companyType: CompanyType; estimatedRooms?: string; estimatedTables?: string }) => void }) {
  const [companyType, setCompanyType] = useState<CompanyType | ''>('');
  const [estimatedRooms, setEstimatedRooms] = useState('');
  const [estimatedTables, setEstimatedTables] = useState('');

  const showHotelFields = companyType === 'hotel' || companyType === 'hotel_fnb';
  const showFnBFields = companyType === 'fnb' || companyType === 'hotel_fnb';

  const cards = [
    {
      key: 'boissons' as const,
      label: 'Distribution Boissons',
      desc: 'Gestion du stock, tournées et finance pour boissons.',
      icon: <Coffee className="h-4 w-4" />,
    },
    {
      key: 'hotel' as const,
      label: 'Hôtellerie',
      desc: 'Réservations, chambres et facturation hébergement.',
      icon: <BedDouble className="h-4 w-4" />,
    },
    {
      key: 'fnb' as const,
      label: 'Restauration F&B',
      desc: 'Service, cuisine et caisse pour restaurants.',
      icon: <UtensilsCrossed className="h-4 w-4" />,
    },
    {
      key: 'hotel_fnb' as const,
      label: 'Mode 3 — Hotel + F&B liés',
      desc: 'Synchronisation totale entre réservation, salle, cuisine et facturation.',
      icon: <Sparkles className="h-4 w-4" />,
    },
  ];

  const roomOptions = ['Moins de 10', '10 à 50', 'Plus de 50'];
  const tableOptions = ['Moins de 10', '10 à 30', 'Plus de 30'];

  return (
    <div className="space-y-4">
      <div className="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600">
        <p className="font-semibold text-slate-900 mb-1">Type d'entreprise</p>
        <p>Choisissez le profil qui décrit le mieux votre activité. Cette sélection détermine le périmètre de démarrage.</p>
      </div>

      <div className="grid grid-cols-2 gap-2">
        {cards.map(card => (
          <button
            key={card.key}
            type="button"
            onClick={() => setCompanyType(card.key)}
            className={`flex flex-col items-start gap-2 p-3 rounded-2xl border text-left transition-all duration-200 ${
              companyType === card.key
                ? 'border-slate-900 bg-slate-900 text-white shadow-sm'
                : 'border-slate-200 bg-white text-slate-700 hover:border-slate-300 hover:bg-slate-50'
            }`}
          >
            <span className={companyType === card.key ? 'text-white' : 'text-slate-500'}>
              {card.icon}
            </span>
            <p className="text-sm font-semibold leading-tight">{card.label}</p>
            <p className="text-[11px] leading-snug text-slate-500">{card.desc}</p>
          </button>
        ))}
      </div>

      <AnimatePresence>
        {companyType === 'hotel_fnb' && (
          <motion.div
            key="mode-3-description"
            initial={{ opacity: 0, y: 10 }}
            animate={{ opacity: 1, y: 0 }}
            exit={{ opacity: 0, y: -10 }}
            className="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-[12px] text-emerald-800"
          >
            <p className="font-semibold">Mode 3 — Hotel + F&B liés</p>
            <p>Vos réservations, commandes de salle et flux de cuisine sont orchestrés dans un parcours connecté, avec facturation et planning centralisés.</p>
          </motion.div>
        )}
      </AnimatePresence>

      {showHotelFields && (
        <div className="rounded-2xl border border-slate-200 bg-white p-4">
          <p className="text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Nombre de chambres estimé</p>
          <div className="flex flex-wrap gap-2">
            {roomOptions.map(opt => (
              <button
                key={opt}
                type="button"
                onClick={() => setEstimatedRooms(estimatedRooms === opt ? '' : opt)}
                className={`text-[11px] px-3 py-2 rounded-xl border transition-all duration-150 ${
                  estimatedRooms === opt
                    ? 'bg-slate-900 text-white border-slate-900'
                    : 'bg-white text-slate-600 border-slate-200 hover:border-slate-300'
                }`}
              >
                {opt}
              </button>
            ))}
          </div>
        </div>
      )}

      {showFnBFields && (
        <div className="rounded-2xl border border-slate-200 bg-white p-4">
          <p className="text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Nombre de tables estimé</p>
          <div className="flex flex-wrap gap-2">
            {tableOptions.map(opt => (
              <button
                key={opt}
                type="button"
                onClick={() => setEstimatedTables(estimatedTables === opt ? '' : opt)}
                className={`text-[11px] px-3 py-2 rounded-xl border transition-all duration-150 ${
                  estimatedTables === opt
                    ? 'bg-slate-900 text-white border-slate-900'
                    : 'bg-white text-slate-600 border-slate-200 hover:border-slate-300'
                }`}
              >
                {opt}
              </button>
            ))}
          </div>
        </div>
      )}

      <button
        type="button"
        onClick={() => companyType && onSubmit({ companyType, estimatedRooms: estimatedRooms || undefined, estimatedTables: estimatedTables || undefined })}
        disabled={!companyType}
        className="w-full h-11 rounded-xl flex items-center justify-center gap-2 text-sm font-medium transition-all duration-200 shadow-sm disabled:cursor-not-allowed disabled:bg-slate-200 disabled:text-slate-500 bg-slate-900 text-white hover:bg-slate-800"
      >
        <ArrowRight className="h-4 w-4" />
        Continuer
      </button>
    </div>
  );
}

Register.layout = (page: React.ReactNode) => <>{page}</>;
