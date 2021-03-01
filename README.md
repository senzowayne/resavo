# RESAVO
- Follow @ActuSenzo -- #CodeWithLove #Resavo

## PRÉSENTATION

RESAVO est un projet Open-Source, l'idée est de créer un système de réservation "clef en main" en y apportant des fonctionnalités avec les dernières technologies.<br>
Nous essayerons de répondre à un large éventail de cas d'usage possible. Nous essayerons de créer une UI & UX avancée.<br>
J'ai fait le choix de créer les ISSUES en français pour faciliter la compréhension aux développeurs débutants.<br>
Nous essayerons de rendre ce projet le plus générique possible et personnalisable (du choix de la couleur du thème, au choix entre Paypal ou Stripe…).

Le projet sous sa forme actuelle a été fait avec une personnalisation spécifique. Vous trouverez sûrement du code à jeter/modifier.

![Screenshot](screenshots/resa.png)
__(Ancien design)__

## COMMENT CONTRIBUER ?

Rendez-vous dans l'onglet "ISSUE". C'est ici qu'on liste les tâches à effectuer.

Une fois que vous avez repéré une ISSUE, assignez-vous celle-ci.
Ensuite, vous devez "fork" le repository "resavo" en cliquant sur le bouton `Fork` sur Github.

Puis, vous clonez le nouveau repository :

```
git clone git@github.com:USERNAME/resavo.git
``` 

Et ajoutez le référent, une fois dans le dossier "resavo" :

```
cd resavo
git remote add upstream git://github.com/senzowayne/resavo.git
```

Vous êtes prêt pour créer votre branche portant le nom de l'ISSUE.

Vous effectuerez votre travail dessus. Après avoir commit 
(en suivant toujours la convention de nommage ```ISSUE-#22 : <la tâche réalisée au sens précis>```) et push votre travail, vous pouvez passer à l'étape suivante, qui est d'ouvrir une Pull Request. On imposera une convention en la nommant ainsi :

`ISSUE-#22 : <la tâche réalisée au sens large>`

> **Concentrez-vous vraiment sur la tâche choisie. Ne mélangez pas une correction que vous auriez envie de faire dans une tâche qui n'a rien à voir.
> Il est souvent facile de s'éparpiller…**

Si vous estimez que vous avez fini votre travail vous pouvez le labelliser en "Prêt pour relecture".
Les contributeurs pourront relire et proposer des corrections ou suggestions à apporter à votre travail.
Une fois celui-ci validé par au moins 2 contributeurs votre travail sera en mesure d'être fusionné dans la branche "master".
Vous pourrez ensuite retourner sur votre branche "master" en local et effectuer un `git pull`.

Pensez à supprimer vos branches au fur et à mesure pour ne pas finir avec d'innombrables branches inutiles.

## INSTALLATION
(__pré-requis docker__)

Exécutez d'abord ces commandes :

```
$ git clone git@github.com:USERNAME/resavo.git
$ cd resavo/
$ make start
```

Vous pouvez executer la commande suivante pour voir la liste des commandes disponibles
```
$ make
```
Le temps que le projet s'initialise tu peux aller boire un café ☕️  

Lien : http://127.0.0.1 ou http://localhost

Vous devriez à present voir la page d'accueil du projet, avec la possibilité de vous inscrire et de vous connecter.
Le chargement des fixtures nous aura créé quelques éléments pour commencer.

Créez ensuite un fichier `.env.local` avec comme variable d'environnement :

```
CLIENT_ID=
CLIENT_SECRET=
```

Ces variables contiendront les identifiants de vos systèmes de paiement.

### Paypal SANDBOX TEST

Créez votre SANDBOX (celle-ci vous permettra d'effectuer des faux paiements et avoir le réel comportement de l'application) :

Cf : https://developer.paypal.com/docs/api/overview/#create-sandbox-accounts

Vous pouvez dès à présent vous connecter avec un compte admin:

* Identifiant : `admin@resavo.fr`
* Mot de passe : `password`

## Technologies

* Symfony 5.2
* Api Platform Core
* Mercure
* Twig
* Vue Js
* ~~Jquery~~
* Bootstrap

## Credit

Merci à @Marlene78 pour la typo et félicitations pour sa première contribution.

Merci @Yanoucrea pour ses bonnes pratiques de conventions ;)

Merci à @Dev-Int pour ces belles contributions au projet.
