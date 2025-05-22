# VroomIA

VroomIA est un projet composé de deux parties :
- Une API backend développée avec Symfony (vroomIA-api)
- Une interface frontend développée avec Next.js (vroomia-front)

## Prérequis

### Pour l'API (vroomIA-api)
- PHP 8.1 ou supérieur
- Composer
- Base de données MySQL/MariaDB
- Wamp, Xampp ou un serveur web équivalent

### Pour le Frontend (vroomia-front)
- Node.js (version récente recommandée)
- NPM, Yarn, pnpm ou Bun

## Installation

### 1. Cloner le dépôt

```bash
git clone https://github.com/Buldozer42/VroomIA.git
cd VroomIA
```

### 2. Configuration de l'API (vroomIA-api)

```bash
cd vroomIA-api
composer install
```

Configurez les paramètres de connexion à la base de données et la clé API dans `.env.local` :

```bash
DATABASE_URL="mysql://<user>:<password>@127.0.0.1:3306/vroomia?serverVersion=8&charset=utf8mb4"
GEMINI_API_KEY="<your_gemini_api_key>"
```

Créez la base de données et exécutez les migrations :

```bash
# Création de la base de données
php bin/console doctrine:database:create

# Exécution des migrations
php bin/console doctrine:migrations:migrate

# Chargement des données de test (facultatif)
php bin/console doctrine:fixtures:load
```

Créez les clés privée et publique pour l'authentification JWT :

```bash
mkdir -p config/jwt
openssl genrsa -out config/jwt/private.pem 4096
openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
```

### 3. Configuration du Frontend (vroomia-front)

```bash
cd ../vroomia-front
npm install
```

## Lancement du projet

### 1. Démarrer l'API

```bash
cd vroomIA-api
symfony server:start --no-tls
```

L'API sera accessible à l'adresse : http://localhost:8000/api

La documentation Swagger de l'API est disponible à cette même adresse.

### 2. Démarrer le Frontend

Dans un autre terminal :

```bash
cd vroomia-front
npm run dev
```

L'interface frontend sera accessible à l'adresse : http://localhost:3000

## Structure du projet

### vroomIA-api
L'API suit l'architecture standard de Symfony avec API Platform :

- `src/Entity/` : Définition des entités (Vehicle, Driver, Garage, Person, Reservation, etc.)
- `src/Repository/` : Interfaces d'accès aux données
- `src/Controller/` : Contrôleurs pour la logique métier
- `src/Service/` : Services métier
- `config/` : Fichiers de configuration
- `migrations/` : Scripts de migration de base de données

### vroomia-front
Le frontend est basé sur Next.js avec App Router :

- `src/app/` : Pages et composants de l'application
- `public/` : Assets statiques