FROM php:8.5-fpm-alpine

ARG ENABLE_XDEBUG=false
ARG ELASTIC_APM_VERSION=1.17.0

RUN apk add --no-cache \
    oniguruma-dev \
    postgresql-dev \
    git \
    curl \
    linux-headers \
    $PHPIZE_DEPS \
    && docker-php-ext-install -j$(nproc) \
        bcmath \
        mbstring \
        pdo \
        pdo_pgsql \
        intl

# Optional Xdebug
RUN if [ "$ENABLE_XDEBUG" = "true" ]; then \
        pecl install xdebug \
        && docker-php-ext-enable xdebug; \
    fi

# Elastic APM PHP agent
RUN ARCH="$(uname -m)" \
    && case "${ARCH}" in \
        x86_64) APM_ARCH="x86_64" ;; \
        aarch64) APM_ARCH="aarch64" ;; \
        *) echo "Unsupported architecture: ${ARCH}" && exit 1 ;; \
    esac \
    && curl -fL \
        -o /tmp/elastic-apm-agent.apk \
        "https://github.com/elastic/apm-agent-php/releases/download/v${ELASTIC_APM_VERSION}/apm-agent-php-${ELASTIC_APM_VERSION}-r0.${APM_ARCH}.apk" \
    && apk add --allow-untrusted /tmp/elastic-apm-agent.apk \
    && rm /tmp/elastic-apm-agent.apk

RUN mkdir /.composer \
    && chown -R www-data:www-data /.composer

COPY config/php.ini /usr/local/etc/php/
COPY config/www.conf /usr/local/etc/php-fpm.d/www.conf

RUN curl -sS https://getcomposer.org/installer \
    | php -- --install-dir=/usr/bin --filename=composer

ENV PATH=/var/www/html/bin:/var/www/html/vendor/bin:$PATH