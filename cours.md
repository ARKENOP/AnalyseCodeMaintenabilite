Voici la transcription de votre document sous forme de fichier Markdown, structurée pour la lisibilité et avec les références exigées :

A.L.I.C.E : Analyse de Code et Maintenabilité

**Antoine Ludwig, Informatique, Conseils, E-solutions - Développement Web** Présente Outil principal mentionné : SonarQube

---

## Sommaire

1. INTRODUCTION À L'ANALYSE DE CODE


2. MÉTRIQUES DE CODE ET MAINTENABILITÉ


3. REFACTORING ET AMÉLIORATION DU CODE



---

1. INTRODUCTION À L'ANALYSE DE CODE

Concepts

**Analyse de code**

* L'analyse de code consiste à examiner un code source pour évaluer sa qualité, détecter des problèmes et proposer des améliorations.


* Elle comprend la détection des bugs, l'amélioration de la lisibilité, la réduction de la complexité et permet d'anticiper les risques futurs.



**Analyse statique vs Analyse dynamique**

*
**Analyse statique** : C'est une analyse du code sans l'exécuter.


* Elle permet de détecter des erreurs potentielles (liées à une mauvaise qualité de code), des conventions non respectées, du code mort, du code inutilement complexe etc.


* L'analyse statique est rapide, simple et facilement automatisable.


* Cependant, elle a ses limites, elle ne détecte que ce qui est lié à la qualité du code et peut produire de faux positifs.


* On utilise des linters pour analyser le code de manière statique (par exemple SonarQube que nous verrons plus tard).




*
**Analyse dynamique** : C'est une analyse du code pendant son exécution.


* Elle permet de détecter des erreurs avérées (qui créent des bugs au runtime), des fuites de mémoires et autres problèmes de performances etc.


* L'analyse dynamique est proche du réel et détecte des bugs et problèmes concrets.


* Cependant, elle a ses limites, elle dépend des cas testés et sa mise en place est plus longue et donc plus coûteuse.


* On va utiliser un faisceau d'outils pour l'analyse dynamique : les tests automatisés, les logs, le profiler etc.





**Qualité logicielle**

* La qualité logicielle peut être analysée sous une multitude d'angles.


* Nous allons nous concentrer sur 3 facteurs principaux et essentiels : la lisibilité, la maintenabilité et la robustesse du code.



1. La lisibilité

* La lisibilité est la capacité à comprendre rapidement ce que fait le code, sans effort excessif.


* Cela passe notamment par des noms de variables, de fonctions et de classes clairs qui reflètent l'intention de ce que le code fait.


* Exemple d'un code peu explicite : `$a=$u->getD();` => c'est correct mais aucune idée de ce que ça fait sans aller faire des allers-retours dans le code.


* Exemple d'un code lisible : `$lastLoginDate = $user->getLastLoginDate();` => c'est pareil mais là on comprend ce que le code fait du premier coup d'œil !


* Un code lisible est un code organisé logiquement (et un découpage en fonctions notamment).


* Et avec une indentation correcte ! Des conditions mal formatées sont fatigantes à lire, tandis qu'une condition bien espacée et indentée va tout de suite beaucoup mieux.


* Enfin un code lisible doit être simple ! Il vaut mieux vérifier l'invalidité des conditions au début et faire un retour anticipé (ex: `if (!$order || $order->getTotal() <= 100) return 0;`) plutôt que d'imbriquer de multiples `if` et `else` profonds.



2. La maintenabilité

* Un code lisible est un code qui peut être modifié facilement sans créer de bug.


* On doit pouvoir ajouter une fonctionnalité, corriger des erreurs ou adapter le code aux usages métier sans tout casser.


* Encore une fois, un code simple est un réel atout, plus le code est complexe plus il est difficile à modifier.


* Les composants doivent être indépendants : c'est le découplage.


* Un controller qui gèrerait la bdd, la logique métier et l'orchestration par exemple crée des couplages forts.


* Si je découpe avec un repository qui gère l'accès aux données, un service qui gère la logique métier et un controller qui gère uniquement l'orchestration, c'est moins couplé et plus maintenable !


* Et pour aller plus loin dans le découplage, il faut penser également modularité !


