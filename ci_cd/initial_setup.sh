#!/bin/bash
#
set -e


# Make config file and get link
if [[ ! -f "server_config/variables.rb" ]]; then
    cp server_config/variables.example.rb server_config/variables.rb
fi

# Config files
CONFIG_FILES=(
    "src/app/Config/database.php"
    "src/app/Config/bootstrap.php"
    )

for CONFIG_FILE in ${CONFIG_FILES[@]}; do
    erb -r ./server_config/variables $CONFIG_FILE.erb > $CONFIG_FILE
done


# Initial setup all environment
if [ ! -f src/app/composer.phar ] && [ -f src/app/composer.json ]; then
    cd src/app;
    curl -sS https://getcomposer.org/installer | php
    cd ../..;
fi

if [ -f src/app/composer.phar ] && [ -f src/app/composer.json ]; then
    (cd src/app; php composer.phar install --optimize-autoloader; cd ../..)
fi

# Fix pagination

# tmp file permission
if [ ! -d src/app/tmp ]; then
    mkdir -p src/app/tmp;
fi

chmod -R 777 src/app/tmp;