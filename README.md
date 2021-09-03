# Runtime

Configurable Docker image for web applications.

## TL;DR

```bash
docker run --name runtime ghcr.io/sitepilot/runtime:<version>
```

## Configuration 

You can override the default runtime configuration by creating / mounting a YAML configuration file to `/opt/runtime/config/custom.yml`. The custom runtime configuration wil be merged with the default configuration.

[You can use the default runtime configuration file for reference.](filesystem/opt/runtime/config/defaults.yml)

## Software

* [Alpine 3.14](https://www.alpinelinux.org/)
* [PHP 7](https://www.php.net/)
* [Nginx](https://www.nginx.com/)
* [Composer 2](https://getcomposer.org/)
* [NodeJS 14](https://nodejs.org/en/)
* [NPM](https://www.npmjs.com/)
* [Supervisor](http://supervisord.org/)
