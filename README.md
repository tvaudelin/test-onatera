# Introduction

J'ai utilisé comme point de départ Symfony docker (https://github.com/dunglas/symfony-docker).
J'ai ensuite ajouté un container database pour tourner sur MySQL et ai appliqué la configuration requise pour que docker tourne normalement.


## Comment lancer le projet

1. Run `docker-compose build --pull --no-cache` to build fresh images
2. Run `docker-compose up` (the logs will be displayed in the current shell)
3. Open `https://localhost` in your favorite web browser and [accept the auto-generated TLS certificate](https://stackoverflow.com/a/15076602/1352334)
4. Run `docker-compose down --remove-orphans` to stop the Docker containers.
