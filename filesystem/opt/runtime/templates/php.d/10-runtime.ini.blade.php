date.timezone="Europe/Amsterdam"
post_max_size={{ $php['uploadSize'] }}
upload_max_filesize={{ $php['uploadSize'] }}
memory_limit={{ $php['memoryLimit'] }}
max_execution_time={{ $php['maxExecutionTime'] }}
max_input_vars={{ $php['maxInputVars'] }}

opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=50000
opcache.revalidate_freq=2
opcache.fast_shutdown=1
opcache.enable_cli=1

expose_php=Off
