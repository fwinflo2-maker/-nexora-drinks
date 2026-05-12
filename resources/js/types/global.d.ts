import type { Auth } from '@/types/auth';
import type { Team } from '@/types/teams';

declare module '@inertiajs/core' {
    export interface InertiaConfig {
        sharedPageProps: {
            name: string;
            auth: Auth;
            sidebarOpen: boolean;
            currentTeam: Team | null;
            teams: Team[];
            [key: string]: unknown;
        };
    }
}

declare global {
    function route(name: string, params?: Record<string, any>): string;
    interface Window {
        route: (name: string, params?: Record<string, any>) => string;
    }
}
