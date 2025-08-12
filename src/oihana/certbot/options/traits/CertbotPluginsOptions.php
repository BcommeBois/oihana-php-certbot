<?php

namespace oihana\certbot\options\traits;

use oihana\files\enums\MakeFileOption;

/**
 * The certbot plugins options.
 */
trait CertbotPluginsOptions
{
    /**
     * Authenticator plugin name. (default: None)
     * @var string|null
     */
    public ?string $authenticator = null ;

    /**
     * Name of the plugin that is both an authenticator and an installer.
     * Should not be used together with --authenticator or --installer.
     * (default: Ask)
     * @var string|null
     */
    public ?string $configurator = null ;

    /**
     * Use the Apache plugin for authentication & installation.
     * @var bool|null
     */
    public ?bool $apache = null ;

    /**
     * Installer plugin name (also used to find domains). (default: None)
     * @var string|null
     */
    public ?string $installer = null ;

    /**
     * Obtain certificates interactively, or using shell script hooks.
     * @var bool|null
     */
    public ?bool $manual = null ;

    /**
     * Use the Nginx plugin for authentication & installation
     * @var bool|null
     */
    public ?bool $nginx = null ;

    /**
     * Run a standalone webserver for authentication.
     * @var bool|null
     */
    public ?bool $standalone = null ;

    /**
     * Place files in a server's webroot folder for authentication.
     * @var bool|null
     */
    public ?bool $webroot = null ;
}