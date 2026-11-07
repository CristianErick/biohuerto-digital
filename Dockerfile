# syntax=docker/dockerfile:1

# PHP 8.4 + Caddy (FrankenPHP). Vercel detecta este Dockerfile y lo ejecuta
# como Container Function. También sirve para Railway, Render o Fly.io.
FROM dunglas/frankenphp:1-php8.4-alpine

# Extensiones necesarias para MySQL, archivos e imágenes
RUN install-php-extensions pdo_mysql gd fileinfo intl zip opcache

# Configuración de PHP (auto_prepend para sesiones compartidas)
COPY php.ini /usr/local/etc/php/conf.d/zz-semillitas.ini

# Servidor web
COPY Caddyfile /etc/caddy/Caddyfile

WORKDIR /app

# Código de la aplicación
COPY . /app

# Carpetas de subida (solo se usan con STORAGE_DRIVER=local)
RUN mkdir -p /app/uploads/plantas /app/uploads/qrs /app/uploads/archivos /app/repositorio/uploads \
    && chown -R www-data:www-data /app/uploads /app/repositorio/uploads

ENV PORT=80
EXPOSE 80

CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
