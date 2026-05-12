import { Form, Head, Link } from '@inertiajs/react';
import { motion, AnimatePresence } from 'framer-motion';
import { Command, LogIn, Loader2 } from 'lucide-react';
import { useRef, useState } from 'react';
import InputError from '@/components/input-error';
import PasswordInput from '@/components/password-input';
import TextLink from '@/components/text-link';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ParticlesBackground } from '@/components/ui/particles-background';
import { Spinner } from '@/components/ui/spinner';
import { store } from '@/routes/login';
import { request } from '@/routes/password';

type Props = {
    status?: string;
    canResetPassword: boolean;
    canRegister: boolean;
};

const fadeUp = {
    hidden: { opacity: 0, y: 24 },
    visible: (i: number) => ({
        opacity: 1,
        y: 0,
        transition: { duration: 0.5, delay: i * 0.08 },
    }),
};

export default function Login({ status, canResetPassword, canRegister }: Props) {
    const [clickCount, setClickCount] = useState(0);
    const resetTimer = useRef<ReturnType<typeof setTimeout> | null>(null);

    /**
     * Easter egg : 3 clics rapides sur le logo → page de connexion Super Admin.
     * Le compteur se remet à zéro 3s après le dernier clic.
     */
    const handleLogoClick = (e: React.MouseEvent) => {
        e.preventDefault();
        const next = clickCount + 1;

        if (resetTimer.current) {
clearTimeout(resetTimer.current);
}

        resetTimer.current = setTimeout(() => setClickCount(0), 3000);

        if (next >= 3) {
            setClickCount(0);
            window.location.href = '/super-admin/login';
        } else {
            setClickCount(next);
        }
    };

    return (
        <Form
            {...store.form()}
            resetOnSuccess={['password']}
        >
            {({ processing, errors }) => (
                <LoginFormContent 
                    processing={processing} 
                    errors={errors} 
                    status={status} 
                    canResetPassword={canResetPassword} 
                    canRegister={canRegister} 
                    handleLogoClick={handleLogoClick}
                />
            )}
        </Form>
    );
}

