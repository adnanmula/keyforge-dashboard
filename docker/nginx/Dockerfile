FROM nginx:1.23.0-alpine as basic

RUN adduser -u 1000 -D -S -G www-data www-data

COPY ./docker/nginx/config/nginx.conf /etc/nginx/nginx.conf
COPY ./docker/nginx/config/default.conf /etc/nginx/conf.d/default.conf

FROM basic as production
COPY ./public /var/www/html
RUN chown -R www-data:www-data /var/www/html
