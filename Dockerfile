FROM php:8.1-cli

ARG INSTALL_XDEBUG=false

RUN apt-get update && apt-get install -y \
    git unzip zip curl bash fish \
    libzip-dev libicu-dev zlib1g-dev libonig-dev tzdata \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install intl zip opcache bcmath mbstring

# Install Xdebug only if needed
RUN if [ "$INSTALL_XDEBUG" = "true" ]; then \
      pecl install xdebug && \
      docker-php-ext-enable xdebug && \
      echo "zend_extension=xdebug" > /usr/local/etc/php/conf.d/xdebug.ini; \
    fi

COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

ENV TZ=UTC
RUN ln -fs /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-interaction --prefer-dist --no-scripts --no-progress

COPY . .

ENTRYPOINT ["fish"]