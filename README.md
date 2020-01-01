# RESAVO

## PRÉSENTATION

RESAVO est un projet open source, l'idée est de créer un système de réservation "clef en main" en y apportant des fonctionnalitées avec les dernières technologies. Nous essayerons de répondre à un large eventaille de cas d'usage possible. Nous essayerons de créer un UI & UX avancée. J'ai fais le choix de créer les ISSUE en francais pour faciliter aux developpeurs qui débutent de contribuer facilement. Nous essayerons de rendre ce projet le plus générique possible et personnalisable ( du choix de la couleur du thème, au choix entre Paypal ou Stripe... )

## INSTALLATION

### Paypal SANDBOX TEST

Crée votre SANDBOX (celle-ci vous permettra d'effectuer des faux paiements et avoir le réel comportement de l'application) :

Cf : https://developer.paypal.com/docs/api/overview/#create-sandbox-accounts

Crée ensuite un fichier .env.local avec comme variable d'environnement:

```
PAYPAL_CLIENT_ID=
PAYPAL_CLIENT_SECRET=
```

Après avoir configuré le .env.local du projet :

```
$ git clone git@github.com:senzowayne/resavo.git
$ cd resavo/
$ composer install
$ php bin/console doctrine:database:create
$ php bin/console doctrine:schema:update -f
$ php bin/console doctrine:fixtures:load
$ php bin/console server:run
```
Lien : http:127.0.0.1:8000/

Vous devriez à present voir la page d'accueil du projet avec la possibilité de vous inscrire et de vous connecter
le chargement des fixtures nous aura crée quelques élèments pour commencer.

Vous pouvez dès présent vous connecter avec un compte admin:


Identifiant : admin@resavo.fr

mdp: password

## Technologie

* Symfony 4.4
* Twig
* Javascript
* Jquery
* Bootstrap
