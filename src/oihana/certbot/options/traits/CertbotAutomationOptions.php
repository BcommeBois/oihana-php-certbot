<?php

namespace oihana\certbot\options\traits;

/**
 * The automation certbot options.
 */
trait CertbotAutomationOptions
{
    /**
     * Agree to the ACME server's Subscriber Agreement.
     * @var bool|null
     */
    public bool|null $agreeTos = null ;

    /**
     * When performing domain validation, do not consider it a failure if authorizations can not be obtained
     * for a strict subset of the requested domains.
     *
     * This may be useful for allowing renewals for multiple domains to succeed even
     * if some domains no longer point at this system. This option cannot be used with --csr. (default: False)
     * @var bool|null
     */
    public bool|null $allowSubsetOfNames = null ;

    /**
     * Allow making a certificate lineage that duplicates an existing one (both can be renewed in parallel)
     * (default: False)
     * @var bool|null
     */
    public ?bool $duplicate = null ;

    /**
     * If an existing certificate is a strict subset of the requested names,
     * always expand and replace it with the additional names. (default: Ask)
     * @var bool|null
     */
    public ?bool $expand = null ;

    /**
     * If a certificate already exists for the requested domains,
     * renew it now, regardless of whether it is near expiry. (Often --keep-until-expiring is more appropriate).
     * Also implies --expand. (default: False)
     * @var bool|null
     */
    public ?bool $forceRenewal = null ;

    /**
     * If the requested certificate matches an existing certificate, always keep the existing one until it is due for renewal (for the
     * 'run' subcommand this means reinstall the existing certificate). (default: Ask)
     * @var bool|null
     */
    public ?bool $keep = null ;

    /**
     * If the requested certificate matches an existing certificate, always keep the existing one until it is due for renewal (for the
     * 'run' subcommand this means reinstall the existing certificate). (default: Ask)
     * @var bool|null
     */
    public ?bool $keepUntilExpiring = null ;

    /**
     * When renewing or replacing a certificate, generate a new private key,
     * even if --reuse-key is set on the existing certificate.
     *
     * Combining --new-key and --reuse-key will result in the private key being replaced
     * and then reused in future renewals. (default: False)
     * @var bool|null
     */
    public ?bool $newKey = null ;

    /**
     * When renewing, do not use the same private key as the existing certificate.
     * Not reusing private keys is the default behavior of Certbot.
     * This option may be used to unset --reuse-key on an existing certificate. (default: False)
     * @var bool|null
     */
    public ?bool $noReuseKey = null ;

    /**
     * Silence all output except errors.
     * Useful for automation via cron. Implies --non-interactive. (default: False)
     * @var bool|null
     */
    public ?bool $quiet = null ;

    /**
     * If the requested certificate matches an existing certificate, always keep the existing one until it is due for renewal (for the
     * 'run' subcommand this means reinstall the existing certificate). (default: Ask)
     * @var bool|null
     */
    public ?bool $reinstall = null ;

    /**
     * When renewing, use the same private key as the existing certificate. (default: False)
     * @var bool|null
     */
    public ?bool $reuseKey = null ;

    /**
     * If a certificate already exists for the requested domains,
     * renew it now, regardless of whether it is near expiry. (Often --keep-until-expiring is more appropriate).
     * Also implies --expand. (default: False)
     * @var bool|null
     */
    public ?bool $renewsByDefault = null ;

    /**
     * If a certificate already exists for the requested certificate name but does not match
     * the requested domains, renew it now, regardless of whether it is near expiry. (default: False)
     * @var bool|null
     */
    public ?bool $renewWithNewDomains = null ;

    /**
     * Show program's version number and exit
     * @var bool|null
     */
    public ?bool $version = null ;
}