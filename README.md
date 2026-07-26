# CraftpickChat

Interface de discussion moderne inspirée de Slack, développée en HTML, CSS et JavaScript pur.

## Fonctionnalités

- Interface responsive pour ordinateur et téléphone
- Liste de canaux et messages directs
- Envoi local de messages
- Création dynamique de canaux
- Recherche dans les messages
- Réactions interactives
- Aucun framework ni installation nécessaire

## Lancer le projet

Ouvre simplement `index.html` dans un navigateur.

Pour le publier avec GitHub Pages :

1. Ouvre les paramètres du dépôt.
2. Va dans **Pages**.
3. Choisis **Deploy from a branch**.
4. Sélectionne la branche `main` et le dossier `/root`.

## Limites actuelles

Cette version est un frontend de démonstration. Les messages ne sont pas encore partagés entre plusieurs utilisateurs et ne sont pas conservés après le rechargement de la page. Un backend Node.js avec Socket.IO et une base de données pourra être ajouté ensuite.
