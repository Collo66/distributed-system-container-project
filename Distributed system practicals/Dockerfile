FROM php:8.2-apache

RUN apt-get update && apt-get install -y msmtp-mta \
    && rm -rf /var/lib/apt/lists/* \
    && printf 'defaults\nauth off\ntls off\naccount default\nhost mail\nport 1025\nfrom noreply@localhost\n' > /etc/msmtprc \
    && chmod 644 /etc/msmtprc \
    && echo 'sendmail_path = "/usr/bin/msmtp -t"' > /usr/local/etc/php/conf.d/mail.ini \
    && docker-php-ext-install pdo pdo_mysql

COPY . /var/www/html/

EXPOSE 80