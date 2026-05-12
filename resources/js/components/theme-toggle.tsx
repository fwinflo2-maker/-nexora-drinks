import { Monitor, Moon, Sun } from 'lucide-react';
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/tooltip';
import { useAppearance } from '@/hooks/use-appearance';
import type { Appearance } from '@/hooks/use-appearance';

const cycleOrder: Appearance[] = ['light', 'dark', 'system'];

const icons: Record<Appearance, React.ElementType> = {
    light: Sun,
    dark: Moon,
    system: Monitor,
};

const labels: Record<Appearance, string> = {
    light: 'Mode clair',
    dark: 'Mode sombre',
    system: 'Mode système',
};

export function ThemeToggle({ className = '' }: { className?: string }) {
    const { appearance, updateAppearance } = useAppearance();

    const handleCycle = () => {
        const currentIndex = cycleOrder.indexOf(appearance);
        const next = cycleOrder[(currentIndex + 1) % cycleOrder.length];
        updateAppearance(next);
    };

    const Icon = icons[appearance];

    return (
        <TooltipProvider delayDuration={0}>
            <Tooltip>
                <TooltipTrigger asChild>
                    <button
                        onClick={handleCycle}
                        aria-label={labels[appearance]}
                        className={`group inline-flex h-9 w-9 items-center justify-center rounded-md bg-transparent text-sm font-medium text-muted-foreground ring-offset-background transition-all duration-200 hover:bg-accent hover:text-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 ${className}`}
                    >
                        <Icon className="h-4 w-4 transition-transform duration-300 group-hover:scale-110" />
                    </button>
                </TooltipTrigger>
                <TooltipContent side="bottom">
                    <p>{labels[appearance]}</p>
                </TooltipContent>
            </Tooltip>
        </TooltipProvider>
    );
}
