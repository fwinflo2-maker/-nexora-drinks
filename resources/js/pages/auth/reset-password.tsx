import { Form, Head } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { KeyRound, Command } from 'lucide-react';
import InputError from '@/components/input-error';
import PasswordInput from '@/components/password-input';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ParticlesBackground } from '@/components/ui/particles-background';
import { Spinner } from '@/components/ui/spinner';
import { update } from '@/routes/password';

const fadeUp = {
    hidden: { opacity: 0, y: 24 },
    visible: (i: number) => ({
        opacity: 1,
        y: 0,
        transition: { duration: 0.5, delay: i * 0.08 },
    }),
};

type Props = {
    token: string;
    email: string;
};

export default function ResetPassword({ token, email }: Props) {
    return (
        <>
            <Head title="Réinitialiser le mot de passe — NEXORA" />
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
                                <KeyRound className="h-5 w-5 text-primary" />
                            </div>
                        </motion.div>

                        <motion.div custom={3} variants={fadeUp} initial="hidden" animate="visible" className="mb-6 text-center">
                            <h1 className="text-2xl font-bold tracking-tight text-foreground">Nouveau mot de passe</h1>
                            <p className="mt-1 text-sm text-muted-foreground">
                                Choisissez un mot de passe sécurisé pour votre compte
                            </p>
                        </motion.div>

                        <Form
                            {...update.form()}
                            transform={(data) => ({ ...data, token, email })}
                            resetOnSuccess={['password', 'password_confirmation']}
                            className="flex flex-col gap-4"
                        >
                            {({ processing, errors }) => (
                                <>
                                    <motion.div custom={4} variants={fadeUp} initial="hidden" animate="visible" className="grid gap-1.5">
                                        <Label htmlFor="email" className="text-sm font-medium">Adresse e-mail</Label>
                                        <Input
                                            id="email"
                                            type="email"
                                            name="email"
                                            autoComplete="email"
                                            value={email}
                                            readOnly
                                            className="h-11 rounded-lg border-border bg-muted text-muted-foreground cursor-not-allowed"
                                        />
                                        <InputError message={errors.email} />
                                    </motion.div>

                                    <motion.div custom={5} variants={fadeUp} initial="hidden" animate="visible" className="grid gap-1.5">
                                        <Label htmlFor="password" className="text-sm font-medium">Nouveau mot de passe</Label>
                                        <PasswordInput
                                            id="password"
                                            name="password"
                                            autoComplete="new-password"
                                            autoFocus
                                            placeholder="••••••••"
                                            className="h-11 rounded-lg border-border bg-background focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                                        />
                                        <InputError message={errors.password} />
                                    </motion.div>

                                    <motion.div custom={6} variants={fadeUp} initial="hidden" animate="visible" className="grid gap-1.5">
                                        <Label htmlFor="password_confirmation" className="text-sm font-medium">Confirmer le mot de passe</Label>
                                        <PasswordInput
                                            id="password_confirmation"
                                            name="password_confirmation"
                                            autoComplete="new-password"
                                            placeholder="••••••••"
                                            className="h-11 rounded-lg border-border bg-background focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                                        />
                                        <InputError message={errors.password_confirmation} />
                                    </motion.div>

                                    <motion.div custom={7} variants={fadeUp} initial="hidden" animate="visible">
                                        <motion.button
                                            type="submit"
                                            disabled={processing}
                                            whileHover={{ scale: 1.015 }}
                                            whileTap={{ scale: 0.985 }}
                                            data-test="reset-password-button"
                                            className="mt-2 w-full h-11 rounded-lg bg-primary text-primary-foreground font-semibold text-sm flex items-center justify-center gap-2 transition-opacity disabled:opacity-60 disabled:cursor-not-allowed"
                                        >
                                            {processing ? <Spinner /> : <KeyRound className="h-4 w-4" />}
                                            {processing ? 'Réinitialisation…' : 'Réinitialiser le mot de passe'}
                                        </motion.button>
                                    </motion.div>
                                </>
                            )}
                        </Form>
                    </motion.div>

                    <motion.p
                        custom={8} variants={fadeUp} initial="hidden" animate="visible"
                        className="mt-6 text-center text-xs text-muted-foreground"
                    >
                        &copy; {new Date().getFullYear()} NEXORA · Tous droits réservés
                    </motion.p>
                </div>
            </div>
        </>
    );
}

ResetPassword.layout = (page: any) => <>{page}</>;
