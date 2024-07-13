FROM node:20.13.1-alpine AS node
FROM php:8.2-fpm-alpine

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin
RUN install-php-extensions mysqli pdo_mysql zip intl opcache bcmath imagick @composer

COPY --from=node /usr/lib /usr/lib
COPY --from=node /usr/local/lib /usr/local/lib
COPY --from=node /usr/local/include /usr/local/include
COPY --from=node /usr/local/bin /usr/local/bin

RUN npm install -g yarn --force
RUN node -v
RUN yarn -v

ENTRYPOINT ["/bin/sh", "-c" , "\
composer install --prefer-dist --quiet \
&& ./artisan migrate --seed \
&& cd frontend/app \
&& yarn install \
&& php-fpm -F"]
