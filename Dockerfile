# Image dasar FrankenPHP (PHP 8.3 + Caddy)
FROM dunglas/frankenphp:php8.3

ENV SERVER_NAME=":80"
# Lokasi proyek di dalam container
WORKDIR /app

# Sistem deps & ekstensi PHP yang umum dipakai Laravel/Filament
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) intl gd zip pdo_mysql \
    && docker-php-ext-enable intl gd zip pdo_mysql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer (buat install deps dari dalam container)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install Node.js 20.x untuk build Vite assets
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Copy konfigurasi Caddy/FrankenPHP
COPY Caddyfile /etc/caddy/Caddyfile

# Copy entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80
EXPOSE 443
EXPOSE 443/udp

# Set permission direktori Laravel
RUN mkdir -p /app/storage /app/bootstrap/cache \
    && chown -R www-data:www-data /app/storage /app/bootstrap/cache

ENTRYPOINT ["docker-entrypoint.sh"]
