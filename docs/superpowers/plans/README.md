# Plans d'Implémentation — Nexora

Ce dossier contient les plans d'implémentation générés par le skill `writing-plans`.

Chaque plan est créé à partir d'une spec approuvée et exécuté par le skill `subagent-driven-development`.

## Format des Fichiers

`YYYY-MM-DD-<feature>.md`

## En-tête Obligatoire

```markdown
# [Nom de la Feature] — Plan d'Implémentation

> **Pour les agents implémenteurs :** SKILL REQUIS : `superpowers:subagent-driven-development`
> ou `superpowers:executing-plans` pour exécuter ce plan tâche par tâche.

**Objectif :** [Une phrase décrivant ce qui est construit]

**Architecture :** [2-3 phrases sur l'approche]

**Stack :** Laravel 13 + Pest v4 + Inertia.js v3 + React 19 + TailwindCSS v4

---
```
