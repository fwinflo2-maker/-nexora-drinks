import { usePage } from '@inertiajs/react';
import { AppContent } from '@/components/app-content';
import { AppShell } from '@/components/app-shell';
import { AppSidebar } from '@/components/app-sidebar';
import { AppSidebarHeader } from '@/components/app-sidebar-header';
import type { AppLayoutProps } from '@/types';

export default function AppSidebarLayout({
    children,
    breadcrumbs = [],
}: AppLayoutProps) {
    const { currentTeam } = usePage().props as any;
    const sector = currentTeam?.sector?.toLowerCase();

    // Drinks has its own specialized app shell and sidebar.
    // If we are in the drinks sector, we should just render the children
    // to avoid double sidebars/headers.
    const hasOwnLayout = ['boissons', 'distribution'].includes(sector);

    if (hasOwnLayout) {
        return <div className="flex-1 flex flex-col h-full overflow-hidden">{children}</div>;
    }

    return (
        <AppShell variant="sidebar">
            <AppSidebar />
            <AppContent variant="sidebar" className="overflow-x-hidden flex flex-col">
                <AppSidebarHeader breadcrumbs={breadcrumbs} />
                <div className="flex flex-1 flex-col">
                    {children}
                </div>
            </AppContent>
        </AppShell>
    );
}
