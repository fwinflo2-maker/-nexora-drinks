# Superpowers — Documentation du Projet Nexora

Ce dossier contient les documents générés par le framework [Superpowers](https://github.com/obra/superpowers) au fil du développement.

## Structure

```
docs/superpowers/
├── specs/          # Spécifications design générées par le skill brainstorming
│   └── YYYY-MM-DD-<sujet>-design.md
└── plans/          # Plans d'implémentation générés par le skill writing-plans
    └── YYYY-MM-DD-<feature>.md
```

## Workflow

1. **Brainstorming** → Génère une spec dans `specs/`
2. **Writing Plans** → Génère un plan dans `plans/`
3. **Subagent-Driven Development** → Exécute le plan tâche par tâche

## Agents de l'Équipe

| Agent | Fichier | Rôle |
|-------|---------|------|
| 🎯 Tech Lead | `.claude/agents/tech-lead.md` | Architecte, brainstorm, coordination |
| ⚙️ Backend Dev | `.claude/agents/backend-dev.md` | Laravel, PHP, Pest TDD |
| 🎨 Frontend Dev | `.claude/agents/frontend-dev.md` | React, Inertia, Tailwind |
| 🧪 QA Engineer | `.claude/agents/qa-engineer.md` | Tests, code review |
| 🔍 Debugger | `.claude/agents/debugger.md` | Débogage systématique |

## Skills Disponibles

Les 14 skills Superpowers sont dans `.claude/skills/superpowers/`.

| Skill | Déclencheur |
|-------|-------------|
| `brainstorming` | Avant toute feature |
| `writing-plans` | Après validation du design |
| `subagent-driven-development` | Pour exécuter un plan |
| `test-driven-development` | Pendant l'implémentation |
| `systematic-debugging` | Pour déboguer |
| `requesting-code-review` | Après chaque tâche |
| `finishing-a-development-branch` | Clôture de branche |
| `using-git-worktrees` | Espace de travail isolé |
| `executing-plans` | Exécution en session parallèle |
| `dispatching-parallel-agents` | Workflows concurrents |
| `receiving-code-review` | Réponse aux reviews |
| `verification-before-completion` | Avant de clôturer |
| `writing-skills` | Créer de nouveaux skills |
| `using-superpowers` | Introduction au système |

## Langue

Toutes les communications, specs et plans sont **en français**.
