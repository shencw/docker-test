FROM alpine

LABEL maintainer="wei"

# 更换镜像源
RUN sed -i 's/dl-cdn.alpinelinux.org/mirrors.aliyun.com/g' /etc/apk/repositories \
  && apk add  gcc g++ libc-dev  wget vim  openssl-dev make  linux-headers \
  && rm -rf /var/cache/apk/*

# 选择Redis版本
ARG REDIS_VERSION=5.0.9

COPY ./install_package/redis-${REDIS_VERSION}.tar.tgz /usr/src/redis-${REDIS_VERSION}.tar.tgz

# 安装Redis
RUN mkdir -p /usr/src/redis \
	&& mkdir -p /usr/src/redis/data \
	&& mkdir -p /usr/src/redis/conf \
	&& mkdir -p /var/log/redis \
	# && wget -O /usr/src/redis-${REDIS_VERSION}.tar.gz http://download.redis.io/releases/redis-${REDIS_VERSION}.tar.gz \
	&& tar -xzf /usr/src/redis-${REDIS_VERSION}.tar.gz -C /usr/src/redis \
	&& rm -rf /usr/src/redis/redis-${REDIS_VERSION}.tar.tgz \
	&& cd /usr/src/redis/redis-${REDIS_VERSION} \
	&& make \
	&& make PREFIX=/usr/local/redis install \
	&& ln -s /usr/local/redis/bin/*  /usr/local/bin/  && rm -rf /usr/src/redis/redis-${REDIS_VERSION}

# 挂载目录
VOLUME ["/usr/src/redis/conf", "/usr/src/redis/data"]

# 暴露端口号
EXPOSE 6379

# 启动Redis
CMD ["/usr/local/bin/redis-server","/usr/src/redis/conf/redis.conf"]


# docker run --name redis -d -p 6379:6379 -v ~/Documents/code/docker/redis/conf/:/usr/src/redis/conf  --privileged=true redis:5.0.9