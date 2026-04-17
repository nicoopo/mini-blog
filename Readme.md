# 🖊 Mini Blog — Symfony

Projet réalisé dans le cadre d'un TP IPSSI — Création d'un mini blog avec Symfony, Docker, PostgreSQL et EasyAdmin.

---

## 📋 Sommaire

- [Technologies](#technologies)
- [Prérequis](#prérequis)
- [Installation](#installation)
- [Structure du projet](#structure-du-projet)
- [Base de données](#base-de-données)
- [Fonctionnalités](#fonctionnalités)
- [Rôles et accès](#rôles-et-accès)
- [Routes principales](#routes-principales)
- [EasyAdmin](#easyadmin)
- [Fixtures](#fixtures)
- [Accès utiles](#accès-utiles)

---

## 🛠 Technologies

- **Symfony 8** — Framework PHP
- **PHP 8.4** — via Docker
- **PostgreSQL 16** — Base de données
- **Apache** — Serveur web via Docker
- **EasyAdmin 5** — Interface d'administration
- **Bootstrap 5** — Interface utilisateur via AssetMapper
- **Docker & Docker Compose** — Environnement de développement
- **pgAdmin 4** — Interface de gestion de la base de données

---

## ✅ Prérequis

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) installé
- [Git](https://git-scm.com/) installé
- Un éditeur de code (PhpStorm recommandé)

---

## 🚀 Installation

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

### 6. (Optionnel) Créer un compte administrateur

```bash
docker compose exec php php bin/console doctrine:query:sql \
  "UPDATE \"user\" SET roles = '[\"ROLE_ADMIN\"]' WHERE email = 'votre@email.com'"
```

---

## 📁 Structure du projet

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
├── migrations/              # Migrations Doctrine
├── src/
│   ├── Controller/
│   │   ├── Admin/           # Controllers EasyAdmin
│   │   ├── ArticleController.php
│   │   ├── HomeController.php
│   │   ├── ProfileController.php
│   │   ├── RegistrationController.php
│   │   └── SecurityController.php
│   ├── DataFixtures/        # Fixtures de test
│   │   ├── UserFixtures.php
│   │   ├── CategoryFixtures.php
│   │   ├── ArticleFixtures.php
│   │   └── CommentFixtures.php
│   ├── Entity/
│   │   ├── Article.php
│   │   ├── Category.php
│   │   ├── Comment.php
│   │   └── User.php
│   ├── Form/
│   │   ├── CommentType.php
│   │   ├── ProfileFormType.php
│   │   └── RegistrationFormType.php
│   ├── Repository/
│   └── Security/
│       └── AuthAuthenticator.php
├── templates/
│   ├── admin/
│   ├── article/
│   ├── home/
│   ├── profile/
│   ├── registration/
│   ├── security/
│   └── base.html.twig
├── compose.yaml
├── compose.override.yaml
└── importmap.php
```

---

## 🗄 Base de données

### Entités

#### User
| Champ | Type | Description |
|---|---|---|
| id | integer | Identifiant unique |
| email | string | Adresse e-mail (unique) |
| password | string | Mot de passe haché |
| roles | array | Rôles (ROLE_USER, ROLE_ADMIN) |
| firstName | string | Prénom |
| lastName | string | Nom |
| username | string | Nom d'utilisateur |
| profilePicture | string | URL photo de profil (nullable) |
| isActive | boolean | Compte actif ou non |
| createdAt | datetime | Date de création |
| updatedAt | datetime | Date de mise à jour (nullable) |

#### Article
| Champ | Type | Description |
|---|---|---|
| id | integer | Identifiant unique |
| title | string | Titre |
| content | text | Contenu |
| picture | string | URL image (nullable) |
| createdAt | datetime | Date de création |
| publishedAt | datetime | Date de publication (nullable) |
| author | ManyToOne → User | Auteur |
| category | ManyToOne → Category | Catégorie (nullable) |

#### Category
| Champ | Type | Description |
|---|---|---|
| id | integer | Identifiant unique |
| name | string | Nom de la catégorie |
| description | text | Description (nullable) |

#### Comment
| Champ | Type | Description |
|---|---|---|
| id | integer | Identifiant unique |
| content | text | Contenu du commentaire |
| createdAt | datetime | Date de création |
| isApproved | boolean | Approuvé ou non |
| status | string | Statut : pending, approved, rejected (nullable) |
| author | ManyToOne → User | Auteur |
| article | ManyToOne → Article | Article associé |

---

## ⚙️ Fonctionnalités

### Authentification
- Inscription avec email, mot de passe, prénom, nom, username
- Connexion par email/mot de passe
- Déconnexion
- Remember me (7 jours)

### Partie publique (Visiteur)
- Page d'accueil avec liste des articles
- Page détail d'un article avec ses commentaires approuvés
- Invitation à se connecter pour commenter

### Partie connectée (ROLE_USER)
- Toutes les pages publiques
- Ajout de commentaires sur un article (soumis en attente de validation)
- Consultation et modification du profil personnel

### Administration (ROLE_ADMIN)
- Dashboard EasyAdmin complet
- CRUD Articles (créer, modifier, supprimer)
- CRUD Catégories
- CRUD Utilisateurs (activer/désactiver)
- Modération des commentaires (approuver/refuser)

---

## 🔐 Rôles et accès

| URL | Visiteur | ROLE_USER | ROLE_ADMIN |
|---|---|---|---|
| `/` | ✅ | ✅ | ✅ |
| `/article/{id}` | ✅ (lecture) | ✅ (+ commenter) | ✅ |
| `/login` | ✅ | ✅ | ✅ |
| `/register` | ✅ | ✅ | ✅ |
| `/profile` | ❌ | ✅ | ✅ |
| `/profile/edit` | ❌ | ✅ | ✅ |
| `/admin` | ❌ | ❌ | ✅ |
| `/admin/article` | ❌ | ❌ | ✅ |
| `/admin/user` | ❌ | ❌ | ✅ |
| `/admin/comment` | ❌ | ❌ | ✅ |
| `/admin/category` | ❌ | ❌ | ✅ |

---

## 🗺 Routes principales

```
GET  /                        app_home
GET  /article/{id}            app_article_show
GET  /login                   app_login
GET  /logout                  app_logout
GET  /register                app_register
GET  /profile                 app_profile
GET  /profile/edit            app_profile_edit
GET  /admin                   admin (EasyAdmin dashboard)
GET  /admin/article           admin_article_index
GET  /admin/user              admin_user_index
GET  /admin/comment           admin_comment_index
GET  /admin/category          admin_category_index
```

---

## 🖥 EasyAdmin

L'interface d'administration est accessible à `/admin` uniquement pour les utilisateurs avec le rôle `ROLE_ADMIN`.

Les CrudControllers sont dans `src/Controller/Admin/` :
- `ArticleCrudController.php`
- `CategoryCrudController.php`
- `CommentCrudController.php`
- `UserCrudController.php`
- `DashboardController.php`

---

## 🧪 Fixtures

Les fixtures permettent de pré-remplir la base de données avec des données de test.

### Fichiers disponibles

| Fichier | Groupe | Contenu |
|---|---|---|
| `UserFixtures.php` | `user` | 1 admin + 5 utilisateurs |
| `CategoryFixtures.php` | `category` | 5 catégories |
| `ArticleFixtures.php` | `article` | 10 articles |
| `CommentFixtures.php` | `comment` | 20 commentaires |

### Charger toutes les fixtures

```bash
# ⚠️ Supprime et recrée toutes les données
docker compose exec php php bin/console doctrine:fixtures:load --no-interaction
```

### Charger groupe par groupe (sans écraser les données existantes)

```bash
# 1. Users en premier (requis par les autres)
docker compose exec php php bin/console doctrine:fixtures:load --group=user --append

# 2. Catégories
docker compose exec php php bin/console doctrine:fixtures:load --group=category --append

# 3. Articles (dépend de user + category)
docker compose exec php php bin/console doctrine:fixtures:load --group=article --append

# 4. Commentaires (dépend de user + article)
docker compose exec php php bin/console doctrine:fixtures:load --group=comment --append
```

### Comptes de test disponibles

| Email | Mot de passe | Rôle |
|---|---|---|
| `admin@blog.com` | `password` | ROLE_ADMIN |
| `user0@blog.com` | `password` | ROLE_USER |
| `user1@blog.com` | `password` | ROLE_USER |
| `user2@blog.com` | `password` | ROLE_USER |
| `user3@blog.com` | `password` | ROLE_USER |
| `user4@blog.com` | `password` | ROLE_USER |

---

## 🌐 Accès utiles

| Service | URL |
|---|---|
| Application | http://localhost:8080 |
| pgAdmin | http://localhost:8081 |
| pgAdmin login | admin@admin.com / admin |
| Mailpit | http://localhost:8025 |

### Connexion pgAdmin → PostgreSQL
- Host : `database`
- Port : `5432`
- Database : `app`
- Username : `app`
- Password : `!ChangeMe!`

---

## 🐳 Commandes Docker utiles

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

## 👨‍💻 Auteur
Nicolas Cataluna  
Projet réalisé dans le cadre du TP IPSSI — Symfony Mini Blog  
Date : Avril 2026
