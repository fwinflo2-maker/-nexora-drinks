import { Form, Head, Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { ArrowLeft, ShieldCheck } from 'lucide-react';
import AppLogoIcon from '@/components/app-logo-icon';
import InputError from '@/components/input-error';
import PasswordInput from '@/components/password-input';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ParticlesBackground } from '@/components/ui/particles-background';
import { Spinner } from '@/components/ui/spinner';
import { store } from '@/routes/login';

type Props = {
    status?: string;
};

const fadeUp = {
    hidden: { opacity: 0, y: 24 },
    visible: (i: number) => ({
        opacity: 1,
        y: 0,
        transition: { duration: 0.45, delay: i * 0.08 },
    }),
};

export default function SuperAdminLogin({ status }: Props) {
    return (
        <>
            <Head title="Accès Restreint — NEXORA" />
            <ParticlesBackground />

            <div className="relative z-10 min-h-[100dvh] flex flex-col items-center pt-6 sm:pt-8 px-4 pb-8">
                <div className="w-full max-w-sm">

                    {/* ── Logo statique (non cliquable) ───────────────────── */}
                    <motion.div
                        custom={0}
                        variants={fadeUp}
                        initial="hidden"
                        animate="visible"
                        className="flex justify-center mb-6"
                    >
                        <Link href="/" className="flex items-center gap-2.5 select-none focus:outline-none hover:opacity-80 transition-opacity" aria-label="NEXORA">
                            <div className="flex h-9 w-9 items-center justify-center rounded-lg bg-foreground text-background">
                                <AppLogoIcon className="h-5 w-5" />
                            </div>
                            <span className="text-2xl font-bold tracking-tight">NEXORA</span>
                        </Link>
                    </motion.div>

                    {/* ── Carte ───────────────────────────────────────────── */}
                    <motion.div
                        custom={1}
                        variants={fadeUp}
                        initial="hidden"
                        animate="visible"
                        className="relative rounded-2xl border border-primary/20 bg-background/80 backdrop-blur-xl shadow-2xl overflow-hidden p-8"
                    >
                        {/* Top accent line */}
                        <div className="absolute top-0 left-0 w-full h-0.5 bg-gradient-to-r from-transparent via-primary to-transparent" />

                        <motion.div custom={2} variants={fadeUp} initial="hidden" animate="visible" className="mb-8 text-center">
                            <div className="mx-auto w-12 h-12 rounded-xl bg-primary/10 border border-primary/20 flex items-center justify-center mb-4">
                                <ShieldCheck className="h-6 w-6 text-primary" />
                            </div>
                            <h1 className="text-xl font-bold tracking-tight text-foreground">Accès restreint</h1>
                            <p className="mt-1 text-sm text-muted-foreground">Interface de Super Administration NEXORA</p>
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

                        <Form
                            {...store.form()}
                            resetOnSuccess={['password']}
                            className="flex flex-col gap-5"
                        >
                            {({ processing, errors }) => (
                                <>
                                    <motion.div custom={3} variants={fadeUp} initial="hidden" animate="visible" className="grid gap-1.5">
                                        <Label htmlFor="email" className="text-sm font-medium">Identifiant</Label>
                                        <Input
                                            id="email"
                                            type="email"
                                            name="email"
                                            required
                                            autoFocus
                                            tabIndex={1}
                                            autoComplete="off"
                                            placeholder="admin@nexora.app"
                                            className="h-11 rounded-lg border-border bg-background focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                                        />
                                        <InputError message={errors.email} />
                                    </motion.div>

                                    <motion.div custom={4} variants={fadeUp} initial="hidden" animate="visible" className="grid gap-1.5">
                                        <Label htmlFor="password" className="text-sm font-medium">Mot de passe</Label>
                                        <PasswordInput
                                            id="password"
                                            name="password"
                                            required
                                            tabIndex={2}
                                            autoComplete="off"
                                            placeholder="••••••••"
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
                                            tabIndex={3}
                                            className="mt-2 w-full h-11 rounded-lg bg-primary text-primary-foreground font-semibold text-sm flex items-center justify-center gap-2 transition-opacity disabled:opacity-60 disabled:cursor-not-allowed"
                                        >
                                            {processing ? <Spinner /> : <ShieldCheck className="h-4 w-4" />}
                                            {processing ? 'Vérification…' : 'Accéder au panneau'}
                                        </motion.button>
                                    </motion.div>
                                </>
                            )}
                        </Form>
                    </motion.div>

                    {/* ── Bouton retour landing page ───────────────────────── */}
                    <motion.div
                        custom={6}
                        variants={fadeUp}
                        initial="hidden"
                        animate="visible"
                        className="mt-8 flex justify-center"
                    >
                        <Link
                            href="/"
                            className="inline-flex items-center gap-2 text-sm font-medium text-muted-foreground hover:text-foreground transition-colors group"
                        >
                            <ArrowLeft className="h-4 w-4 transition-transform group-hover:-translate-x-1" />
                            Retourner à l'accueil
                        </Link>
                    </motion.div>

                </div>
            </div>
        </>
    );
}

SuperAdminLogin.layout = undefined;
