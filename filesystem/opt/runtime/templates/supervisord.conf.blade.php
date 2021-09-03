[program:php-fpm]
command=php-fpm7 -F
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0
autorestart=false
startretries=0

[program:nginx]
command=nginx -g 'daemon off;'
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0
autorestart=false
startretries=0

@foreach($supervisor ?? [] as $item)
[program:{{ $item['name'] }}]
@if(!empty($item['directory']))
directory={{ $item['directory'] }}
@endif
command={{ $item['command'] }}
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0
autorestart=true
startretries=0
@endforeach

[supervisord]
nodaemon=true
logfile=/dev/null
logfile_maxbytes=0
pidfile=/tmp/supervisord.pid

[unix_http_server]
file=/run/supervisor.sock
username = runtime
password = runtime

[supervisorctl]
serverurl=unix:///run/supervisor.sock
username = runtime
password = runtime

[rpcinterface:supervisor]
supervisor.rpcinterface_factory = supervisor.rpcinterface:make_main_rpcinterface

[eventlistener:processes]
command=bash -c "printf 'READY\n' && while read line; do kill -SIGQUIT $PPID; done < /dev/stdin"
events=PROCESS_STATE_EXITED,PROCESS_STATE_FATAL
