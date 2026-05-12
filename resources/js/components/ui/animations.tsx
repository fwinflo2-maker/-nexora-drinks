import { motion } from 'framer-motion';
import { ReactNode } from 'react';

interface FadeInUpProps {
    children: ReactNode;
    delay?: number;
    duration?: number;
    className?: string;
}

export function FadeInUp({
    children,
    delay = 0,
    duration = 0.5,
    className = '',
}: FadeInUpProps) {
    return (
        <motion.div
            className={className}
            initial={{ opacity: 0, y: 24 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{
                duration,
                delay,
            }}
        >
            {children}
        </motion.div>
    );
}

interface ScaleInProps {
    children: ReactNode;
    delay?: number;
    duration?: number;
    className?: string;
}

export function ScaleIn({
    children,
    delay = 0,
    duration = 0.4,
    className = '',
}: ScaleInProps) {
    return (
        <motion.div
            className={className}
            initial={{ opacity: 0, scale: 0.95 }}
            animate={{ opacity: 1, scale: 1 }}
            transition={{
                duration,
                delay,
                ease: 'easeOut',
            }}
        >
            {children}
        </motion.div>
    );
}

interface SlideInProps {
    children: ReactNode;
    direction?: 'left' | 'right' | 'up' | 'down';
    delay?: number;
    duration?: number;
    className?: string;
}

export function SlideIn({
    children,
    direction = 'left',
    delay = 0,
    duration = 0.5,
    className = '',
}: SlideInProps) {
    const variants = {
        left: { x: -48 },
        right: { x: 48 },
        up: { y: 48 },
        down: { y: -48 },
    };

    return (
        <motion.div
            className={className}
            initial={{ opacity: 0, ...variants[direction] }}
            animate={{ opacity: 1, x: 0, y: 0 }}
            transition={{
                duration,
                delay,
            }}
        >
            {children}
        </motion.div>
    );
}
