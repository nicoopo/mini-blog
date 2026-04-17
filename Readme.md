# 🖊 Mini Blog — Symfony

Projet réalisé dans le cadre d'un TP IPSSI — Création d'un mini blog avec Symfony, Docker, PostgreSQL et EasyAdmin.

---

## Table des matières

1. [Technologies](#1-technologies)
2. [Prérequis](#2-prérequis)
3. [Installation](#3-installation)
4. [Structure du projet](#4-structure-du-projet)
5. [Base de données](#5-base-de-données)
6. [Fonctionnalités](#6-fonctionnalités)
7. [Rôles et accès](#7-rôles-et-accès)
8. [Routes principales](#8-routes-principales)
9. [EasyAdmin](#9-easyadmin)
10. [Fixtures](#10-fixtures)
11. [Accès utiles](#11-accès-utiles)

---

## 1. Technologies

- **Symfony 8** — Framework PHP
- **PHP 8.4** — via Docker
- **PostgreSQL 16** — Base de données
- **Apache** — Serveur web via Docker
- **EasyAdmin 5** — Interface d'administration
- **Bootstrap 5** — Interface utilisateur via AssetMapper
- **Docker & Docker Compose** — Environnement de développement
- **pgAdmin 4** — Interface de gestion de la base de données

---

## 2. Prérequis

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) installé
- [Git](https://git-scm.com/) installé
- Un éditeur de code (PhpStorm recommandé)

---

## 3. Installation

### 1. Cloner le projet

```bash
git clone <url-du-repo>
cd mini-blog
```

### 2. Lancer les conteneurs Docker

```bash
docker compose up -d --build
```

### 3. Installer les dépendances

```bash
docker compose exec php composer install
```

### 4. Configurer l'environnement

Copier le fichier `.env` et adapter si besoin :

```bash
cp .env .env.local
```

Vérifier que la variable `DATABASE_URL` pointe bien vers le conteneur Docker :

```env
DATABASE_URL="postgresql://app:!ChangeMe!@database:5432/app?serverVersion=16&charset=utf8"
```

### 5. Créer la base de données et jouer les migrations

```bash
docker compose exec php php bin/console doctrine:database:create
docker compose exec php php bin/console doctrine:migrations:migrate
```

### 6. Optionnel — Créer un compte administrateur

```bash
docker compose exec php php bin/console doctrine:query:sql \
  "UPDATE \"user\" SET roles = '[\"ROLE_ADMIN\"]' WHERE email = 'votre@email.com'"
```

---

## 4. Structure du projet

```
mini-blog/
├── assets/                  # AssetMapper (JS, CSS)
│   ├── app.js
│   └── styles/app.css
├── config/                  # Configuration Symfony
│   └── packages/
│       ├── security.yaml
│       └── twig.yaml
├── docker/
│   ├── apache/
│   │   ├── Dockerfile
│   │   └── vhost.conf
│   └── php/
│       └── Dockerfile
├── src/
│   ├── Controller/
│   ├── Entity/
│   ├── Form/
│   └── Repository/
├── templates/
├── migrations/
├── .env
├── compose.yaml
└── compose.override.yaml
```

---

## 5. Base de données

| Entité | Description |
|--------|-------------|
| `User` | Utilisateurs du blog |
| `Article` | Articles publiés |
| `Category` | Catégories des articles |
| `Comment` | Commentaires sur les articles |

---

## 6. Fonctionnalités

- Inscription et connexion des utilisateurs
- Création, modification et suppression d'articles
- Système de catégories
- Commentaires sur les articles
- Interface d'administration EasyAdmin
- Envoi d'emails (Mailpit en dev)

---

## 7. Rôles et accès

| Rôle | Accès |
|------|-------|
| `ROLE_USER` | Lecture des articles, ajout de commentaires |
| `ROLE_ADMIN` | Accès complet + interface EasyAdmin |

---

## 8. Routes principales

| Route | Méthode | Description |
|-------|---------|-------------|
| `/` | GET | Page d'accueil |
| `/article/{id}` | GET | Détail d'un article |
| `/article/new` | GET/POST | Créer un article |
| `/article/{id}/edit` | GET/POST | Modifier un article |
| `/article/{id}/delete` | POST | Supprimer un article |
| `/login` | GET/POST | Connexion |
| `/register` | GET/POST | Inscription |
| `/logout` | GET | Déconnexion |
| `/admin` | GET | Interface EasyAdmin |

---

## 9. EasyAdmin

L'interface d'administration est accessible à l'adresse :

```
http://localhost:8080/admin
```

Elle permet de gérer :

- Les **utilisateurs** (rôles, emails)
- Les **articles** (contenu, catégorie, auteur)
- Les **catégories**
- Les **commentaires**

---

## 10. Fixtures

### Fichiers disponibles

| Fichier | Groupe | Contenu |
|---------|--------|---------|
| `UserFixtures.php` | `user` | 1 admin + 5 utilisateurs |
| `CategoryFixtures.php` | `category` | 5 catégories |
| `ArticleFixtures.php` | `article` | 10 articles |
| `CommentFixtures.php` | `comment` | 20 commentaires |

### Charger toutes les fixtures

```bash
docker compose exec php php bin/console doctrine:fixtures:load --no-interaction
```

### Charger groupe par groupe

```bash
docker compose exec php php bin/console doctrine:fixtures:load --group=user --append
docker compose exec php php bin/console doctrine:fixtures:load --group=category --append
docker compose exec php php bin/console doctrine:fixtures:load --group=article --append
docker compose exec php php bin/console doctrine:fixtures:load --group=comment --append
```

### Comptes de test disponibles

| Email | Mot de passe  | Rôle |
|-------|---------------|------|
| `admin@blog.com` | `admin123`    | ROLE_ADMIN |
| `user0@blog.com` | `password123` | ROLE_USER |
| `user1@blog.com` | `password123` | ROLE_USER |
| `user2@blog.com` | `password123` | ROLE_USER |
| `user3@blog.com` | `password123` | ROLE_USER |
| `user4@blog.com` | `password123` | ROLE_USER |

---

## 11. Accès utiles

| Service | URL |
|---------|-----|
| Application | http://localhost:8080 |
| pgAdmin | http://localhost:8081 |
| Mailpit | http://localhost:8025 |

### Connexion pgAdmin vers PostgreSQL

- **Host** : `database`
- **Port** : `5432`
- **Database** : `app`
- **Username** : `app`
- **Password** : `!ChangeMe!`

---

## Commandes Docker utiles

```bash
# Démarrer les conteneurs
docker compose up -d

# Arrêter les conteneurs
docker compose down

# Accéder au conteneur PHP
docker compose exec php bash

# Vider le cache Symfony
docker compose exec php php bin/console cache:clear

# Créer une migration
docker compose exec php php bin/console make:migration

# Jouer les migrations
docker compose exec php php bin/console doctrine:migrations:migrate

# Voir les routes
docker compose exec php php bin/console debug:router
```

---

## Auteur

Nicolas Cataluna  
Projet réalisé dans le cadre du TP IPSSI — Symfony Mini Blog  
Date : Avril 2026
