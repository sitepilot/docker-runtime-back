[global]
; Log to stderr
error_log = /dev/stderr

[www]
listen = /run/php.sock
listen.owner = {{ env('RUNTIME_USER_NAME') }}
listen.group = {{ env('RUNTIME_USER_NAME') }}
listen.mode = 0660

pm = ondemand
pm.max_children = 100
pm.process_idle_timeout = 10s;
pm.max_requests = 500
catch_workers_output = yes
decorate_workers_output = no
ping.path = /-/ping
