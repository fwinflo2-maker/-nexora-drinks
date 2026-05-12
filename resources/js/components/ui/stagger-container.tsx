import { motion, Variants } from 'framer-motion';
import { ReactNode } from 'react';

interface StaggerContainerProps {
    children: ReactNode;
    className?: string;
    staggerChildren?: number;
    delayChildren?: number;
}

const staggerContainerVariants: Variants = {
    hidden: { opacity: 0 },
    visible: (custom = { staggerChildren: 0.1, delayChildren: 0 }) => ({
        opacity: 1,
        transition: {
            staggerChildren: custom.staggerChildren,
            delayChildren: custom.delayChildren,
        },
    }),
};

const staggerItemVariants: Variants = {
    hidden: { opacity: 0, y: 20 },
    visible: {
        opacity: 1,
        y: 0,
        transition: {
            duration: 0.5,
        },
    },
};

export function StaggerContainer({
    children,
    className = '',
    staggerChildren = 0.1,
    delayChildren = 0,
}: StaggerContainerProps) {
    return (
        <motion.div
            className={className}
            variants={staggerContainerVariants}
            initial="hidden"
            animate="visible"
            custom={{ staggerChildren, delayChildren }}
        >
            {children}
        </motion.div>
    );
}

export function StaggerItem({ children, className = '' }: { children: ReactNode; className?: string }) {
    return (
        <motion.div
            className={className}
            variants={staggerItemVariants}
        >
            {children}
        </motion.div>
    );
}
