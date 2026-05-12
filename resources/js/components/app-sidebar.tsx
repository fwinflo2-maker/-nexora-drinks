import { Link, usePage } from '@inertiajs/react';
import {
    LayoutGrid, Package, Truck, Users, Settings, BookOpen,
    ShoppingCart, FileText, CreditCard, BarChart3,
    ArrowLeftRight, Bell, DollarSign, TrendingDown, Box,
} from 'lucide-react';
import { useState } from 'react';
import AppLogo from '@/components/app-logo';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import { TeamSwitcher } from '@/components/team-switcher';
import {
    Sheet, SheetContent, SheetHeader, SheetTitle,
} from '@/components/ui/sheet';
import {
    Sidebar, SidebarContent, SidebarFooter, SidebarHeader,
    SidebarMenu, SidebarMenuButton, SidebarMenuItem,
} from '@/components/ui/sidebar';
import type { NavItem } from '@/types';

/**
 * Returns nav items adapted to the role for the Drinks sector
 */
function getNavItems(teamSlug: string, role: string): NavItem[] {
    const drinksNav = {
        admin: [
            { title: "Vue d'ensemble",     href: `/${teamSlug}/drinks/dashboard`,               icon: LayoutGrid  },
            { title: 'Articles & Tarifs',  href: `/${teamSlug}/drinks/articles`,                icon: Box         },
            { title: 'Stocks',             href: `/${teamSlug}/drinks/inventories`,             icon: Package     },
            { title: 'Mouvements',         href: `/${teamSlug}/drinks/stock-movements`,         icon: ArrowLeftRight },
            { title: 'Ventes',             href: `/${teamSlug}/drinks/sales`,                   icon: ShoppingCart },
            { title: 'Approvisionnements', href: `/${teamSlug}/drinks/procurements`,            icon: Truck       },
            { title: 'Règlements',         href: `/${teamSlug}/drinks/payments`,                icon: FileText    },
            { title: 'Charges',            href: `/${teamSlug}/drinks/expenses`,                icon: CreditCard  },
            { title: 'Rapports',           href: `/${teamSlug}/drinks/reports/brouillard`,      icon: BarChart3   },
            { title: 'Membres',            href: `/${teamSlug}/dashboard/profiles`,             icon: Users       },
            { title: 'Configuration',      href: `/${teamSlug}/drinks/settings`,                icon: Settings    },
        ],
        gerant: [
            { title: "Vue d'ensemble",     href: `/${teamSlug}/drinks/dashboard`,               icon: LayoutGrid   },
            { title: 'Approvisionnements', href: `/${teamSlug}/drinks/procurements`,            icon: Truck        },
            { title: 'Ventes',             href: `/${teamSlug}/drinks/sales`,                   icon: ShoppingCart },
            { title: 'Stocks',             href: `/${teamSlug}/drinks/inventories`,             icon: Package      },
            { title: 'Rapports',           href: `/${teamSlug}/drinks/reports/brouillard`,      icon: BarChart3    },
        ],
        ops: [
            { title: "Vue d'ensemble",     href: `/${teamSlug}/drinks/dashboard`,               icon: LayoutGrid   },
            { title: 'Approvisionnements', href: `/${teamSlug}/drinks/procurements`,            icon: Truck        },
            { title: 'Inventaires',        href: `/${teamSlug}/drinks/inventories`,             icon: Package      },
            { title: 'Pertes',             href: `/${teamSlug}/drinks/losses`,                  icon: TrendingDown },
        ],
        caissier: [
            { title: "Vue d'ensemble",     href: `/${teamSlug}/drinks/dashboard`,               icon: LayoutGrid   },
            { title: 'Ventes',             href: `/${teamSlug}/drinks/sales`,                   icon: ShoppingCart },
            { title: 'Emballages',         href: `/${teamSlug}/drinks/packagings`,              icon: Box          },
            { title: 'Règlements clients', href: `/${teamSlug}/drinks/payments`,                icon: FileText     },
        ],
        comptable: [
            { title: "Vue d'ensemble",     href: `/${teamSlug}/drinks/dashboard`,               icon: LayoutGrid   },
            { title: 'Charges',            href: `/${teamSlug}/drinks/expenses`,                icon: CreditCard   },
            { title: 'Apports',            href: `/${teamSlug}/drinks/cash-inputs`,             icon: DollarSign   },
            { title: 'Versements',         href: `/${teamSlug}/drinks/cash-deposits`,           icon: BarChart3    },
            { title: 'Rapports financiers',href: `/${teamSlug}/drinks/reports/brouillard`,      icon: BarChart3    },
        ],
        magasinier: [
            { title: "Vue d'ensemble",     href: `/${teamSlug}/drinks/dashboard`,               icon: LayoutGrid   },
            { title: 'Inventaires',        href: `/${teamSlug}/drinks/inventories`,             icon: Package      },
            { title: 'Emballages',         href: `/${teamSlug}/drinks/packagings`,              icon: Box          },
            { title: 'Pertes',             href: `/${teamSlug}/drinks/losses`,                  icon: TrendingDown },
            { title: 'Mouvements',         href: `/${teamSlug}/drinks/stock-movements`,         icon: ArrowLeftRight },
        ],
        member: [
            { title: "Vue d'ensemble",     href: `/${teamSlug}/drinks/dashboard`,               icon: LayoutGrid   },
        ]
    };

    return drinksNav[role as keyof typeof drinksNav] || drinksNav.member;
}


