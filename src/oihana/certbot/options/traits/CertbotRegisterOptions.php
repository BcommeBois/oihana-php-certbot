<?php

namespace oihana\certbot\options\traits;

/**
 * The global Certbot options.
 */
trait CertbotRegisterOptions
{
    /**
     * Share your e-mail address with EFF (default: None)
     * @var bool|null
     */
    public ?bool $effEmail = null ;

    /**
     * Email used for registration and recovery contact. Use comma to register multiple emails,
     * ex: u1@example.com,u2@example.com. (default: Ask).
     * @var string|null
     */
    public ?string $email = null ;

    /**
     * Don't share your e-mail address with EFF (default: None)
     * @var bool|null
     */
    public ?bool $noEffEmail = null ;

    /**
     * Specifying this flag enables registering an account with no email address.
     *
     * This is strongly discouraged, because you will be unable to receive notice about impending expiration
     * or revocation of your certificates or problems with your Certbot installation that will lead to failure to renew.
     * (default: False)
     * @var bool|null
     */
    public ?bool $registerUnsafelyWithoutEmail = null ;
}