# Используем PHP 8.3 FPM для обработки PHP файлов
FROM php:8.3-fpm

# Установка необходимых пакетов
RUN apt-get update && apt-get install -yq --no-install-recommends \
    software-properties-common \
    mc \
    git \
    sox \
    zip \
    curl \
    wget \
    unzip \
    catdoc \
    mp3val \
    xlsx2csv \
    redis-server \
    imagemagick \
    iputils-ping \
    build-essential \
    libgd3 \
    libpq-dev \
    libicu-dev \
    libpng-dev \
    libjpeg-dev \
    libzip-dev \
    zlib1g-dev \
    libonig-dev \
    libxml2-dev \
    libldap2-dev \
    libmcrypt-dev \
    libfreetype6-dev \
    libdebhelper-perl \
    libarchive-zip-perl \
    libarchive-cpio-perl \
    libsub-override-perl \
    libcurl4-openssl-dev \
    libfile-stripnondeterminism-perl \
    libz-dev \
    libssl-dev \
    libprotobuf-dev \
    protobuf-compiler \
    pkg-config \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Установка и настройка расширений PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_pgsql zip xml curl intl ldap pcntl bcmath mbstring

# Установка расширений через PECL
RUN pecl install mcrypt xdebug redis \
    && docker-php-ext-enable xdebug opcache redis

# Установка Composer (официальная инструкция)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Добавление конфигурации PHP-FPM
ADD zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf

WORKDIR /var/www