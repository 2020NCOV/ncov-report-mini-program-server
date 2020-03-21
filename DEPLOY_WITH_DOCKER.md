# 通过 Docker 进行部署

本项目提供了 Dockerfile 供用户自主构建容器镜像并进行容器化部署。

## Dockerfile 解析

[Dockerfile](./Dockerfile) 见项目。本构建项目使用 Ubuntu 16.04 为基础镜像，并提供了一系列相关[配置文件](./docker_configs/)用于构建容器镜像，包括：

- [sources.list](./docker_configs/sources.list)：该文件为 apt-get 安装源配置文件，为加速国内用户镜像构建，使用阿里云安装源替换原始安装源，若为海外用户，可以删除 [Dockerfile 相关命令](./Dockerfile#L7)以避免安装源被替换。
- [site.conf](./docker_configs/site.conf)：该文件为 Apache 服务器配置文件，该文件将容器中的 `/var/www/html` 文件夹挂载到 80 端口，并赋予相关权限。
- [php.ini](./docker_configs/php.ini)：该文件为 PHP7 配置文件。
- [start.sh](./docker_configs/start.sh)：该文件为容器启动脚本，容器启动时会先打开 Apache2 服务器的 rewrite 模块，并在前台启动服务器并输出日志到前台以供调试。

镜像的构建过程为：

- 以 Ubuntu 16.04 作为基础镜像，使用 daocloud 镜像源以加速国内构建过程，海外用户可以使用 Docker Hub 官方源。
- 替换 apt-get 安装源为阿里云安装源，海外用户可以使用官方源。
- 安装相关依赖，主要是 Apache2 Web 服务器与 PHP7 及 MySQL 与 Curl 扩展。
- 拷贝 Apache 与 PHP 配置文件覆盖默认配置。
- 将当前文件拷贝到 `/app` 文件夹。
- 将 `public` 创建软链接到 `/var/www/html` 以便可以被服务器访问。
- 创建日志目录 `/app/src/runtime` 并赋予相关权限。
- 对镜像启动入口 `/app/docker_configs/start.sh` 赋予执行权限。
- 设定默认暴露端口为 80 端口，并设置启动入口。

## Docker 容器镜像构建

由于该项目已提供了完整的 Dockerfile 及构建所需的相关配置文件，故可直接通过命令行一键构建容器镜像，可通过命令：

`sudo docker build -t ncov-mini-server .`

来构建镜像，执行该命令后会在本地构建出名为 `ncov-mini-server` 的 Docker 容器镜像。

## Docker 镜像启动

本项目中的数据库及微信小程序相关配置，可以通过容器镜像启动时的环境变量直接置入，程序会通过环境变量读取[数据库配置](./src/application/database.php#L16)和[微信小程序配置](./src/application/config.php#L17)，本项目目前支持的环境变量包括：

```
DB_HOST: 数据库地址，默认为 127.0.0.1
DB_DB: 数据库名称，默认为 ncov
DB_USER: 数据库用户名，默认为 ncov
DB_PASS: 数据库密码，默认为 ncov
DB_PORT: 数据库端口，默认为 3306

WECHAT_APPID: 小程序的 AppID
WECHAT_SECRET: 小程序的 Secret
```

例如，在构建容器镜像后可以通过以下命令启动容器镜像：

`sudo docker run -d -p 8088:80 --env WECHAT_APPID=YOUR_WECHAT_APPID --env WECHAT_SECRET=YOUR_WECHAT_SECRET ncov-mini-server`

以上命令会使用传入的环境变量覆盖微信小程序相关配置，并使用默认数据库配置启动镜像，镜像将以守护进程方式启动在后台，并将端口映射到本地 8080 端口，则可以通过 8080 端口访问容器服务。
