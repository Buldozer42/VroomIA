# VroomIA API

## À propos

VroomIA-api est l'API du projet VroomIA.

## Prérequis

- PHP 8.1 ou supérieur
- Composer
- Base de données compatible avec Doctrine ORM (MySQL/MariaDB recommandé)
- Wamp, Xampp ou un serveur web équivalent
- Git

## Installation

1. Cloner le dépôt :
```bash
git clone https://github.com/Buldozer42/VroomIA.git VroomIA

cd VroomIA/vroomIA-api
```

2. Installer les dépendances :
```bash
composer install
```

3. Configurer l'environnement :
   - Configurez les paramètres de connexion à la base de données dans `.env.local`

4. Créer la base de données :
```bash
php bin/console doctrine:database:create
```

5. Exécuter les migrations :
```bash
php bin/console doctrine:migrations:migrate
```

## Architecture du projet

Le projet suit l'architecture standard de Symfony avec API Platform :

- `src/Entity/` : Définition des entités et de leurs relations
  - Vehicle : Gestion des véhicules
  - Driver : Gestion des conducteurs
  - Garage : Gestion des garages
  - Person : Informations personnelles
  - Reservation : Système de réservation
  - Adress : Gestion des adresses
- `src/Repository/` : Interfaces d'accès aux données
- `config/` : Fichiers de configuration
- `migrations/` : Scripts de migration de base de données

## API Endpoints

**ToDo**

## Développement

Pour lancer le serveur de développement :

```bash
symfony server:start
```

## Documentation API

Une fois l'application lancée, la documentation Swagger est disponible à l'adresse :
```
http://localhost:8000/api
```
