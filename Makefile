# Variables
DOCKER_COMPOSE = docker-compose --env-file .env
APP_CONTAINER = traveseros
DB_CONTAINER = traveseros_db
UID=$(shell id -u)
GID=$(shell id -g)

# Levantar los servicios (compila las imágenes si es necesario)
start:
	$(DOCKER_COMPOSE) up -d --remove-orphans

# Detener los servicios
down:
	$(DOCKER_COMPOSE) down

# Ver los logs de los servicios
logs:
	$(DOCKER_COMPOSE) logs -f

# Reconstruir las imágenes
build:
	$(DOCKER_COMPOSE) build

# Instalar dependencias de Symfony
install-deps:
	$(DOCKER_COMPOSE) exec $(APP_CONTAINER) composer install

# Ejecutar migraciones de la base de datos
migrate:
	$(DOCKER_COMPOSE) exec $(APP_CONTAINER) php bin/console doctrine:migrations:migrate --no-interaction

# Ejecutar comandos dentro del contenedor de Symfony (ej: make exec cmd="php bin/console cache:clear")
bash:
	$(DOCKER_COMPOSE) exec -u ${UID}:${GID} $(APP_CONTAINER) sh

docker-clean:
	docker-compose down --volumes --remove-orphans

# Mostrar ayuda
help:
	@echo "Comandos disponibles:"
	@echo "  make up                Levantar los servicios en segundo plano."
	@echo "  make down              Detener los servicios."
	@echo "  make logs              Ver logs de los servicios."
	@echo "  make build             Reconstruir las imágenes."
	@echo "  make install-deps      Instalar dependencias de Symfony."
	@echo "  make migrate           Ejecutar migraciones de la base de datos."
	@echo "  make exec cmd='<cmd>'  Ejecutar un comando dentro del contenedor de Symfony."
