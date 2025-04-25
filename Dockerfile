# 使用 PHP 8.2 的 Apache 镜像
FROM php:8.2-apache

# 拷贝当前目录所有文件到 Web 根目录
COPY . /var/www/html/

# 可选：开启 URL 重写
RUN a2enmod rewrite
