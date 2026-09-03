FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    libxml2-dev \
    unzip \
    git \
    && rm -rf /var/lib/apt/lists/*

# Configure & install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mysqli \
        gd \
        intl \
        zip \
        bcmath \
        opcache

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Configure PHP settings for QloApps
RUN echo "memory_limit = 256M" > /usr/local/etc/php/conf.d/qloapps.ini \
    && echo "upload_max_filesize = 64M" >> /usr/local/etc/php/conf.d/qloapps.ini \
    && echo "post_max_size = 64M" >> /usr/local/etc/php/conf.d/qloapps.ini \
    && echo "max_execution_time = 300" >> /usr/local/etc/php/conf.d/qloapps.ini \
    && echo "date.timezone = Asia/Ho_Chi_Minh" >> /usr/local/etc/php/conf.d/qloapps.ini

WORKDIR /var/www/html
