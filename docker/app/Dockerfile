FROM php:8.4-fpm-bookworm

ARG UID=1000
ARG GID=1000

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV PATH="/home/app/.composer/vendor/bin:${PATH}"

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    curl \
    libicu-dev \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    default-mysql-client \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" bcmath exif gd intl pcntl pdo_mysql zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY --from=node:22-bookworm-slim /usr/local/bin/node /usr/local/bin/node
COPY --from=node:22-bookworm-slim /usr/local/lib/node_modules /usr/local/lib/node_modules

RUN ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
    && ln -s /usr/local/lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx

RUN groupadd --gid "${GID}" app \
    && useradd --uid "${UID}" --gid app --create-home --shell /bin/bash app \
    && sed -ri 's/^user = .*/user = app/' /usr/local/etc/php-fpm.d/www.conf \
    && sed -ri 's/^group = .*/group = app/' /usr/local/etc/php-fpm.d/www.conf \
    && sed -ri 's|^listen\.owner = .*|listen.owner = app|' /usr/local/etc/php-fpm.d/www.conf \
    && sed -ri 's|^listen\.group = .*|listen.group = app|' /usr/local/etc/php-fpm.d/www.conf

WORKDIR /var/www/html

USER app

CMD ["php-fpm"]
