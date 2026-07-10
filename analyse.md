# Analyse du projet

Stack : Symfony 6.2 (PHP >=8.1), architecture MVC classique (Controller/Entity/Form/Repository), Twig, Postgres. Pas de frontend séparé.

## Analyse statique

### Lisibilité

- Pas de README donc aucune documentation sur le projet (installation, architecture, contribution).
- Nommage globalement cohérent (camelCase Symfony) mais incohérence dans `src/Entity/User.php:44` : `$is_verified` en snake_case au milieu de propriétés camelCase.
- Commentaires de qualité inégale : certains redondants (`AdminMainController.php:120-121` : `//persist data`, `// push`), d'autres utiles. Plusieurs TODO explicites laissés en l'état :
    - `RegisterController.php:151` : `// TODO : j'ai pas fait de vérif en cas d'erreur ...`
    - `Entity/Contract.php:50` : `// TODO : revoir le delete cascade`
    - `Form/NewUserType.php:68` : `// TODO : revoir cette partie sur le MDP provisoire`
- Code mort (commenté) laissé en place : `Class/Mail.php:13-15,41-43,53-55,82-84`, `Security/LoginFormAuthenticator.php:48,67`, `Controller/ResetPasswordController.php:86,91,93`.
- `AdminMainController.php` fait 419 lignes et mélange gestion des users, des customers et du contenu dynamique dans une seule classe (6 dépendances injectées) — responsabilité unique non respectée.
- Indentation à 4 espaces globalement, mais incohérences ponctuelles (2 espaces sur des appels chaînés dans certains `Form/*Type.php`, ex. `ContactType.php:36`). Pas d'outil de formatage automatique en place pour l'imposer.
- Aucun fichier n'utilise `declare(strict_types=1)`. Plusieurs paramètres non typés (`Class/Mail.php:13`, `AdminMainController.php:149` — `$id`, `$slug` non typés).

### Maintenabilité

- Attribut `version: '3'` obsolète dans `docker-compose.yml` **et** `docker-compose.override.yml` (ligne 1 des deux fichiers) — ignoré par Compose V2, génère un avertissement.
- Seuls les services `database` (Postgres) et `mailer` (mailcatcher) sont dockerisés — aucun service applicatif PHP/serveur web dans la stack, l'appli tourne donc forcément hors Docker. Pas de reproductibilité complète de l'environnement.
- Dossier `migrations/` exclu du versioning (`.gitignore:8`) : les migrations Doctrine ne sont jamais commitées, donc pas de traçabilité/reproductibilité du schéma de BDD entre postes.
- Tests très limités : 4 fichiers, 432 lignes au total. Aucun test sur les contrôleurs les plus critiques (`RegisterController`, `ResetPasswordController`, `SecurityController`, `AdminMainController`). `UserUnitTest.php` ne teste que des getters/setters triviaux.
- Aucune CI/CD (pas de `.github/workflows`, pas de `.gitlab-ci.yml`).
- Aucun linter/formatter configuré (pas de php-cs-fixer, phpstan, ecs, rector, editorconfig).
- Dépendances datées : Symfony figé sur `6.2.*`, PHPUnit `^9.5` (obsolète), et usage actif de `sensio/framework-extra-bundle` qui est officiellement déprécié (`#[ParamConverter]` utilisé dans `AdminMainController.php:65,148,208,264,330,331`).
- Duplication de code : logique de slugification dupliquée à l'identique à 3 endroits (`AdminMainController.php:113-116`, `:298-301`, `RegisterController.php:61-64`) ; `Class/Mail.php` a deux méthodes quasi identiques (payload Mailjet copié-collé) ; plusieurs paires de FormType dupliquées au lieu d'être factorisées (`ContactType`/`EditContactType`, `ContractType`/`EditContractType`, `CustomerType`/`EditCustomerType`).
- Couplage fort dans les contrôleurs : aucune couche service/use-case, la logique métier est directement dans les contrôleurs (ex. `AdminMainController` avec 5 repositories + EntityManager injectés).
- `.env.example` présent et versionné (bon point), mais contient des incohérences de formatage (espace en tête ligne 20, bloc Flex mal fermé ligne 33).

### Robustesse

- Gestion d'erreurs (try/catch) présente dans seulement 3 contrôleurs sur 13 ; le reste ne protège pas les accès BDD ou transformations de données.
- Aucune contrainte `Assert\*` dans les 11 entités de `src/Entity/` (sauf `User.php:23-25`) : la validation repose uniquement sur les FormType, donc absente si une entité est manipulée hors du flux formulaire (fixtures, commandes, services).
- Pas de secrets/clés en dur dans `src/`, mais éléments métier codés en dur : adresse email `no-reply@alice-le-blog.fr` (6 occurrences), `TemplateID` Mailjet en dur (`Mail.php:32,72`).
- `var_dump()` laissé en code de production : `Class/Mail.php:48,89` — fuite potentielle d'infos de debug côté réponse API Mailjet.
- Mot de passe Postgres par défaut en clair dans `docker-compose.yml:10` (`!ChangeMe!`, acceptable en dev seulement).
- Points positifs : CSRF activé, login throttling configuré (`security.yaml`), mots de passe hashés avant persist, non-divulgation d'existence de compte au reset password (`ResetPasswordController.php:143-146`).
- Aucun logging applicatif des erreurs métier (ex. échec d'envoi email) — seulement `var_dump` ou échec silencieux, alors que `monolog.yaml` est configuré mais inutilisé dans le code métier.

## Analyse dynamique

_Non réalisée._