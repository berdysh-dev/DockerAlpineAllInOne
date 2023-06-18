PHP = php
USER = berdyshdev2
REPO = docker_alpine_all_in_one
TAG = latest
NAME = work

UID = berdyshdev2

all: kill rmi build run

build:
	docker build -f Dockerfile --tag=${TAG} --rm=true .

run2:
	docker run `${PHP} DockerUtil.php -ImageID ${TAG}`

run:
	docker run \
-v /usr/local/GIT/PgSQL:/var/lib/postgresql \
-v /usr/local/GIT/MySQL:/var/lib/mysql \
-v /usr/local/GIT/DockerHomePage/www:/var/www/html \
-p 9000:9000 \
-p 2222:2222 \
-p 16379:16379 \
`${PHP} DockerUtil.php -ImageID ${TAG}`

stop:
	docker stop `${PHP} DockerUtil.php -ImageID ${TAG}`

sh2:
	${PHP} DockerUtil.php -ContainerID ${TAG}

sh:
	@docker exec -it `${PHP} DockerUtil.php -ContainerID ${TAG}` sh

login:
	docker login -u=${UID}

logout:
	docker logout

con:
	${PHP} DockerUtil.php -ContainerID ${TAG}

im:
	${PHP} DockerUtil.php -ImageID ${TAG}

ps:
	${PHP} DockerUtil.php -Ps_A ${TAG}

commit:
	@echo docker container commit `${PHP} DockerUtil.php -ContainerID ${TAG}` ${USER}/${REPO}
	@docker container commit `${PHP} DockerUtil.php -ContainerID ${TAG}` ${USER}/${REPO}

tag:
	@echo docker tag `${PHP} DockerUtil.php -ImageID ${TAG}` ${USER}/${REPO}
	@docker tag `${PHP} DockerUtil.php -ImageID ${TAG}` ${USER}/${REPO}

push:
	docker push ${USER}/${REPO}

pull:
	docker pull ${USER}/${REPO}:latest

rmi:
	${PHP} DockerUtil.php -rmi

kill:
	${PHP} DockerUtil.php -kill


# Emulate Docker CLI using podman. Create /etc/containers/nodocker to quiet msg.

