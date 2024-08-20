#!/usr/bin/env bash
# Author: Reny Ramos <renyramosgarcia@gmail.com>
# Licence: GPL-2
# Description.

nombre=$(cat .env | grep COMPOSE_PROJECT_NAME | cut -d "=" -f 2)
#######################################
# FUNCTIONS
#######################################

projectName() {
    echo $nombre
}

getComposeProjectName() {
    echo $nombre
}

globalProjectName() {
    #$(projectName)
    echo $nombre
}

# Run an Artisan command
artisan() {
    docker exec $nombre-php-1 php artisan "${@:1}"
}

dbSeed() {
    docker exec $nombre-php-1 php artisan migrate:fresh --seed
}

tinker() {
    docker exec -it $nombre-php-1 php artisan tinker
}

php() {
    docker exec $nombre-php-1 php "${@:1}"
}

terminal() {
    docker exec $nombre-php-1 "${@:1}"
}

# Build all of the images or the specified one
build() {
    docker-compose build "${@:1}"
}

# Generate a new certificate
cert_generate() {
    rm -Rf .docker/nginx/certs/yukikaze.*
    docker-compose run --rm webserver sh -c "cat /etc/ssl/openssl.cnf > /etc/nginx/certs/openssl.cnf && echo \"\" >> openssl.cnf && echo \"[ SAN ]\" >> openssl.cnf && echo \"subjectAltName=DNS.1:yukikaze,DNS.2:*.yukikaze\" >> /etc/nginx/certs/openssl.cnf && openssl req -x509 -sha256 -nodes -newkey rsa:4096 -keyout yukikaze.key -out yukikaze.crt -days 3650 -subj \"/CN=*.yukikaze\" -config openssl.cnf -extensions SAN && rm /etc/nginx/certs/openssl.cnf"
}

# Install the certificate
cert_install() {
    if [[ "$OSTYPE" == "darwin"* ]]; then
        sudo security add-trusted-cert -d -r trustRoot -k /Library/Keychains/System.keychain .docker/nginx/certs/yukikaze.crt
    elif [[ "$OSTYPE" == "linux-gnu" ]]; then
        sudo ln -s "$(pwd)/.docker/nginx/certs/yukikaze.crt" /usr/local/share/ca-certificates/yukikaze.crt
        sudo update-ca-certificates
    else
        echo "Could not install the certificate on the host machine, please do it manually"
    fi

    docker-compose exec php update-ca-certificates
}

# Run a Composer command
bash() {
    docker exec $nombre-php-1 /bin/bash
}

composer() {
    docker exec $nombre-php-1 composer "${@:1}"
}

# Remove the entire Docker environment
destroy() {
    read -p "This will delete containers, volumes and images. Are you sure? [y/N]: " -r
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then exit; fi
    docker-compose down -v --rmi all --remove-orphans
}

# Stop and destroy all containers
down() {
    docker-compose down "${@:1}"
}

# Create .env from .env.example
env() {
    if [ ! -f .env ]; then
        cp .env.example .env
    fi
}

# Initialise the Docker environment and the application
init() {
    env &&
        down -v &&
        build &&
        docker-compose run --rm --entrypoint="//opt/files/init" php && 
        yarn install

    if [ ! -f .docker/nginx/certs/yukikaze.crt ]; then
        cert_generate
    fi

    start && cert_install
}

# Display and tail the logs of all containers or the specified one's
logs() {
    docker-compose logs -f "${@:1}"
}

# Restart the containers
restart() {
    stop && start
}

# Start the containers
start() {
    docker-compose up -d
}

# Stop the containers
stop() {
    docker-compose stop
}

# Update the Docker environment
update() {
    git pull &&
        build &&
        composer install &&
        artisan migrate &&
        yarn install &&
        start
}

# Run a Yarn command
yarn() {
    docker-compose run --rm frontend yarn "${@:1}"
}

#######################################
# MENU
#######################################

case "$1" in
artisan | art)
    artisan "${@:2}"
    ;;
tinker)
    tinker
    ;;
db:seed)
    dbSeed
    ;;
php)
    php "${@:2}"
    ;;
composer)
    composer "${@:2}"
    ;;
build)
    build "${@:2}"
    ;;
projectName)
    projectName
    ;;
globalProjectName)
    globalProjectName
    ;;
terminal)
   terminal "${@:2}"
   ;;
cert)
    case "$2" in
    generate)
        cert_generate
        ;;
    install)
        cert_install
        ;;
    *)
        cat <<EOF
Certificate management commands.
Usage:
    dock cert <command>
Available commands:
    generate .................................. Generate a new certificate
    install ................................... Install the certificate
EOF
        ;;
    esac
    ;;
composer)
    composer "${@:2}"
    ;;
destroy)
    destroy
    ;;
down)
    down "${@:2}"
    ;;
init)
    init
    ;;
logs)
    logs "${@:2}"
    ;;
restart)
    restart
    ;;
start)
    start
    ;;
stop)
    stop
    ;;
update)
    update
    ;;
yarn)
    yarn "${@:2}"
    ;;
*)
    cat <<EOF
Command line interface for the Docker-based web development environment Docky.
Usage:
    dock <command> [options] [arguments]
Available commands:
    artisan|art ............................... Run an Artisan command
    php ....................................... Run an php command
    build [image] ............................. Build all of the images or the specified one
    cert ...................................... Certificate management commands
        generate .............................. Generate a new certificate
        install ............................... Install the certificate
    composer .................................. Run a Composer command
    destroy ................................... Remove the entire Docker environment
    down [-v] ................................. Stop and destroy all containers
                                                Options:
                                                    -v .................... Destroy the volumes as well
    init ...................................... Initialise the Docker environment and the application
    logs [container] .......................... Display and tail the logs of all containers or the specified one's
    restart ................................... Restart the containers
    start ..................................... Start the containers
    stop ...................................... Stop the containers
    update .................................... Update the Docker environment
    yarn ...................................... Run a Yarn command
EOF
    exit
    ;;
esac

