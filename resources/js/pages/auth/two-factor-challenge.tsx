import { Form, Head, setLayoutProps } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { REGEXP_ONLY_DIGITS } from 'input-otp';
import { Command, ShieldCheck, RefreshCw } from 'lucide-react';
import { useMemo, useState } from 'react';
import InputError from '@/components/input-error';
import { Input } from '@/components/ui/input';
import {
    InputOTP,
    InputOTPGroup,
    InputOTPSlot,
} from '@/components/ui/input-otp';
import { ParticlesBackground } from '@/components/ui/particles-background';
import { Spinner } from '@/components/ui/spinner';
import { OTP_MAX_LENGTH } from '@/hooks/use-two-factor-auth';
import { store } from '@/routes/two-factor/login';

const fadeUp = {
    hidden: { opacity: 0, y: 24 },
    visible: (i: number) => ({
        opacity: 1,
        y: 0,
        transition: { duration: 0.5, delay: i * 0.08 },
    }),
};

export default function TwoFactorChallenge() {
    const [showRecoveryInput, setShowRecoveryInput] = useState<boolean>(false);
    const [code, setCode] = useState<string>('');

    const authConfigContent = useMemo<{
        title: string;
        description: string;
        toggleText: string;
        toggleLabel: string;
    }>(() => {
        if (showRecoveryInput) {
            return {
                title: 'Code de récupération',
                description: 'Saisissez l\'un de vos codes de récupération d\'urgence pour accéder à votre compte.',
                toggleText: 'Utiliser un code d\'authentification',
                toggleLabel: 'auth',
            };
        }

        return {
            title: 'Authentification à deux facteurs',
            description: 'Saisissez le code fourni par votre application d\'authentification.',
            toggleText: 'Utiliser un code de récupération',
            toggleLabel: 'recovery',
        };
    }, [showRecoveryInput]);

    setLayoutProps({
        title: authConfigContent.title,
        description: authConfigContent.description,
    });

    const toggleRecoveryMode = (clearErrors: () => void): void => {
        setShowRecoveryInput(!showRecoveryInput);
        clearErrors();
        setCode('');
    };

    return (
        <>
            <Head title="Double authentification — NEXORA" />
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
                                <ShieldCheck className="h-5 w-5 text-primary" />
                            </div>
                        </motion.div>

                        <motion.div custom={3} variants={fadeUp} initial="hidden" animate="visible" className="mb-6 text-center">
                            <h1 className="text-2xl font-bold tracking-tight text-foreground">
                                {showRecoveryInput ? 'Code de récupération' : 'Double authentification'}
                            </h1>
                            <p className="mt-1 text-sm text-muted-foreground">
                                {authConfigContent.description}
                            </p>
                        </motion.div>

                        <Form
                            {...store.form()}
                            className="flex flex-col gap-4"
                            resetOnError
                            resetOnSuccess={!showRecoveryInput}
                        >
                            {({ errors, processing, clearErrors }) => (
                                <>
                                    {showRecoveryInput ? (
                                        <motion.div custom={4} variants={fadeUp} initial="hidden" animate="visible" className="grid gap-1.5">
                                            <Input
                                                name="recovery_code"
                                                type="text"
                                                placeholder="Entrez votre code de récupération"
                                                autoFocus={showRecoveryInput}
                                                required
                                                className="h-11 rounded-lg border-border bg-background focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-center tracking-widest font-mono"
                                            />
                                            <InputError message={errors.recovery_code} />
                                        </motion.div>
                                    ) : (
                                        <motion.div custom={4} variants={fadeUp} initial="hidden" animate="visible"
                                            className="flex flex-col items-center gap-3"
                                        >
                                            <InputOTP
                                                name="code"
                                                maxLength={OTP_MAX_LENGTH}
                                                value={code}
                                                onChange={(value) => setCode(value)}
                                                disabled={processing}
                                                pattern={REGEXP_ONLY_DIGITS}
                                            >
                                                <InputOTPGroup className="gap-2">
                                                    {Array.from({ length: OTP_MAX_LENGTH }, (_, index) => (
                                                        <InputOTPSlot
                                                            key={index}
                                                            index={index}
                                                            className="h-12 w-10 rounded-lg border-border text-base font-semibold"
                                                        />
                                                    ))}
                                                </InputOTPGroup>
                                            </InputOTP>
                                            <InputError message={errors.code} />
                                        </motion.div>
                                    )}

                                    <motion.div custom={5} variants={fadeUp} initial="hidden" animate="visible">
                                        <motion.button
                                            type="submit"
                                            disabled={processing}
                                            whileHover={{ scale: 1.015 }}
                                            whileTap={{ scale: 0.985 }}
                                            className="mt-2 w-full h-11 rounded-lg bg-primary text-primary-foreground font-semibold text-sm flex items-center justify-center gap-2 transition-opacity disabled:opacity-60 disabled:cursor-not-allowed"
                                        >
                                            {processing ? <Spinner /> : <ShieldCheck className="h-4 w-4" />}
                                            {processing ? 'Vérification…' : 'Continuer'}
                                        </motion.button>
                                    </motion.div>

                                    <motion.div custom={6} variants={fadeUp} initial="hidden" animate="visible"
                                        className="text-center"
                                    >
                                        <button
                                            type="button"
                                            onClick={() => toggleRecoveryMode(clearErrors)}
                                            className="text-sm text-muted-foreground hover:text-foreground flex items-center gap-1.5 mx-auto transition-colors"
                                        >
                                            <RefreshCw className="h-3.5 w-3.5" />
                                            {authConfigContent.toggleText}
                                        </button>
                                    </motion.div>
                                </>
                            )}
                        </Form>
                    </motion.div>

                    <motion.p
                        custom={7} variants={fadeUp} initial="hidden" animate="visible"
                        className="mt-6 text-center text-xs text-muted-foreground"
                    >
                        &copy; {new Date().getFullYear()} NEXORA · Tous droits réservés
                    </motion.p>
                </div>
            </div>
        </>
    );
}

TwoFactorChallenge.layout = (page: any) => <>{page}</>;
