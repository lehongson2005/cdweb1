FROM php:8.1-apache

# Cập nhật và cài extension cần thiết
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    git \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mysqli pdo pdo_mysql

# Cài extension Redis
RUN pecl install redis \
    && docker-php-ext-enable redis

# Bật mod_rewrite của Apache (quan trọng nếu bạn có .htaccess)
RUN a2enmod rewrite

# Đặt thư mục làm việc
WORKDIR /var/www/html

# Copy code vào container (không cần nếu bạn mount volume từ docker-compose)
# COPY ./11-training-php /var/www/html

# Expose port 80 (sẽ map sang 78 bên ngoài ở docker-compose)
EXPOSE 80
