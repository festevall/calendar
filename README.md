Symfony
========================
Installation

git clone

php app/console doctrine:schema:update --force

php app/console fos:user:create admin --super-admin

php app/console fos:user:activate admin

CONFIG NGINX

    server {
        listen 80;
        server_name calendar.local;
        root /var/www/calendar.local/web;
    
        location / {
            # try to serve file directly, fallback to app.php
            try_files $uri /app_dev.php$is_args$args;
        }
        # DEV
        # This rule should only be placed on your development environment
        # In production, don't include this and don't deploy app_dev.php or config.php
        location ~ ^/(app_dev|config)\.php(/|$) {
            #fastcgi_pass unix:/run/php/php7.0-fpm.sock;
            fastcgi_pass 127.0.0.1:9071;
            #fastcgi_pass unix:/usr/local/var/run/php71-fpm.sock;
            fastcgi_split_path_info ^(.+\.php)(/.*)$;
            include fastcgi_params;
            fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
            fastcgi_param HTTPS off;
        }
        # PROD
        location ~ ^/app\.php(/|$) {
            #fastcgi_pass unix:/var/run/php5-fpm.sock;
            fastcgi_pass 127.0.0.1:9071;
            #fastcgi_pass unix:/usr/local/var/run/php71-fpm.sock;
            fastcgi_split_path_info ^(.+\.php)(/.*)$;
            include fastcgi_params;
            fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
            fastcgi_param HTTPS off;
            # Prevents URIs that include the front controller. This will 404:
            # http://domain.tld/app.php/some-path
            # Remove the internal directive to allow URIs like this
            internal;
        }
        #log_format  graylog2_format  '$remote_addr - $remote_user [$time_local] "$request" $status $body_bytes_sent "$http_referer" "$http_user_agent" "$http_x_forwarded_for" <msec=$msec|connection=$connection|connection_requests=$connection_requests|millis=$request_time>';
    
        # replace the hostnames with the IP or hostname of your Graylog2 server
        #access_log syslog:server=127.0.0.1:12301 graylog2_format;
        #error_log syslog:server=127.0.0.1:12302;
    
        error_log /var/logs/calendar/error.log;
        access_log /var/logs/calendar/access.log;
    }