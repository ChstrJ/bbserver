FROM richarvey/nginx-php-fpm:3.0.0

WORKDIR /var/www/html

# INSTALL DEPENDENCIES
RUN apk update && apk add --no-cache \
    git \
    libpng-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo_mysql gd

# INSTALL COMPOSER
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY composer.json composer.lock ./
COPY package-lock.json ./
COPY laravel.conf /etc/nginx/sites-available/default

RUN composer install --no-scripts --no-autoloader

COPY . .

RUN composer dump-autoload

RUN echo "generating application key..."
RUN php artisan key:generate --show

RUN echo "Caching config..."
RUN php artisan config:cache

RUN echo "Caching routes..."
RUN php artisan route:cache

RUN ln -s /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
