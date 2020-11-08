#!/bin/bash

echo -e ""
echo -e " 0: 启动Docker服务"
echo -e " 1: 展示Docker容器"
echo -e " 2: 启动Docker容器"
echo -e " 3: 关闭Docker容器"
echo -e " 4: 重启Docker容器"
echo -e " 5: 进入Docker容器"
echo -e ""

# shellcheck disable=SC2162
read -p "选择启动类型 :" idx

echo -e ""

show_docker_container() {
  echo -e "\033[33m====> 展示Docker容器列表\033[0m"
  docker ps -a
}

if [[ '0' == "$idx" ]]; then
  open /Applications/Docker.app

  echo -e "\n\033[32m====> 启动成功 \033[0m"
elif [[ '1' == "$idx" ]]; then
  show_docker_container
elif [[ '2' == "$idx" ]]; then
  show_docker_container

  echo -e "\n\033[32m====> 开始启动Docker容器 \033[0m"
  docker ps -a | awk '{if(NR>1)for(i=NF;i==NF;i++){print $i}}' | sort -r | xargs docker start
  echo -e "\033[32m====> 启动成功 \033[0m \n"

  show_docker_container
elif [[ '3' == "$idx" ]]; then
  show_docker_container

  echo -e "\n\033[31m====> 开始关闭Docker容器 \033[0m"
  docker ps -a | awk '{if(NR>1)for(i=NF;i==NF;i++){print $i}}' | xargs docker stop
  echo -e "\033[31m====> 关闭完成 \033[0m \n"

  show_docker_container
elif [[ '4' == "$idx" ]]; then
  show_docker_container

  echo -e "\n\033[35m====> 开始重启Docker容器 \033[0m"
  docker ps -a | awk '{if(NR>1)for(i=NF;i==NF;i++){print $i}}' | sort -r  | xargs docker restart
  echo -e "\033[35m====> 重启完成 \033[0m \n"

  show_docker_container
elif [[ '5' == "$idx" ]]; then
  echo -e "\033[33m====> 展示Docker容器列表\033[0m"
  docker ps -a | awk '{if(NR>1)for(i=NF;i==NF;i++)if((NR-1)%5!=0){printf("%s\t", $i)}else{printf("%s\n", $i)}}'
  echo -e ""
  # shellcheck disable=SC2162
  read -p "选择容器类型 :" container
  docker exec -it "${container}" /bin/bash
fi
