# Touche pas au klaxon

Application web intranet de **covoiturage inter-sites**. Les employés d'une entreprise multi-sites publient leurs trajets entre agences ; leurs collègues consultent les trajets qui ont encore des places et contactent le conducteur.

Projet réalisé en **PHP (architecture MVC maison)**, **MySQL / MariaDB**, **Bootstrap 5 + Sass**, avec **PHPStan** et **PHPUnit**.

## Fonctionnalités

| Profil | Possibilités |
|---|---|
| **Visiteur** | Voir la liste des trajets à venir qui ont encore des places (triés par départ croissant) |
| **Employé connecté** | Idem, plus : détails d'un trajet dans une fenêtre modale (auteur, téléphone, email, places totales), création d'un trajet, modification et suppression de **ses** trajets |
| **Administrateur** | Tableau de bord ; liste des utilisateurs ; création, modification, suppression des agences ; liste de **tous** les trajets (passés et complets compris) et suppression |

Règles de cohérence d'un trajet : agences de départ et d'arrivée différentes, départ dans le futur, arrivée après le départ, 1 à 8 places au total, places disponibles comprises entre 0 et le total. Elles sont vérifiées côté serveur **et** par des contraintes `CHECK` en base.

## Prérequis

- PHP **8.2 ou plus** avec les extensions `pdo_mysql`, `mbstring` et `zip`
- MySQL 8 ou MariaDB 10.4+
- [Composer](https://getcomposer.org/)
- [Node.js](https://nodejs.org/) et npm (pour compiler Bootstrap/Sass)
- Git

## Installation

```bash
# 1. Récupérer le projet
git clone <URL_DU_DEPOT> touche-pas-au-klaxon
cd touche-pas-au-klaxon

# 2. Dépendances PHP et front
composer install
npm install

# 3. Compiler le CSS (palette Sass + Bootstrap), copier le JS et les icônes
npm run build

# 4. Configuration : copier le modèle puis y renseigner vos identifiants MySQL
cp config/config.example.php config/config.php        # Windows : copy config\config.example.php config\config.php
```

Éditez `config/config.php` (bloc `db` : `host`, `port`, `name`, `user`, `password`). Ce fichier n'est **pas versionné**.

### Base de données

Le script de création **supprime et recrée** la base `klaxon` : à lancer une seule fois, dans cet ordre.

```bash
mysql -u root -p -e "source database/schema.sql"
mysql -u root -p -e "source database/seed.sql"
```

(Les commandes utilisent `source` pour fonctionner aussi dans PowerShell. Vous pouvez aussi ouvrir les deux fichiers dans HeidiSQL, DBeaver ou phpMyAdmin et les exécuter dans le même ordre.)

Le jeu d'essais contient **12 agences**, **21 employés** (dont 1 administrateur) et **17 trajets** : 13 disponibles, 2 complets, 2 passés. Ses dates sont **relatives à la date d'exécution** du script, donc toujours dans le futur pour les trajets à venir.

### Lancer l'application

```bash
php -S localhost:4000 -t public
```

Ouvrir <http://localhost:4000>. Le MySQL doit être démarré.

## Comptes de test

| Rôle | Identifiant | Mot de passe |
|---|---|---|
| Administrateur | `admin@klaxon.fr` | `Admin2026!` |
| Employé | `alexandre.martin@email.fr` | `Klaxon2026!` |

Tous les autres employés du jeu d'essais (ex. `julien.bernard@email.fr`) utilisent le même mot de passe `Klaxon2026!`. Les mots de passe sont stockés hachés (`password_hash`).

## Qualité du code

```bash
composer stan    # analyse statique PHPStan (niveau 6)
composer test    # tests unitaires PHPUnit
```

Les tests utilisent une base **séparée `klaxon_test`**, créée automatiquement au premier lancement à partir de `database/schema.sql` : la base `klaxon` n'est jamais modifiée. L'utilisateur MySQL de `config/config.php` doit donc pouvoir créer cette base.

Les tests couvrent toutes les écritures en base (création, modification, suppression de trajets et d'agences), les contraintes d'intégrité, les validateurs et les outils du noyau (CSRF, messages flash, échappement HTML).

## Architecture

```
public/               Seul dossier exposé au navigateur
  index.php           Contrôleur frontal (point d'entrée unique)
  assets/js/app.js    Modale de détails, confirmation de suppression
src/
  Core/               NOYAU RÉUTILISABLE (aucune logique "covoiturage")
    Router, Controller, View, Database (PDO), Session, Flash, Csrf, Auth
  Controllers/        AuthController, TrajetController, AdminController
  Models/             TrajetRepository, AgenceRepository, EmployeRepository
  Validators/         TrajetValidator, AgenceValidator
  Views/              layouts/, partials/ (headers, flash), trajets/, auth/, admin/, errors/
config/               config.example.php, routes.php
database/             schema.sql, seed.sql
scss/                 _variables.scss (palette), main.scss
tests/                Tests PHPUnit
docs/                 MCD (mcd.png) et MLD (mld.md)
```

**Principe MVC.** Le routeur (`config/routes.php`) associe une URL à une méthode de contrôleur. Le contrôleur interroge un *repository* (modèle) et transmet les données à une vue PHP affichée dans le layout commun.

### Réutiliser le code pour un autre site de l'intranet

1. Copier le dossier `src/Core/`, le `scss/`, `public/` et `config/` : le noyau ne connaît ni les trajets ni les agences.
2. Écrire ses propres contrôleurs, modèles et vues, et déclarer ses routes dans `config/routes.php`.
3. Changer la **palette** en modifiant uniquement `scss/_variables.scss` (variables Bootstrap `$primary`, `$secondary`, `$danger`...), puis `npm run build`.

## Routes

| Méthode | URL | Accès | Action |
|---|---|---|---|
| GET | `/` | Tous | Liste des trajets disponibles |
| GET, POST | `/login` | Visiteur | Connexion |
| POST | `/logout` | Connecté | Déconnexion |
| GET, POST | `/trajets/create` | Connecté | Créer un trajet |
| GET, POST | `/trajets/{id}/edit` | Auteur | Modifier un trajet |
| POST | `/trajets/{id}/delete` | Auteur | Supprimer un trajet |
| GET | `/admin` | Admin | Tableau de bord |
| GET | `/admin/users` | Admin | Liste des utilisateurs |
| GET | `/admin/agences` | Admin | Liste des agences |
| GET, POST | `/admin/agences/create`, `/admin/agences/{id}/edit` | Admin | Créer / modifier une agence |
| POST | `/admin/agences/{id}/delete` | Admin | Supprimer une agence (refusé si elle est utilisée) |
| GET | `/admin/trajets` | Admin | Liste de tous les trajets |
| POST | `/admin/trajets/{id}/delete` | Admin | Supprimer un trajet |

## Sécurité

- Requêtes SQL **préparées** (PDO) : pas d'injection SQL.
- Sortie HTML **échappée** par `View::e()` : pas de XSS.
- **Jeton CSRF** sur tous les formulaires POST.
- Mots de passe hachés (`password_hash` / `password_verify`), identifiant de session régénéré à la connexion.
- **Autorisations vérifiées côté serveur** : un employé ne peut modifier ou supprimer que ses trajets (403 sinon), et seules les pages d'administration exigent le rôle `admin`.
- Les coordonnées des conducteurs ne sont **jamais envoyées** aux visiteurs non connectés.

## Documentation de conception

- MCD : [`docs/mcd.png`](docs/mcd.png)
- MLD : [`docs/mld.md`](docs/mld.md)
