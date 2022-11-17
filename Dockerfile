FROM php:8.1-cli-alpine3.15

LABEL maintainer="Adly Shadowbane <adly.shadowbane@gmail.com>"

ARG VERSION=unreleased

# Install composer
RUN php -r "readfile('https://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer

RUN mkdir /opt/cloudflare-dyndns

# Copy all directory to /opt
COPY . /opt/cloudflare-dyndns

# Build the app
RUN export COMPOSER_ALLOW_SUPERUSER=1 && cd /opt/cloudflare-dyndns && \
    composer install

RUN php /opt/cloudflare-dyndns/cfddns app:build -q --build-version=$VERSION

RUN cp /opt/cloudflare-dyndns/builds/cfddns /usr/bin/cfddns && \
    chmod +x /usr/bin/cfddns

# create log file
RUN touch /var/log/cfddns.log && \
    chmod 777 /var/log/cfddns.log

# cleanup
RUN rm -rf /opt/cloudflare-dyndns

COPY entrypoint.sh /usr/bin/entrypoint.sh

RUN chmod +x /usr/bin/entrypoint.sh

RUN echo "*/1 * * * * php /usr/bin/cfddns syncdns >> /dev/null 2>&1" >> /var/spool/cron/crontabs/root

WORKDIR /opt/cloudflare-dyndns

CMD ["/usr/bin/entrypoint.sh"]
