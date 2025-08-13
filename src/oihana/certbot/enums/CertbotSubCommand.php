<?php

namespace oihana\certbot\enums;

use oihana\reflect\traits\ConstantsTrait;

/**
 * The subcommands enumeration of the 'certbot' command.
 */
class CertbotSubCommand
{
    use ConstantsTrait ;

    /**
     * The 'certbot' main command.
     */
    public const string CERTBOT = 'certbot' ;

    /**
     * Obtain or renew a certificate, but do not install it.
     */
    public const string CERT_ONLY = 'certbot certonly' ;

    /**
     * Add security enhancements to your existing configuration.
     */
    public const string ENHANCE = 'certbot enhance' ;

    /**
     * Obtain & install a certificate in your current webserver (default).
     */
    public const string RUN = 'certbot run' ;

    // ----- Manage

    /**
     * List certificates managed by Certbot.
     */
    public const string CERTIFICATES = 'certbot certificates' ;

    /**
     * Clean up all files related to a certificate.
     */
    public const string DELETE = 'certbot delete' ;

    /**
     * Renew all previously obtained certificates that are near expiry. (or one specified with --cert-name).
     */
    public const string RENEW = 'certbot renew' ;

    /**
     * Revoke a certificate specified with --cert-path or --cert-name
     */
    public const string REVOKE = 'certbot revoke' ;

    /**
     * Recreate symlinks in your /etc/letsencrypt/live/ directory
     */
    public const string UPDATE_SYMLINKS = 'certbot update_symlinks' ;
}