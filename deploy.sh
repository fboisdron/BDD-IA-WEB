#!/bin/bash

set -e

SERVER="f_boisdr@f-boisdr.projets.isen-ouest.info"
REMOTE_DIR="/var/www/f_boisdr"
LOCAL_WEBAPP="$(cd "$(dirname "$0")/webapp" && pwd)"

echo "==> Déploiement vers $SERVER:$REMOTE_DIR"

# Contenu de public/ → racine du serveur
echo "--- Envoi des fichiers publics..."
rsync -avz --progress \
  --exclude='.env' \
  --exclude='*.DS_Store' \
  "$LOCAL_WEBAPP/public/" \
  "$SERVER:$REMOTE_DIR/"

# app/, config/, lib/, sql/ → sous-dossiers à la racine
for DIR in app config lib sql; do
  echo "--- Envoi de $DIR/..."
  rsync -avz --progress \
    --exclude='.env' \
    --exclude='*.DS_Store' \
    "$LOCAL_WEBAPP/$DIR/" \
    "$SERVER:$REMOTE_DIR/$DIR/"
done

echo ""
echo "==> Déploiement terminé."
echo ""
echo "N'oublie pas de vérifier/créer le fichier .env sur le serveur :"
echo "  ssh $SERVER"
echo "  nano $REMOTE_DIR/.env"
