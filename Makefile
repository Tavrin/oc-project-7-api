#Makefile

help:
	@echo "Makefile permettant d'installer le projet en une commande, utiliser 'make install', configurer le lien de la base de données dans un fichier .env.local avant"

install:
	composer-install setup-database schema-update fixtures cache-clear 

composer-install:
	composer install --no-dev --optimize-autoloader

fixtures:
	symfony console doctrine:fixtures:load

setup-database:
	symfony console doctrine:database:create

schema-update:
	symfony console doctrine:schema:update --force

cache-clear:
	symfony console c:c
