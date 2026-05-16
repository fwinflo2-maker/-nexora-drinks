import { usePage } from '@inertiajs/react';

interface PageProps {
  has_hotel: boolean;
  has_fnb: boolean;
  team_modules: string[];
  [key: string]: unknown;
}

export function useModules() {
  const { has_hotel, has_fnb, team_modules } = usePage<PageProps>().props;

  return {
    hasHotel: has_hotel ?? false,
    hasFnB: has_fnb ?? false,
    activeModules: team_modules ?? [],
    hasModule: (module: string) => (team_modules ?? []).includes(module),
  };
}
