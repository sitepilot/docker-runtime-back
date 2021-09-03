# Set to number of CPU cores, auto will try to autodetect.
worker_processes auto;

# Maximum open file descriptors per process. Should be greater than worker_connections.
worker_rlimit_nofile 8192;

# File that stores the process ID. Rarely needs changing.
pid /run/nginx.pid;

# Default error log
error_log /dev/stderr;

events {
    # Set the maximum number of connection each worker process can open. Anything higher than this
    # will require Unix optimisations.
    worker_connections 8000;

    # Accept all new connections as they're opened.
    multi_accept on;
}

http {
    # HTTP
    include {{ $paths['config'] }}/nginx.d/http/http.conf;

    # MIME Types
    include {{ $paths['config'] }}/nginx.d/http/mime-types.conf;
    default_type application/octet-stream;

    # How long each connection should stay open for.
    keepalive_timeout 15;

    # Timeout for reading client request body.
    client_body_timeout 30;

    # Timeout for reading client request header.
    client_header_timeout 30;

    # Timeout for transmitting reponse to client.
    send_timeout 30;

    # Set the maximum allowed size of client request body. This should be set
    # to the value of files sizes you wish to upload to the WordPress Media Library.
    # You may also need to change the values `upload_max_filesize` and `post_max_size` within
    # your php.ini for the changes to apply.
    client_max_body_size {{ $php['uploadSize'] }};

    # Some WP plugins that push large amounts of data via cookies
    # can cause 500 HTTP errors if these values aren't increased.
    fastcgi_buffers 16 16k;
    fastcgi_buffer_size 32k;

    # Gzip
    include {{ $paths['config'] }}/nginx.d/http/gzip.conf;

    # Exposes configured php pool on $upstream variable
    include {{ $paths['config'] }}/nginx.d/http/php-pool.conf;

    # Map https to forwarded proto
    map $http_x_forwarded_proto $https_forwarded {
        default off;
        https on;
    }
    
    map $https $https_flag {
        default $https_forwarded;
        on on;
    }

    server {
        server_name runtime;
        listen 80 default_server;
        resolver 127.0.0.11 valid=30s;

@if(!empty($ssl["key"]) && !empty($ssl["cert"]))
        listen 443 ssl default_server;
        ssl_certificate {{ $paths['config'] }}/ssl.cert;
        ssl_certificate_key {{ $paths['config'] }}/ssl.key;
@endif

        # Defaults
        include {{ $paths['config'] }}/nginx.d/server/defaults.conf; 

        # Locations
        location ~ ^/-/(ping)$ {
            access_log off;
            include {{ $paths['config'] }}/nginx.d/fastcgi/params.conf;
            fastcgi_pass $upstream;
        }

@foreach($locations as $location) 
        location {!! $location['path'] !!} {
            access_log off;
            proxy_http_version 1.1;
            proxy_set_header Upgrade $http_upgrade;
            proxy_set_header Connection "upgrade";
            proxy_set_header Host $host;
            {!! $location['config'] ?? '' !!}
            proxy_pass {!! $location['proxy'] !!};
        }
@endforeach

        location ~ ^/ {
            try_files $uri $uri/ /index.php?$args;

            # Logs
            access_log {{ $paths['home'] }}/logs/access.log;

            # Root
            root {{ $paths['home'] }}/public{{ $docRoot }};

            # PHP
            location ~ \.php$ {
                try_files $uri =404;
                
                include {{ $paths['config'] }}/nginx.d/fastcgi/params.conf;

                fastcgi_param PLATFORM  "Sitepilot";
                fastcgi_param PHP_VALUE "mail.log={{ $paths['home'] }}/logs/php-mail.log;\nerror_log={{ $paths['home'] }}/logs/php-error.log;";

                fastcgi_pass $upstream;
            }
        }		
    }
}