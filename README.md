[![GitHub CI](https://github.com/bbalet/web-ecf/actions/workflows/symfony.yml/badge.svg)]
[![codecov](https://codecov.io/gh/bbalet/web-ecf/graph/badge.svg?token=JARJRZD07D)](https://codecov.io/gh/bbalet/web-ecf)
[![Maintainability](https://api.codeclimate.com/v1/badges/d319208984315580dace/maintainability)](https://codeclimate.com/github/bbalet/web-ecf/maintainability)

# Cinephoria

**Cinephoria** est une application fictive de gestion de cinéma

## Installation

    composer install --no-progress --prefer-dist --optimize-autoloader
    npm install
    npm run build

## Installation avec jeu d'essai

    php bin/console secrets:generate-keys
    php bin/console doctrine:schema:drop --full-database --force
    php bin/console doctrine:migrations:migrate --no-interaction
    php bin/console doctrine:fixtures:load --no-interaction
    composer install

L'application est accessible avec les comptes suivants

admin@example.org   admin
employee@example.org   employee
visitor@example.org   visitor
et 10 comptes visitor (i.e. visitor1, visitor2....)

https://github.com/chillerlan/php-qrcode
For the QRCode reader, either ext-gd or ext-imagick is required!

Pour une API key pour les posters:
https://codepen.io/pixelnik/pen/pgWQBZ
