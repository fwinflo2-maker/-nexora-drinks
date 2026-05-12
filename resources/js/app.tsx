import { createInertiaApp } from '@inertiajs/react';
import { Toaster } from '@/components/ui/sonner';
import { TooltipProvider } from '@/components/ui/tooltip';
import { initializeTheme } from '@/hooks/use-appearance';
import AppLayout from '@/layouts/app-layout';
import AuthLayout from '@/layouts/auth-layout';
import SettingsLayout from '@/layouts/settings/layout';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

window.route = function (name: string, params: Record<string, any> = {}) {
    const team = params.current_team || params.team || '';
    
    // Dashboard Routes
    if (name === 'dashboard.overview') {
return `/${team}/dashboard`;
}

    if (name === 'dashboard.food.overview') {
return `/${team}/dashboard/food`;
}

    if (name === 'dashboard.commercial') {
return `/${team}/dashboard/commercial`;
}

    // Drinks Routes
    const base = team ? `/${team}/drinks` : '/drinks';

    if (name === 'drinks.dashboard') {
return `${base}/dashboard`;
}

    if (name.startsWith('drinks.reports.')) {
        const report = name.replace('drinks.reports.', '');

        return `${base}/reports/${report.replace('.', '/')}`;
    }

    if (name.startsWith('drinks.')) {
        const parts = name.replace('drinks.', '').split('.');
        const module = parts[0];
        const action = parts[1];
        const idKey = Object.keys(params).find(k => k !== 'current_team' && k !== 'team');
        const id = idKey ? params[idKey] : '';

        if (action === 'index' || action === 'store') {
return `${base}/${module}`;
}

        if (action === 'create') {
return `${base}/${module}/create`;
}

        if (action === 'edit') {
return `${base}/${module}/${id}/edit`;
}

        if (id) {
return `${base}/${module}/${id}/${action === 'show' ? '' : action}`.replace(/\/$/, '');
}

        return `${base}/${module}/${action}`;
    }

    // Fallback to home or the name itself if it looks like a path
    if (name === 'home') {
return '/';
}

    return name.startsWith('/') ? name : `/${name}`;
};

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    layout: (name) => {
        switch (true) {
            case name === 'welcome':
            case name === 'docs':
            case name === 'auth/pending-approval':
            case name.startsWith('super-admin/'):
                return null;
            case name.startsWith('auth/'):
                return AuthLayout;
            case name.startsWith('settings/'):
            case name.startsWith('teams/'):
                return [AppLayout, SettingsLayout];
            default:
                return null;
        }
    },
    strictMode: true,
    withApp(app) {
        return (
            <TooltipProvider delayDuration={0}>
                {app}
                <Toaster />
            </TooltipProvider>
        );
    },
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on load...
initializeTheme();
