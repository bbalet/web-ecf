![GitHub CI](https://github.com/bbalet/web-ecf/actions/workflows/symfony.yml/badge.svg)
[![codecov](https://codecov.io/gh/bbalet/web-ecf/graph/badge.svg?token=JARJRZD07D)](https://codecov.io/gh/bbalet/web-ecf)
[![Maintainability](https://api.codeclimate.com/v1/badges/d319208984315580dace/maintainability)](https://codeclimate.com/github/bbalet/web-ecf/maintainability)
[![License: AGPL v3](https://img.shields.io/badge/License-AGPL_v3-blue.svg)](https://www.gnu.org/licenses/agpl-3.0)

# Cinephoria

**Cinephoria** est une application fictive de gestion de cinéma

## Prerequis

Cinephoria nécessite un serveur de basse de données MySQL (ou MariaDB) et un serveur MongoDB pour fonctionner.

## Installation

Executez les commandes suivantes :

    composer install --no-progress --prefer-dist --optimize-autoloader
    php bin/console secrets:generate-keys
    npm install
    npm run build

## Installation avec un jeu d'essai

    composer install
    php bin/console secrets:generate-keys
    php bin/console doctrine:schema:drop --full-database --force
    php bin/console doctrine:migrations:migrate --no-interaction
    php bin/console doctrine:fixtures:load --no-interaction

L'application est alors accessible avec les comptes suivants

| **Login**             | **Mot de passe**  |
|-----------------------|-------------------|
| admin@example.org     | admin             |
| employee@example.org  | employee          |
| visitor@example.org   | visitor           |
| visitor0@example.org  | visitor0          |
| visitor...            | ...               |
| visitor19@example.org | visitor19         |

## Charger des données dans 


## Librairies

https://github.com/chillerlan/php-qrcode
For the QRCode reader, either ext-gd or ext-imagick is required!

Pour une API key pour les posters:
https://codepen.io/pixelnik/pen/pgWQBZ
