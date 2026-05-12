/**
 * Admin Dashboard Components Index
 * ─────────────────────────────────
 * Specialized admin dashboards by sector
 */

export { default as AdminDashboard } from './AdminDashboard';
export { default as AdminDashboardFood } from './AdminDashboardFood';


/**
 * Sector to Dashboard mapping
 */
export const SECTOR_DASHBOARD_MAP = {
    alimentaire:  'AdminDashboardFood',
    restaurant:   'AdminDashboardFood',
} as const;

export type SectorType = keyof typeof SECTOR_DASHBOARD_MAP;
