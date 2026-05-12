import { Form, Head } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { MailCheck, Command, LogOut, RefreshCw } from 'lucide-react';
import TextLink from '@/components/text-link';
import { ParticlesBackground } from '@/components/ui/particles-background';
import { Spinner } from '@/components/ui/spinner';
import { logout } from '@/routes';
import { send } from '@/routes/verification';

const fadeUp = {
    hidden: { opacity: 0, y: 24 },
    visible: (i: number) => ({
        opacity: 1,
        y: 0,
        transition: { duration: 0.5, delay: i * 0.08 },
    }),
};

export default function VerifyEmail({ status }: { status?: string }) {
    return (
        <>
            <Head title="Vérification de l'email — NEXORA" />
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
                                <MailCheck className="h-5 w-5 text-primary" />
                            </div>
                        </motion.div>

                        <motion.div custom={3} variants={fadeUp} initial="hidden" animate="visible" className="mb-6 text-center">
                            <h1 className="text-2xl font-bold tracking-tight text-foreground">Vérifiez votre email</h1>
                            <p className="mt-1 text-sm text-muted-foreground">
                                Un lien de vérification vous a été envoyé. Cliquez dessus pour activer votre compte.
                            </p>
                        </motion.div>

                        {status === 'verification-link-sent' && (
                            <motion.div
                                initial={{ opacity: 0, scale: 0.95 }}
                                animate={{ opacity: 1, scale: 1 }}
                                className="mb-6 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700 text-center"
                            >
                                Un nouveau lien de vérification a été envoyé à votre adresse email.
                            </motion.div>
                        )}

                        <Form {...send.form()} className="flex flex-col gap-4">
                            {({ processing }) => (
                                <>
                                    <motion.div custom={4} variants={fadeUp} initial="hidden" animate="visible">
                                        <motion.button
                                            type="submit"
                                            disabled={processing}
                                            whileHover={{ scale: 1.015 }}
                                            whileTap={{ scale: 0.985 }}
                                            className="w-full h-11 rounded-lg bg-primary text-primary-foreground font-semibold text-sm flex items-center justify-center gap-2 transition-opacity disabled:opacity-60 disabled:cursor-not-allowed"
                                        >
                                            {processing ? <Spinner /> : <RefreshCw className="h-4 w-4" />}
                                            {processing ? 'Envoi en cours…' : 'Renvoyer l\'email de vérification'}
                                        </motion.button>
                                    </motion.div>

                                    <motion.div custom={5} variants={fadeUp} initial="hidden" animate="visible"
                                        className="flex justify-center"
                                    >
                                        <TextLink
                                            href={logout()}
                                            className="flex items-center gap-1.5 text-sm text-muted-foreground hover:text-foreground transition-colors"
                                        >
                                            <LogOut className="h-3.5 w-3.5" />
                                            Se déconnecter
                                        </TextLink>
                                    </motion.div>
                                </>
                            )}
                        </Form>
                    </motion.div>

                    <motion.p
                        custom={6} variants={fadeUp} initial="hidden" animate="visible"
                        className="mt-6 text-center text-xs text-muted-foreground"
                    >
                        &copy; {new Date().getFullYear()} NEXORA · Tous droits réservés
                    </motion.p>
                </div>
            </div>
        </>
    );
}

VerifyEmail.layout = (page: any) => <>{page}</>;
