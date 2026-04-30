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
mysql -u <user> -p saint_quentin_arbre < webapp/sql/schema_mysql.sql
```
