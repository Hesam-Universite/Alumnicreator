# HESAM - ALUMNI CREATOR

Outil de communication performant pour toute entité souhaitant développer son réseau des anciens.

Le logiciel AlumniCreator est une application internet contenant un site vitrine (front end) ayant les fonctionnalités suivantes :

-  Gestion d’un annuaire d’anciens élèves
-  Système de communication comprenant:
  -  Lien avec les réseaux sociaux essentiellement Linkedin, Facebook et Twitter
  -  Système d’envoi de newsletter
  -  Messagerie interne
-  Espaces carrières : Cvthèque, offres d’emploi, dépôt de candidature, annuaire d’entreprise
-  Interconnexion de deux instances d’AlumniCreator (une instance correspond à une installation de l’application). L’objectif de l’interconnexion est de permettre la communication, le partage de données entre 2 installations soit une utilisée par une école A et une seconde par une école B par exemple.


## Table des matières

- [Stack](#stack)
- [Configuration du serveur](#configuration-du-serveur)
  - [Mise à jour du système](#mise--jour-du-systme)
  - [Installation d'apache2](#installation-dapache2)
  - [Installation de PHP 8.1](#installation-de-php-81)
  - [Installation de Composer](#installation-de-composer)
  - [Installation de MariaDB](#installation-de-mariadb)
  - [Installation de node et yarn](#installation-de-node-et-yarn)
- [Installation d'Alumni creator](#installation-dalumni-creator)
  - [Téléchargement](#tlchargement)
    - [En utilisant le clonage](#en-utilisant-le-clonage)
    - [En téléchargeant l'archive](#en-tlchargeant-larchive)
  - [Installation](#installation)
  - [Configuration](#configuration)
  - [Mise en place des tâches planifiées](#mise-en-place-des-tches-planifies)
- [Configuration d'apache2](#configuration-dapache2)
- [Mettre à jour Alumni Creator](#mettre--jour-alumni-creator)
  - [Si vous avez utiliser le clonage](#si-vous-avez-utiliser-le-clonage)
  - [Si vous avez télécharger l'archive](#si-vous-avez-tlcharger-larchive)
  - [Mettre a jour l'application](#mettre-a-jour-lapplication)
- [Configurer les authentifications sociale](#configurer-les-authentifications-sociale)
- [Régénerer le cache après des modifications de configuration](#rgnerer-le-cache-aprs-des-modifications-de-configuration)
- [Configurer la publication de posts Facebook (optionnel)](#configurer-la-publication-de-posts-facebook-optionnel)
  - [Création d'une application Facebook](#cration-dune-application-facebook)
  - [Configurer l'ID et le secret de l'application dans Alumni Creator](#configurer-lid-et-le-secret-de-lapplication-dans-alumni-creator)
  - [Passer l'application en mode live](#passer-lapplication-en-mode-live)
- [Crédits](#crédits)

## Stack

- php 8.1
- Symfony 5.4
- Mariadb 10.5
- node 16+
- yarn

## Configuration du serveur

Les instructions suivantes s'appliquent pour une Debian 11 (version "stable" au moment de l'écriture de ce guide).

### Mise à jour du système

Pour commencer, s'assurer que le système d'exploitation du serveur est à jour avec la commande suivante :

```
apt update && apt upgrade -y
```

### Installation d'apache2

Installer le serveur web apache2 :

```
apt install apache2 -y
service apache2 start
```

### Installation de PHP 8.1

Avant d'installer php, il faut installer quelques dépendances :

```
apt install ca-certificates apt-transport-https software-properties-common wget curl lsb-release vim -y
```

On va s'appuyer sur le dépôt "packages.sury.org" afin de récupérer une version de php récente :

```
curl -sSL https://packages.sury.org/php/README.txt | bash -x
apt update
```

Installation de php 8.1 et des paquets nécessaires :

```
apt install php8.1 libapache2-mod-php8.1 php8.1-intl php8.1-zip php8.1-gd php8.1-xsl php8.1-mysql php8.1-common php8.1-curl php8.1-bcmath php8.1-mbstring php8.1-xmlrpc php8.1-mcrypt php8.1-gd php8.1-xml php8.1-cli -y
service apache2 restart
```
### Installation de Composer

Composer est un gestionnaire de paquet php qui va nous aider à installer l'application Alumni Creator :

```
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"
mv composer.phar /usr/local/bin/composer
```

### Installation de MariaDB

Pour stocker les informations dans la base de données, installer MariaDB :

```
apt install mariadb-server mariadb-client -y
service mariadb start
```

Pour sécuriser au maximum l'installation, taper la commande suivante :

```
mysql_secure_installation

# Répondre oui aux questions suivantes :

Switch to unix_socket authentication?
Change the root password? => modifier le mot de passe de l'administrateur ;
Remove anonymous users? => supprimer les utilisateurs anonymes ;
Disallow root login remotely? => interdire les connexions distantes de l'administrateur ;
Remove test database and access to it? => supprimer la base de test et les droits associés ;
Reload privilege tables now? => recharger les privilèges pour prise en compte immédiate.
```
### Installation de node et yarn

La gestion des assets se fait grâce aux outils fourni par le gesetionnaire de paquet nodejs et la commande yarn. On va se servir du dépôt "nodesource" pour avoir une version récente :

```
curl -sL https://deb.nodesource.com/setup_16.x | bash -
apt install nodejs
npm install --global yarn
```

## Installation d'Alumni creator

### Téléchargement

Pour télécharger Alumni Creator, vous pouvez soit cloner le repository soit télécharger une archive. Si vous souhaitez effectuer des modifications, il est conseillé de forker le repository pour pouvoir faire vos modifications.

#### En utilisant le clonage

Il faut que git soit présent sur le système. Installer git avec la commande suivante :

```
apt install git -y
```

Puis cloner le repository :

```
cd /var/www
git clone https://github.com/Hesam-Universite/Alumnicreator.git
```

#### En téléchargeant l'archive

Si vous souhaitez télécharger l'archive de la dernière version d'Alumni Creator :

```
cd /var/www
wget https://github.com/Hesam-Universite/Alumnicreator/archive/refs/heads/main.zip
unzip main.zip
mv Alumnicreator-main Alumnicreator
rm main.zip
```

### Installation

Pour installer l'application, il faut installer toutes ses dépendances à l'aide des outils composer et yarn :

```
cd Alumnicreator
composer install --no-dev --optimize-autoloader
yarn
yarn encore prod
chmod -R 777 var/
```

### Configuration

Pour configurer l'application, il faut commencer par copier le fichier de configuration intial pour pouvoir entrer des valeurs et passer l'application en mode "production" :

```
cp .env .env.local
vim .env.local
```

Modifier la variable "X_INTERNAL_API_TOKEN" à la ligne 38 par une chaine de caractère de votre choix.

Ensuite, il faut créer un utilisateur dans MariaDB :

```
mysql -u root
MariaDB [(none)]> GRANT ALL PRIVILEGES ON nom_de_la_db.* TO 'nom_du_user'@'localhost' IDENTIFIED BY 'mot_de_passe';
MariaDB [(none)]> FLUSH PRIVILEGES;
MariaDB [(none)]> exit;
```

Puis il faut créer une base de données et la lier à l'application. Nous allons commencer par configurer le fichier de configuration de l'application :

```
vim .env.local
```

Dans ce fichier, il faut modifier la variable "DATABASE_URL" à la ligne 31 :

```
DATABASE_URL="mysql://app:!ChangeMe!@127.0.0.1:3306/app?serverVersion=8&charset=utf8mb4"

A modifier par :

DATABASE_URL="mysql://nom_du_user:mot_de_passe@127.0.0.1:3306/nom_de_la_db?serverVersion=mariadb-10.5.1&charset=utf8mb4"
```

Puis taper les commandes suivantes pour créer la base de données :

```
php bin/console doctrine:database:create
```

Installer le schéma et les options par défaut de l'application :

```
php bin/console doctrine:migrations:migrate

# Répondre "yes"
```

Enfin, générer le premier adminitrateur. Tout d'abord il faut "hasher" son mot de passe : 

```
php bin/console security:hash-password

# Taper le mot de passe souhaité (c'est normal qu'il n'y ait pas d'indicateurs visuels) puis appuyer sur entrée.
# Il faut récupérer la chaine de caractère "Password hash" qui ressemble à ça : $2y$13$4Jt82eW9/Ztb1aGnOO08auTC08XTFebFO9Z3IcrN9d28WA6jOUOSy
```

Puis insérer l'utilisateur en base de données :

```
mysql -u root
[MariaDB [(none)]> use nom_de_la_db;
[MariaDB [(nom_de_la_db)]> INSERT INTO `users` (`id`, `email`, `roles`, `password`, `status`, `name`, `firstname`, `phone`, `birthday`, `address`, `class`, `training`, `personal_link`, `activity_area_id`, `siret`, `company_name`, `role_in_the_company`, `company_address`, `google_id`, `profile_completed`, `linkedin_id`, `microsoft_id`, `is_verified`, `activity_area_other`, `is_approved`, `registration_date`, `picture_name`, `updated_at`, `postal_code`, `city`, `accepted_notifications`, `linkedin_link`, `receive_message_notifications_by_email`) VALUES (NULL, 'adresse_email@domaine.com', '[\"ROLE_SUPER_ADMIN\"]', 'mot_de_passe_hashe', NULL, 'nom_de_famille', 'prenom', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, '1', NULL, '1', NOW(), NULL, NOW(), NULL, NULL, '1', NULL, '1');
[MariaDB [(nom_de_la_db)]> exit;
```

### Mise en place des tâches planifiées

Afin d'assurer son fonctionnement Alumni Creator a besoin que certaines commandes soient lancées périodiquement.

Si ce n'est pas fait, installer cron sur votre serveur :

```
apt install cron -y
```

Pour les configurer, il faut ouvrir la crontab :

```
crontab -e
```

Ajouter les lignes suivantes :

```
# configurer la liaison et la récupération des informations entre les instances alumni creator (toutes les heures)
0 * * * * /var/www/Alumnicreator/bin/console cron:getdata

# supprimer les profils inactifs depuis 3 ans (une fois par jour)
27 7 * * * /var/www/Alumnicreator/bin/console cron:inactiveusers

# suppression des offres d'emploi publiées il y a plus de 90j (une fois par jour)
22 5 * * * /var/www/Alumnicreator/bin/console cron:job

# envoi des newsletter (toutes les minutes)
* * * * * /var/www/Alumnicreator/bin/console cron:newsletter
```

## Configuration d'apache2

Pour terminer, il faut créer un vhost pour configurer apache 2 afin qu'il serve l'application. Commencer par créer un fichier de configuration :

```
vim /etc/apache2/sites-available/alumni-creator.conf
```

Puis mettre les informations suivantes dedans (pensez à changer les lignes 2 et 3) :

```
<VirtualHost *:80>
    ServerName domain.com
    ServerAlias www.domain.com

    DocumentRoot /var/www/Alumnicreator/public
    <Directory /var/www/Alumnicreator/public>
        AllowOverride All
        Order Allow,Deny
        Allow from All
    </Directory>

    # uncomment the following lines if you install assets as symlinks
    # or run into problems when compiling LESS/Sass/CoffeeScript assets
    # <Directory /var/www/Alumnicreator>
    #     Options FollowSymlinks
    # </Directory>

    ErrorLog /var/log/apache2/Alumnicreator_error.log
    CustomLog /var/log/apache2/Alumnicreator_access.log combined
</VirtualHost>
```
Puis exécuter les commandes suivantes :

```
# Désactive le site par défaut :
a2dissite 000-default.conf

# Active le site Alumni Creator :
a2ensite alumni-creator.conf

# Active le mode rewrite :
a2enmod rewrite

# Redémarre apache :
service apache2 restart
```

Rendez-vous sur votre nom de domaine et la page d'accueil d'Alumni Creator devrait être disponible !

## Mettre à jour Alumni Creator

La mise à jour de l'application va dépendre de votre méthode d'installation.

### Si vous avez utiliser le clonage

Il suffit de se rendre dans le répertoire de l'installation et de charger les nouvelles données :

```
cd /var/www/Alumnicreator
git pull
```

### Si vous avez télécharger l'archive

Il faut tout d'abord récupérer la dernière version, puis extraire l'archive dans le dossier `/var/www/Alumnicreator`. Toutes vos éventuelles modifications seront écrasées. Si vous souhaitez modifier Alumnicreator, il est conseillé d'utiliser le clone ou le fork.

### Mettre a jour l'application

```
cd /var/www/Alumnicreator
composer install
php bin/console doctrine:migrations:migrate --no-interaction
yarn
yarn encore prod
chmod -R 777 var/
```

## Configurer les authentifications sociale

Alumni creator est configuré pour fonctionner avec trois authentifications sociales :

- Google
- Linkedin
- Microsoft

Pour qu'elle fonctionne, il faut créer une application oauth sur chacune de ces plateformes :

- Google : https://support.google.com/cloud/answer/6158849?hl=en
- Linkedin : https://developer.linkedin.com
- Microsoft : https://learn.microsoft.com/en-us/azure/active-directory/develop/quickstart-register-app

Après avoir créé les applications nécessaires, il faut récupérer les ID des applications et leur secret, afin de pouvoir modifier le fichier de configuration :

```
vim .env.local
```

Ajouter les lignes suivantes en fin de fichier :
```
GOOGLE_ID=id_de_l_application_creee
GOOGLE_SECRET=secret_de_l_application_creee
LINKEDIN_ID=id_de_l_application_creee
LINKEDIN_SECRET=secret_de_l_application_creee
MICROSOFT_ID=id_de_l_application_creee
MICROSOFT_SECRET=secret_de_l_application_creee
```

## Régénerer le cache après des modifications de configuration

Après avoir modifier le fichier `.env.local`, il arrive de devoir régénrer le cache de l'application afin que les modifications soient prises en compte. Pour cela, il faut se placer dans le répertoire de l'application et taper la commande suivante :

```
php bin/console cache:clear
```

## Configurer la publication de posts Facebook (optionnel) 

Alumni Creator peut vous permettre de programmer la publication de posts sur votre page Facebook depuis le backoffice (section "Posts Facebook" dans le menu). Pour cela, il faut commencer par créer une application Facebook.

### Création d'une application Facebook

Se rendre sur le lien suivant : https://developers.facebook.com/docs/development/create-an-app/?locale=fr_FR

Au moment de la création de l'application, choisir le type "Entreprise", puis ajouter le nom de l'application.
Une fois l'application créée, configurer le produit "Facebook Login for Business". Pour qu'Alumni Creator fonctionne, cliquer sur le lien "Basculer sur Facebook Login".

Dernière étape de la configuration, chercher le champ "URI de redirection OAuth valides" et entrer l'URL suivante : `https://votredomaine.fr/oauth/check`.

### Configurer l'ID et le secret de l'application dans Alumni Creator

Au niveau de la page de votre application Facebook, se rendre dans la section "paramètres > général". Vous trouverez ici l'identifiant de l'application et la clé secrète.

La configuration se fait dans le fichier `.env.local`. Ajouter à la fin de ce fichier les lignes suivantes :

```
OAUTH_FACEBOOK_ID=id_de_l_application
OAUTH_FACEBOOK_SECRET=secret_de_l_application
```

### Passer l'application en mode live

Comme cette authentification ne sert qu'à poster des posts sur votre page, et n'a pas vocation à servir d'authentification pour d'autres utilisateurs, vous n'avez théoriquement pas besoin de passer celle-ci en mode "Live".

Lorsque vous vous connecterez depuis le back office, Facebook vous précisera simplement que l'application n'a pas été validée par leur soins. Personne d'autre que vous ne verra ce message, et uniquement au moment de la connection de l'application Facebook a Alumni Creator la première fois.

## Crédits

Développé par 
- <img src="https://www.hesam.eu/themes/custom/hesam/images/hesam-logo.svg" height=100>
- <img src="https://ldiro.fr/wp-content/uploads/2021/08/logo-ldiro-transparent-header.png" height=100>

Soutenu par:
- <img src="https://www.enseignementsup-recherche.gouv.fr/sites/default/files/site_logo/2022-05/1_MESR_RVB.svg" height=100>
- Le projet « HESAM 2030 - Construisons nos Métiers ! » est lauréat du second appel à projets « Nouveaux cursus à l’université » du troisième Programme d’Investissements d’Avenir (PIA 3) - convention n°ANR – 18 - NCU – 0028.
- <img src="https://www.hesam.eu/sites/default/files/images/Logo-HESAM-2030-x-France-2030-Uni.png"> 

