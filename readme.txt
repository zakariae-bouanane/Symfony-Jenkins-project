Client CRUD - Documentation
- L'entité Client a été créée via la commande `make:entity`, avec les relations nécessaires (notamment avec l'utilisateur).
- Le contrôleur ClientController regroupe toutes les routes liées aux opérations clients sous le préfixe `/client`.
- Le service personnalisé ClientService est injecté pour centraliser la logique métier (création, modification, suppression).
- L'attribut MapEntity est utilisé pour récupérer automatiquement un client à partir de son ID dans l'URL.
- Le formulaire ClientType est utilisé pour gérer la saisie des données dans les vues.

Fonctionnalités mises en place :
- Liste des clients (`GET /client`) :
  - Récupération des clients liés à l'utilisateur connecté via `ClientRepository::findBy(['user' => $user])`.
  - Affichage dans la vue `client/index.html.twig`.

- Ajout d’un client (`GET|POST /client/new`) :
  - Affichage d’un formulaire basé sur ClientType.
  - Association du client à l’utilisateur connecté (`setUser()`).
  - Sauvegarde via `ClientService::saveClient()`.
  - Redirection vers la liste des clients.

- Détail d’un client (`GET /client/{id}`) :
  - Récupération de l'entité via MapEntity.
  - Affichage dans `client/show.html.twig`.

- Modification d’un client (`GET|POST /client/{id}/edit`) :
  - Formulaire pré-rempli avec les données existantes.
  - Mise à jour via `ClientService::updateClient()` après validation.

- Suppression d’un client (`POST /client/{id}`) :
  - Vérification du token CSRF.
  - Suppression via `ClientService::deleteClient()`.

=========================

Authentification :
J’ai généré l’entité User via la commande make:user.
J’ai créé un contrôleur /login et un formulaire FormType contenant l’email, le mot de passe et le token CSRF.
J’ai configuré security.yaml pour protéger toutes les routes et implémenté un authenticator personnalisé LoginFormAuthenticator.
Ce dernier hérite de AbstractLoginFormAuthenticator et contient la logique d’authentification.
Je récupère les données du formulaire,je vérifie l’existence de l’utilisateur et je compare le mot de passe avec md5.
En cas de succès, je remplis la session, retourne un SelfValidatingPassport avec le UserBadge et le token CSRF, puis l’utilisateur est redirigé vers la page d’accueil.