function LoginFormContent({ processing, errors, status, canResetPassword, canRegister, handleLogoClick }: any) {
    return (
        <>
            <AnimatePresence>
                {processing && (
                    <motion.div
                        initial={{ opacity: 0 }}
                        animate={{ opacity: 1 }}
                        exit={{ opacity: 0 }}
                        className="fixed inset-0 z-[100] flex flex-col items-center justify-center bg-background/90 backdrop-blur-2xl"
                    >
                        <motion.div
                            initial={{ scale: 0.8, opacity: 0 }}
                            animate={{ scale: 1, opacity: 1 }}
                            transition={{ type: "spring", damping: 20 }}
                            className="flex flex-col items-center gap-6"
                        >
                            <div className="relative">
                                <motion.div
                                    animate={{ rotate: 360 }}
                                    transition={{ repeat: Infinity, duration: 2, ease: "linear" }}
                                    className="w-20 h-20 rounded-full border-2 border-primary/20 border-t-primary"
                                />
                                <div className="absolute inset-0 flex items-center justify-center">
                                    <Command className="h-8 w-8 text-primary animate-pulse" />
                                </div>
                            </div>
                            <div className="text-center">
                                <h2 className="text-xl font-bold tracking-tight text-foreground">Initialisation de NEXORA</h2>
                                <p className="text-sm text-muted-foreground mt-1">Connexion sécurisée en cours...</p>
                            </div>
                        </motion.div>
                    </motion.div>
                )}
            </AnimatePresence>
            <Head title="Connexion — NEXORA" />
            <ParticlesBackground />

            <div className="relative z-10 min-h-[100dvh] flex flex-col items-center pt-6 sm:pt-8 px-4 pb-8">
                <div className="w-full max-w-sm">

                    {/* ── Logo ─ Clique une fois pour aller au landing, 3 clics secrets → super-admin ───────────────────────────────── */}
                    <motion.div
                        custom={0}
                        variants={fadeUp}
                        initial="hidden"
                        animate="visible"
                        className="flex justify-center mb-8"
                    >
                        <Link
                            href="/"
                            className="flex items-center gap-3 select-none focus:outline-none group"
                            aria-label="NEXORA landing page"
                        >
                            <motion.div 
                                onClick={handleLogoClick}
                                whileHover={{ rotate: 10, scale: 1.1 }}
                                whileTap={{ scale: 0.9 }}
                                className="flex h-10 w-10 items-center justify-center rounded-xl bg-foreground text-background shadow-lg shadow-foreground/10 group-hover:bg-primary group-hover:text-primary-foreground transition-colors duration-300 cursor-pointer"
                            >
                                <Command className="h-6 w-6" />
                            </motion.div>
                            <div className="flex flex-col">
                                <span className="text-2xl font-black tracking-tighter text-foreground group-hover:text-primary transition-colors duration-300">NEXORA</span>
                                <span className="text-[10px] uppercase tracking-[0.2em] font-bold text-muted-foreground/60 -mt-1 ml-0.5">Enterprise OS</span>
                            </div>
                        </Link>
                    </motion.div>

                    {/* ── Carte de connexion ───────────────────────────────── */}
                    <motion.div
                        custom={1}
                        variants={fadeUp}
                        initial="hidden"
                        animate="visible"
                        className="rounded-2xl border border-border bg-background/80 backdrop-blur-xl shadow-xl p-8"
                    >
                        <motion.div custom={2} variants={fadeUp} initial="hidden" animate="visible" className="mb-6 text-center">
                            <h1 className="text-2xl font-bold tracking-tight text-foreground">Bon retour</h1>
                            <p className="mt-1 text-sm text-muted-foreground">Connectez-vous à votre espace NEXORA</p>
                        </motion.div>

                        {status && (
                            <motion.div
                                initial={{ opacity: 0, scale: 0.95 }}
                                animate={{ opacity: 1, scale: 1 }}
                                className="mb-6 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700 text-center"
                            >
                                {status}
                            </motion.div>
                        )}

                        <div className="flex flex-col gap-4">
                            <motion.div custom={3} variants={fadeUp} initial="hidden" animate="visible" className="grid gap-1.5">
                                <Label htmlFor="email" className="text-sm font-medium">Adresse e-mail</Label>
                                <Input
                                    id="email"
                                    type="email"
                                    name="email"
                                    required
                                    autoFocus
                                    tabIndex={1}
                                    autoComplete="email"
                                    placeholder="vous@entreprise.com"
                                    className="h-11 rounded-lg border-border bg-background focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                                />
                                <InputError message={errors.email} />
                            </motion.div>

                            <motion.div custom={4} variants={fadeUp} initial="hidden" animate="visible" className="grid gap-1.5">
                                <div className="flex items-center justify-between">
                                    <Label htmlFor="password" className="text-sm font-medium">Mot de passe</Label>
                                    {canResetPassword && (
                                        <TextLink href={request()} className="text-xs text-muted-foreground hover:text-primary transition-colors" tabIndex={5}>
                                            Mot de passe oublié ?
                                        </TextLink>
                                    )}
                                </div>
                                <PasswordInput
                                    id="password"
                                    name="password"
                                    required
                                    tabIndex={2}
                                    autoComplete="current-password"
                                    placeholder="••••••••"
                                    defaultValue="nexora_admin2026"
                                    className="h-11 rounded-lg border-border bg-background focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                                />
                                <InputError message={errors.password} />
                            </motion.div>

                            <motion.div custom={5} variants={fadeUp} initial="hidden" animate="visible">
                                <motion.button
                                    type="submit"
                                    disabled={processing}
                                    whileHover={{ scale: 1.015 }}
                                    whileTap={{ scale: 0.985 }}
                                    tabIndex={4}
                                    data-test="login-button"
                                    className="mt-2 w-full h-11 rounded-lg bg-primary text-primary-foreground font-semibold text-sm flex items-center justify-center gap-2 transition-opacity disabled:opacity-60 disabled:cursor-not-allowed"
                                >
                                    {processing ? <Loader2 className="h-4 w-4 animate-spin" /> : <LogIn className="h-4 w-4" />}
                                    {processing ? 'Connexion…' : 'Se connecter'}
                                </motion.button>
                            </motion.div>

                            {canRegister && (
                                <motion.div custom={6} variants={fadeUp} initial="hidden" animate="visible" className="text-center text-sm text-muted-foreground">
                                    Pas encore de compte ?{' '}
                                    <TextLink href="/register" tabIndex={6} className="font-medium text-foreground hover:text-primary transition-colors">
                                        Créer un compte gratuit
                                    </TextLink>
                                </motion.div>
                            )}
                        </div>
                    </motion.div>

                    <motion.p
                        custom={7}
                        variants={fadeUp}
                        initial="hidden"
                        animate="visible"
                        className="mt-6 text-center text-xs text-muted-foreground"
                    >
                        &copy; {new Date().getFullYear()} NEXORA · Tous droits réservés
                    </motion.p>
                </div>
            </div>
        </>
    );
}

Login.layout = (page: any) => <>{page}</>;
