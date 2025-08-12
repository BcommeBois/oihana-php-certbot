<?php

namespace oihana\certbot\options\traits\plugins;

/**
 * Nginx Web Server plugin (options).
 */
trait CertbotNginxOptions
{
    /**
     * Nginx server root directory. (default: /etc/nginx)
     * @var string|null
     */
    public ?string $nginxServerRoot = null ;

    /**
     * Path to the 'nginx' binary, used for 'configtest' and retrieving nginx version number. (default: nginx)
     * @var bool|null
     */
    public ?bool $nginxCtl = null ;

    /**
     * Number of seconds to wait for nginx configuration changes to apply when reloading. (default: 1)
     * @var int|null
     */
    public ?int $nginxSleepSeconds = null ;
}