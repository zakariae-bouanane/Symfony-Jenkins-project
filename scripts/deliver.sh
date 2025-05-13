#!/usr/bin/env bash

# Display the process
echo 'The following Composer command installs the Symfony project dependencies.'
set -x
composer install --working-dir=code
set +x

echo 'The following command clears the Symfony cache.'
set -x
php code/bin/console cache:clear --env=prod
set +x

echo 'The following command runs Symfony migrations (if any) for the database.'
set -x
php code/bin/console doctrine:migrations:migrate --no-interaction
set +x

# Docker build process (adjust paths and tags based on your actual Symfony setup)
echo 'Building the Docker image for Symfony project.'
set -x
docker build -t symfony-app:v1.${BUILD_NUMBER} -f infra/php/Dockerfile code/
set +x

echo 'Tagging the Docker image for DockerHub.'
DOCKER_USERNAME=your_dockerhub_username
set -x
docker tag symfony-app:v1.${BUILD_NUMBER} ${DOCKER_USERNAME}/symfony-app:v1.${BUILD_NUMBER}
set +x

echo 'Pushing the Docker image to DockerHub.'
set -x
docker push ${DOCKER_USERNAME}/symfony-app:v1.${BUILD_NUMBER}
set +x

echo 'Docker image has been successfully pushed to DockerHub.'

# Optionally, running the Symfony application using Docker or directly in the container
echo 'Running the Symfony application in Docker container (or locally if required).'
set -x
docker run -d -p 80:80 ${DOCKER_USERNAME}/symfony-app:v1.${BUILD_NUMBER}
set +x
