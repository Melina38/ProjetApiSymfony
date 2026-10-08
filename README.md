# Movies API

## Présentation

Movies API est une API REST développée avec Symfony.

Elle permet de gérer des films, des catégories, des utilisateurs et comporte un système de note de films, de suivis d'utilisateurs et d'importation de données.

L'API permet notamment de :

- consulter une liste de films ;
- rechercher des films par titre ou par année ;
- paginer les résultats ;
- consulter, créer, modifier et supprimer des films ;
- authentifier un utilisateur avec un token JWT ;
- consulter l'utilisateur connecté ;
- attribuer une note à un film ;
- modifier ou supprimer une note ;
- consulter ses dernières notes ;
- consulter les films avec sa note personnelle ;
- suivre et ne plus suivre des utilisateurs ;
- consulter les utilisateurs.

---

## Technologies

- PHP 8.4.1 minimum
- Symfony 5.2 minimum
- Doctrine ORM
- SQLite
- JWT Authentication
- Symfony Serializer
- Symfony CLI

---

## Installation

Installer les dépendances :
```bash
composer install
```
Lancer le serveur :
```bash
php -S localhost:8000 -t public
```
## Commande personalisée

Importer 150 films :
```bash
php bin/console make:command
```
Nom de la commande: app:init-data

## Tester les requêtes

Dans le fichier api.http de mon folder http, vous trouverez toutes les requêtes possibles de mon API. Il faudra rajouter l'extension HttpYack ou équivalent sur votre IDE.

# Structure de la base de données

L'API utilise une base de données SQLite composée de plusieurs tables permettant
de gérer les films, les utilisateurs, les catégories, les notes et les relations
entre utilisateurs.

## Table `movie`

La table `movie` contient les informations principales des films.

| Champ | Description |
|---|---|
| `id` | Identifiant unique du film |
| `title` | Titre du film |
| `description` | Description du film |
| `year` | Année du film |

Un film peut être associé à plusieurs catégories et peut recevoir plusieurs notes.

---

## Table `category`

La table `category` contient les différentes catégories auxquelles un film peut appartenir.

| Champ | Description |
|---|---|
| `id` | Identifiant unique de la catégorie |
| `name` | Nom de la catégorie |

Un film peut appartenir à plusieurs catégories et une catégorie peut être associée
à plusieurs films.

La relation entre les films et les catégories est donc une relation **Many-to-Many**.

---

## Table `user`

La table `user` contient les utilisateurs de l'application.

| Champ | Description |
|---|---|
| `id` | Identifiant unique de l'utilisateur |
| `email` | Adresse email de l'utilisateur |
| `password` | Mot de passe hashé |
| `roles` | Rôles de l'utilisateur |

Un utilisateur peut attribuer plusieurs notes et peut suivre plusieurs autres utilisateurs.

---

## Table `rating`

La table `rating` représente les notes données par les utilisateurs aux films.

| Champ | Description |
|---|---|
| `id` | Identifiant unique de la note |
| `rating` | Note attribuée au film, de 1 à 10 |
| `date` | Date et heure de création de la note |
| `movie_id` | Film noté |
| `user_id` | Utilisateur ayant attribué la note |

Chaque note appartient à un seul utilisateur et à un seul film.

Un utilisateur peut donc noter plusieurs films et un film peut recevoir plusieurs notes.

---

## Table de relation entre les utilisateurs

Une table de relation permet de gérer les utilisateurs suivis.

Elle représente la relation entre un utilisateur et les autres utilisateurs qu'il suit.

Cette relation permet notamment de :

- suivre un utilisateur ;
- arrêter de suivre un utilisateur ;
- récupérer les activités des utilisateurs suivis.

La relation entre les utilisateurs est une relation **Many-to-Many**.