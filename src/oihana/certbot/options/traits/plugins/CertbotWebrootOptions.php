<?php

namespace oihana\certbot\options\traits\plugins;

/**
 * Place files in webroot directory (options).
 */
trait CertbotWebrootOptions
{
    /**
     * public_html / webroot path.
     *
     * This can be specified multiple times to handle different domains; each domain will have the webroot
     * path that preceded it.
     *
     * For instance: `-w /var/www/example -d example.com -d www.example.com -w /var/www/thing -d thing.net -d m.thing.net` (default: Ask)
     * @var string|null
     */
    public ?string $webrootPath = null ;

    /**
     * JSON dictionary mapping domains to webroot paths; this implies -d for each entry.
     *
     * You may need to escape this from your shell.
     * E.g.: --webroot-map '{"eg1.is,m.eg1.is":"/www/eg1/", "eg2.is":"/www/eg2"}'
     *
     * This option is merged with, but takes precedence over,
     * -w / -d entries. At present, if you put webroot-map in a config file, it needs to be on a single line, like: webroot-map =
     * {"example.com":"/var/www"}. (default: {})
     * @var string|null
     */
    public ?string $webrootMap = null ;
}