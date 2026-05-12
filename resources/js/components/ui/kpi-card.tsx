import { motion } from 'framer-motion';
import { ReactNode } from 'react';
import { LucideIcon } from 'lucide-react';

interface KPICardProps {
    title: string;
    value: string | number;
    subtitle?: string;
    icon?: LucideIcon;
    color?: string;
    delay?: number;
    trend?: {
        value: number;
        isPositive: boolean;
    };
}

export function KPICard({
    title,
    value,
    subtitle,
    icon: Icon,
    color = 'bg-blue-500/10 text-blue-400',
    delay = 0,
    trend,
}: KPICardProps) {
    return (
        <motion.div
            initial={{ opacity: 0, y: 16 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.5, delay }}
            whileHover={{ y: -4, transition: { duration: 0.2 } }}
            className={`rounded-xl border border-border bg-card/40 backdrop-blur-sm p-6 transition-all duration-300 hover:shadow-lg hover:border-border/60 ${color}`}
        >
            <div className="flex items-start justify-between">
                <div className="flex-1">
                    <p className="text-sm font-medium text-muted-foreground mb-1">{title}</p>
                    <div className="flex items-baseline gap-2">
                        <p className="text-2xl font-bold text-foreground">{value}</p>
                        {trend && (
                            <motion.span
                                initial={{ opacity: 0, x: -8 }}
                                animate={{ opacity: 1, x: 0 }}
                                className={`text-xs font-semibold ${trend.isPositive ? 'text-emerald-400' : 'text-rose-400'}`}
                            >
                                {trend.isPositive ? '+' : ''}{trend.value}%
                            </motion.span>
                        )}
                    </div>
                    {subtitle && <p className="text-xs text-muted-foreground mt-1">{subtitle}</p>}
                </div>
                {Icon && (
                    <motion.div
                        whileHover={{ scale: 1.1, rotate: 5 }}
                        transition={{ type: 'spring', stiffness: 300 }}
                        className={`w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 ${color.split(' ')[0]}`}
                    >
                        <Icon className="h-5 w-5" />
                    </motion.div>
                )}
            </div>
        </motion.div>
    );
}

interface AnimatedCardProps {
    children: ReactNode;
    className?: string;
    delay?: number;
}

export function AnimatedCard({ children, className = '', delay = 0 }: AnimatedCardProps) {
    return (
        <motion.div
            initial={{ opacity: 0, scale: 0.95 }}
            animate={{ opacity: 1, scale: 1 }}
            transition={{ duration: 0.4, delay, ease: 'easeOut' }}
            whileHover={{ y: -2, transition: { duration: 0.2 } }}
            className={className}
        >
            {children}
        </motion.div>
    );
}
