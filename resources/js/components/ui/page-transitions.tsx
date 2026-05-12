import { motion } from 'framer-motion';
import { ReactNode } from 'react';

interface PageTransitionProps {
    children: ReactNode;
    className?: string;
}

export function PageTransition({ children, className = '' }: PageTransitionProps) {
    return (
        <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            transition={{ duration: 0.3, ease: 'easeInOut' }}
            className={className}
        >
            {children}
        </motion.div>
    );
}

interface PageSlideProps {
    children: ReactNode;
    className?: string;
    direction?: 'left' | 'right' | 'up' | 'down';
}

export function PageSlide({
    children,
    className = '',
    direction = 'right',
}: PageSlideProps) {
    const variants = {
        left: { initial: { x: -100, opacity: 0 }, animate: { x: 0, opacity: 1 }, exit: { x: -100, opacity: 0 } },
        right: { initial: { x: 100, opacity: 0 }, animate: { x: 0, opacity: 1 }, exit: { x: 100, opacity: 0 } },
        up: { initial: { y: 100, opacity: 0 }, animate: { y: 0, opacity: 1 }, exit: { y: 100, opacity: 0 } },
        down: { initial: { y: -100, opacity: 0 }, animate: { y: 0, opacity: 1 }, exit: { y: -100, opacity: 0 } },
    };

    const v = variants[direction];

    return (
        <motion.div
            initial={v.initial}
            animate={v.animate}
            exit={v.exit}
            transition={{ duration: 0.4 }}
            className={className}
        >
            {children}
        </motion.div>
    );
}
