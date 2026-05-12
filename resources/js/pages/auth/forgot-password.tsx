import { Form, Head } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { LoaderCircle, Command, ArrowLeft, Mail } from 'lucide-react';
import InputError from '@/components/input-error';
import TextLink from '@/components/text-link';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ParticlesBackground } from '@/components/ui/particles-background';
import { login } from '@/routes';
import { email } from '@/routes/password';

const fadeUp = {
    hidden: { opacity: 0, y: 24 },
    visible: (i: number) => ({
        opacity: 1,
        y: 0,
        transition: { duration: 0.5, delay: i * 0.08 },
    }),
};

export default function ForgotPassword({ status }: { status?: string }) {
    return (
        <>
            <Head title="Mot de passe oublié — NEXORA" />
            <ParticlesBackground />

            <div className="relative z-10 min-h-[100dvh] flex flex-col items-center pt-6 sm:pt-8 px-4 pb-8">
                <div className="w-full max-w-sm">

                    {/* Logo */}
                    <motion.div
                        custom={0} variants={fadeUp} initial="hidden" animate="visible"
                        className="flex justify-center mb-6"
                    >
                        <div className="flex items-center gap-2.5 select-none">
                            <div className="flex h-9 w-9 items-center justify-center rounded-lg bg-foreground text-background">
                                <Command className="h-5 w-5" />
                            </div>
                            <span className="text-2xl font-bold tracking-tight">NEXORA</span>
                        </div>
                    </motion.div>

                    {/* Card */}
                    <motion.div
                        custom={1} variants={fadeUp} initial="hidden" animate="visible"
                        className="rounded-2xl border border-border bg-background/80 backdrop-blur-xl shadow-xl p-8"
                    >
                        <motion.div custom={2} variants={fadeUp} initial="hidden" animate="visible"
                            className="flex justify-center mb-5"
                        >
                            <div className="h-12 w-12 rounded-full bg-primary/10 flex items-center justify-center">
                                <Mail className="h-5 w-5 text-primary" />
                            </div>
                        </motion.div>

                        <motion.div custom={3} variants={fadeUp} initial="hidden" animate="visible" className="mb-6 text-center">
                            <h1 className="text-2xl font-bold tracking-tight text-foreground">Mot de passe oublié ?</h1>
                            <p className="mt-1 text-sm text-muted-foreground">
                                Saisissez votre email pour recevoir un lien de réinitialisation
                            </p>
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

                        <Form {...email.form()} className="flex flex-col gap-4">
                            {({ processing, errors }) => (
                                <>
                                    <motion.div custom={4} variants={fadeUp} initial="hidden" animate="visible" className="grid gap-1.5">
                                        <Label htmlFor="email" className="text-sm font-medium">Adresse e-mail</Label>
                                        <Input
                                            id="email"
                                            type="email"
                                            name="email"
                                            autoComplete="off"
                                            autoFocus
                                            placeholder="vous@entreprise.com"
                                            className="h-11 rounded-lg border-border bg-background focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                                        />
                                        <InputError message={errors.email} />
                                    </motion.div>

                                    <motion.div custom={5} variants={fadeUp} initial="hidden" animate="visible">
                                        <motion.button
                                            type="submit"
                                            disabled={processing}
                                            whileHover={{ scale: 1.015 }}
                                            whileTap={{ scale: 0.985 }}
                                            data-test="email-password-reset-link-button"
                                            className="mt-2 w-full h-11 rounded-lg bg-primary text-primary-foreground font-semibold text-sm flex items-center justify-center gap-2 transition-opacity disabled:opacity-60 disabled:cursor-not-allowed"
                                        >
                                            {processing
                                                ? <LoaderCircle className="h-4 w-4 animate-spin" />
                                                : <Mail className="h-4 w-4" />
                                            }
                                            {processing ? 'Envoi en cours…' : 'Envoyer le lien'}
                                        </motion.button>
                                    </motion.div>
                                </>
                            )}
                        </Form>
                    </motion.div>

                    <motion.div
                        custom={6} variants={fadeUp} initial="hidden" animate="visible"
                        className="mt-5 flex justify-center"
                    >
                        <TextLink
                            href={login()}
                            className="flex items-center gap-1.5 text-sm text-muted-foreground hover:text-foreground transition-colors"
                        >
                            <ArrowLeft className="h-3.5 w-3.5" />
                            Retour à la connexion
                        </TextLink>
                    </motion.div>

                    <motion.p
                        custom={7} variants={fadeUp} initial="hidden" animate="visible"
                        className="mt-4 text-center text-xs text-muted-foreground"
                    >
                        &copy; {new Date().getFullYear()} NEXORA · Tous droits réservés
                    </motion.p>
                </div>
            </div>
        </>
    );
}

ForgotPassword.layout = (page: any) => <>{page}</>;
