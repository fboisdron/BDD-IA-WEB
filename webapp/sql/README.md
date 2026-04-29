# Fichiers de schéma de base de données

## Fichiers disponibles

- **schema.sql** → Schéma PostgreSQL original (gardé pour compatibilité)
- **schema_mysql.sql** → Schéma MySQL compatible

## Comment charger le schéma

### PostgreSQL (défaut)

```bash
psql -U postgres -d saint_quentin_arbre -f webapp/sql/schema.sql
```

### MySQL

```bash
mysql -u root -p saint_quentin_arbre < webapp/sql/schema_mysql.sql
```

## À noter

Les deux schémas définissent les mêmes tables avec les mêmes contraintes CHECK. Les différences sont purement syntaxiques:

- Autoincréments: PostgreSQL utilise `BIGSERIAL`, MySQL utilise `BIGINT AUTO_INCREMENT`
- Types numériques: conversion `NUMERIC` → `DECIMAL`, `DOUBLE PRECISION` → `DOUBLE`
- Index sur TEXT: MySQL requiert une limite de longueur `(100)` sur les clés TEXT
- Timestamps: PostgreSQL `WITHOUT TIME ZONE` → MySQL `DEFAULT CURRENT_TIMESTAMP`

Voir [MIGRATION_MYSQL.md](MIGRATION_MYSQL.md) pour les étapes complètes de migration.