* Une classe avec 1000 lignes de code, c'est pas simple à maintenir alors que plusieurs classes spécialisées et indépendantes dans des fichiers à part, c'est beaucoup plus clair et maintenable !



3. La robustesse

* Un code robuste est un code qui peut gérer les erreurs sans planter.


* Ça permet d'éviter les crashs et comportements incohérents.


* Ça permet aussi d'éviter d'exposer des vulnérabilités.


* Pour s'assurer de la robustesse de votre code, il faut implémenter :


* La gestion des exceptions : utiliser `try - catch` => on évite le crash et on log l'erreur.


* La validation des données => on ne fait jamais confiance aux entrées, on valide toujours !


* On pense à l'éventualité où c'est null, où c'est vide, ou on a une valeur inattendue etc.





Enjeux

*
**Dette technique** : C'est l'accumulation de choix techniques qui ralentissent les évolutions futures.


* Par exemple du code dupliqué, l'absence de tests ou une architecture désorganisée.


* Elle peut être maîtrisée et réduite progressivement (refactoring, tests, amélioration de l'architecture), sinon elle risque de s'accumuler et à terme ralentir fortement le développement et mener à des refontes importantes (et coûteuses).




*
**Coût de maintenance** : La maintenance peut représenter jusqu'à 60 à 80% du temps d'une équipe.


* Trois types de maintenance :


* Corrective : corriger des bugs => temps exponentiel si faible qualité au départ.


* Évolutive : ajouter de nouvelles features.


* Adaptative : s'adapter à un changement d'environnement (OS, framework, API tierce).






*
**Risques techniques** : Un code de mauvaise qualité expose à des risques techniques, c'est-à-dire qu'il peut induire des bugs et des régressions : une correction à un endroit casse un autre endroit du code sans forcément que ça se voit de suite.


* Limiter le couplage permet de réduire le risque de régressions.




*
**Sécurité** : Les mauvaises pratiques de développement sont à l'origine de la majorité des failles de sécurité applicative (injections SQL, rétro-ingénierie, escalade de privilèges, XSS via des champs non échappés etc.).


*
**Risques organisationnels** : Un code illisible est un code qui ne vit que dans la tête de celui qui l'a écrit.


* Quand ce développeur part, la connaissance part avec lui, et l'équipe se retrouve à maintenir un système que personne ne comprend vraiment.


* De plus, quand le code est trop complexe, l'intégration des nouveaux est ralentie.





Bonnes Pratiques

*
**Clean code** :


* Mieux vaut un code simple qu'un code qui a l'air très "intelligent".


* SRP => une fonction = une seule responsabilité.


* DRY => pas de duplication de code.


* Mieux vaut être complet que concis si c'est au détriment de la lisibilité.




*
**Conventions de nommage** :


* Utiliser des noms explicites décrivant l'intention.


* Utiliser l'anglais (ou le français mais toujours la même langue).


* Rester cohérent dans tout le projet sur ces conventions.




*
**Respecter les règles de l'architecture choisie** :


* Par exemple si on choisit une architecture MVC :


* Entity représente une donnée métier.


* Controller reçoit la requête HTTP, orchestre la réponse.


* Service contient la logique métier.


* Repository gère l'accès à la base de données.




* Donc on ne met pas de logique métier dans le controller par exemple.





Cas Pratique : Mise en route du projet

* En groupes, vous allez récupérer une appli qui n'a pas été maintenue depuis plusieurs années et qui a été réalisée initialement sans réelle prise en compte des bonnes pratiques.


* Installez et lancez le projet en l'état (si possible).


* Explorez la structure du code, identifiez les principales fonctionnalités et les premiers éléments qui semblent problématiques.


* En bref, faites un premier diagnostic basé sur vos premières observations sans autre outil que votre bon sens et votre sens de l'analyse.



---

2. MÉTRIQUES DE CODE ET MAINTENABILITÉ

Métriques de code

* Les métriques sont des mesures objectives du code qui permettent de quantifier sa qualité, de comparer des versions dans le temps et de prioriser les actions d'amélioration.



**Compléxité cyclomatique**

* La complexité cyclomatique est le nombre de chemins d'exécution indépendants dans une fonction.


* Ça permet de mesurer la complexité structurelle du code.


* Ça se mesure avec un score calculé ainsi : nbre de points de décision + 1.


* Une fonction avec un return a un score de 1 (pas de décision), une fonction avec un if a deux chemins possibles donc un point de décision donc un score de 2 etc.


* À noter qu'un `else` n'ajoute pas de point car la décision est prise avec le `if`.


* Par exemple, une fonction contenant un `if` et un `elseif` aura un score de 3 (1 de base + 1 pour le `if` + 1 pour le `elseif`).


* On va compter aussi les `catch` (chemin d'erreur), les boucles, les `case` d'un switch etc.


* Tout ce qui crée un chemin d'exécution supplémentaire ajoute 1 au score.


* Le score va avoir un impact sur les actions à mener, plus le score est élevé plus le refactoring sera recommandé pour conserver une maintenabilité acceptable.


* La complexité cyclomatique se calcule par fonction, donc le score baisse si on distribue la complexité en unités plus petites => on peut avoir le même nombre de chemins mais dans plusieurs fonctions avec chacune une responsabilité bien définie plutôt que tout dans une seule fonction.


* Le score indique également le nombre de tests minimum par fonction. Donc un score élevé reflète une fonction difficile à tester.


*
**Attention**, la complexité cyclomatique est un indicateur parmi d'autres et ne se suffit pas à elle-même ! On peut avoir un score faible et une fonction illisible qui ne respecte pas les conventions de nommage et qui est inutilement complexe même avec un seul chemin de sortie. Un score faible n'est pas un gage absolu de qualité à lui seul.



**Taille des classes et méthodes**

* On va analyser notre code également via la taille de ses classes et méthodes : Combien de lignes par méthode ? Combien de méthodes par classe ? Combien de paramètres par méthode ?


* Il n'y a pas de nombre exact précis à respecter mais si une classe est manifestement disproportionnée par rapport aux autres ou si une méthode a plus de 4 ou 5 paramètres par exemple, on doit se poser la question du refactoring pour améliorer la qualité.


* Le danger est d'avoir une "God Class", une classe qui fait tout et connait tout, fortement couplée, très longue etc.


* Généralement, si on a du mal à nommer sa classe, c'est qu'elle fait trop de choses.



**Couplage et cohésion**

* Le couplage est le fait pour une classe de dépendre d'autres classes.


* On réduit le couplage via l'injection de dépendances plutôt que l'utilisation directe d'autres classes.


* La cohésion mesure le fait que les méthodes d'une même classe constituent une unité logique et travaillent ensemble sur le même sujet.


* On vise une forte cohésion et un faible couplage pour avoir une architecture de qualité.



**Duplication de code**

* On analyse le code dupliqué car il est une cause majeure de régression (par exemple on a trois fois le même code, il évolue, on modifie une des occurrences et pas les 2 autres et ça casse).


* Et ça induit des coûts de maintenance plus importants.


* La duplication n'est pas seulement le code rigoureusement identique. Il s'agit également de copier-coller avec de légères variations.


* Encore plus dangereux, la duplication où le code est différent ! On a une logique similaire mais exprimée différemment.



**Taux de commentaires**

* Le code doit s'expliquer tout seul, sinon il n'est pas assez lisible.


* Les commentaires sont là pour expliquer les décisions pas forcément évidentes, ils doivent expliquer le pourquoi on a fait comme ça et pas le comment on a fait.


* On pense souvent au code pas suffisamment commenté mais le code sur-commenté est aussi problématique et contribue à la mauvaise maintenabilité.



SonarQube

* SonarQube est un outil d'analyse de code multi-langages et open source. Il est très utilisé et s'intègre facilement en CI/CD.


* Il est utilisé au fur et à mesure du développement via une intégration dans l'IDE et de manière plus globale via Sonar Scanner.


* On va tester ça en local !


* On crée un conteneur SonarQube avec la commande : `docker run --name sonarqube-custom -p 9000:9000 sonarqube:community`. L'identifiant et le mot de passe par défaut sont "admin".


* Avec VsCode, on peut installer l'extension "SonarQube for IDE" pour lier le projet au SonarQube Server qui tourne sur Docker. Ça permet une analyse au fur et à mesure qu'on code.


* Pour analyser le code dans sa globalité, on va avoir besoin de Sonar Scanner.


* Pour ça, on crée à la racine du projet un fichier `sonar-project.properties` qui permettra de définir la connexion au serveur et ce qu'on souhaite analyser :


*
`sonar.projectKey=ALICE-CRM` et `sonar.projectName=ALICE CRM` (version 1.0).


* Le dossier source principal se configure avec `sonar.sources=src`.


* L'URL du serveur s'indique avec `sonar.host.url=http://host.docker.internal:9000`.


* Le Token d'authentification s'indique avec `sonar.token=abcd1234` (on le génère sur le serveur dans *My account → Security*).




* À la racine du projet, on peut lancer l'analyse en conteneur via la commande : `docker run --rm -v "$(pwd):/usr/src" sonarsource/sonar-scanner-cli`.


* On obtient le rapport dans l'onglet Projets du serveur.



Cas Pratique

* Reprenez le projet de CRM et analysez le plus en détails. N'hésitez pas utiliser d'autres outils que SonarQube au besoin.


* Notez les endroits les plus critiques en vue de leur correction.



---

3. REFACTORING ET AMÉLIORATION DU CODE

Principes du refactoring

* Le refactoring est la transformation du code interne d'un programme pour améliorer sa structure, sans modifier son comportement observable.


*
**Règles fondamentales** :


* Progresser petit à petit, un changement à la fois et on teste immédiatement.


* Les tests doivent passer avant et après pour s'assurer qu'on n'a pas induit de régression.


* Lisibilité, lisibilité et lisibilité => si le code n'est pas plus compréhensible après, on a loupé sa refacto.


* Pas d'ajout de fonctionnalité, ce n'est pas l'objet du refactoring.




*
**Quand refactorer ?**


* Avant d'ajouter une fonctionnalité dans une zone complexe.


* Après avoir détecté un bug ou une anomalie.


* Lors d'une revue de code.


* En continu après chaque passage dans le code (on doit laisser le code plus propre que l'état dans lequel on l'a trouvé initialement).





Techniques de refactoring

*
**Extraction de méthode** :


* On découpe une méthode en plusieurs méthodes plus petites, mieux nommées, avec des responsabilités claires etc.


* La méthode principale injecte les méthodes ciblées et devient ainsi très simple à lire et plus maintenable.




*
**Simplification des conditions** :


* On remplace une ou plusieurs conditions par une méthode explicite. Par exemple, extraire une suite complexe de conditions `&&` vers une méthode séparée comme `isEligibleForAccess(User $user)`.


* On remplace des ternaires imbriqués par une méthode explicite (ex: au lieu d'enchaîner les opérateurs `? :` pour retourner une note, on crée une fonction avec plusieurs instructions `if` qui retournent immédiatement la valeur).




*
**Renommage** :


* On renomme pour exprimer l'intention réelle.


* Avant : `$d = $u->getD();` / Après : `$lastLoginDate = $user->getLastLoginDate();`.


* Avant : `function calc($a, $b)` / Après : `function calculatePriceWithTax(float $unitPrice, int $quantity): float`.




*
**Suppression des duplications** :


* Mutualiser des logiques similaires éparpillées. Par exemple, au lieu d'avoir un `InvoiceService` et un `QuoteService` qui contiennent chacun exactement la même méthode `getTaxRate`, on crée une classe unique `TaxService` qui gère ce taux d'imposition pour les deux.




*
**Découpage de classes** :


* Au lieu d'avoir une seule classe qui fait tout (ex: `UserManager` qui crée l'utilisateur, envoie un email, hache le mot de passe, gère les logs et exporte en CSV), on sépare les responsabilités en plusieurs classes spécialisées (ex: `UserCreationService`, `NotificationService`, `PasswordHasher`, `AuditLogger`, `UserExportService`).




*
**Injection de dépendances** :


* Au lieu de créer des dépendances en interne, créant un couplage fort (ex: instancier une nouvelle connexion base de données avec `new` directement dans la méthode d'un `ReportService`), on injecte les dépendances par le constructeur en utilisant des interfaces.





Cas Pratique

* Refactorer le CRM en documentant les changements et en commitant à chaque étape.


* Produisez l'analyse avant / après et commentez les résultats en fonction des métriques obtenues.


* Fournissez le code refactorisé (même partiellement).


* Justifiez vos changements (quels problèmes, quels changements quels bénéfices).