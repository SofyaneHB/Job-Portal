# Job Portal Application

Une plateforme web dédiée à la gestion des offres d'emploi et des candidatures, conçue pour mettre en relation les recruteurs et les candidats de manière fluide et sécurisée.

## 🚀 Description du Projet
Ce projet est une application full-stack développée dans le cadre d'un stage d'initiation. Elle permet une séparation claire des rôles (Candidat, Recruteur, Administrateur) et offre une expérience utilisateur moderne.

## 🛠️ Stack Technique
* **Backend:** PHP
* **Base de données:** MySQL
* **Frontend:** JavaScript, Tailwind CSS

## ✨ Fonctionnalités Principales
* **Gestion des utilisateurs:** Inscription, authentification sécurisée et gestion de sessions.
* **Contrôle d'accès:** Interfaces spécifiques pour Candidats, Recruteurs et Administrateurs.
* **Recrutement:** Publication d'offres d'emploi, moteur de recherche, et gestion des candidatures.
* **Gestion de documents:** Upload et stockage sécurisé des CV et logos d'entreprises.

## 🛡️ Aspects Techniques & Sécurité
Ce projet met un accent particulier sur la sécurité et les bonnes pratiques :
* **Accès aux données:** Utilisation de **PDO** avec requêtes préparées pour prévenir les injections SQL.
* **Hachage:** Mots de passe sécurisés via l'algorithme **BCRYPT** (`password_hash`).
* **Gestion de l'état:** Utilisation des sessions PHP et de l'API `window.history` (JS) pour un nettoyage propre des URLs.
* **Validation:** Validation rigoureuse des données côté client et côté serveur.

## 📂 Structure du Projet
```text
job-portal/
├── Public/          # Points d'entrée
├── config/          # Configuration DB
├── auth/            # Logique d'authentification
├── includes/        # Composants réutilisables
├── assets/          # CSS, JS et images
├── uploads/         # Stockage (CV, logos)
├── candidate/       # Espace candidat
├── company/         # Espace recruteur
├── admin/           # Espace administrateur
├── jobs/            # Gestion des offres
└── database/        # Script SQL