const DOC_SECTIONS = [
    {
        title: 'Vue d\'ensemble',
        icon: LayoutGrid,
        content: 'Le tableau de bord vous donne une vue synthétique de votre activité : chiffre d\'affaires, clients actifs, alertes stock et performances logistiques.',
    },
    {
        title: 'Stocks & Articles',
        icon: Package,
        content: 'Gérez vos articles, suivez les niveaux de stock et enregistrez les inventaires ou pertes.',
    },
    {
        title: 'Ventes & Clients',
        icon: ShoppingCart,
        content: 'Enregistrez les ventes, gérez vos clients et suivez les règlements.',
    },
    {
        title: 'Approvisionnements',
        icon: Truck,
        content: 'Gérez vos achats auprès des fournisseurs et suivez les réceptions de marchandises.',
    },
    {
        title: 'Finances',
        icon: FileText,
        content: 'Suivez vos charges, apports et versements bancaires.',
    },
];

export function AppSidebar() {


    const page = usePage();
    const currentTeam = page.props.currentTeam as any;
    const auth = page.props.auth as any;

    const isSuperAdmin = auth?.user?.nexora_role === 'super_admin';
    const dashboardUrl = isSuperAdmin
        ? '/super-admin/dashboard'
        : currentTeam
            ? `/${currentTeam.slug}/dashboard`
            : '/';

    const nexoraRole = auth?.user?.nexora_role as string | null ?? null;
    const teamRole   = currentTeam?.role as string | null ?? null;
    
    const roleMap: Record<string, string> = {
        owner: 'admin', admin: 'admin', super_admin: 'admin',
        gerant: 'gerant', ops: 'ops', caissier: 'caissier',
        comptable: 'comptable', logisticien: 'ops',
        commercial: 'caissier', magasinier: 'magasinier',
        manager: 'gerant', livreur: 'ops', member: 'member',
    };
    const roleSource = isSuperAdmin ? nexoraRole : teamRole;
    const role = roleMap[(roleSource || 'member').toLowerCase()] ?? 'member';

    const mainNavItems = getNavItems(currentTeam?.slug ?? '', role);

    const allNavItems: NavItem[] = [
        ...mainNavItems,
        { title: 'Notifications', href: '#',                 icon: Bell      },
        { title: 'Paramètres',    href: '/settings/profile', icon: Settings  },
        { title: 'Documentation', href: '/docs', icon: BookOpen },
    ];

    return (
        <>
            <Sidebar collapsible="icon" variant="inset">
                <SidebarHeader>
                    <SidebarMenu>
                        <SidebarMenuItem>
                            <SidebarMenuButton size="lg" asChild>
                                <Link href={dashboardUrl} prefetch>
                                    <AppLogo />
                                </Link>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    </SidebarMenu>
                    <SidebarMenu>
                        <SidebarMenuItem>
                            <TeamSwitcher />
                        </SidebarMenuItem>
                    </SidebarMenu>
                </SidebarHeader>

                <SidebarContent>
                    <NavMain items={allNavItems} />
                </SidebarContent>

                <SidebarFooter>
                    <NavUser />
                </SidebarFooter>
            </Sidebar>


        </>
    );
}
