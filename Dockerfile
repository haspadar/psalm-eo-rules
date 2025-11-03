FROM php:8.1-cli

RUN apt-get update && apt-get install -y \
    git unzip zip curl bash fish \
    libzip-dev libicu-dev zlib1g-dev libonig-dev tzdata \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install intl zip opcache bcmath mbstring

COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

ENV TZ=UTC
RUN ln -fs /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-interaction --prefer-dist --no-scripts --no-progress

COPY . .

ENTRYPOINT ["fish"]