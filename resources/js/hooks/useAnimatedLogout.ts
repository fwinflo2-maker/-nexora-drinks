import { router } from '@inertiajs/react';
import { useState } from 'react';

export function useAnimatedLogout() {
    const [isLoggingOut, setIsLoggingOut] = useState(false);

    const logout = (logoutUrl: string) => {
        setIsLoggingOut(true);
        // Attendre que l'animation se termine avant de faire le logout
        setTimeout(() => {
            router.post(logoutUrl, {}, {
                onFinish: () => {
                    // Redirige vers la page de login après la déconnexion
                    window.location.href = '/login';
                },
            });
        }, 300);
    };

    return { isLoggingOut, logout };
}
