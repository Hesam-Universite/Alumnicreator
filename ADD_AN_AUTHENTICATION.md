# Ajouter une authentification sociale à Alumni Creator

Si vous souhaitez ajouter une authentification à la plateforme, voici les étapes à suivre :

- [Installer la librairie du client](#installer-la-librairie-du-client)
- [Configurer le provider](#configurer-le-provider)
- [Utiliser le service client](#utiliser-le-service-client)
- [Modifier la base de données](#modifier-la-base-de-donnes)
- [Rediriger vers la nouvelle route créée](#rediriger-vers-la-nouvelle-route-cre)

## Installer la librairie du client

En se basant sur [ce tableau](https://github.com/knpuniversity/oauth2-client-bundle#step-1-download-the-client-library), installer la libraie correspondante au client que vous souhaitez installer *via* composer. Exemple pour la librairie Facebook :

```
composer require league/oauth2-facebook
```

Vous pouvez également [créer un client générique](https://github.com/knpuniversity/oauth2-client-bundle#configuring-a-generic-provider).

## Configurer le provider

Après avoir installé la librairie, il faut configurer le fichier `config/packages/knpu_oauth2_client.yaml` et rajouter les lignes suivantes :

```
knpu_oauth2_client:
    clients:
        ... # les autres clients
        # la clé "facebook" peut être n'importe quoi, cela
        # va créer un service: "knpu.oauth2.client.facebook"
        facebook:
            type: facebook
            client_id: '%env(FACEBOOK_ID)%'
            client_secret: '%env(FACEBOOK_SECRET)%'
            redirect_route: oauth_check
            redirect_params:
                service: facebook
            graph_api_version: v2.12
```

La liste éléments peut changer d'un provider à un autre. Par exemple, ici, la clé `graph_api_version` n'est nécessaire que pour ce provider. Vous pouvez retrouver la liste complète de toutes les configurations supportées [ici](https://github.com/knpuniversity/oauth2-client-bundle#configuration).

Il va falloir également modifier le fichier `.env.local` pour ajouter les deux variables d'environnement que l'on vient d'utiliser dans le fichier de configuration (`FACEBOOK_ID` et `FACEBOOK_SECRET`) :

```
# Ajouter les lignes suivantes au fichier .env.local

FACEBOOK_ID=id_de_l_application_oauth_facebook
FACEBOOK_SECRET=secret_de_l_application_oauth_facebook
```

## Utiliser le service client

Pour démarrer le processus d'authentification *via* OAuth, il faut créer une route et un controller qui redirige vers notre client (Facebook pour notre exemple). Pour cela, il faut modifier le fichier `src/Controller/SecurityController` et ajouter une nouvelle méthode :

```php
/**
 * Auth with Facebook
 *
 * @param ClientRegistry $clientRegistry
 * @return RedirectResponse
 */
#[Route(path: '/connect/facebook', name: 'facebook_connect')]
public function connectFacebook(ClientRegistry $clientRegistry): RedirectResponse
{
    /** @var FacebookClient $client */
    // la valeur du paramètre doit être la même que la clé créée dans le fichier config/packages/knpu_oauth2_client.yaml
    $client = $clientRegistry->getClient('facebook');
    
    // Ici, on peut choisir les scopes auxquels on veut accéder. Se référer à la configuration de la librairie pour en savoir plus
    return $client->redirect(['email']);
}
```

## Modifier la base de données

Nous allons stocker le token envoyé par le provider en base de données, dans la table `users`, dans une nouvelle colonne. Se connecter à la base mysql et exécuter les instructions suivantes :

```
mysql -u nom_du_user -p
[MariaDB [(none)]> use nom_de_la_db;

### Attention : la colonne doit s'appeler ainsi : clédufichierdeconfiguration_id
[MariaDB [(nom_de_la_db)]> ALTER TABLE `users` ADD `facebook_id` VARCHAR(255) DEFAULT NULL;

[MariaDB [(nom_de_la_db)]> exit;
```

## Rediriger vers la nouvelle route créée

Il ne reste plus qu'a modifier le fichier `templates/security/login.html.twig` et ajouter un bouton de connection qui redirige vers la route nouvelle créée :

```html
<!-- la valeur a mettre dans la fonction "path" est la même que celle déclarée dans l'attribut "name" au dessus de la fonction du controller -->
<a href="{{ path('facebook_connect') }}">
    Se connecter avec Facebook
</a>
```

Le bouton va rediriger l'utilisateur vers notre route et notre controller créés précédemment, qui va lui-même rediriger l'utilisateur vers la page d'authentification du client.

A son retour, l'utilisateur est redirigée vers la méthode `registerOAuth` du controller `SecurityController` (grâce à la ligne `redirect_route: oauth_check` que l'on a rajouté dans la configuration).\
Cette fonction est "interceptée" par un service spécial qui se trouve dans le fichier `src/Security/SocialAuthenticator.php`. C'est ce fichier qui se charge de récupérer les informations renvoyées par le client et d'authentifier l'utilisateur.
