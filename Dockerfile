FROM php:8.4-fpm

# Atualiza e instala dependências do sistema
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip git curl libpng-dev libjpeg-dev libfreetype6-dev libicu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql zip intl gd opcache bcmath calendar

# Instala o Xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Copia o código da aplicação
WORKDIR /var/www/html
COPY ./ /var/www/html

# Instala o Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Instala dependências do projeto
RUN composer install --no-dev --optimize-autoloader

# Ajusta permissões
RUN chown -R www-data:www-data /var/www/html

EXPOSE 9000
CMD ["php-fpm"]