#!/usr/bin/env bash

if [[ ! -f dist-docker.sh ]]; then
    echo "The script must be called from the same directory where it is located."
    exit 1
fi

mkdir -p dist
rm -Rf dist/*

git checkout-index -a -f --prefix=./dist/build/
if [[ -f backend/.env ]]; then
    # database connection parameters are required to generate the doctrine proxy classes
    cp backend/.env dist/build/backend/.env
fi

docker-compose exec neucore_php sh -c "cd ../dist/build/backend && composer install --no-dev --optimize-autoloader --no-interaction"
docker-compose exec neucore_php sh -c "cd ../dist/build/backend && bin/doctrine orm:generate-proxies"
docker-compose exec neucore_php sh -c "cd ../dist/build/backend && composer openapi"

docker-compose run neucore_java /app/dist/build/frontend/openapi.sh
docker-compose run neucore_node sh -c "cd ../dist/build/frontend/neucore-js-client && npm install"
docker-compose run neucore_node sh -c "cd ../dist/build/frontend/neucore-js-client && npm run build"
docker-compose run neucore_node sh -c "cd ../dist/build/frontend && npm install"
docker-compose run neucore_node sh -c "cd ../dist/build/frontend && npm run build"

cd dist/build || exit
./dist-collect-files.sh

if [[ "$1" ]]; then
    NAME=$1
else
    NAME=$(git rev-parse --short HEAD)
fi
cd .. || exit
tar -czf neucore-"${NAME}".tar.gz neucore
sha256sum neucore-"${NAME}".tar.gz > neucore-"${NAME}".sha256

rm -Rf build
rm -Rf neucore
