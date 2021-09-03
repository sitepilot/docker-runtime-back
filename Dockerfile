FROM alpine:3.14

LABEL maintainer="support@sitepilot.io" \
    org.opencontainers.image.source="https://github.com/sitepilot/docker-runtime"

# Environment
ENV RUNTIME_USER_ID=1001
ENV RUNTIME_USER_NAME=runtime
ENV PATH="${PATH}:/opt/runtime/bin:/home/runtime/public"
ENV PHP_INI_SCAN_DIR="/etc/php7/conf.d:/opt/runtime/config/php.d"

# Install packages
RUN apk --no-cache add php7 php7-fpm php7-opcache php7-mysqli php7-json php7-openssl php7-curl \
    php7-zlib php7-xml php7-phar php7-intl php7-dom php7-xmlreader php7-ctype php7-session php7-pdo_mysql \
    php7-mbstring php7-intl php7-gd php7-bcmath php7-tokenizer php7-iconv php7-exif php7-fileinfo \
    php7-imagick php7-zip php7-redis php7-simplexml php7-pdo php7-soap php7-pcntl php7-xmlwriter php7-posix \
    nginx supervisor curl less nano bash nodejs npm composer \
    && rm -fr /var/cache/apk/*

# Runtime CLI
RUN wget -q https://github.com/sitepilot/runtime-cli/releases/download/v1.0.3/runtime -O /usr/local/bin/runtime-cli \
    && chmod +x /usr/local/bin/runtime-cli \
    && runtime-cli --version

# Configure nginx
RUN ln -sf /opt/runtime/config/nginx.conf /etc/nginx/nginx.conf

# Configure php-fpm
RUN ln -sf /opt/runtime/config/fpm-pool.conf /etc/php7/php-fpm.d/www.conf

# Create user
RUN addgroup -S ${RUNTIME_USER_NAME} --gid ${RUNTIME_USER_ID} \
    && adduser -s /bin/bash -S ${RUNTIME_USER_NAME} -G ${RUNTIME_USER_NAME} --uid ${RUNTIME_USER_ID} --home /home/runtime

# Copy filesystem
COPY --chown=$RUNTIME_USER_NAME filesystem /

# Set permissions
RUN chmod +x /opt/runtime/bin/runtime \
    && /opt/runtime/bin/runtime reset-permissions

# Set runtime user
USER ${RUNTIME_USER_ID}

# Set workdir
WORKDIR /home/runtime/public

# Expose the ports
EXPOSE 80
EXPOSE 443

# Run entrypoint scripts
ENTRYPOINT ["/opt/runtime/bin/entrypoint"]

# Let supervisord start services
CMD ["/usr/bin/supervisord", "-c", "/opt/runtime/config/supervisord.conf"]
