# Ubuntu 16.04
FROM daocloud.io/php:5.6.10-apache

MAINTAINER Frank Zhao <syzhao1988@126.com>

# COPY Apache 配置文件
COPY docker_configs/site.conf /etc/apache2/sites-enabled/

# 配置默认放置 App 的目录
RUN mkdir -p /app && rm -rf /var/www/html
COPY . /app
WORKDIR /app
# 链接 public 目录、创建 Log 目录并赋予权限
RUN ln -s /app/src/public /var/www/html && mkdir ./src/runtime && chmod -R 777 ./src/runtime && chmod -R 777 ./src/public && chmod 755 ./docker_configs/start.sh

EXPOSE 80
CMD ["./docker_configs/start.sh"]
