### Full Stack Software as a service

Full Stack software as a service with react 18, typescript, tailwindcss, inertia, Dokcer and Laravel 11

![My logo](public/img/2024-06-23_21-120.png)

## Demo

comming soon...

## Installation with docker

1.Clone the project

    git@github.com:Moyhe/Software_As_A_Service.git

2.Run composer install && npm install then

Navigate into project folder using terminal and run

    docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    composer install --ignore-platform-reqs

3.Copy .env.example into .env

    cp .env.example .env

4.Start the project in detached mode

    ./vendor/bin/sail up -d

5.start vite serever

    npm run dev

From now on whenever you want to run artisan command you should do this from the container.
Access to the docker container

    ./vendor/bin/sail bash

6.Set encryption key

    php artisan key:generate --ansi

7.Run migrations

    php artisan migrate

8.to seed data

    php artisan migrate --seed

## Features

1. Registration & Login
2. feature 1 and feature 2 for calculating differnce and sum of two numbers
3. Show dashboard with overview information
4. payment integration using stripe to buy credits
5. pagination for features
6. notification
