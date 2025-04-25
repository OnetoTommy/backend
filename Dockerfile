FROM php:8.2-apache

# 安装依赖项
RUN docker-php-ext-install pdo pdo_mysql

# 拷贝网站代码到容器
COPY . /var/www/html/

# 启用 Apache rewrite 模块（如果你需要）
RUN a2enmod rewrite

# 设置工作目录
WORKDIR /var/www/html
