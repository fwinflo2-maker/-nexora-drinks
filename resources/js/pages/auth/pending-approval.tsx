import { Head, Link, router } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { ShieldCheck, ArrowLeft, Clock, Command, Mail, LogOut, MessageCircle, AlertTriangle } from 'lucide-react';
import { ParticlesBackground } from '@/components/ui/particles-background';

const fadeUp = {
    hidden: { opacity: 0, y: 24 },
    visible: (i: number) => ({
        opacity: 1,
        y: 0,
        transition: { duration: 0.5, delay: i * 0.08 },
    }),
};

export default function PendingApproval({ isSuspended = false }: { isSuspended?: boolean }) {
    return (
        <>
            <Head title="Demande en cours d'examen — NEXORA" />
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
                        {/* Animated clock icon */}
                        <motion.div
                            custom={2} variants={fadeUp} initial="hidden" animate="visible"
                            className="flex justify-center mb-5"
                        >
                            <div className="relative">
                                <div className={`h-16 w-16 rounded-full flex items-center justify-center border ${isSuspended ? 'bg-red-500/10 border-red-500/20' : 'bg-amber-500/10 border-amber-500/20'}`}>
                                    {isSuspended ? (
                                        <ShieldCheck className="h-7 w-7 text-red-500" />
                                    ) : (
                                        <Clock className="h-7 w-7 text-amber-500" />
                                    )}
                                </div>
                                <motion.div
                                    className="absolute inset-0 rounded-full border-2 border-amber-500/30"
                                    animate={{ scale: [1, 1.2, 1], opacity: [0.5, 0, 0.5] }}
                                    transition={{ duration: 2.5, repeat: Infinity, ease: 'easeInOut' }}
                                    style={{ borderColor: isSuspended ? 'rgba(239, 68, 68, 0.3)' : 'rgba(245, 158, 11, 0.3)' }}
                                />
                            </div>
                        </motion.div>

                        <motion.div custom={3} variants={fadeUp} initial="hidden" animate="visible" className="mb-4 text-center">
                            <h1 className="text-2xl font-bold tracking-tight text-foreground">
                                {isSuspended ? 'Compte suspendu' : 'Demande reçue !'}
                            </h1>
                            <p className="mt-2 text-sm text-muted-foreground leading-relaxed">
                                {isSuspended 
                                    ? "Votre accès à NEXORA a été temporairement suspendu par un administrateur."
                                    : "Votre dossier a bien été enregistré. Nos équipes vont l'examiner et vous contacteront par email pour activer votre accès NEXORA."}
                            </p>
                        </motion.div>

                        {/* Status badge */}
                        <motion.div custom={4} variants={fadeUp} initial="hidden" animate="visible"
                            className="flex justify-center mb-6"
                        >
                            <div className={`flex items-center gap-2 text-xs font-semibold px-4 py-2 rounded-full border ${isSuspended ? 'bg-red-500/10 text-red-500 border-red-500/20' : 'bg-amber-500/10 text-amber-500 border-amber-500/20'}`}>
                                {isSuspended ? <AlertTriangle className="w-3.5 h-3.5" /> : <ShieldCheck className="w-3.5 h-3.5" />}
                                {isSuspended ? 'Action requise : Contactez le support' : "En cours d'examen par un administrateur"}
                            </div>
                        </motion.div>

                        {/* Info box */}
                        <motion.div custom={5} variants={fadeUp} initial="hidden" animate="visible"
                            className="rounded-xl bg-primary/5 border border-primary/10 p-4 flex flex-col gap-3"
                        >
                            <div className="flex gap-3">
                                {isSuspended ? <MessageCircle className="h-4 w-4 text-primary shrink-0 mt-0.5" /> : <Mail className="h-4 w-4 text-primary shrink-0 mt-0.5" />}
                                <p className="text-xs text-muted-foreground leading-relaxed">
                                    {isSuspended 
                                        ? "Pour réactiver votre compte ou pour toute question, veuillez contacter notre service client directement sur WhatsApp."
                                        : "Vous recevrez un email de confirmation dès que votre compte entreprise sera validé. Pensez à vérifier vos spams."}
                                </p>
                            </div>

                            {isSuspended && (
                                <a 
                                    href="https://wa.me/237698302962" 
                                    target="_blank" 
                                    rel="noopener noreferrer"
                                    className="flex items-center justify-center gap-2 w-full py-2 rounded-lg bg-green-500 text-white text-xs font-bold hover:bg-green-600 transition-colors mt-1"
                                >
                                    <MessageCircle className="w-3.5 h-3.5" />
                                    WhatsApp : +237 698 302 962
                                </a>
                            )}
                        </motion.div>
                    </motion.div>

                    {/* Actions */}
                    <motion.div
                        custom={6} variants={fadeUp} initial="hidden" animate="visible"
                        className="mt-6 flex flex-col items-center gap-4"
                    >
                        <button
                            onClick={() => router.post('/logout')}
                            className="flex items-center justify-center gap-2 w-full max-w-[200px] py-2.5 rounded-xl bg-red-500/10 hover:bg-red-500/20 text-red-500 border border-red-500/20 text-sm font-semibold transition-all"
                        >
                            <LogOut className="h-4 w-4" />
                            Se déconnecter
                        </button>

                        <Link
                            href="/"
                            className="flex items-center gap-1.5 text-sm text-muted-foreground hover:text-foreground transition-colors"
                        >
                            <ArrowLeft className="h-3.5 w-3.5" />
                            Retour à l'accueil
                        </Link>
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

PendingApproval.layout = (page: any) => <>{page}</>;
