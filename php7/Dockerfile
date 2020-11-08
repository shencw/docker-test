# https://github.com/docker-library/php/blob/7ccfc9272c3b5a5f100b38a75e91c99fdd2cb47a/7.3/buster/fpm/Dockerfile
FROM php:7.3-fpm

LABEL maintainer="wei"

VOLUME ["/var/www/html"]

ARG PHPREDIS_VERSION=5.1.1

# 设置镜像为网易
RUN mv /etc/apt/sources.list /etc/apt/sources.list.bak && \
		echo -e "deb http://mirrors.163.com/debian/ buster main contrib non-free \n# deb-src http://mirrors.163.com/debian/ buster main contrib non-free \ndeb http://mirrors.163.com/debian/ buster-updates main contrib non-free \n# deb-src http://mirrors.163.com/debian/ buster-updates main contrib non-free \ndeb http://mirrors.163.com/debian/ buster-backports main contrib non-free \n# deb-src http://mirrors.163.com/debian/ buster-backports main contrib non-free \ndeb http://mirrors.163.com/debian-security buster/updates main contrib non-free \n# deb-src http://mirrors.163.com/debian-security buster/updates main contrib non-free" > /etc/apt/sources.list

# install modules : GD iconv
RUN apt-get update && apt-get install -y \
		vim \
		iputils-ping \
        procps \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        openssl \
        libssh-dev \
        libpcre3 \
        libpcre3-dev \
        libnghttp2-dev \
        libhiredis-dev \
        curl \
        wget \
        zip \
        unzip \
        net-tools \
        libzstd-dev && \
        apt-get autoremove && apt-get clean

# install php pdo_mysql opcache
# WARNING: Disable opcache-cli if you run you php
# mbstring && json
COPY ./ext/redis-5.1.1.tgz /tmp/redis.tar.tgz
RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ && \
    docker-php-ext-install \
    iconv \
    gd \
    pdo_mysql \
    mysqli \
    iconv \
    mbstring \
    json \
    opcache \
    sockets \
    pcntl && \
    echo "opcache.enable_cli=0" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini && \
    pecl install /tmp/redis.tar.tgz && \
    rm -rf /tmp/redis.tar.tgz && \
    docker-php-ext-enable redis


COPY ./ext/libevent-2.1.8-stable.tar.gz /tmp/libevent.tar.tgz
COPY ./ext/event-2.5.7.tgz /tmp/event.tar.tgz

RUN tar zxvf /tmp/libevent.tar.gz && cd libevent-* && \
    ./configure --prefix=/usr/local/libevent && \
    make && make install && cd ../ && \
    tar zxvf /tmp/event.tar.tgz && cd event-* && \
    phpize && \
    ./configure --with-php-config=/usr/local/bin/php-config \
    --with-event-libevent-dir=/usr/local/libevent/ && \
    make && make install && \
    echo 'extension=event.so' > /usr/local/etc/php/conf.d/event.ini


COPY ./ext/inotify-2.0.0.tgz /tmp/inotify.tgz

RUN tar zxvf /tmp/inotify.tar && cd inotify-* && \
    phpize && ./configure --with-php-config=/usr/local/bin/php-config && \
    make && make install && \
    echo 'extension=inotify.so' > /usr/local/etc/php/conf.d/inotify.ini

COPY ./ext/swoole-v4.5.4.tar.gz /tmp/swoole.tar.gz

RUN tar zxvf /tmp/swoole.tar.gz && cd swoole-src* && \
    phpize && \
    ./configure \
    --enable-coroutine \
    --enable-openssl  \
    --enable-http2  \
    --enable-async-redis \
    --enable-mysqlnd && \
    make && make install && \
    docker-php-ext-enable swoole && \
    echo -e "swoole.fast_serialize=On\nswoole.use_shortname=On" >> /usr/local/etc/php/conf.d/docker-php-ext-swoole-serialize.ini && \
    rm -rf /tmp/*


# set China timezone
RUN /bin/cp /usr/share/zoneinfo/Asia/Shanghai /etc/localtime && \
    echo 'Asia/Shanghai' > /etc/timezone && \
    echo "[Date]\ndate.timezone=Asia/Shanghai" > /usr/local/etc/php/conf.d/timezone.ini


# docker build -t php7.3-swoole .
# 注意最后有个点，代表使用当前路径的 Dockerfile 进行构建 ，
# -t  php7.3-swoole  给新构建的镜像取名为 php7 并设定版本为swoole
# docker build： 用 Dockerfile 构建镜像的命令关键词。
# [OPTIONS] : 命令选项，常用的指令包括 -t 指定镜像的名字，
#     -f 显示指定构建镜像的 Dockerfile 文件（Dockerfile 可不在当前路径下），
#      如果不使用 -f，则默认将上下文路径下的名为 Dockerfile 的文件认为是构建镜像的 "Dockerfile" 。
# 上下文路径|URL： 指定构建镜像的上下文的路径，构建镜像的过程中，可以且只可以引用上下文中的任何文件 。


# docker run --name swoole -d  -v ~/Documents/code/docker-test/code:/var/www/html --privileged=true php7.3-swoole





