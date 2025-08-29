#!/bin/bash

echo "🔄 Réinitialisation de la base de données via Docker..."

# Vérifier si les conteneurs sont démarrés
if ! docker-compose ps | grep -q "mysql.*Up"; then
    echo "⚠️  MySQL n'est pas démarré. Démarrage des services..."
    docker-compose up -d mysql
    sleep 5
fi

# Exécuter le script SQL directement dans le conteneur MySQL
echo "⚡ Exécution du script SQL dans le conteneur MySQL..."
docker-compose exec -T mysql mysql -u root -pdevmdp < src/sql/reset-database.sql

if [ $? -eq 0 ]; then
    echo "✅ Base de données réinitialisée avec succès!"
    echo "📊 Toutes les tables ont été supprimées et recréées"
    echo "🆕 La base de données 'ia_platform' est prête à être utilisée"
    echo ""
    echo "🎉 Réinitialisation terminée!"
    echo "💡 Vous pouvez maintenant charger les fixtures avec: php load-fixtures.php"
else
    echo "❌ Erreur lors de la réinitialisation de la base de données"
    exit 1
fi
