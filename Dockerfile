FROM php:8.1-cli

# --- Build arguments ---
ARG INSTALL_XDEBUG=false

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    curl \
    bash \
    fish \
    libzip-dev \
    libicu-dev \
    zlib1g-dev \
    libonig-dev \
    tzdata \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install intl zip opcache bcmath mbstring

# Install Xdebug (optional)
RUN if [ "$INSTALL_XDEBUG" = "true" ]; then \
      pecl install xdebug && \
      docker-php-ext-enable xdebug; \
    fi

COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# Install Composer
COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

# Set timezone
ENV TZ=UTC
RUN ln -fs /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

WORKDIR /app

# Optimize layer caching
COPY composer.json composer.lock ./
RUN composer install --no-interaction --prefer-dist --no-scripts --no-progress

COPY . .

ENTRYPOINT ["fish"]