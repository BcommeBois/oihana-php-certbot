<?php

namespace oihana\certbot\options\traits\plugins;

/**
 * Authenticate through manual configuration or custom shell scripts.
 *
 * When using shell scripts, an authenticator script must be provided.
 *
 * The environment variables available to this script depend on the type of challenge.
 * $CERTBOT_DOMAIN will always contain the domain being authenticated.
 *
 * For HTTP-01 and DNS-01, $CERTBOT_VALIDATION is the validation string, and $CERTBOT_TOKEN is the filename
 * of the resource requested when performing an HTTP-01 challenge.
 *
 * An additional cleanup script can also be provided and can use
 * the additional variable $CERTBOT_AUTH_OUTPUT which contains the stdout output from the auth script.
 *
 * For both authenticator and cleanup script, on HTTP-01 and DNS-01 challenges,
 * $CERTBOT_REMAINING_CHALLENGES will be equal to the number of challenges that remain after the current one,
 * and $CERTBOT_ALL_DOMAINS contains a comma-separated list of all domains that are challenged for the current certificate.
 */
trait CertbotManualOptions
{
    /**
     * Path or command to execute for the authentication script (default: None).
     * @var string|null
     */
    public ?string $manualAuthHook = null ;

    /**
     * Path or command to execute for the cleanup script (default: None)
     * @var string|null
     */
    public ?string $manualCleanupHook = null ;
}