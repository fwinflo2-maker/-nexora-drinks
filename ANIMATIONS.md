# 🎨 Animations NEXORA

## Résumé des améliorations

### ✅ Navigation & Redirection
- **Logo NEXORA sur login** : Clique une fois → Landing page (`/`), 3 clics secrets → Super Admin login
- **Logout** : Redirige automatiquement vers `/login` après déconnexion
- **ParticlesBackground** : Opacité augmentée à 0.8 pour meilleure visibilité (particules noires)

### ✅ Composants d'animation réutilisables

#### 1. **AnimatedButton** (`components/ui/animated-button.tsx`)
```tsx
import { AnimatedButton } from '@/components/ui/animated-button';

<AnimatedButton onClick={handleClick} className="px-4 py-2 rounded-lg">
    Cliquez-moi
</AnimatedButton>
```
- Scale au hover: 1.02
- Tap: 0.98
- Transition spring pour un effet naturel

#### 2. **Stagger Container** (`components/ui/stagger-container.tsx`)
```tsx
import { StaggerContainer, StaggerItem } from '@/components/ui/stagger-container';

<StaggerContainer staggerChildren={0.1}>
    <StaggerItem>Item 1</StaggerItem>
    <StaggerItem>Item 2</StaggerItem>
    <StaggerItem>Item 3</StaggerItem>
</StaggerContainer>
```
- Animations échelonnées sur les éléments enfants
- Configurable: `staggerChildren`, `delayChildren`

#### 3. **Animations d'entrée** (`components/ui/animations.tsx`)
```tsx
import { FadeInUp, ScaleIn, SlideIn } from '@/components/ui/animations';

// Fade up
<FadeInUp delay={0.2}>Contenu</FadeInUp>

// Scale in
<ScaleIn>Contenu</ScaleIn>

// Slide in
<SlideIn direction="left">Contenu</SlideIn>
```
- Direction: 'left', 'right', 'up', 'down'
- Configurable: `delay`, `duration`

#### 4. **KPI Cards** (`components/ui/kpi-card.tsx`)
```tsx
import { KPICard, AnimatedCard } from '@/components/ui/kpi-card';
import { TrendingUp } from 'lucide-react';

<KPICard
    title="CA du Mois"
    value="28.4M"
    subtitle="XAF"
    icon={TrendingUp}
    color="bg-emerald-500/10 text-emerald-400"
    delay={0.1}
    trend={{ value: 12, isPositive: true }}
/>
```
- Hover effect: translate Y et shadow
- Icône rotatée au hover
- Trend indicator animé

#### 5. **Page Transitions** (`components/ui/page-transitions.tsx`)
```tsx
import { PageTransition, PageSlide } from '@/components/ui/page-transitions';

// Fade transition
<PageTransition>
    <YourContent />
</PageTransition>

// Slide transition
<PageSlide direction="right">
    <YourContent />
</PageSlide>
```
- Direction: 'left', 'right', 'up', 'down'

### ✅ Dashboards animés

#### **AdminDashboard**
- Header avec fade-up
- Tabs navigation avec slide
- Tab content avec transition

#### **CommercialDashboard**
- KPIs avec stagger animation
- Tables avec fade-in
- Cartes avec hover effect
- Accès rapides avec animations

#### **DrinksApp Sidebar**
- Navigation items avec hover
- Logout button avec icon rotation
- Sidebar collapse/expand (si implémenté)

### ✅ Pages animées

#### **Login Page**
- Logo avec fade-up
- Form card avec backdrop blur
- Input fields avec stagger
- Bouton submit avec scale

#### **Welcome (Landing Page)**
- Hero section avec stagger
- Buttons avec hover effects
- Features grid avec stagger
- Sector selector avec animations

### 🎯 Bonnes pratiques

1. **Utilisez les composants réutilisables** au lieu de créer des animations partout
2. **Stagger pour les listes** : `StaggerContainer` + `StaggerItem`
3. **Hover effects** : `whileHover` sur motion components
4. **Transitions fluides** : ease `[0.22, 1, 0.36, 1]` pour smoothness
5. **Accessibilité** : Les animations respectent les préférences utilisateur (avec `prefers-reduced-motion` si nécessaire)

### 📦 Imports centralisés

```tsx
// Tous les composants d'animation
import {
    AnimatedButton,
    StaggerContainer,
    StaggerItem,
    FadeInUp,
    ScaleIn,
    SlideIn,
    KPICard,
    AnimatedCard,
} from '@/components/ui';
```

### 🎬 Exemples d'utilisation

#### Formulaire avec animations échelonnées
```tsx
<StaggerContainer>
    <StaggerItem><LabelInput name="email" /></StaggerItem>
    <StaggerItem><LabelInput name="password" /></StaggerItem>
    <StaggerItem><AnimatedButton type="submit">Connexion</AnimatedButton></StaggerItem>
</StaggerContainer>
```

#### Grid de cartes KPI
```tsx
<div className="grid grid-cols-1 md:grid-cols-4 gap-4">
    {kpis.map((kpi, i) => (
        <KPICard
            key={kpi.id}
            {...kpi}
            delay={i * 0.1}
        />
    ))}
</div>
```

### 🚀 Performance

- Animations utilisent GPU via `transform` et `opacity`
- Pas d'animations sur des milliers d'éléments simultanément
- Stagger avec délai court (0.1s) pour meilleur UX
- Transition duration: 0.3-0.5s pour rapidité et smoothness

## Futures améliorations possibles

- [ ] Animations de loading (spinners)
- [ ] Animations d'erreur/succès pour les formulaires
- [ ] Micro-interactions sur les tableaux
- [ ] Scroll-triggered animations
- [ ] Animations theme toggle
