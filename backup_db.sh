#!/bin/bash
# Variables
DB_USER="recette-user"
DB_PASSWORD="password"
DB_NAME="recettes"
BACKUP_DIR="~/backup_dir"
DATE=$(date +\%Y-\%m-\%d)

# Créer le répertoire de sauvegarde s'il n'existe pas
mkdir -p $BACKUP_DIR

# Effectuer la sauvegarde
mysqldump -u $DB_USER -p$DB_PASSWORD $DB_NAME > $BACKUP_DIR/$DB_NAME-$DATE.sql

# Supprimer les sauvegardes de plus de 7 jours
find $BACKUP_DIR/* -mtime +7 -exec rm {} \;

# Rendez ce script exécutable :
chmod +x backup_db.sh
