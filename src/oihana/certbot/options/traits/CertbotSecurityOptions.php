<?php

namespace oihana\certbot\options\traits;


/**
 * The certbot security options.
 */
trait CertbotSecurityOptions
{
    /**
     * Gradually increasing max-age value for HTTP Strict Transport Security security header (default: False)
     * @var string|null
     */
    public ?string $autoHsts = null ;

    /**
     * Name of the plugin that is both an authenticator and an installer.
     * Should not be used together with --authenticator or --installer.
     * (default: Ask)
     * @var string|null
     */
    public ?string $configurator = null ;

    /**
     * The SECG elliptic curve name to use. Please see RFC 8446 for supported values. (default: secp256r1).
     * @var string|null
     */
    public ?string $ellipticCurve = null ;

    /**
     * Add the Strict-Transport-Security header to every HTTP response.
     * Forcing browser to always use SSL for the domain. Defends against SSL Stripping. (default: None)
     * @var bool|null
     */
    public ?bool $hsts = null ;

    /**
     * Type of generated private key. Only *ONE* per invocation can be provided at this time.
     * Values : {rsa,ecdsa} - Default : ecdsa
     * @var string|null
     */
    public ?string $keyType = null ;

    /**
     * Adds the OCSP Must-Staple extension to the certificate.
     * Autoconfigures OCSP Stapling for supported setups (Apache version >= 2.3.3). (default: False)
     * @var bool|null
     */
    public ?bool $mustStaple = null ;

    /**
     * Do not automatically redirect all HTTP traffic to HTTPS for the newly authenticated vhost.
     * (default: redirect enabled for install and run, disabled for enhance)
     * @var bool|null
     */
    public ?bool $noRedirect = null ;

    /**
     * Automatically redirect all HTTP traffic to HTTPS for the newly authenticated vhost.
     * (default: redirect enabled for install and run, disabled for enhance)
     * @var bool|null
     */
    public ?bool $redirect = null ;

    /**
     * Size of the RSA key. (default: 2048)
     * @var int|null
     */
    public ?int $rsaKeySize = null ;

    /**
     * Enables OCSP Stapling.
     * A valid OCSP response is stapled to the certificate that the server offers during TLS. (default: None)
     * @var bool|null
     */
    public ?bool $stapleOcsp = null ;

    /**
     * Require that all configuration files are owned by the current user;
     * only needed if your config is somewhere unsafe like /tmp/ (default: False)
     * @var bool|null
     */
    public ?bool $strictPermissions = null ;

    /**
     * Add the "Content-Security-Policy: upgrade-insecure-requests" header to every HTTP response.
     * Forcing the browser to use https:// for every http:// resource. (default: None)
     * @var bool|null
     */
    public ?bool $uir = null ;
}