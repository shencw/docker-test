FROM nginx

VOLUME ["/etc/nginx/conf.d", "/usr/share/nginx/html"]




# -p 第一个参数是宿主机端口，第二个参数容器端口

# docker run --name nginx1 -d -p 80:80 \
# 	-v ~/Documents/code/docker-test/code:/usr/share/nginx/html \
# 	-v ~/Documents/code/docker-test/nginx/conf:/etc/nginx/conf.d \
# 	--link swoole:php  --privileged=true nginx