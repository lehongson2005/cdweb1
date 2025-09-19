FROM php:8.3.9-apache

# Khai báo version Composer
ENV COMPOSER_VERSION=2.7.7
ENV APACHE_DOCUMENT_ROOT=/var/www/html

# Cài đặt các gói cần thiết + PHP extensions
RUN apt-get update \
    && apt-get install -y \
        curl \
        zip \
        unzip \
        git \
        supervisor \
        libpng-dev \
        libpq-dev \
        libzip-dev \
        libexif-dev \
        gnupg \
        tzdata \
    && docker-php-ext-install \
        bcmath \
        gd \
        opcache \
        sockets \
        zip \
        exif \
        pdo_mysql \
        mysqli \
    && docker-php-ext-enable pdo_mysql mysqli

# Cài Xdebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Cấu hình DocumentRoot Apache
RUN sed -ri -e "s!/var/www/html!${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/sites-available/*.conf \
    && sed -ri -e "s!/var/www/!${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Cho phép .htaccess và Require all granted
RUN echo "<Directory ${APACHE_DOCUMENT_ROOT}>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>" > /etc/apache2/conf-available/z-allowoverride.conf \
    && a2enconf z-allowoverride

# Cài Composer với version chỉ định
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer --version=$COMPOSER_VERSION

# Bật mod_rewrite
RUN a2enmod rewrite

# Thêm user "app" tránh lỗi permission khi mount volume
RUN useradd -ms /bin/bash -u 1000 app

# Copy script start server
COPY start.sh /usr/local/bin/start
RUN chmod +x /usr/local/bin/start

# Phân quyền cho thư mục web
RUN chown -R www-data:www-data ${APACHE_DOCUMENT_ROOT} \
    && chmod -R 755 ${APACHE_DOCUMENT_ROOT}

# Expose cổng 80
EXPOSE 80

# Start Apache qua script
CMD ["start"]
