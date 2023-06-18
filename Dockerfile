FROM alpine:latest

MAINTAINER 0.1 http://berdysh.net/

RUN adduser -u 1200 mysql ; exit 0
RUN adduser -u 1201 postgres ; exit 0
RUN adduser -u 1202 redis ; exit 0
RUN adduser -u 1204 nginx ; exit 0
RUN adduser -u 1205 apache ; exit 0
RUN adduser -u 1206 sshd ; exit 0

RUN apk update
RUN apk add git bind-tools bash tcsh busybox busybox-extras build-base linux-headers alpine-sdk
RUN apk add rsyslog
RUN apk add nginx
RUN apk add libcurl curl
RUN apk add grpc protobuf
RUN apk add php81 php81-fpm
RUN apk add php81-pear php81-dev php81-pecl-xdebug
RUN apk add php81-openssl php81-sockets php81-curl
RUN apk add php81-pecl-igbinary php81-pecl-yaml php81-pecl-uuid
RUN apk add php81-pdo php81-pdo_mysql php81-pdo_pgsql php81-pdo_sqlite 
RUN apk add php81-pecl-redis php81-pecl-memcache php81-pecl-memcached
RUN apk add php81-pecl-ssh2 php81-gd php81-ldap
RUN apk add php81-pecl-protobuf 
RUN apk add libmaxminddb libmaxminddb-dev php81-pecl-maxminddb
RUN apk add redis
RUN apk add mariadb mariadb-client mariadb-common mariadb-connector-c-dev mysql-dev mysql-client
RUN apk add postgresql

# RUN pecl install grpc
ADD Alpine/php_modules.tar.gz /usr/lib/php81/modules/
RUN echo extension=grpc > /etc/php81/conf.d/grpc.ini

COPY php.ini /etc/php81/php.ini
COPY www.conf /etc/php81/php-fpm.d/www.conf
COPY php-fpm.conf /etc/php81/php-fpm.conf

RUN mkdir /var/www/html
COPY www/* /var/www/html

RUN apk add openssh
COPY ssh_config sshd_config /etc/ssh

COPY redis.conf /etc/redis.conf

COPY rsyslog.conf /etc/rsyslog.conf

COPY entry.sh /usr/local/bin/entry.sh

# /var/lib/mysql

CMD ["sh","/usr/local/bin/entry.sh"]

