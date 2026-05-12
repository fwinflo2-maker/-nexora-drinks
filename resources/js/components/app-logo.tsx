import AppLogoIcon from '@/components/app-logo-icon';

export default function AppLogo() {
    return (
        <div className="flex items-center gap-3 ml-1">
            <div className="flex h-8 w-8 items-center justify-center rounded-md bg-foreground text-background">
                <AppLogoIcon className="h-4 w-4" />
            </div>
            <div className="grid flex-1 text-left text-sm">
                <span className="mb-0.5 truncate leading-tight font-bold text-lg">
                    NEXORA
                </span>
                <span className="truncate text-xs text-muted-foreground">
                    Système ERP
                </span>
            </div>
        </div>
    );
}
