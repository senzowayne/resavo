# RESAVO

## INSTALLATION

### Paypal SANDBOX TEST

Crée votre SANDBOX de test :

https://developer.paypal.com/docs/api/overview/#create-sandbox-accounts

Crée ensuite un fichier .env.local avec comme variable d'environnement:

```
PAYPAL_CLIENT_ID=
PAYPAL_CLIENT_SECRET=
```

Après avoir configuré le .env.local du projet :

```
$ git clone xxx
$ cd xxx/
$ composer install
$ php bin/console doctrine:database:create
$ php bin/console doctrine:migration:migrate
$ php bin/console server:run
```

## Technologie

* Symfony 4.4
* Twig
* Javascript
* Jquery
* Bootstrap
